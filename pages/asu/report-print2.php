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
          height: 50px;
          vertical-align: middle;
        }
    </style>  
    </head>
  <body>
    <div id="appReports" v-cloak>
        <div class="text-center mt-3">
          <h5>ตารางการปฎิบัติหน้าที่วันหยุดราชการของข้าราชการศาลยุติธรรม พนักงานราชการ และลูกจ้าง</h5>
          <h5>เพื่อปฏิบัติงานตาม พ.ร.บ.ศาลเยาวชนและครอบครัวและวิธีพิจารณาคดีศาลเยาวชนและครอบครัว พ.ศ.2533 </h5>
          <h5>ประจำเดือน {{date_thai_my(datas.vc.ven_month)}} ตั้งแต่เวลา 08.30 - 16.30 นาฬิกา</h5>
          <h5>แนบท้ายคำสั่งศาลเยาวชนและครอบครัวกลาง ที่ {{datas.vc.ven_com_num}} ลงวันที่ {{date_thai(datas.vc.ven_com_date)}}</h5>
            
            <!-- {{datas.vc}} -->
        </div>
        <table class="table table-bordered d-print-inline d-print-table ">
            <thead>
                <tr class="text-center">
                    <th colspan="2" >วัน เดือน ปี</th>                    
                    <th v-for="c in datas.respJSON[0].cmt">{{c}}</th>                    
                </tr>               
            </thead>
            <tbody>
                <tr v-for="d in datas.respJSON">
                    <td class="text-nowrap">{{date_thai_day(d.ven_date)}}</td>
                    <td class="text-nowrap">{{date_thai_dt(d.ven_date)}}</td>
                    <td class="text-nowrap" v-for="dun in d.u_name">
                      {{dun}}
                    </td>
                </tr>                
            </tbody>
           
        </table> 
        <div class="text-center mt-5 me-5 ">
          
          <h5> หมายเหตุ * ปฏิบัติงานในหน้าที่ เป็นคณะกรรมการเก็บรักษาเงิน และเป็นหัวหน้าเวรกำกับดูแลหรือมอบหมายเจ้าหน้าที่เวรดูแลการลงลายมือชื่อ</h5>
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
      localStorage.removeItem("print")
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