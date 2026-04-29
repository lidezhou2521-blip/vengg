Vue.createApp({
  data() {
    return {
      q:'2254',
      url_base:'',
      url_base_app:'',
      url_base_now:'',
      datas: '',    
      ven_coms:'',
      ven_coms_g:'',
      vc:'',
      heads:[],
      date_start: '',
      date_end: '',
      sel_month: '',

    isLoading : false,
  }
  },
  mounted(){
    this.url_base = window.location.protocol + '//' + window.location.host;
    this.url_base_app = window.location.protocol + '//' + window.location.host + '/adminphp/';
    
    // Set current month as default (YYYY-MM)
    const now = new Date();
    this.sel_month = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0');

    this.get_ven_coms()
  },
  watch: {
    
  },
  methods: {
    get_ven_coms(){
      // this.isLoading = true
      axios.post('../../server/asu/report/get_ven_coms.php')
      .then(response => {
          if (response.data.status) {
              this.ven_coms = response.data.respJSON;
              this.ven_coms_g = response.data.respJSON_G;

          } 
      })
      .catch(function (error) {
          console.log(error);
      })
      .finally(() => {
        this.isLoading = false;
      })
    },

    get_ven_all(){
      // this.isLoading = true
      axios.get('../../server/asu/report/reportK.php')
      .then(response => {
          // console.log(response.data.respJSON);
          if (response.data.status) {
              this.datas = response.data.respJSON;
          } 
      })
      .catch(function (error) {
          console.log(error);
      })
      .finally(() => {
        this.isLoading = false;
      })
    },

    print(vcid, ven_month){
      // this.isLoading = true
      let excluded = this.getExcludedDuties(ven_month);
      axios.post('../../server/asu/report/report.php',{vcid:vcid, excluded_duties: excluded})    
          .then(response => {
              if (response.data.status) {
                var print = JSON.stringify(response.data);    
                localStorage.setItem("print",print);
                window.open('./report-print.php','_blank')
              }else{
                this.alert('warning',response.data.message,0)
              } 
          })
          .catch(function (error) {
              console.log(error);
          });

    }, 
    print2(vcid, ven_month){
      // this.isLoading = true
      let excluded = this.getExcludedDuties(ven_month);
      axios.post('../../server/asu/report/report2.php',{vcid:vcid, excluded_duties: excluded})    
          .then(response => {
              if (response.data.status) {
                var print = JSON.stringify(response.data);    
                localStorage.setItem("print",print);
                window.open('./report-print2.php','_blank')
              }else{
                this.alert('warning',response.data.message,0)
              } 
          })
          .catch(function (error) {
              console.log(error);
          });

    }, 
    print3(ven_month){
      let excluded = this.getExcludedDuties(ven_month);
      axios.post('../../server/asu/report/report3.php',{ven_month:ven_month, excluded_duties: excluded})    
          .then(response => {
              if (response.data.status) {
                var print = JSON.stringify(response.data);    
                localStorage.setItem("print",print);
                window.open('./report-print3.php','_blank')
              }else{
                this.alert('warning',response.data.message,0)
              } 
          })
          .catch(function (error) {
              console.log(error);
          });

    }, 
    print4(ven_month,ven_com_num,ven_com_date){
      axios.post('../../server/asu/report/report4.php',{
          ven_month:ven_month,
          ven_com_num:ven_com_num,
          ven_com_date:ven_com_date,
        })    
          .then(response => {
              if (response.data.status) {
                var print = JSON.stringify(response.data);    
                localStorage.setItem("print",print);
                window.open('./report-print4.php','_blank')
              }else{
                this.alert('warning',response.data.message,0)
              } 
          })
          .catch(function (error) {
              console.log(error);
          });

    }, 
    print_master(ven_month){
      axios.post('../../server/asu/report/report_master.php',{ven_month:ven_month})    
          .then(response => {
              if (response.data.status) {
                var print = JSON.stringify(response.data);    
                localStorage.setItem("print_master",print);
                window.open('./report-print-master.php','_blank')
              }else{
                this.alert('warning',response.data.message,0)
              } 
          })
          .catch(function (error) {
              console.log(error);
          });

    }, 
    print_groups(ven_month){
      axios.post('../../server/asu/report/report_groups.php',{ven_month:ven_month})    
          .then(response => {
              if (response.data.status) {
                var print = JSON.stringify(response.data);    
                localStorage.setItem("print_groups",print);
                window.open('./report-print-groups.php','_blank')
              }else{
                this.alert('warning',response.data.message,0)
              } 
          })
          .catch(function (error) {
              console.log(error);
          });
    }, 
    print_dutytype(ven_month){
      let excluded = this.getExcludedDuties(ven_month);
      axios.post('../../server/asu/report/report_dutytype.php',{ven_month:ven_month, excluded_duties: excluded})    
          .then(response => {
              if (response.data.status) {
                var print = JSON.stringify(response.data);    
                localStorage.setItem("print_dutytype",print);
                window.open('./report-print-dutytype.php','_blank')
              }else{
                this.alert('warning',response.data.message,0)
              } 
          })
          .catch(function (error) {
              console.log(error);
          });

    }, 
    print5(vcid, ven_month){
      let excluded = this.getExcludedDuties(ven_month);
      axios.post('../../server/asu/report/report5.php',{vcid:vcid, date_start: this.date_start, date_end: this.date_end, excluded_duties: excluded})    
          .then(response => {
              if (response.data.status) {
                var print = JSON.stringify(response.data);    
                localStorage.setItem("print5",print);
                window.open('./report-print5.php?v=' + Date.now(), '_blank')
              }else{
                this.alert('warning',response.data.message,0)
              } 
          })
          .catch(function (error) {
              console.log(error);
          });
    }, 
    print5_district(vcid, ven_month){
      let excluded = this.getExcludedDuties(ven_month);
      axios.post('../../server/asu/report/report5_district.php',{vcid:vcid, date_start: this.date_start, date_end: this.date_end, excluded_duties: excluded})    
          .then(response => {
              if (response.data.status) {
                var print = JSON.stringify(response.data);    
                localStorage.setItem("print5",print);
                window.open('./report-print5.php?v=' + Date.now(), '_blank')
              }else{
                this.alert('warning',response.data.message,0)
              } 
          })
          .catch(function (error) {
              console.log(error);
          });
    }, 
    print5_release(vcid, ven_month){
      let excluded = this.getExcludedDuties(ven_month);
      axios.post('../../server/asu/report/report5_release.php',{vcid:vcid, date_start: this.date_start, date_end: this.date_end, excluded_duties: excluded})    
          .then(response => {
              if (response.data.status) {
                var print = JSON.stringify(response.data);    
                localStorage.setItem("print5",print);
                window.open('./report-print5.php?v=' + Date.now(), '_blank')
              }else{
                this.alert('warning',response.data.message,0)
              } 
          })
          .catch(function (error) {
              console.log(error);
          });
    }, 
    print6(vcid, ven_month){
      let excluded = this.getExcludedDuties(ven_month);
      axios.post('../../server/asu/report/report6.php',{vcid:vcid, date_start: this.date_start, date_end: this.date_end, excluded_duties: excluded})    
          .then(response => {
              if (response.data.status) {
                var print = JSON.stringify(response.data);    
                localStorage.setItem("print6",print);
                window.open('./report-print6.php?v=' + Date.now(), '_blank')
              }else{
                this.alert('warning',response.data.message,0)
              } 
          })
          .catch(function (error) {
              console.log(error);
          });
    },
    print7(vcid, ven_month){
      let excluded = this.getExcludedDuties(ven_month);
      axios.post('../../server/asu/report/report7.php',{vcid:vcid, date_start: this.date_start, date_end: this.date_end, excluded_duties: excluded})    
          .then(response => {
              if (response.data.status) {
                var print = JSON.stringify(response.data);    
                localStorage.setItem("print7",print);
                window.open('./report-print7.php?v=' + Date.now(), '_blank')
              }else{
                this.alert('warning',response.data.message,0)
              } 
          })
          .catch(function (error) {
              console.log(error);
          });
    },
    print_single(ven_month, search_name){
      let excluded = this.getExcludedDuties(ven_month);
      axios.post('../../server/asu/report/report_single.php',{ven_month:ven_month, search_name:search_name, excluded_duties: excluded})    
          .then(response => {
              if (response.data.status) {
                var print = JSON.stringify(response.data);    
                localStorage.setItem("print_single",print);
                window.open('./report-print-single.php','_blank')
              }else{
                this.alert('warning',response.data.message,0)
              } 
          })
          .catch(function (error) {
              console.log(error);
          });

    }, 
    view(vcid){
      axios.post('../../server/asu/report/report.php',{vcid:vcid})    
          .then(response => {
              if (response.data.status) {
                // this.alert('success',response.data.message,1000)
                this.datas = response.data.respJSON; 
                this.heads = response.data.heads; 
                this.$refs.show_modal.click()
              }else{
                this.alert('warning',response.data.message,0)
              }
          })
          .catch(function (error) {
              console.log(error);
          })
          .finally(() => {
            this.isLoading = false;
          })
    },

    con_f(ven_month){
      // console.log('test')
      Swal.fire({
        title: 'Are you sure?',
        text: "You is this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, is it!'
      }).then((result) => {
        if (result.isConfirmed) {
          this.isLoading = true;
          axios.post('../../server/asu/report/conf.php',{ven_month:ven_month})    
              .then(response => {
                  if (response.data.status) { 
                    this.alert('success',response.data.message,1000)
                    this.$refs.close_modal.click()
                  }else{
                    this.alert('warning',response.data.message,0)
                  }
              })
              .catch(function (error) {
                  console.log(error);
              })
              .finally(() => {
                this.isLoading = false;
              })
                  }
    })
    }, 
    approve_ven(ven_com_idb){
      Swal.fire({
        title: 'ยืนยันการอนุมัติเวร?',
        text: "คุณต้องการเปลี่ยนสถานะเวรในคำสั่งนี้ให้เป็น 'ใช้งานจริง' ใช่หรือไม่?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ใช่, อนุมัติเลย',
        cancelButtonText: 'ยกเลิก'
      }).then((result) => {
        if (result.isConfirmed) {
          this.isLoading = true;
          axios.post('../../server/asu/report/approve_ven.php',{ven_com_id:ven_com_idb})    
              .then(response => {
                  if (response.data.status) { 
                    this.alert('success',response.data.message,1500)
                    this.get_ven_coms()
                  }else{
                    this.alert('warning',response.data.message,2500)
                  }
              })
              .catch(function (error) {
                  console.log(error);
              })
              .finally(() => {
                this.isLoading = false;
              })
        }
      })
    },
    unapprove_ven(ven_com_idb){
      Swal.fire({
        title: 'ยกเลิกการอนุมัติเวร?',
        text: "คุณต้องการคืนค่าสถานะเวรในคำสั่งนี้ให้เป็น 'รออนุมัติ' ใช่หรือไม่?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ใช่, ยกเลิกอนุมัติ',
        cancelButtonText: 'ปิด'
      }).then((result) => {
        if (result.isConfirmed) {
          this.isLoading = true;
          axios.post('../../server/asu/report/unapprove_ven.php',{ven_com_id:ven_com_idb})    
              .then(response => {
                  if (response.data.status) { 
                    this.alert('success',response.data.message,1500)
                    this.get_ven_coms()
                  }else{
                    this.alert('warning',response.data.message,2500)
                  }
              })
              .catch(function (error) {
                  console.log(error);
              })
              .finally(() => {
                this.isLoading = false;
              })
        }
      })
    },
    uptogcal(ven_com_idb){
      Swal.fire({
        title: 'Are you sure?',
        text: "You is this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, is it!'
      }).then((result) => {
        if (result.isConfirmed) {
          this.isLoading = true;
          axios.post('../../server/asu/report/send_to_gcal.php',{ven_com_id:ven_com_idb})    
              .then(response => {
                  if (response.data.status) { 
                    this.alert('success',response.data.message,1000)
                    // this.$refs.close_modal.click()
                  }else{
                    this.alert('warning',response.data.message,0)
                  }
              })
              .catch(function (error) {
                  console.log(error);
              })
              .finally(() => {
                this.isLoading = false;
              })
            }
      })
    },

    getYM(dat){
      let MyDate = new Date(dat);
      let MyDateString;
      // MyDate.setDate(MyDate.getDate() + 20);
      MyDateString = MyDate.getFullYear() + '-' + ("0" + (MyDate.getMonth()+1)).slice(-2)
      return ("0" + MyDate.getDate()).slice(-2)+ '-' + ("0" + (MyDate.getMonth()+1)).slice(-2) + '-' + (MyDate.getFullYear() + 543)
  },
  date_thai(day){
    var monthNamesThai = ["มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม"];
    var dayNames = ["วันอาทิตย์ที่","วันจันทร์ที่","วันอังคารที่","วันพุธที่","วันพฤหัสบดีที่","วันศุกร์ที่","วันเสาร์ที่"];
    var monthNamesEng = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var dayNamesEng = ['Sunday','Monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
    var d = new Date(day);
    return d.getDate() + ' ' + monthNamesThai[d.getMonth()] + "  " + (d.getFullYear() + 543)
  },    
  date_thai_dt(day){
    var monthNamesThai = ["มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม"];
    var dayNames = ["วันอาทิตย์ที่","วันจันทร์ที่","วันอังคารที่","วันพุธที่","วันพฤหัสบดีที่","วันศุกร์ที่","วันเสาร์ที่"];
    var monthNamesEng = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var dayNamesEng = ['Sunday','Monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
    var d = new Date(day);
    return dayNames[d.getDay()] + ' '+ d.getDate() + ' ' + monthNamesThai[d.getMonth()] + "  " + (d.getFullYear() + 543)
  },    
  date_thai_my(day){
    var monthNamesThai = ["มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม"];
    var dayNames = ["วันอาทิตย์ที่","วันจันทร์ที่","วันอังคารที่","วันพุธที่","วันพฤหัสบดีที่","วันศุกร์ที่","วันเสาร์ที่"];
    var monthNamesEng = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var dayNamesEng = ['Sunday','Monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
    var d = new Date(day);
    return monthNamesThai[d.getMonth()] + "  " + (d.getFullYear() + 543)
  }, 
  alert(icon,message,timer=0){
    swal.fire({
      icon: icon,
      title: message,
      showConfirmButton: true,
      timer: timer
    });
  },  
    // ดึง excluded_duties จาก localStorage ตามเดือน
    getExcludedDuties(ven_month) {
      if (!ven_month) return [];
      let stored = localStorage.getItem('excluded_duties_' + ven_month);
      return stored ? JSON.parse(stored) : [];
    },
    
    

  },
  
        

}).mount('#asuReport')
