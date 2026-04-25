Vue.createApp({
  data() {
    return {
      q:'2254',
      url_base:'',
      url_base_app:'',
      url_base_now:'',
      datas: [
        {
            id: 'a',
            title: 'my event',
            start: '2022-09-01',
            extendedProps: {
                uid: 5555,
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
    data_event:{ 
        uid: 5555,
        uname: '',
        ven_date: '',
        ven_time: '',
        DN: '',
        ven_month: '',
        ven_com_id: '',
        st: '',
    },
    profiles:[],

    ven_coms  :[],
    ven_coms_index:'',

    ven_com_id  : '',
    ven_month   : '',
    ven_com_name : '',
    ven_com_num : '',
    DN          : '',
    u_role      : '',
    price       : '',

    ssid :'',
    my_v :[],
    vh :[],
    d_now:'',
    my_v_show : 'false',
    ch_v1:'',
    ch_v2:'',
    users:[],
    u_id2:'',
    u_name2:'',
    u_img2:'',
    act:'a',
    ch_a:false,
    ch_b:false,

    duty_types: [],
    selected_types: [], // Store keys like "name|u_role"
    calendar: null,
    isLoading : false,
    filterMyDuty: false,
    search_query: '',
  }
  },
  computed: {
    dutyStats() {
      try {
        if (!this.duty_types || !Array.isArray(this.duty_types)) return [];
        
        const stats = {};
        
        this.duty_types.forEach(type => {
          if (type && type.name && type.u_role) {
            const key = type.name + '|' + type.u_role;
            stats[key] = {
              key: key,
              name: type.name,
              u_role: type.u_role,
              color: type.color || '#cccccc',
              count: 0,
              active: this.selected_types.includes(key)
            };
          }
        });

        if (this.datas && Array.isArray(this.datas)) {
            this.datas.forEach(event => {
            if (event && event.backgroundColor) {
                const type = this.duty_types.find(t => t.color === event.backgroundColor);
                if (type) {
                const key = type.name + '|' + type.u_role;
                if (stats[key]) {
                    stats[key].count++;
                }
                }
            }
            });
        }

        return Object.values(stats).filter(s => s.count > 0);
      } catch (e) {
        console.error("dutyStats error:", e);
        return [];
      }
    },
    filteredEvents() {
        if (!this.datas) return [];
        let filtered = this.datas;

        // Filter by selected types
        if (this.selected_types.length > 0) {
            filtered = filtered.filter(event => {
                const type = this.duty_types.find(t => t.color === event.backgroundColor);
                if (!type) return true; // Keep if we don't know the type
                const key = type.name + '|' + type.u_role;
                return this.selected_types.includes(key);
            });
        }

        // Filter by search query
        if (this.search_query && this.search_query.trim() !== '') {
            const query = this.search_query.toLowerCase();
            filtered = filtered.filter(event => {
                const titleMatch = event.title && event.title.toLowerCase().includes(query);
                
                // Check extendedProps
                const ep = event.extendedProps || {};
                const uNameMatch = ep.u_name && ep.u_name.toLowerCase().includes(query);
                const uRoleMatch = ep.u_role && ep.u_role.toLowerCase().includes(query);
                const venComMatch = ep.ven_com_name && ep.ven_com_name.toLowerCase().includes(query);
                const dnMatch = ep.DN && ep.DN.toLowerCase().includes(query);

                return titleMatch || uNameMatch || uRoleMatch || venComMatch || dnMatch;
            });
        }

        // Filter by My Duty
        if (this.filterMyDuty && this.ssid) {
            filtered = filtered.filter(event => {
                const ep = event.extendedProps || {};
                return String(ep.user_id) === String(this.ssid);
            });
        }
        
        return filtered;
    }
  },
  mounted(){
    this.url_base = window.location.protocol + '//' + window.location.host;    
    this.ven_month = new Date();
    this.get_vens()
  },
  watch: {
    q(){
      this.ch_search_pro()
    },
    filteredEvents: {
        handler(newEvents) {
            if (this.calendar) {
                // Efficiently update events without full re-render if possible
                const source = this.calendar.getEventSources()[0];
                if (source) {
                    // This is a bit tricky with raw FullCalendar
                    // The simplest way is to destroy and re-render or use removeEvents/addEvents
                    // But for now, let's just use the reactive updates in cal_render
                    this.cal_render();
                }
            }
        },
        deep: true
    },
    filterMyDuty(newVal) {
        this.cal_render();
    }
  },
  methods: {
    cal_render(){
      var calendarEl = this.$refs['calendar'];
      let currentDate = this.ven_month;
      if (this.calendar) {
        currentDate = this.calendar.getDate();
        this.calendar.destroy();
      }
      this.calendar = new FullCalendar.Calendar(calendarEl, {
        initialView : 'dayGridMonth',
        initialDate : currentDate,
        height      : 1240,
        locale      : 'th',
        firstDay    : 1,
        eventOrder  : 'start',
        events      : this.filteredEvents,
        eventColor  : '#378006',
        eventClick: (info)=> {
            this.cal_click(info.event.id)
        },
        eventDidMount: (info) => {
            let comment = '';
            if (info.event.extendedProps && info.event.extendedProps.comment) {
                comment = info.event.extendedProps.comment;
            }
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
        },
      })
      this.calendar.render();
    },
    cal_click(id){
      if(this.ssid != ''){
        this.isLoading = true;
        axios.post('../../server/dashboard/get_ven.php',{id:id,uid:this.ssid})
          .then(response => {
            // console.log(response.data);
            if (response.data.status) {
              this.data_event = response.data.respJSON
              this.my_v = response.data.my_v
              this.vh = response.data.vh
              this.d_now = response.data.d_now
              this.users = response.data.users
              // this.get_users(this.data_event.ven_name,this.data_event.u_role)
              this.$refs['show_modal'].click()  
            }else{               
              this.alert('warning',response.data.message ,0)  
            }
          })
          .catch(function (error) {        
          console.log(error);  
          })
          .finally(() => {
            this.isLoading = false;
          });
          // this.$refs.show_modal.click()
        }else{
          this.alert('warning','กรุณา Login..' ,0) 
        }
    },
    get_vens(){
      this.isLoading = true;
      axios.get('../../server/dashboard/get_vens.php')
      .then(response => {
          if (response.data.status) {
            this.datas = response.data.respJSON;
            this.duty_types = response.data.res || [];
            
            // Initialize selected_types with all found types if empty
            if (this.selected_types.length === 0) {
                this.duty_types.forEach(type => {
                    const key = type.name + '|' + type.u_role;
                    if (!this.selected_types.includes(key)) {
                        this.selected_types.push(key);
                    }
                });
            }

            this.ssid = response.data.ssid
            this.cal_render()
          }else{
            this.duty_types = response.data.res || [];
            this.cal_render()
          } 
      })
      .catch(function (error) {
          console.log(error);
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

    change_a(my_v_index){
      this.act = 'a'
      this.$refs.show_modal_b.click()
      this.ch_v1 = this.my_v[my_v_index]
      this.ch_v2 = this.data_event

    },
    change_b(uid,u_name,img){
      console.log(uid)
      console.log(u_name)
      console.log(img)
      this.act        = 'b'
      this.ch_v1      = this.data_event
      this.user_id2   = uid
      this.u_name2    = u_name
      this.u_img2     = img
      this.$refs.show_modal_b.click()
    },
    change_save(){
      this.isLoading = true;
      axios.post('../../server/dashboard/change_save.php',{ch_v1:this.ch_v1, ch_v2:this.ch_v2})
      .then(response => {
          if (response.data.status) {
            this.get_vens()
            this.$refs.close_modal.click()
            this.$refs.close_modal_b.click()
            this.alert('success',response.data.message,1000) 
            window.open('../history/index.php','_self')            
          } else{
            this.alert('warning',response.data.message,0) 
          }
          this.act = 'a'
      })
      .catch(function (error) {
        this.alert('warning',error,0)
        console.log(error);
      })
      .finally(() => {
        this.isLoading = false;
      })      
    },
    change_save_bb(){
      this.isLoading = true;
      axios.post('../../server/dashboard/change_save_b.php',{ch_v1:this.ch_v1, user_id2:this.user_id2, u_name2:this.u_name2})
      .then(response => {
          console.log(response.data);
          if (response.data.status) {
            this.get_vens()
            this.$refs.close_modal.click()
            this.$refs.close_modal_b.click()
            this.alert('success',response.data.message,1000) 
            window.open('../history/index.php','_self')
          } else{
            this.alert('warning',response.data.message,0) 
          }
          // this.act = 'a'
      })
      .catch(function (error) {
          console.log(error);
      })
      .finally(() => {
        this.isLoading = false;
      })
      
    },
    // get_users(ven_name,uvn){
    //   this.isLoading = true;
    //   axios.post('../../server/dashboard/get_users.php',{ven_name:ven_name, uvn:uvn})
    //   .then(response => {
    //       console.log(response.data);
    //       if (response.data.status) {
    //         this.users =response.data.respJSON
    //         // this.alert('success',response.data.message,1000) 
    //       } else{
    //         // this.alert('warning',response.data.message,0) 
    //       }
          
    //   })
    //   .catch(function (error) {
    //       console.log(error);
    //   })
    //   .finally(() => {
    //     this.isLoading = false;
    //   })      

    // },
   
    
    close_m(){
      this.ch_a =false
      this.ch_b =false
    },
    close_m_b(){
      this.$refs.close_modal.click()
    },
    report_jk(ven_date,DN){
      this.isLoading = true;
      axios.post('../../server/dashboard/report_jk.php',{ven_date:ven_date,DN:DN})
      .then(response => {
          if (response.data.status) {
            this.alert("success",response.data.message,timer=1000)
            window.open('../../uploads/ven_jk.docx','_blank')
          } else{
            this.alert("warning",response.data.message,timer=0)
          }
      })
      .catch(function (error) {
          console.log(error);
      })
      .finally(() => {
        this.isLoading = false;
      })

    },
    
    alert(icon,message,timer=0){
      swal.fire({
        icon: icon,
        title: message,
        showConfirmButton: false,
        timer: timer
      });
    },
  },

}).mount('#dashboard')
