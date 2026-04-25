Vue.createApp({
  data() {
    return {
      q:'',
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
        st : '',
        act : 'insert',
      },
      user_role_form:{'username':'',password:'',repassword:'',role:''},

      sel_fname     : [],
      sel_dep       : [],
      sel_workgroup : [],
      user_img      : {
        uid:'',
        title:'',
        val:''
      },

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
    q(){
      this.ch_search_user()
    }
  },
  methods: {
    get_users(){
      this.isLoading = true
      axios.post('../../server/users/users_gdms.php')
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
              this.user = response.data.data;
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
                        workgroup : '', phone : '', bank_account : '', bank_comment : '', st : '', act : 'insert'}
    },
    get_sel_fname(){
      axios.post('../../server/users/get_sel_fname.php')
      .then(response => {
          
          if (response.data.status) {
            this.sel_fname = response.data.data;
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
            this.sel_dep = response.data.data;
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
            this.sel_workgroup = response.data.data;
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
      this.$refs.close_modal_user.click()
      this.isLoading = true
      axios.post('../../server/users/user.php',{uid:uid})
      .then(response => {
          
          if (response.data.status) {
              this.user_form = response.data.data;
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
    user_update_role(uid){
      this.isLoading = true
      axios.post('../../server/users/user_role.php',{uid:uid})
      .then(response => {          
          if (response.data.status) {
              this.user_role_form = response.data.data;
              this.user_role_form.password = '';
              this.$refs.show_modal_user_u_r_form.click()
          } 
      })
      .catch(function (error) {
          console.log(error);
      })
      .finally(() => {
        this.isLoading = false;
      }) 
    },
    user_update_role_save(){
      if(this.user_role_form.password!=''){
        if(this.user_role_form.password != this.user_role_form.repassword){
          return this.alert("warning",'password ไม่ตรงกัน',timer=0)
        }
      }
      this.isLoading = true
      axios.post('../../server/users/user_update_role_save.php',{user:this.user_role_form})
      .then(response => {
          
          if (response.data.status) {
              this.get_users()
              this.$refs.close_modal_user_u_r_form.click()
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
    user_status(id){
      // if(st == 1){
        
            // this.isLoading = true;
            axios.post('../../server/users/user_update_status_gdms.php',{user_id:id})
                .then(response => {
                    
                    if (response.data.status) {
                        let icon = 'success'
                        let message = response.data.message
                        this.alert(icon,message,timer=1500)
                        location.reload();
                        // this.get_users()
                    }else{
                      let icon = 'error'
                      let message = response.data.message
                      this.alert(icon,message,timer=0)
                    }
                })
                .catch(function (error) {
                    console.log(error);
                })
                // .finally(() => {
                //   this.isLoading = false;
                // })   
        
    },
    profile_del(uid){
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

          axios.post('../../server/users/user_del_gdms.php',{user_id:uid})
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
        }
      }) 
    },
    ch_search_user(){
      console.log(this.q)
      if(this.q.length > 0){
        this.isLoading = true;
        axios.post('../../server/users/user_search.php',{q:this.q})
          .then(response => {
              if (response.data.status){
                this.datas = response.data.data;                    
              }
          })
          .catch(function (error) {
              console.log(error);
          })
          .finally(() => {
            this.isLoading = false;
          })
      }else{
        this.get_users()
      }
    },

    b_user_img(uid,index){
      this.user_img.uid = uid;   
      this.user_img.img = this.datas[index].img;   
      this.$refs.show_user_img.click()     
    },
    onChangeInput(event){
      this.onUpload()
    },
    onUpload(){
      // console.log(this.$refs.myFiles.files[0].name);
      var image = this.$refs.myFiles.files
      if (image.length > 0) {
        if(image[0].type == 'image/jpeg' || image[0].type =='image/png') {
          var formData = new FormData();
          // var imagefile = document.querySelector('#file');
          // var imagefile = document.querySelector('#file');
          formData.append("sendimage", image[0]);
          formData.append("uid", this.user_img.uid);
          axios.post('../../server/users/upload_img.php', 
            formData, 
            {headers:{'Content-Type': 'multipart/form-data'}
          })
            .then(response => {
                if (response.data.status) {
                  swal.fire({
                    icon: 'success',
                    title: response.data.message,
                    showConfirmButton: true,
                    timer: 1500
                  });
                  this.get_users();
                  this.user_img.img = response.data.img;
                  this.$refs.close_user_img.click()

                }else {
                    swal.fire({
                        icon: 'error',
                        title: response.data.message,
                        showConfirmButton: true,
                        timer: 1500
                    });
                }
            })
        } else{
            swal.fire({
                icon: 'error',
                title: "ไฟล์ที่อัพโหลดต้องเป็นไฟล์ jpeg หรือ png เท่านั้น",
                showConfirmButton: true,
                timer: 1500
            });
          }
      }

    } ,
  },
  
        

}).mount('#usersIndex')
