Vue.createApp({
  data() {
    return {
      q:'',
      ven_app_g :'',
      ven_app :'',
      
      isLoading : false,
    }
  },
  mounted(){

    // const d = 
    this.get_ven_ch()
  },
  watch: {
    q(){
      this.get_ven_ch()
    }
  },
  methods: {
    get_ven_ch(){
      this.isLoading = true
      axios.post('../../server/asu/ven_approve/get_ven_ch.php',{q:this.q})
      .then(response => {
          if (response.data.status) {
              this.ven_app_g = response.data.respJSON_G;
              this.ven_app = response.data.respJSON;
          } 
      })
      .catch(function (error) {
          console.log(error);
      })
      .finally(() => {
        this.isLoading = false;
      })
    },

    ven_ch_app(id){
      Swal.fire({
        title: 'Are you sure?',
        text: "อนุมัติใบเปลี่ยน!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, is it!'
      }).then((result) => {
        if (result.isConfirmed) {
          this.isLoading = true;
          axios.post('../../server/asu/ven_approve/ven_ch_acc.php',{id:id})
            .then(response => {
                if (response.data.status) { 
                  this.get_ven_ch()
                  this.alert('success',response.data.message,1500)
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
    ven_ch_cancle(id){
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, is it!'
      }).then((result) => {
        if (result.isConfirmed) {
          this.isLoading = true;
          axios.post('../../server/asu/ven_approve/ven_ch_cancle.php',{id:id})
            .then(response => {
                if (response.data.status) { 
                  this.get_ven_ch()
                  this.alert('success',response.data.message,1500)
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


    get_ven_coms(){
      this.isLoading = true
      axios.post('../../server/asu/get_ven_coms.php')
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
   
    get_ven_com(id){
      this.isLoading = true
      axios.post('../../server/asu/get_ven_com.php',{id:id})
      .then(response => {
          if (response.data.status) {
              this.vc_form = response.data.respJSON;
          } 
      })
      .catch(function (error) {
          console.log(error);
      })
      .finally(() => {
        this.isLoading = false;
      })
    },
    
    get_ven_name_subs(){
      this.isLoading = true
      axios.post('../../server/asu/get_ven_name_subs.php')
      .then(response => {
          if (response.data.status) {
              this.ven_name_subs = response.data.respJSON;
          } 
      })
      .catch(function (error) {
          console.log(error);
      })
      .finally(() => {
        this.isLoading = false;
      })
    },
    get_ven_users(){
      this.isLoading = true
      axios.post('../../server/asu/get_ven_users.php')
      .then(response => {
          if (response.data.status) {
              this.ven_users = response.data.respJSON;
          } 
      })
      .catch(function (error) {
          console.log(error);
      })
      .finally(() => {
        this.isLoading = false;
      })
    },
    get_users(){
      axios.post('../../server/users/users.php')
      .then(response => {
          if (response.data.status) {
              this.users = response.data.respJSON;
          } 
      })
    },
    vu_add(vni,vnsi){
      this.$refs.show_vu_form.click()
      this.vu_form.ven_name  = this.ven_names[vni].name
      this.vu_form.uvn    = this.ven_name_subs[vnsi].name
      this.vu_form.DN     = this.ven_names[vni].DN
      this.vu_form.v_time = this.DN[this.ven_names[vni].DN] +':'+this.ven_names[vni].srt + this.ven_name_subs[vnsi].srt
      this.vu_form.price  = this.ven_name_subs[vnsi].price
      this.vu_form.color  = this.ven_name_subs[vnsi].color
      
      
    },
    clear_vu_form(){
      this.vu_form = {user_id :'',order : '',DN : '',price : '',ven_name : '',uvn : '',v_time : '',color : ''}
    },

    vu_save(){
      if(this.vu_form.user_id != '' && this.vu_form.order != ''){
        this.isLoading = true;
        axios.post('../../server/asu/user_ven_act.php',{ven_user:this.vu_form, act:this.vu_form_act})
        .then(response => {
            if (response.data.status) {            
              this.$refs.close_vu.click()
              this.get_ven_names()
              this.get_ven_name_subs()
              this.get_ven_users()
              this.get_users()
              this.alert('success',response.data.message,1500)
                // this.ven_name_subs = response.data.respJSON;
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
        if(this.vu_form.order == ''){message.push('กรุณากรอกลำดับที่')}
        this.alert('warning',message,0)
      }
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
          axios.post('../../server/asu/user_ven_act.php',{id:id, act:'delete'})
            .then(response => {
                if (response.data.status) {  
                  this.get_ven_names()
                  this.get_ven_name_subs()
                  this.get_ven_users()
                  this.get_users()
                  this.alert('success',response.data.message,1500)
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
    
  },
  
        

}).mount('#venApp')
