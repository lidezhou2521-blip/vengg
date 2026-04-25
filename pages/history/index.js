// const { info } = require("console");



Vue.createApp({
  data() {
    return {
      q:'2254',
      url_base:'',
      url_base_app:'',
      url_base_now:'',
      ssid :'',
      datas: [],


    isLoading : false,
  }
  },
  mounted(){
    this.url_base = window.location.protocol + '//' + window.location.host;
    this.url_base_app = window.location.protocol + '//' + window.location.host + '/venset/';
    this.ssid = localStorage.getItem("ss_uid")
    this.get_ven_ch();
    
  },
  watch: {
    q(){
      this.ch_search_pro()
    },
   
  },
  methods: {
        
    get_ven_ch(){
      this.ssid = localStorage.getItem("ss_uid")
      if(this.ssid !=''){
        this.isLoading = true;
        axios.post('../../server/history/get_ven_change.php',{user_id:this.ssid})
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
      }
    },
    ch_cancle(id){
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
          axios.post('../../server/history/change_cancle.php',{id:id})
          .then(response => {
              // console.log(response.data.respJSON);
              if (response.data.status) {
                this.get_ven_ch();
                this.alert("success",response.data.message,timer=1000)

              } else{
                this.alert("warning",response.data.message,timer=0)
                this.get_ven_ch();
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

    print(id){
      this.isLoading = true;
      axios.post('../../server/history/print.php',{id:id})
      .then(response => {
          if (response.data.status) {
            this.alert("success",response.data.message,timer=1000)
            window.open('../../uploads/ven.docx','_blank')
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
    print_2(id){
      this.isLoading = true;
      axios.post('../../server/history/print2.php',{id:id})
      .then(response => {
        if (response.data.status) {
          this.alert("success",response.data.message,timer=1000)
          var print = JSON.stringify(response.data.respJSON);    
          localStorage.setItem("print",print);
          window.open('./report-print.php','_blank')
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
  
        

}).mount('#index')
