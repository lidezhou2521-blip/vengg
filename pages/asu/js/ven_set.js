
Vue.createApp({
  data() {
    return {
      q: '',

      url_base: '',
      url_base_app: '',
      url_base_now: '',
      datas: [
        {
          id: 'a',
          title: 'my event',
          start: '2022-09-01',
          extendedProps: {
            user_id: 5555,
            uname: '',
            ven_date: '',
            ven_time: '',
            DN: '',
            ven_month: '',
            ven_com_id: '',
            st: '',

          }
        }
      ],
      data_event: {
        user_id: 5555,
        uname: '',
        ven_date: '',
        ven_time: '',
        DN: '',
        ven_month: '',
        ven_com_id: [],
        st: '',
      },
      data_event_ven_coms: [],

      months: [],
      ven_month: '',

      profiles: [],
      profiles_filtered: [],
      qp: '',
      ven_name_subs: [],
      ven_coms: [],
      vc_index: '',
      vns_index: '',


      /** เตรียมส่ง */
      ven_com: '',
      ven_name_sub: '',

      vn_id: 0,
      vns_id: 0,

      label_message: '<--กรุณาเลือกคำสั่ง',
      isLoading: false,
      show_sidebar: true,
      calendar: null,
      
      duty_types: [],
      selected_types: [],
      search_query: '',
    }
  },
  computed: {
    dutyStats() {
      try {
        if (!this.duty_types || !Array.isArray(this.duty_types)) return [];
        const stats = {};
        this.duty_types.forEach(type => {
          if (type && type.vn_id) {
            const key = String(type.vn_id);
            stats[key] = {
              key: key,
              name: type.name,
              u_role: type.u_role || '',
              color: type.color || '#cccccc',
              count: 0,
              active: this.selected_types.includes(key)
            };
          }
        });
        if (this.datas && Array.isArray(this.datas)) {
          this.datas.forEach(event => {
            if (event && event.extendedProps && event.extendedProps.vn_id) {
              const key = String(event.extendedProps.vn_id);
              if (stats[key]) stats[key].count++;
            }
          });
        }
        return Object.values(stats).filter(s => s.count > 0);
      } catch (e) { console.error('dutyStats error:', e); return []; }
    },
    filteredEvents() {
      if (!this.datas) return [];
      let filtered = this.datas;

      // Filter by selected types — match by vn_id
      if (this.selected_types.length > 0) {
        filtered = filtered.filter(ev => {
          const ep = ev.extendedProps || {};
          if (!ep.vn_id) return true; // keep if unknown
          return this.selected_types.includes(String(ep.vn_id));
        });
      }

      // Filter by search query — check extendedProps fields
      if (this.search_query && this.search_query.trim() !== '') {
        const query = this.search_query.toLowerCase();
        filtered = filtered.filter(ev => {
          const titleMatch = ev.title && ev.title.toLowerCase().includes(query);
          const ep = ev.extendedProps || {};
          const uNameMatch = ep.u_name && ep.u_name.toLowerCase().includes(query);
          const uRoleMatch = ep.u_role && ep.u_role.toLowerCase().includes(query);
          const venComMatch = ep.ven_com_name && ep.ven_com_name.toLowerCase().includes(query);
          return titleMatch || uNameMatch || uRoleMatch || venComMatch;
        });
      }

      return filtered;
    }
  },
  mounted() {
    this.url_base = window.location.protocol + '//' + window.location.host;
    this.url_base_app = window.location.protocol + '//' + window.location.host + '/venset/';
    // const d = 
    this.ven_month = new Date();
    this.get_vens()
    this.get_ven_month1()


  },
  watch: {
    q() {
      this.ch_search_pro()
    },
    qp(val) {
      if (val.trim() === '') {
        this.profiles_filtered = this.profiles;
      } else {
        const query = val.toLowerCase();
        this.profiles_filtered = this.profiles.filter(p => p.u_name.toLowerCase().includes(query));
      }
    },
    filteredEvents: {
      handler() {
        this.cal_render();
      },
      deep: true
    },
    show_sidebar() {
      this.$nextTick(() => {
        this.cal_render();
      });
    }
  },
  methods: {
    toggleType(key) {
      const index = this.selected_types.indexOf(key);
      if (index > -1) {
        this.selected_types.splice(index, 1);
      } else {
        this.selected_types.push(key);
      }
    },
    open_check_duty() {
      // ดึง ven_month ปัจจุบันจาก calendar หรือ this.ven_month
      let month = this.ven_month;
      if (this.calendar) {
        const d = this.calendar.getDate();
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        month = y + '-' + m;
      }
      let excluded = JSON.parse(localStorage.getItem('excluded_duties_' + month) || '[]');
      axios.post('../../server/asu/report/report_dutytype.php', { ven_month: month, excluded_duties: excluded })
        .then(response => {
          if (response.data.status) {
            localStorage.setItem('print_dutytype', JSON.stringify(response.data));
            window.open('./report-check-duty.php', '_blank');
          } else {
            this.alert('warning', response.data.message || 'ไม่พบข้อมูล', 3000);
          }
        })
        .catch(err => {
          console.error(err);
          this.alert('warning', 'เกิดข้อผิดพลาดในการโหลดข้อมูล', 0);
        });
    },
    // get_ven_names(){
    //   axios.post('../../server/asu/ven_set/get_ven_names.php')
    //     .then(response => {
    //       if (response.data.status) {
    //         this.ven_names = response.data.respJSON
    //       }else{
    //         this.alert('warning',response.data.message,0)
    //         this.ven_names = []

    //       }
    //     })
    //     .catch(function (error) {        
    //     console.log(error);
    //   });
    // },
    get_ven_coms() {
      axios.post('../../server/asu/ven_set/get_ven_coms.php', { ven_month: this.ven_month })
        .then(response => {
          if (response.data.status) {
            this.ven_coms = response.data.respJSON;
            // this.alert('success',response.data.message,1000)
          } else {
            this.ven_coms = []
            this.alert('warning', response.data.message, 0)
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    ch_sel_ven_month() {
      this.ven_coms = []
      this.ven_com = ''
      this.ven_name_subs = []
      this.ven_name_sub = ''
      this.vc_index = ''
      this.vns_index = ''
      this.profiles = []
      this.profiles_filtered = []
      // Destroy old calendar so cal_render uses new ven_month
      if (this.calendar) {
        this.calendar.destroy();
        this.calendar = null;
      }
      this.get_ven_coms()
      this.cal_render()

    },

    ch_sel_ven_name(index) {
      this.ven_name_sub = ''

      this.ven_name_subs = []
      this.ven_com = this.ven_coms[index]
      axios.post('../../server/asu/ven_set/get_vns_vs.php', { vn_id: this.ven_com.vn_id })
        .then(response => {
          if (response.data.status) {
            this.ven_name_subs = response.data.respJSON
          } else {
            this.vc_index = ''
            this.ven_name_sub = ''
            this.ven_name_subs = []
            this.alert('warning', response.data.message, 0)
          }
          this.vns_index = ''
          this.profiles = []
        })
        .catch(function (error) {
          console.log(error);
        });

    },

    ch_sel_vns(index) {
      console.log(index)
      this.ven_name_sub = this.ven_name_subs[index]

      axios.post('../../server/asu/ven_set/get_ven_users.php', { vn_id: this.ven_name_sub.vn_id, vns_id: this.ven_name_sub.vns_id })
        .then(response => {
          if (response.data.status) {
            this.profiles = response.data.respJSON
            this.profiles_filtered = this.profiles
          } else {
            this.vns_index = ''
            this.ven_name_sub = ''
            this.profiles = []
            this.profiles_filtered = []
            this.alert('warning', response.data.message, 0)

          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    cal_render() {
      var calendarEl = this.$refs['calendar'];
      let currentDate = this.ven_month;
      if (this.calendar) {
        currentDate = this.calendar.getDate();
        this.calendar.destroy();
      }
      this.calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        initialDate: currentDate,
        firstDay: 1,
        height: 1200,
        locale: 'th',
        events: this.filteredEvents,
        eventContent: function(arg) {
            let html = '<div class="fc-event-main-inner" style="display:flex; justify-content:space-between; align-items:center; width:100%; overflow:hidden;">';
            html += '<span style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis; display:block;">' + arg.event.title + '</span>';
            if (arg.event.extendedProps && arg.event.extendedProps.vc_count > 1) {
                html += '<span class="badge bg-danger rounded-pill" style="font-size: 0.7em; margin-left: 2px; padding: 2px 4px; line-height: 1;">' + arg.event.extendedProps.vc_count + '</span>';
            }
            html += '</div>';
            return { html: html };
        },
        eventClick: (info) => {
          // console.log(info.event.id +' '+info.event.title)
          // console.log(info.event.extendedProps)
          this.cal_click(info.event.id)
        },
        dateClick: (date) => {
          // console.log(date)
          // this.cal_modal.date_meet = date.dateStr
          this.$refs['modal-show'].click();
        },
        editable: true,
        eventDrop: (info) => {
          // console.log(info.event)
          if (!this.event_drop(info.event.id, info.event.start)) {
            info.revert();
          }
        },
        droppable: true,
        drop: (info) => {
          // console.log(info.draggedEl.dataset.uid)              
          this.drop_insert(info.draggedEl.dataset.uid, info.dateStr)

        },
        eventDidMount: (info) => {
            // Check via extendedProps or direct property
            let comment = '';
            if (info.event.extendedProps && info.event.extendedProps.comment) {
                comment = info.event.extendedProps.comment;
            }
            // Fallback: check if bg color is red = warning event
            if (!comment && info.event.backgroundColor === '#ff0000') {
                comment = '⚠️ เวรนี้อาจมีเวลาชนกับเวรอื่น';
            }
            if (comment && comment.trim() !== '') {
                const el = info.el;
                el.style.cursor = 'help';

                const tooltip = document.createElement('div');
                tooltip.className = 'fc-custom-tooltip';
                tooltip.innerHTML = '⚠️ ' + comment.replace(/\n/g, '<br>');
                Object.assign(tooltip.style, {
                    display: 'none',
                    position: 'fixed',
                    zIndex: '99999',
                    background: '#d32f2f',
                    color: '#fff',
                    padding: '10px 14px',
                    borderRadius: '8px',
                    fontSize: '14px',
                    fontWeight: 'bold',
                    maxWidth: '350px',
                    whiteSpace: 'normal',
                    boxShadow: '0 4px 12px rgba(0,0,0,0.4)',
                    border: '2px solid #fff',
                    pointerEvents: 'none'
                });

                document.body.appendChild(tooltip);
                el.addEventListener('mouseenter', (e) => {
                    tooltip.style.display = 'block';
                    tooltip.style.left = (e.pageX + 10) + 'px';
                    tooltip.style.top = (e.pageY + 10) + 'px';
                });
                el.addEventListener('mousemove', (e) => {
                    tooltip.style.left = (e.pageX + 10) + 'px';
                    tooltip.style.top = (e.pageY + 10) + 'px';
                });
                el.addEventListener('mouseleave', () => {
                    tooltip.style.display = 'none';
                });
            }
        }
      });
      this.calendar.render();
    },

    cal_click(id) {
      axios.post('../../server/asu/ven_set/get_ven.php', { id: id })
        .then(response => {
          if (response.data.status) {
            this.data_event = response.data.respJSON
            this.data_event_ven_coms = response.data.ven_coms
            // this.data_event.ven_com_id = JSON.parse(response.data.respJSON.ven_com_id)
            this.$refs['show_modal'].click()
            // this.ven_month = response.data.respJSON.ven_month
            // this.get_ven_coms()

          } else {
            let icon = 'warning'
            let message = response.data.message
            this.alert(icon, message, 0)

          }
        })
        .catch(function (error) {
          console.log(error);

        });
    },

    drop_insert(uid, dateStr) {
      console.log(uid)
      axios.post('../../server/asu/ven_set/ven_insert.php', {
        uid: uid,
        ven_date: dateStr,
        ven_month: this.ven_month,
        vc_id: this.ven_com.vc_id,
        vn_id: this.ven_name_sub.vn_id,
        vns_id: this.ven_name_sub.vns_id,
        DN: this.ven_com.DN,
        ven_name: this.ven_com.name,
        ven_com_num: this.ven_com.ven_com_num,
        u_role: this.ven_name_sub.name,
        price: this.ven_name_sub.price,
        color: this.ven_name_sub.color,
        vn_srt: this.ven_name_sub.vn_srt,
        vns_srt: this.ven_name_sub.vns_srt,
        ven_com: this.ven_com,
        ven_name_sub: this.ven_name_sub,
        act: 'insert'
      })
        .then(response => {
          // console.log(response.data);
          if (response.data.status) {
            let icon = response.data.icon ? response.data.icon : 'success';
            let timer = icon === 'warning' ? 5000 : 1000;
            this.alert(icon, response.data.message, timer)
          } else {
            this.alert('warning', response.data.message, 0)
          }
          this.get_vens()
          this.cal_render()
        })
        .catch(function (error) {
          console.log(error);

        });


    },
    event_drop(id, start) {
      axios.post('../../server/asu/ven_set/ven_move.php', { id: id, start: start })
        .then(response => {
          if (response.data.status) {
            this.datas = response.data.respJSON;
            this.get_vens()
            this.cal_render()
            
            this.alert('success', response.data.message, 1000)
            return true
          } else {
            this.alert('warning', response.data.message, 0)
            return false
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    get_vens() {
      axios.get('../../server/asu/ven_set/get_vens.php')
        .then(response => {
          if (response.data.status) {
            this.datas = response.data.respJSON;
            this.duty_types = response.data.res || [];
            
            if (this.selected_types.length === 0) {
              this.duty_types.forEach(type => {
                const key = String(type.vn_id);
                if (key && key !== 'undefined' && !this.selected_types.includes(key)) {
                  this.selected_types.push(key);
                }
              });
            }

            this.cal_render()
            this.$refs['calendar'].focus()
          }

        })
        .catch(function (error) {
          console.log(error);
        });
    },

    ven_save() {
      axios.post('../../server/asu/ven_set/ven_up_vcid.php', { data_event: this.data_event })
        .then(response => {


          if (response.data.status) {
            // this.get_vens()
            this.cal_click(this.data_event.id)
            // this.alert('success',response.data.message,1000)
            // this.$refs['close_modal'].click()
          } else {
            this.alert('warning', response.data.message, 0)
          }
          this.cal_render()
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    ven_save2() {
      axios.post('../../server/asu/ven_set/ven_up_vcid2.php', { data_event: this.data_event })
        .then(response => {

          if (response.data.status) {
            this.get_vens()
            this.cal_click(this.data_event.id)
            this.alert('success', response.data.message, 1000)
            // this.$refs['close_modal'].click()
          } else {
            this.alert('warning', response.data.message, 0)
          }
          this.cal_render()
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    ven_del(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          this.isLoading = true
          axios.post('../../server/asu/ven_set/ven_del.php', { id: id })
            .then(response => {

              if (response.data.status) {
                icon = "success";
                message = response.data.message;
                this.alert(icon, message, 1000)
                this.$refs['close_modal'].click()
              } else {
                icon = "warning";
                message = response.data.message;
                this.alert(icon, message)
              }
              this.get_vens()
              this.cal_render()
            })
            .catch(function (error) {
              console.log(error);
            })
            .finally(() => {
              this.isLoading = false;
            });
        }
      })

    },
    ven_dis_open(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          this.isLoading = true
          axios.post('../../server/asu/ven_set/ven_dis_open.php', { id: id })
            .then(response => {

              if (response.data.status) {
                icon = "success";
                message = response.data.message;
                this.alert(icon, message, 1000)
                this.$refs['close_modal'].click()
                this.get_vens()
              } else {
                icon = "warning";
                message = response.data.message;
                this.alert(icon, message)
              }
            })
            .catch(function (error) {
              console.log(error);
            })
            .finally(() => {
              this.isLoading = false;
            });
        }
      })

    },

    close_m() {
      // this.get_vens()
    },

    alert(icon, message, timer = 0) {
      swal.fire({
        position: 'top-end',
        icon: icon,
        title: message,
        showConfirmButton: false,
        timer: timer
      });
    },

    reset_search() {
      this.q = ''
    },
    get_ven_month1() {
      let m = new Date();
      let y = m.getFullYear().toString()
      console.log(y)
      for (let i = -1; i < 10; i++) {
        const d = new Date(y, m.getMonth() + i);
        this.months.push({ 'ven_month': this.convertToYearMonthNum(d), 'name': this.convertToDateThai(d) })
      }
    },
    convertToYearMonthNum(date) {
      var months_num = ["", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];
      return result = date.getFullYear() + "-" + (months_num[(date.getMonth() + 1)]);
    },
    convertToDateThai(date) {
      var month_th = ["", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];
      return result = month_th[(date.getMonth() + 1)] + " " + (date.getFullYear() + 543);
    }
  }
}).mount('#venSet')
