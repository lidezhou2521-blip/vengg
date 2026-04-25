<?php 
require_once('../../server/authen.php'); 
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Single Duty Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" >
    <style>
        [v-cloak] > * { display:none; }
        [v-cloak]::before { content: "loading..."; }
        @font-face {
            font-family: Sarabun;
            src: url(../../assets/fonts/Sarabun/Sarabun-Regular.ttf);
        }
        * {
            font-family : Sarabun;
            font-size   : 16px;
        }
        .table-bordered { border: 1px solid black !important; }
        .table-bordered th, .table-bordered td { border: 1px solid black !important; padding: 10px; }
        @media print { .no-print { display: none; } }
    </style>  
    </head>
  <body>
    <div id="app" v-cloak class="container mt-5">
        <div class="text-center mb-4">
            <h3>ตารางการปฏิบัติหน้าที่ {{datas.search_name}}</h3>
            <h4>ประจำเดือน {{datas.ven_month_th}}</h4>
        </div>

        <table class="table table-bordered text-center">
            <thead>
                <tr class="table-light">
                    <th style="width: 250px;">วัน/เดือน/ปี</th>
                    <th>รายชื่อผู้ปฏิบัติหน้าที่</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="row in datas.respJSON" :key="row.date">
                    <td>{{date_thai(row.date)}}</td>
                    <td class="text-start">{{row.names}}</td>
                </tr>
            </tbody>
        </table>

        <div class="mt-4 no-print text-center">
            <button class="btn btn-primary" onclick="window.print()">พิมพ์เอกสาร</button>
        </div>
    </div>

    <script src="../../node_modules/vue/dist/vue.global.js"></script>
    <script>
      Vue.createApp({
        data() { return { datas: { respJSON: [] } } },
        mounted(){
          const printData = localStorage.getItem("print_single")
          if (printData) { this.datas = JSON.parse(printData) }
        },
        methods: {
          date_thai(day){
            var monthNamesThai = ["มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม"];
            var dayNames = ["วันอาทิตย์","วันจันทร์","วันอังคาร","วันพุธ","วันพฤหัสบดี","วันศุกร์","วันเสาร์"];
            var d = new Date(day);
            return dayNames[d.getDay()] + 'ที่ ' + d.getDate() + ' ' + monthNamesThai[d.getMonth()] + ' ' + (d.getFullYear() + 543);
          }
        }
      }).mount('#app')
    </script>
  </body>
</html>
