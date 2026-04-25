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
            font-size   : small;
        }
        th {
          /* height: 50px; */
          vertical-align: middle;
        }
        .text-sm {
          font-size: 10px;
        }
        .text-md {
          font-size: 14px;
        }
        td {
          font-size: 16px;
        }
        .table-bordered  {
          border-color: black;

        }
        .table-success {
          border-color: black;

        }
        .table-info {
          border-color: black;

        }
    </style>  
    </head>
  <body>
    <div id="appReports" v-cloak>
        <div class="text-center">
          <h5>ตารางการปฎิบัติงานในวันหยุดราชการและนอกเวลาราชการ</h5>
          <h5>ประจำเดือน {{date_thai_my(datas.ven_month)}}</h5>
          <h5>แนบท้ายคำสั่งที่ {{datas.ven_com}}</h5>
            
            <!-- {{datas.vc}} -->
        </div>
        <table class="table table-bordered d-print-inline d-print-table ">
            <thead class="text-nowrap table-info">
                <tr class="text-center">
                    <th colspan="1" rowspan="2">
                      <span class="text-md">วัน</span>
                    </th>
                    <th colspan="1" rowspan="2">
                      <span class="text-md">วันที่</span>
                    </th>
                    <th colspan="1" rowspan="2">
                      <span class="text-md">ฟื้นฟู/จับ-ค้น</span><br>
                      <span class="text-md">ตรวจสอบการจับ</span><br>
                      <span class="text-md">ผู้พิพากษา</span><br><br><br>
                      <span class="text-md">(08.30-16.30)</span>
                    </th>
                    <th colspan="1" rowspan="2">
                      <span class="text-md">จับ-ค้น</span><br>
                      <span class="text-md">ผู้พิพากษา</span><br><br><br><br>
                      <span class="text-md">(16.30-08.30)</span></th>
                    <th colspan="3">
                      <span class="text-md">ฟื้นฟู/ตรวจสอบการจับ (08.30-16.30)</span>
                    </th>
                    <th colspan="1" rowspan="2">
                      <span class="text-md">จนท.</span><br>
                      <span class="text-md">หมายจับ-ค้น</span><br><br><br><br>
                      <span class="text-md">(16.30-08.30)</span></th>
                    <th colspan="1" rowspan="2">หมายเหตุ</th>
                    
                  </tr>
                  <tr class="text-center">
                    <th>
                      <span class="text-md">ผอ./แทน</span><br>
                      <span class="text-sm">(กรรมการเก็บรักษาเงินและลงนาม)</span><br><br>
                      <span class="text-md">งานประชาสัมพันธ์</span>
                    </th>
                    <th>
                      <span class="text-md">จนท. คนที่1</span><br>
                      <span class="text-sm">(กรรมการเก็บรักษาเงินและลงนาม)</span><br>หมายจับ-ค้น<br>
                      <span class="text-sm">งานรับฟ้อง/งานหมายอาญา/งานหน้าบัลลังก์/งานการเงิน</span>
                    </th>
                    <th>
                      <span class="text-md">จนท. คนที่2</span><br>
                      <span class="text-sm">(กรรมการเก็บรักษาเงินและลงนาม)</span><br><br>
                      <span class="text-sm">งานรับฟ้อง/งานหมายอาญา/งานหน้าบัลลังก์/งานการเงิน</span>
                    </th>                    
                </tr>
               
            </thead>
            <tbody>
                <tr v-for="d in datas.respJSON" >
                    <td :class="d.hld +' text-center text-nowrap'">{{date_thai_day(d.ven_date)}}</td>
                    <td :class="d.hld +' text-center text-nowrap'">{{date_thai_d(d.ven_date)}}</td>
                    <td v-for="dun in d.u_name" :class="'text-center text-nowrap ' + d.hld">
                      {{dun}}
                    </td>
                </tr>                
            </tbody>
            
        </table> 
        
        
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
          return d.getDate() + ' ' + monthNamesThai[d.getMonth()] + "  " + (d.getFullYear() + 543)
        },
        date_thai_d(day){
          var monthNamesThai = ["มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม"];
          var dayNames = ["วันอาทิตย์ที่","วันจันทร์ที่","วันอังคารที่","วันพุธที่","วันพฤหัสบดีที่","วันศุกร์ที่","วันเสาร์ที่"];
          var monthNamesEng = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
          var dayNamesEng = ['Sunday','Monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
          var d = new Date(day);
          return d.getDate() 
        },
        date_thai_day(day){
          var monthNamesThai = ["มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม"];
          var dayNames = ["วันอาทิตย์ที่","วันจันทร์ที่","วันอังคารที่","วันพุธที่","วันพฤหัสบดีที่","วันศุกร์ที่","วันเสาร์ที่"];
          var monthNamesEng = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
          var dayNamesEng = ['Sunday','Monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
          var d = new Date(day);
          return dayNames[d.getDay()]
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