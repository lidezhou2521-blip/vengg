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
            font-size   : 14px;
        }
        .udl {
            border-bottom: 1px solid #aaa;
        }
    </style>  
    </head>
  <body>
    <div id="appReports" v-cloak>
      <div class="row mt-3 ">
        <div class="col">
                    
        </div>
        <div class="col text-center ">
            <h3>ใบขอเปลี่ยนเวร</h3>          
        </div>
        <div class="col text-end">
            <h5>{{datas.id}}</h5>          
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
          <div class="col text-start mt-1">
            ผู้อำนวยการสำนักงานปรระจำศาลเยาวชนและครอบครัวจังหวัดประจวบคีรีขันธ์
          </div>
        </div>

        <div class="row mb-2">
          <div class="col-2 text-end mt-1">
          </div>
          <div class="col-7 text-start ">
            ตามคำสั่งศาลเยาวชนและครอบครัวจังหวัดประจวบคีรีขันธ์ ที่ 
          </div>
          <div class="col text-center udl">
            {{datas.ven_com_num_all}}
          </div>
        </div>

        <div class="row mb-2" v-if="datas.vcod">
          <div class="col-2" >            
           ลงวันที่ 
          </div>
          <div class="col-10 text-center udl">
            {{datas.ven_com_date}}
          </div>

          <div class="row mt-2 mb-2" v-for="da in datas.vcod">
            <div class="col-2" > 
              
            </div>
            <div class="col-10 text-center udl" >
              {{da}}
            </div>
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
          <div class="col-2 text-start">            
           อยู่เวร 
          </div>
          <div class="col-4 text-center udl">
            {{datas.ven_com_name}}
      </div>
      <div class="col-1 text-center">
        วันที่
      </div>
        
        <div class="col-5 text-center udl">
            {{datas.ven_date1}}
          </div>
        </div>

        <div class="row  mb-2 ">
          <div class="col-1 text-start mt-1">            
           เนื่องจาก 
          </div>
          <div class="col-11 text-center mt-1 udl">
            {{datas.comment == null || datas.comment == '' ? 'ติดภาระกิจจำเป็น' : datas.comment}}
          </div>          
        </div>

        <div class="row mb-2">
          <div class="col-3 text-start">            
           จึงขอเปลี่ยนเวรกับ 
          </div>
          <div class="col-9 text-center udl">
            {{datas.name2}}
          </div>          
          <!-- <div class="col-3 text-start udl">            
           
          </div> -->
        </div>

        <div class="row mb-4 ">
          <div class="col-8 text-start">            
            เป็นผู้ปฎิบัติหน้าที่แทน และข้าพเจ้าจะมาปฎิบัติหน้าที่แทนในวันที่
          </div>
          <div class="col-4 text-center udl">            
          {{datas.ven_date2}}
          </div>
         
        </div>
        <div class="row mb-4">
          <div class="col-3 text-start">            
            </div>
            <div class="col-6 text-center ">
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
        <div class="row">
          <div class="col-6 text-start">            
            </div>
            <div class="col-6 text-start">
            ({{datas.name2}})
          </div> 
        </div>
        
        
<!-- {{datas}} -->
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../../node_modules/vue/dist/vue.global.js"></script>
    <script src="../../node_modules/vue/dist/vue.global.prod.js"></script>
    <script src="../../node_modules/axios/dist/axios.js"></script>
    <script>
 
  Vue.createApp({
    data() {
      return {
        datas:''     
      }
    },
    mounted(){   
      this.datas = JSON.parse(localStorage.getItem("print"))
      // localStorage.removeItem("print")
      // window.print()
    },
    methods: {    
      
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
          var monthNamesThai = ["มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤษจิกายน","ธันวาคม"];
          var dayNames = ["วันอาทิตย์ที่","วันจันทร์ที่","วันอังคารที่","วันพุทธที่","วันพฤหัสบดีที่","วันศุกร์ที่","วันเสาร์ที่"];
          var monthNamesEng = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
          var dayNamesEng = ['Sunday','Monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
          var d = new Date(day);
          return d.getDate() + ' ' + monthNamesThai[d.getMonth()] + "  " + (d.getFullYear() + 543)
        },    
        date_thai_dt(day){
          var monthNamesThai = ["มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤษจิกายน","ธันวาคม"];
          var dayNames = ["วันอาทิตย์ที่","วันจันทร์ที่","วันอังคารที่","วันพุทธที่","วันพฤหัสบดีที่","วันศุกร์ที่","วันเสาร์ที่"];
          var monthNamesEng = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
          var dayNamesEng = ['Sunday','Monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
          var d = new Date(day);
          return dayNames[d.getDay()] + ' '+ d.getDate() + ' ' + monthNamesThai[d.getMonth()] + "  " + (d.getFullYear() + 543)
        },    
        date_thai_my(day){
          var monthNamesThai = ["มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤษจิกายน","ธันวาคม"];
          var dayNames = ["วันอาทิตย์ที่","วันจันทร์ที่","วันอังคารที่","วันพุทธที่","วันพฤหัสบดีที่","วันศุกร์ที่","วันเสาร์ที่"];
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