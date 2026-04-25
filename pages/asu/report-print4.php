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
          font-size: 3px;
        }
        .text-md {
          font-size: 5px;
        }
        td {
          font-size: 12px;
        }
    </style>  
    </head>
  <body>
    <div id="appReports" v-cloak>
        <div class="text-center">
          <h5>คำสั่งศาลเยาวชนและครอบครัวจังหวัดประจวบคีรีขันธ์</h5>
          <h5>ที่ {{datas.ven_com_num}}</h5>
          <h5>เรื่อง ให้เจ้าหน้าที่อยู่เวรรักษาการณ์และทำหน้าทีเป็นผู้ตรวจเวร</h5>
          <h5>ประจำเดือน {{datas.month}}</h5>
          <h5>*************************</h5>
          <h6>เพื่อให้การดูแลรักษาความปลอดภัยอาคารสถานที่ทำการเป็นไปด้วยความเรียบร้อย
จึงได้มีคำสั่งให้บุคคลดังมีรายชื่อต่อไปนี้เข้าเวรรักษาการณ์และทำหน้าที่เป็นผู้ตรวจเวรรักษาการณ์ดังนี้</h6>
            
            <!-- {{datas.vc}} -->
        </div>
        <table class="table table-bordered d-print-inline d-print-table ">
            <thead class="text-nowrap table-info">
                <tr class="text-center">
                    <th >ลำดับที่</th>
                    <th >ชื่อ-สกุล</th>
                    <th >วันที่อยู่เวร<br>16.30 - 08.30 น.</th>
                    <th >วันที่ตรวจเวร<br>08.30 - 16.30 น.</th>
                    <th >วันที่ตรวจเวร<br>16.30 - 08.30 น.</th>
                    
                  </tr>
                  
               
            </thead>
            <tbody>
                <tr v-for="d,index in datas.resp" :class="d.hld +' text-center text-nowrap'">
                    <td class="text-center ">{{index +1}}</td>
                    <td class="text-start" >
                      {{d.name}}
                    </td>
                    <td >
                      <span v-for="dvn,index in d.vn"> {{dvn}}{{d.vn.length == index + 1 ? '':', '}}</span>
                    </td>
                    <td >
                      <span v-for="dvhd,index in d.vhd"> {{dvhd}}{{d.vhd.length == index + 1 ? '':', '}} </span>
                      
                    </td>
                    <td >
                      <span v-for="dvhn,index in d.vhn"> {{dvhn}}{{d.vhn.length == index + 1 ? '':', '}}</span>
                    </td>
                </tr>                
            </tbody>
            
        </table> 
        <div class="text-center">
          <h5>สั่ง ณ วันที่ {{datas.ven_com_date}}</h5>
          <br>
          <br>
          <br>
          <h5>นายประยุทธ แก้วภักดี</h5>
          <h5>ผู้พิพากษาหัวหน้าศาลเยาวชนและครอบครัวจังหวัดประจวบคีรีขันธ์</h5>
            
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