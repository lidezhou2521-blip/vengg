<?php 
require_once('../../server/authen.php'); 
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Master Summary Report</title>
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
            font-size   : 14px;
        }
        .table-bordered {
            border: 1px solid black !important;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid black !important;
            padding: 5px;
            vertical-align: middle;
        }
        .header-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        @media print {
            .no-print { display: none; }
            @page { size: portrait; margin: 1cm; }
        }
        .bg-gray {
            background-color: #f2f2f2 !important;
        }
    </style>  
    </head>
  <body>
    <div id="app" v-cloak class="container-fluid mt-3">
        <div class="text-center">
            <div class="header-title">ตารางเวรประจำเดือน {{datas.ven_month_th}}</div>
        </div>

        <table class="table table-bordered text-center">
            <thead>
                <tr class="bg-gray">
                    <th style="width: 100px;">วันที่</th>
                    <th v-for="col in datas.columns" :key="col.id">{{col.name}}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="row in datas.respJSON" :key="row.date">
                    <td :class="isWeekend(row.date) ? 'text-danger fw-bold' : ''">
                        {{date_thai_short(row.date)}}
                    </td>
                    <td v-for="col in datas.columns" :key="col.id" style="white-space: pre-line;">
                        {{row.duties[col.id]}}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="mt-4 no-print text-center">
            <button class="btn btn-primary" onclick="window.print()">พิมพ์เอกสาร</button>
        </div>
    </div>

    <script src="../../node_modules/vue/dist/vue.global.js"></script>
    <script>
      const { createApp } = Vue
      createApp({
        data() {
          return {
            datas: { columns: [], respJSON: [] }
          }
        },
        mounted(){
          const printData = localStorage.getItem("print_master")
          if (printData) {
            this.datas = JSON.parse(printData)
            // localStorage.removeItem("print_master")
          }
        },
        methods: {
          date_thai_short(day){
            const d = new Date(day);
            const dayNames = ["อา.","จ.","อ.","พ.","พฤ.","ศ.","ส."];
            return dayNames[d.getDay()] + ' ' + d.getDate();
          },
          isWeekend(day){
            const d = new Date(day);
            return d.getDay() === 0 || d.getDay() === 6;
          }
        }
      }).mount('#app')
    </script>
  </body>
</html>
