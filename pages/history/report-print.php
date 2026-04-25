<?php 

require_once('../../server/authen.php'); 

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print-Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" >
    <style>
        [v-cloak] > * { display:none; }
        [v-cloak]::before { content: "loading..."; }
        @font-face {
            font-family: Sarabun;
            src: url(../../assets/fonts/Sarabun/Sarabun-Regular.ttf);
            /* font-weight: bold; */
        }

        * {
            font-family : Sarabun;
            /* font-size   : small; */
            font-size   : 12px;
        }
        .udl {
            border-bottom: 1px solid #aaa;
        }
        body {
          margin-left : 50px;
          margin-right : 50px;
          margin-top : 50px;
        }
    </style>  
    </head>
  <body>
    <div class="d-print" id="appReports" v-cloak>
      <div class="row mt-3 ">
        <div class="col">
                    
        </div>
        <div class="col text-center">
            <h3>ใบขอเปลี่ยนเวร</h3>          
        </div>
        <div class="col text-end">          
          <span class="d-block ">{{datas.id}}</span>
        </div>
      </div>
      <div class="row mt-5">
        <div class="col-5"></div>
        <div class="col-7 text-end">
          (เขียนที่)ศาลเยาวชนและครอบครัวจังหวัดประจวบคีรีขันธ์
        </div>
      </div>

        <div class="row mb-2">
          <div class="col-6 text-end mt-1">            
          </div>
          <div class="col-6 text-start mt-1">
            วันที่ {{datas.doc_date}}
          </div>
        </div>
        <div class="row mb-2">
          <div class="col-1 text-start mt-1">            
            เรื่อง
          </div>
          <div class="col-6 text-start mt-1">
            ขอเปลี่ยนเวร
          </div>
        </div>

        <div class="row mb-2">
          <div class="col-1 text-start mt-1">            
            เรียน
          </div>
          <div class="col text-start mt-1" v-if="datas.u_role1 =='ผู้พิพากษา'">
            ผู้พิพากษาหัวหน้าศาลเยาวชนและครอบครัวจังหวัดประจวบคีรีขันธ์
          </div>
          <div class="col text-start mt-1" v-else>
            ผู้อำนวยการสำนักงานประจำศาลเยาวชนและครอบครัวจังหวัดประจวบคีรีขันธ์
          </div>
        </div>

        <div class="row mb-2">
          <div class="col-2 text-end mt-1">
          </div>
          <div class="col-7 text-start ">
            ตามคำสั่งศาลเยาวชนและครอบครัวจังหวัดประจวบคีรีขันธ์ ที่ 
          </div>
          <div class="col text-center udl">
            <!-- {{datas.ven_com_num_all}} -->
            {{datas.ven_com_name_idb}}
          </div>
        </div>

        <div class="row mb-2" v-if="datas.comment">
          <div class="col-2" >            
           ลงวันที่ 
          </div>
          <div class="col-10 text-center udl">
            {{datas.ven_com_date}}
          </div>
          <div class="col-2" > 

          </div>
          <div class="col-10 udl" >
            {{datas.comment}}
          </div>
          <div class="col-1 text-start">
            ให้ 
          </div>
          <div class="col-11 text-center udl">
            {{datas.name1}}
          </div>
        </div>

        <div class="row mb-2" v-else>
          <div class="col-2  mt-1" >            
           ลงวันที่ 
          </div>
          <div class="col-3 text-center udl">
            {{datas.ven_com_date}}
          </div>
          
          <div class="col-1 text-center">
            ให้ 
          </div>
          <div class="col-6 text-center udl">
            {{datas.name1}}
          </div>
        </div>
        

        <div class="row mb-3">
          <div class="col-1 text-start">            
           อยู่เวร 
          </div>
          <div class="col-4 text-center udl">
            {{datas.ven_com_name}}
          </div>
          <div class="col-1 text-center">
            วันที่ 
          </div>
          <div class="col-6 text-center udl">
            {{datas.ven_date1}}
          </div>
          
        </div>

        <div class="row  mb-2 ">
          <div class="col-1 text-start mt-1">            
           เนื่องจาก 
          </div>
          <div class="col-11 text-center mt-1 udl">
            ติดภารกิจจำเป็น
          </div>          
        </div>

        <div class="row mb-2">
          <div class="col-3 text-start">            
           จึงขอเปลี่ยนเวรกับ 
          </div>
          <div class="col-6 text-center udl">
            {{datas.name2}}
          </div>          
          <div class="col-3 text-start udl">            
           
          </div>
        </div>

        <div class="row mb-5 ">
          <div class="col-7 text-start">            
           เป็นผู้ปฎิบัติหน้าที่แทน และข้าพเจ้าจะมาปฎิบัติหน้าที่แทนในวันที่
          </div>
          <div class="col-5 text-center udl">            
          {{datas.ven_date2}}
          </div>
         
        </div>
        <div class="row mb-5">
          <div class="col-2 text-end">            
            </div>
            <div class="col-10 text-start ">
            จึงเรียนมาเพื่อโปรดพิจารณา
          </div>         
          
        </div>
        <div class="row">
          <div class="col-5 text-start">            
            </div>
            <div class="col-7 text-start">
            (ลงชื่อ).....................................................ผู้ขอเปลี่ยนเวร
          </div>         
          
        </div>
        <div class="row mb-5">
          <div class="col-6 text-start">            
            </div>
            <div class="col-6 text-start">
            ({{datas.name1}})
          </div> 
        </div>
        
        <div class="row">
          <div class="col-5 text-start">            
            </div>
            <div class="col-7 text-start">
            (ลงชื่อ).....................................................ผู้รับเปลี่ยนเวร
          </div>         
          
        </div>
        <div class="row ">
          <div class="col-6 text-start">            
            </div>
            <div class="col-6 text-start">
            ({{datas.name2}})
          </div> 
        </div>

        <div class="row mt-5">
          <div class="col-6 text-start">            
            <div class="row">
              <div class="text-center  mb-5">                
              [ / ] อนุญาต         
              </div>
              <div class="text-center">  
             
              .................................         
              </div>
              <div class="text-center" v-if="sbn[0]">  
                ({{sbn[0].name}})<br>
                {{sbn[0].dep1}}<br>
                {{sbn[0].dep2}}<br>
                {{sbn[0].dep3}}
                <!-- (นางสาววราภรณ์ คริศณุ)         -->
                <!-- </div> -->
                <!-- <div class="text-center">   -->
              
                <!-- ผู้พิพากษาหัวหน้าศาลเยาวชนและครอบครัวจังหวัดประจวบคีรีขันธ์        -->
              </div>
             
            </div>     
          </div>
          <div class="col-6 text-start">            
            <div class="row" v-if="datas.u_role1 !='ผู้พิพากษา'">
              <div class="text-center mb-5">  
              ขอประทานเสนอ ผู้พิพากษาหัวหน้าศาล<br>
                - เพื่อโปรดพิจารณา
    
              </div>
              <div class="text-center">  
             
              .................................         
              </div>
              <div class="text-center" v-if="sbn[1]">  
                ({{sbn[1].name}})<br>
                {{sbn[1].dep1}}<br>
                {{sbn[1].dep2}}<br>
                {{sbn[1].dep3}}
                <!-- (นางสาววนิดา พิพัฒน์นภาพร)         -->
                <!-- (นางสายฝน กุญชร ณ อยุธยา)         -->
                <!-- </div>
                <div class="text-center">   -->
              
                <!-- ผู้อำนวยการสำนักงานประจำศาลเยาวชนและครอบครัวจังหวัดประจวบคีรีขันธ์     -->
                <!-- นิติกรชำนาญการพิเศษ<br>
                รักษาราชการแทน ผู้อำนวยการสำนักงานประจำ<br>
                ศาลเยาวชนและครอบครัวจังหวัดประจวบคีรีขันธ์ -->
              </div>
             
            </div>     
          </div>
          <div class="text-center">
            <button class="d-print-none btn btn-warning" onclick="window.print()">print</button>
          </div>
          <br>
        <div class="text-center mt-5 w-25" v-if="qr_link + datas.id">          
          <span class="d-block "><img :src="qr_link + datas.id" alt=""></span>
          <span class="d-block ">{{datas.id}}</span>
        </div>
    </div>
        
        
        
