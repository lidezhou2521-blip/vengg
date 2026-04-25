Vue.createApp({
  data() {
    return {
      q:'',
      url_base:'',
      url_base_app:'',
      url_base_now:'',
      datas: [],    
      form:'',
      act : 'insert',

    isLoading : false,
  }
  },
  mounted(){
    this.url_base = window.location.protocol + '//' + window.location.host;
    this.url_base_app = window.location.protocol + '//' + window.location.host + '/adminphp/';
    // const d = 
    this.get_groups()  
  },
  watch: {
    q(){
      this.ch_search_group()
    }
  },
  methods: {
    get_groups(){
      this.isLoading = true
      axios.post('../../server/users/group/get_groups.php')
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

    get_group(id){
      this.isLoading = true
      axios.post('../../server/users/group/get_group.php',{id:id})
      .then(response => {          
          if (response.data.status) {
              this.form = response.data.respJSON;
          } 
      })
      .catch(function (error) {
          console.log(error);
      })
      .finally(() => {
        this.isLoading = false;
      })
      
    },   

    group_update(id){
      this.get_group(id)
      this.$refs.show_modal_form.click()
      this.act = 'update'
              
    },
    
    group_insert(){
      this.form = {name : ''}
      this.$refs.show_modal_form.click()
      this.act = 'insert'
    },


    group_save(){
      this.isLoading = true;
      axios.post('../../server/users/group/group_save.php',{form:this.form, act:this.act})
        .then(response => {
            
            if (response.data.status) {
              this.alert('success',response.data.message,1000)
              this.$refs.close_modal_form.click()
              this.get_groups()
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

    close_modal_form(){
      this.form = {name : ''}
      this.act = 'insert'
    },
   

    alert(icon,message,timer=0){
        swal.fire({
        icon: icon,
        title: message,
        showConfirmButton: true,
        timer: timer
      });
    },    
    
    

    group_del(id){        
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
          axios.post('../../server/users/group/group_save.php',{id:id, act:'del'})
            .then(response => {                
                if (response.data.status) {
                  this.alert('success',response.data.message,1000)
                    this.get_groups()
                }else{
                  this.alert('error',response.data.message,timer=0)
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

    ch_search_group(){
      console.log(this.q)
      if(this.q.length > 0){
        this.isLoading = true;
        axios.post('../../server/users/group/group_search.php',{q:this.q})
          .then(response => {
              if (response.data.status){
                this.datas = response.data.respJSON;                    
              }else{
                this.datas = []
              }
          })
          .catch(function (error) {
              console.log(error);
          })
          .finally(() => {
            this.isLoading = false;
          })
      }else{
        this.get_groups()
      }
    },
  },
  
        

}).mount('#usersGroup')
