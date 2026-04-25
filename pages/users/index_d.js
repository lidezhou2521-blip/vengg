Vue.createApp({
  data() {
    return {
      q:'2254',
      url_base:'',
      url_base_app:'',
      url_base_now:'',
      datas: [],
      user: '',
      user_form: {
        username : '',
        password : '',
        repassword : '',
        fname : '',
        name : '',
        sname : '',
        dep : '',
        workgroup : '',
        phone : '',
        bank_account : '',
        bank_comment : '',
        act : 'insert',
      },
      sel_fname : [],
      sel_dep : [],
      sel_workgroup : [],
    

    isLoading : false,
  }
  },
  mounted(){
    this.url_base = window.location.protocol + '//' + window.location.host;
    this.url_base_app = window.location.protocol + '//' + window.location.host + '/adminphp/';
    // const d = 
    this.get_users()    
    this.get_sel_fname()
    this.get_sel_dep()
    this.get_sel_group()
  },
  watch: {
    
  },
  methods: {
    get_users(){
      this.isLoading = true
      axios.post('../../server/users/users_dis.php')
      .then(response => {
          
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
    get_user(uid){
      this.isLoading = true
      axios.post('../../server/users/user.php',{uid:uid})
      .then(response => {
          
          if (response.data.status) {
              this.user = response.data.respJSON;
          } 
      })
      .catch(function (error) {
          console.log(error);
      })
      .finally(() => {
        this.isLoading = false;
      })
      
    },
    view(uid){
      this.get_user(uid)
      this.$refs.show_modal_user.click()
    },
    reset_user(){
      this.user = '';
    },
    user_form_insert_show(){
      this.close_modal_user_form()
      this.$refs.show_modal_user_form.click()
      this.user_form.act = 'insert'
    },
    user_insert(){

      // console.log('user_insert')
      if(this.user_form.username != '' && this.user_form.password != '' && this.user_form.repassword != '' && this.user_form.fname != '' 
        && this.user_form.name != '' && this.user_form.sname != '' && this.user_form.password == this.user_form.repassword){
          axios.post('../../server/users/user_insert.php',{user:this.user_form})
            .then(response => {
                
                if (response.data.status) {
                  let icon = 'success' 
                  this.alert(icon,response.data.message,1000)
                  this.$refs.close_modal_user_form.click()
                  this.get_users()
                }else{
                  let icon = 'warning' 
                  let message = response.data.message
                  this.alert(icon,message,0)
                } 
            })
            .catch(function (error) {
                console.log(error);
            })
      }else{
        const message = []
        if(this.user_form.password != this.user_form.repassword){message.push('password ไม่ตรงกัน')}
        if(this.user_form.username == '' || this.user_form.password == '' || this.user_form.repassword == '' || this.user_form.fname == '' 
        || this.user_form.name == '' || this.user_form.sname == ''){message.push('กรุณากรอกข้อมูลให้ครบ')}
        let icon = 'warning' 
        this.alert(icon,message,0)
      }
    },
    close_modal_user_form(){
      this.user_form = {username : '', password : '', repassword : '', fname : '', name : '', sname : '', dep : '',
                        workgroup : '', phone : '', bank_account : '', bank_comment : '', act : 'insert'}
    },
    get_sel_fname(){
      axios.post('../../server/users/get_sel_fname.php')
      .then(response => {
          
          if (response.data.status) {
            this.sel_fname = response.data.respJSON;
          } 
      })
      .catch(function (error) {
          console.log(error);
      })
    },
    get_sel_dep(){
      axios.post('../../server/users/get_sel_dep.php')
      .then(response => {
          
          if (response.data.status) {
            this.sel_dep = response.data.respJSON;
          } 
      })
      .catch(function (error) {
          console.log(error);
      })
    },
    get_sel_group(){
      axios.post('../../server/users/get_sel_group.php')
      .then(response => {
          
          if (response.data.status) {
            this.sel_workgroup = response.data.respJSON;
          } 
      })
      .catch(function (error) {
          console.log(error);
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
    user_update(uid){
      this.isLoading = true
      axios.post('../../server/users/user.php',{uid:uid})
      .then(response => {
          
          if (response.data.status) {
              this.user_form = response.data.respJSON;
              this.$refs.show_modal_user_update_form.click()
          } 
      })
      .catch(function (error) {
          console.log(error);
      })
      .finally(() => {
        this.isLoading = false;
      })      
    },
    user_update_save(uid){
      this.isLoading = true
      axios.post('../../server/users/user_update_save.php',{user:this.user_form})
      .then(response => {
          
          if (response.data.status) {
              this.get_users()
              this.$refs.close_modal_user_update_form.click()
              let icon = 'success'
              let message = response.data.message
              this.alert(icon,message,timer=1500)
          }else{
            let icon = 'error'
              let message = response.data.message
              this.alert(icon,message,timer=0)
          }
      })
      .catch(function (error) {
          console.log(error);
      })
      .finally(() => {
        this.isLoading = false;
      })      
    },
    user_status(id,st){
      if(st == 1){
        
            this.isLoading = true;
            axios.post('../../server/users/user_update_status.php',{user_id:id,st:st})
                .then(response => {
                    
                    if (response.data.status) {
                        let icon = 'success'
                        let message = response.data.message
                        this.alert(icon,message,timer=1500)
                        this.get_users()
                    }else{
                      let icon = 'error'
                      let message = response.data.message
                      this.alert(icon,message,timer=0)
                    }
                })
                .catch(function (error) {
                    console.log(error);
                })
                .finally(() => {
                  this.isLoading = false;
                })   
        

      }else{
        this.isLoading = true;
        axios.post('../../server/users/user_update_status.php',{user_id:id,st:st})
                .then(response => {
                    
                    if (response.data.status) {
                        let icon = 'success'
                        let message = response.data.message
                        this.alert(icon,message,timer=1500)
                        this.get_users()
                    }else{
                      let icon = 'error'
                      let message = response.data.message
                      this.alert(icon,message,timer=0)
                    }
                })
                .catch(function (error) {
                    console.log(error);
                })
                .finally(() => {
                  this.isLoading = false;
                })   
      }

         
    
    }
  },
  
        

}).mount('#usersIndex')