<!-- {{datas.u_role1}} -->
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../../node_modules/vue/dist/vue.global.js"></script>
    <script src="../../node_modules/vue/dist/vue.global.prod.js"></script>
    <script src="../../node_modules/axios/dist/axios.js"></script>
    <script>
 
  Vue.createApp({
    data() {
      return {
        datas:'',
        sbn:'', 
        qr_link:'https://chart.googleapis.com/chart?cht=qr&chs=100x100&chl=http://10.37.64.01/vengg/pages/asu/ven_approve_id.php?ref='    
      }
    },
    mounted(){   
      this.datas = JSON.parse(localStorage.getItem("print"))
      this.get_sbn()
      // localStorage.removeItem("print")
      // window.print()
    },
    methods: { 
       
      get_sbn(){
        this.isLoading = true;
        axios.get('/main/api/vengg/get_sign.php')
        .then(response => {
            // console.log(response.data.respJSON);
            if (response.data.status) {
                this.sbn = response.data.respJSON;
                
            } 
        })
        .catch(function (error) {
            console.log(error);
        })
        .finally(() => {
          this.isLoading = false;
        })      
      }, 
      
      formatCurrency(number) {
          number = parseFloat(number);
          return number.toFixed(2).replace(/./g, function(c, i, a) {
              return i > 0 && c !== "." && (a.length - i) % 3 === 0 ? "," + c : c;
          });
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
      

    },
  }).mount('#appReports');
</script>
  </body>
</html>