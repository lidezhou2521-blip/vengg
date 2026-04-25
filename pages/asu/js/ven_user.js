Vue.createApp({
  
  data() {
    return {
      q:'2254',
      datas : [],
      ven_names :'',
      ven_name_subs :'',
      ven_users : [],
      users : [],
      judge : [],
      not_judge : [],
      vu_form   :{
        user_id :'',
        order   : 0,
        vn_id   : 0,
        vns_id  : 0,
      },
      vu_form_act :'insert',
      user      : '',
      DN        : {
        'กลางวัน' : '08:30',
        'กลางคืน' : '16:30'
      },
      isLoading : false
    }
  },
  mounted(){
    this.url_base = window.location.protocol + '//' + window.location.host;
    this.url_base_app = window.location.protocol + '//' + window.location.host + '/adminphp/';
    // const d = 
    // this.get_ven_names()
    this.get_ven_users()
    this.get_users()
  },
  watch: {
    
  },
  methods: {
    // get_ven_names(){
    //   this.isLoading = true
    //   axios.post('../../server/asu/ven_user/get_ven_names.php')
    //   .then(response => {
    //       if (!response.data.status) {
    //         this.alert('warning',response.data.message,timer=0)  
    //       } 
    //         this.ven_names = response.data.respJSON;
    //   })
    //   .catch(function (error) {
    //       console.log(error);
    //   })
    //   .finally(() => {
    //     this.isLoading = false;
    //   })
    // },
    
    get_ven_users(){
      this.isLoading = true
      axios.post('../../server/asu/ven_user/get_ven_users.php')
      .then(response => {
          if (response.data.status) {

          } else{   
            this.ven_users = []
          }
          this.datas  = response.data.respJSON;
          // this.ven_users  = response.data.respJSON;
      })
      .catch(function (error) {
          console.log(error);
      })
      .finally(() => {
        this.isLoading = false;
      })
    },
    get_users(){
      axios.post('../../server/asu/ven_user/get_users.php')
      .then(response => {
          // if (response.data.status) {
              this.users = response.data.respJSON;
              this.judge = response.data.judge;
              this.not_judge = response.data.not_judge;
          // } 
      })
    },
    
    vu_add(index){
      // this.get_ven_users()
      // this.vu_form = this.datas[index]
      this.vu_form.vn_id  = this.datas[index].vn_id
      this.vu_form.vns_id = this.datas[index].vns_id
      this.vu_form.order  = 0
      this.vu_form.user_id = ''
      this.vu_form_act    = 'insert'
      this.$refs.show_vu_form.click()   
    },
    vu_add_user_all(vni,vnsi){

      this.vu_form.ven_name  = this.ven_names[vni].name
      this.vu_form.uvn    = this.ven_name_subs[vnsi].name
      this.vu_form.DN     = this.ven_names[vni].DN
      this.vu_form.v_time = this.DN[this.ven_names[vni].DN] +':'+this.ven_names[vni].srt + this.ven_name_subs[vnsi].srt
      this.vu_form.price  = this.ven_name_subs[vnsi].price
      this.vu_form.color  = this.ven_name_subs[vnsi].color

      this.isLoading = true;

      Swal.fire({
        title: 'Are you sure?',
        text: "คุณต้องการเพิ่ม USER ทั้งหมดนะ!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Is it!'
      }).then((result) => {
        if (result.isConfirmed) {
          axios.post('../../server/asu/ven_user_insert_all.php',{
            ven_name  : this.ven_names[vni].name,
            uvn    : this.ven_name_subs[vnsi].name,
            DN     : this.ven_names[vni].DN,
            v_time : this.DN[this.ven_names[vni].DN] +':'+this.ven_names[vni].srt + this.ven_name_subs[vnsi].srt,
            price  : this.ven_name_subs[vnsi].price,
            color  : this.ven_name_subs[vnsi].color,
          })
          .then(response => {
              if (response.data.status) {            
                this.alert('success',response.data.message,1000)
                this.get_ven_users()                
              } 
          })
          .catch(function (error) {
              console.log(error);
          })
          .finally(() => {
            this.isLoading = false;
          })         
        }
        this.isLoading = false;
      })
    },

    clear_vu_form(){
      this.vu_form = {user_id :'', order   : 0, vn_id   : 0, vns_id  : 0,}
      this.vu_form_act = 'insert'
    },

    vu_save(){
      if(this.vu_form.user_id != ''){
        this.isLoading = true;
        axios.post('../../server/asu/ven_user/ven_user_act.php',{ven_user:this.vu_form, act:this.vu_form_act})
        .then(response => {
            if (response.data.status) {            
              this.$refs.close_vu.click()
              // this.get_ven_names()
              // this.get_users()
              this.get_ven_users()
              this.alert('success',response.data.message,1000)
            } else{
              this.alert('warning',response.data.message,0)
            }
        })
        .catch(function (error) {
            console.log(error);
        })
        .finally(() => {
          this.isLoading = false;
        })
      }else{
        const message = []
        if(this.vu_form.user_id == ''){message.push('กรุณาเลือกชื่อ')}
        if(this.vu_form.order   == ''){message.push('กรุณากรอกลำดับที่')}
        this.alert('warning',message,0)
      }
    },
    vu_up(id){
      this.isLoading = true;
        axios.post('../../server/asu/ven_user/get_ven_user.php',{id:id})
        .then(response => {
            if (response.data.status) {            
              this.vu_form_act  = 'update'
              this.vu_form      = response.data.respJSON;
              this.$refs.show_vu_form.click()

            }else{
              this.alert('warning',response.data.message,1000)
            } 
        })
        .catch(function (error) {
            console.log(error);
        })
        .finally(() => {
          this.isLoading = false;
        })
    },
    vu_del(id){
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
          axios.post('../../server/asu/ven_user/ven_user_act.php',{id:id, act:'delete'})
            .then(response => {
                if (response.data.status) { 
                  this.get_ven_users()
                  this.alert('success',response.data.message,1000)
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
    alert(icon,message,timer=0){
      swal.fire({
        icon: icon,
        title: message,
        showConfirmButton: true,
        timer: timer
      });
    },    
  }

}).mount('#venUser');
