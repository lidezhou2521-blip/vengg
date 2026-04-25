<?php 
require_once('../../server/authen.php'); 
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Group-based Duty Report</title>
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
            font-size   : 13px;
        }
        .table-bordered {
            border: 1px solid black !important;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid black !important;
            padding: 4px;
            vertical-align: middle;
        }
        .group-header {
            background-color: #e9ecef;
            font-weight: bold;
            text-align: left;
            padding-left: 10px !important;
        }
        .header-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .tick {
            font-size: 16px;
            color: black;
        }
        @media print {
            .no-print { display: none; }
            @page { size: landscape; margin: 1cm; }
            .page-break { page-break-after: always; }
        }
    </style>  
    </head>
  <body>
    <div id="app" v-cloak class="container-fluid mt-3">
        <div class="text-center">
            <div class="header-title">บัญชีรายชื่อข้าราชการฝ่ายตุลาการศาลยุติธรรม</div>
            <div class="header-title">ประจำเดือน {{datas.ven_month_th}}</div>
        </div>

        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 40px;">ที่</th>
                    <th rowspan="2" style="width: 200px;">ชื่อ-นามสกุล</th>
                    <th :colspan="datas.dates.length">วันที่</th>
                    <th rowspan="2">หน้าที่ความรับผิดชอบ</th>
                    <th rowspan="2" style="width: 100px;">หมายเหตุ</th>
                </tr>
                <tr>
                    <th v-for="d in datas.dates" :key="d.ven_date" style="width: 30px;">
                        {{get_day(d.ven_date)}}
                    </th>
                </tr>
            </thead>
            <template v-for="(users, groupName) in datas.groups" :key="groupName">
                <tbody>
                    <tr>
                        <td :colspan="4 + datas.dates.length" class="group-header">
                            {{groupName}}
                        </td>
                    </tr>
                    <tr v-for="(u, uid, index) in users" :key="uid">
                        <td>{{index + 1}}</td>
                        <td class="text-start">{{u.name}}</td>
                        <td v-for="d in datas.dates" :key="d.ven_date">
                            <span v-if="hasDuty(u.dates, d.ven_date)" class="tick">✓</span>
                        </td>
                        <td class="text-start"></td>
                        <td></td>
                    </tr>
                </tbody>
            </template>
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
            datas: { dates: [], groups: {} }
          }
        },
        mounted(){
          const printData = localStorage.getItem("print_groups")
          if (printData) {
            this.datas = JSON.parse(printData)
            // localStorage.removeItem("print_groups")
          }
        },
        methods: {
          get_day(dateStr){
            return new Date(dateStr).getDate();
          },
          hasDuty(userDates, targetDate){
            return userDates.includes(targetDate);
          }
        }
      }).mount('#app')
    </script>
  </body>
</html>
