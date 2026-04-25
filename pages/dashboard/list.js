const { createApp } = Vue

createApp({
    data() {
        return {
            datas: [],
            dutyTypes: [],
            isLoading: false,
            search: '',
            filter_month: new Date().getMonth() + 1,
            filter_year: new Date().getFullYear(),
            filterMyDuty: false,
            ssid: '',
            selected_types: [],
            months: ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"],
            years: []
        }
    },
    computed: {
        currentUserId() {
            return this.ssid;
        },
        dutyStats() {
            if (!this.dutyTypes || !Array.isArray(this.dutyTypes)) return [];
            const stats = {};
            this.dutyTypes.forEach(type => {
                const key = type.name + '|' + type.u_role;
                stats[key] = {
                    key: key,
                    name: type.name,
                    u_role: type.u_role,
                    color: type.color || '#cccccc',
                    count: 0,
                    active: this.selected_types.includes(key)
                };
            });
            this.datas.forEach(event => {
                const type = this.dutyTypes.find(t => t.color === event.backgroundColor);
                if (type) {
                    const key = type.name + '|' + type.u_role;
                    if (stats[key]) stats[key].count++;
                }
            });
            return Object.values(stats).filter(s => s.count > 0);
        },
        filteredEvents() {
            let filtered = this.datas;

            // Search filter
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

            // Month filter
            if (this.filter_month !== '') {
                filtered = filtered.filter(ev => {
                    const d = new Date(ev.start);
                    return (d.getMonth() + 1) == this.filter_month;
                });
            }

            // Year filter
            if (this.filter_year !== '') {
                filtered = filtered.filter(ev => {
                    const d = new Date(ev.start);
                    return d.getFullYear() == this.filter_year;
                });
            }

            // Type filter (selected_types)
            if (this.selected_types.length > 0) {
                filtered = filtered.filter(ev => {
                    const type = this.dutyTypes.find(t => t.color === ev.backgroundColor);
                    if (!type) return true;
                    const key = type.name + '|' + type.u_role;
                    return this.selected_types.includes(key);
                });
            }

            // My Duty filter
            if (this.filterMyDuty && this.ssid) {
                filtered = filtered.filter(ev => {
                    return String(ev.extendedProps.user_id) === String(this.ssid);
                });
            }

            return filtered;
        },
        groupedEvents() {
            const groups = {};
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
                
                const typeKey = ev.backgroundColor + '|' + ev.extendedProps.ven_com_name;
                if (!groups[dateStr].dutyGroups[typeKey]) {
                    groups[dateStr].dutyGroups[typeKey] = {
                        name: ev.extendedProps.ven_com_name,
                        color: ev.backgroundColor,
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
                        dutyGroups: Object.values(group.dutyGroups)
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
                        
                        // Initialize selected_types with all found types
                        if (this.selected_types.length === 0) {
                            this.dutyTypes.forEach(type => {
                                const key = type.name + '|' + type.u_role;
                                if (!this.selected_types.includes(key)) {
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
        }
    },
    watch: {
        filterMyDuty(newVal) {
            console.log("filterMyDuty changed:", newVal);
            console.log("Filtered result count:", this.filteredEvents.length);
        }
    }
}).mount('#list-view')
