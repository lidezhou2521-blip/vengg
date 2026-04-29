const { createApp } = Vue

createApp({
    data() {
        return {
            datas: [],
            dutyTypes: [],
            isLoading: false,
            search: '',
            filter_month: '',
            filter_year: '',
            filterMyDuty: false,
            ssid: '',
            selected_types: [],
            months: ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"],
            years: [],
            // Modal states
            data_event: { 
                uid: 5555,
                uname: '',
                ven_date: '',
                ven_time: '',
                DN: '',
                ven_month: '',
                ven_com_id: '',
                st: '',
            },
            my_v: [],
            vh: [],
            d_now: '',
            users: [],
            ch_v1: '',
            ch_v2: '',
            act: 'a',
            ch_a: false,
            ch_b: false,
            user_id2: '',
            u_name2: '',
            u_img2: '',
        }
    },
    computed: {
        currentUserId() {
            return this.ssid;
        },
        // All filters EXCEPT type-filter (used for badge counts)
        filteredBySearch() {
            let filtered = this.datas;
            if (this.search.trim() !== '') {
                const q = this.search.toLowerCase();
                filtered = filtered.filter(ev => {
                    const ep = ev.extendedProps || {};
                    return (ev.title && ev.title.toLowerCase().includes(q)) ||
                           (ep.u_name && ep.u_name.toLowerCase().includes(q)) ||
                           (ep.u_role && ep.u_role.toLowerCase().includes(q)) ||
                           (ep.ven_com_name && ep.ven_com_name.toLowerCase().includes(q));
                });
            }
            if (this.filter_month !== '') {
                filtered = filtered.filter(ev => (new Date(ev.start).getMonth() + 1) == this.filter_month);
            }
            if (this.filter_year !== '') {
                filtered = filtered.filter(ev => new Date(ev.start).getFullYear() == this.filter_year);
            }
            if (this.filterMyDuty && this.ssid) {
                filtered = filtered.filter(ev => String((ev.extendedProps || {}).user_id) === String(this.ssid));
            }
            return filtered;
        },
        dutyStats() {
            if (!this.dutyTypes || !Array.isArray(this.dutyTypes)) return [];
            const stats = {};
            this.dutyTypes.forEach(type => {
                if (!type.vn_id) return;
                const key = String(type.vn_id);
                stats[key] = { key, name: type.name, u_role: type.u_role || '', color: type.color || '#cccccc', count: 0, active: this.selected_types.includes(key) };
            });
            // Count from filteredBySearch so badge count matches visible list
            this.filteredBySearch.forEach(event => {
                const ep = event.extendedProps || {};
                if (ep.vn_id) {
                    const key = String(ep.vn_id);
                    if (stats[key]) stats[key].count++;
                }
            });
            return Object.values(stats).filter(s => s.count > 0);
        },
        filteredEvents() {
            // Start from pre-filtered set (search + month + year + myDuty already applied)
            let filtered = this.filteredBySearch;

            // Type filter — match by vn_id (duty name level)
            if (this.selected_types.length > 0) {
                filtered = filtered.filter(ev => {
                    const ep = ev.extendedProps || {};
                    if (!ep.vn_id) return true;
                    return this.selected_types.includes(String(ep.vn_id));
                });
            }

            return filtered;
        },
        groupedEvents() {
            const groups = {};

            // Build a lookup: vn_id → original color from dutyTypes
            const colorMap = {};
            (this.dutyTypes || []).forEach(t => {
                if (t.vn_id) colorMap[String(t.vn_id)] = t.color || '#cccccc';
            });

            this.filteredEvents.forEach(ev => {
                const dateStr = ev.start.split(' ')[0];
                if (!groups[dateStr]) {
                    const d = new Date(dateStr);
                    const dateText = `${d.getDate()} ${this.months[d.getMonth()]} ${d.getFullYear() + 543}`;
                    groups[dateStr] = {
                        date: dateStr,
                        dateText: dateText,
                        dutyGroups: {}
                    };
                }

                // Key by vn_id (not backgroundColor) so conflict-red entries stay in same group
                const ep = ev.extendedProps || {};
                const typeKey = String(ep.vn_id || '') + '|' + (ep.ven_com_name || '');
                if (!groups[dateStr].dutyGroups[typeKey]) {
                    // Use original duty color, not the conflict red
                    const origColor = colorMap[String(ep.vn_id)] || ev.backgroundColor;
                    groups[dateStr].dutyGroups[typeKey] = {
                        name: ep.ven_com_name,
                        color: origColor,
                        events: []
                    };
                }
                groups[dateStr].dutyGroups[typeKey].events.push(ev);
            });

            // Convert to array and sort
            return Object.values(groups)
                .sort((a, b) => a.date.localeCompare(b.date))
                .map(group => {
                    return {
                        ...group,
                        dutyGroups: Object.values(group.dutyGroups).map(dg => {
                            // Sort by ven_time (encoded in ev.start) — correctly reflects queue order
                            dg.events.sort((a, b) => a.start.localeCompare(b.start));
                            return dg;
                        })
                    };
                });
        }
    },
    mounted() {
        const currentYear = new Date().getFullYear();
        for (let i = currentYear - 5; i <= currentYear + 5; i++) {
            this.years.push(i);
        }
        this.fetchData();
    },
    methods: {
        fetchData() {
            this.isLoading = true;
            axios.get('../../server/dashboard/get_vens.php')
                .then(response => {
                    if (response.data.status) {
                        this.datas = response.data.respJSON;
                        this.dutyTypes = response.data.res || [];
                        this.ssid = response.data.ssid || '';
                        console.log("Datas fetched:", this.datas.length);
                        console.log("SSID from API:", this.ssid);

                        // Initialize selected_types with all duty names (using vn_id)
                        if (this.selected_types.length === 0) {
                            this.dutyTypes.forEach(type => {
                                const key = String(type.vn_id);
                                if (key && key !== 'undefined' && !this.selected_types.includes(key)) {
                                    this.selected_types.push(key);
                                }
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error("Error fetching data:", error);
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        toggleType(key) {
            const index = this.selected_types.indexOf(key);
            if (index > -1) {
                this.selected_types.splice(index, 1);
            } else {
                this.selected_types.push(key);
            }
        },
        showDetail(id) {
            console.log("Opening detail for event ID:", id);
            if (this.ssid != '') {
                this.isLoading = true;
                axios.post('../../server/dashboard/get_ven.php', { id: id, uid: this.ssid })
                    .then(response => {
                        if (response.data.status) {
                            this.data_event = response.data.respJSON
                            this.my_v = response.data.my_v
                            this.vh = response.data.vh
                            this.d_now = response.data.d_now
                            this.users = response.data.users
                            this.$refs['show_modal'].click()
                        } else {
                            this.alert('warning', response.data.message, 0)
                        }
                    })
                    .catch(error => {
                        console.error(error);
                    })
                    .finally(() => {
                        this.isLoading = false;
                    });
            } else {
                this.alert('warning', 'กรุณา Login..', 0)
            }
        },
        change_a(my_v_index) {
            this.act = 'a'
            this.$refs.show_modal_b.click()
            this.ch_v1 = this.my_v[my_v_index]
            this.ch_v2 = this.data_event
        },
        change_b(uid, u_name, img) {
            this.act = 'b'
            this.ch_v1 = this.data_event
            this.user_id2 = uid
            this.u_name2 = u_name
            this.u_img2 = img
            this.$refs.show_modal_b.click()
        },
        change_save() {
            this.isLoading = true;
            axios.post('../../server/dashboard/change_save.php', { ch_v1: this.ch_v1, ch_v2: this.ch_v2 })
                .then(response => {
                    if (response.data.status) {
                        this.fetchData()
                        this.$refs.close_modal.click()
                        this.$refs.close_modal_b.click()
                        this.alert('success', response.data.message, 1000)
                        window.open('../history/index.php', '_self')
                    } else {
                        this.alert('warning', response.data.message, 0)
                    }
                    this.act = 'a'
                })
                .catch(error => {
                    this.alert('warning', error, 0)
                    console.log(error);
                })
                .finally(() => {
                    this.isLoading = false;
                })
        },
        change_save_bb() {
            this.isLoading = true;
            axios.post('../../server/dashboard/change_save_b.php', { ch_v1: this.ch_v1, user_id2: this.user_id2, u_name2: this.u_name2 })
                .then(response => {
                    if (response.data.status) {
                        this.fetchData()
                        this.$refs.close_modal.click()
                        this.$refs.close_modal_b.click()
                        this.alert('success', response.data.message, 1000)
                        window.open('../history/index.php', '_self')
                    } else {
                        this.alert('warning', response.data.message, 0)
                    }
                })
                .catch(error => {
                    console.log(error);
                })
                .finally(() => {
                    this.isLoading = false;
                })
        },
        close_m() {
            this.ch_a = false
            this.ch_b = false
        },
        close_m_b() {
            this.$refs.close_modal.click()
        },
        report_jk(ven_date, DN) {
            this.isLoading = true;
            axios.post('../../server/dashboard/report_jk.php', { ven_date: ven_date, DN: DN })
                .then(response => {
                    if (response.data.status) {
                        this.alert("success", response.data.message, 1000)
                        window.open('../../uploads/ven_jk.docx', '_blank')
                    } else {
                        this.alert("warning", response.data.message, 0)
                    }
                })
                .catch(error => {
                    console.log(error);
                })
                .finally(() => {
                    this.isLoading = false;
                })
        },
        alert(icon, message, timer = 0) {
            swal.fire({
                icon: icon,
                title: message,
                showConfirmButton: false,
                timer: timer
            });
        },
    },
    watch: {
        filterMyDuty(newVal) {
            console.log("filterMyDuty changed:", newVal);
            console.log("Filtered result count:", this.filteredEvents.length);
        }
    }
}).mount('#list-view')
