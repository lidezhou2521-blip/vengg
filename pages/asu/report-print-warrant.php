<?php 
require_once('../../server/authen.php'); 
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Warrant Duty Schedule</title>
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
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .report-container {
            background-color: white;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 0;
            margin: auto;
            border: 1px solid #dee2e6;
            max-width: 297mm; /* A4 Landscape */
        }
        .report-header {
            background-color: #34ace0;
            color: white;
            padding: 8px;
            text-align: center;
            border-bottom: 1px solid #000;
        }
        .report-header h4 {
            margin: 0;
            font-weight: bold;
            font-size: 18px;
            letter-spacing: 1px;
        }
        .report-subheader {
            background-color: white;
            color: #333;
            padding: 5px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            border-bottom: 1px solid #000;
        }
        .table {
            margin-bottom: 0;
            border-collapse: collapse;
        }
        .table-bordered {
            border: 1px solid #000 !important;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #000 !important;
            padding: 4px 2px;
            vertical-align: middle;
            text-align: center;
        }
        .table-bordered th {
            background-color: white;
        }
        .col-no { width: 35px; font-weight: bold; }
        .col-name { width: 220px; text-align: left !important; padding-left: 10px !important; }
        .col-day { width: 30px; font-size: 12px; font-weight: bold; }
        .tick {
            color: black;
            font-weight: bold;
            font-size: 16px;
        }
        .row-hover:hover {
            background-color: #f0f0f0;
        }
        /* Highlight example like in the image (No 11) */
        .row-highlight {
            background-color: #a0a0a0 !important;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 0; background-color: white; }
            .report-container { box-shadow: none; border: none; width: 100%; max-width: none; }
            @page { size: landscape; margin: 0.5cm; }
            .table-bordered th, .table-bordered td {
                border: 1px solid #000 !important;
            }
            .report-header { background-color: #34ace0 !important; -webkit-print-color-adjust: exact; }
            .table th { background-color: white !important; -webkit-print-color-adjust: exact; }
        }
    </style>  
    </head>
  <body>
    <div id="app" v-cloak class="report-container">
        <div class="report-header">
            <h4>เวรหมายค้นหมายจับ</h4>
        </div>
        <div class="report-subheader">
            ประจำเดือน {{datas.ven_month_th}} (เวลา 16.30-08.30 น.)
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th rowspan="2" class="col-no">ที่</th>
                        <th rowspan="2" class="col-name">ชื่อ-นามสกุล</th>
                        <th :colspan="datas.dates.length">วันที่</th>
                    </tr>
                    <tr>
                        <th v-for="d in datas.dates" :key="d.ven_date" class="col-day">
                            {{get_day(d.ven_date)}}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(u, index) in allUsers" :key="u.uid" class="row-hover">
                        <td>{{index + 1}}</td>
                        <td class="col-name text-nowrap">{{u.name}}</td>
                        <td v-for="d in datas.dates" :key="d.ven_date">
                            <span v-if="hasDuty(u.dates, d.ven_date)" class="tick">✓</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="p-4 no-print text-center">
            <button class="btn btn-primary btn-lg px-5 shadow" onclick="window.print()">
                <i class="bi bi-printer"></i> พิมพ์เอกสาร
            </button>
            <button class="btn btn-outline-secondary btn-lg ms-3" onclick="window.history.back()">
                กลับ
            </button>
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
        computed: {
          allUsers() {
            if (!this.datas || !this.datas.groups) return [];
            let users = [];
            for (const groupName in this.datas.groups) {
              // ในตารางคิวเวรหมายจับ โดยปกติจะไม่เอาผู้พิพากษามารวมในรายการเจ้าหน้าที่
              if (groupName === 'ผู้พิพากษา') continue;
              
              const groupData = this.datas.groups[groupName];
              const groupUsers = groupData.users || {};
              let currentGroupUsers = [];
              if (Array.isArray(groupUsers)) {
                currentGroupUsers = groupUsers;
              } else if (typeof groupUsers === 'object') {
                currentGroupUsers = Object.values(groupUsers);
              }
              users = users.concat(currentGroupUsers);
            }
            
            // Filter: only users who have at least one Warrant/Search duty
            users = users.filter(u => {
                if (!u.dates || !Array.isArray(u.dates)) return false;
                return u.dates.some(d => {
                    const dName = typeof d === 'object' ? (d.ven_name || '') : '';
                    return dName.includes('หมายจับ') || dName.includes('หมายค้น');
                });
            });

            // Sort: numerically by order, then by name
            return users.sort((a, b) => {
              const orderA = parseInt(a.order) || 999;
              const orderB = parseInt(b.order) || 999;
              if (orderA !== orderB) return orderA - orderB;
              return a.name.localeCompare(b.name, 'th');
            });
          }
        },
        mounted(){
          const printData = localStorage.getItem("print_warrant")
          if (printData) {
            try {
              this.datas = JSON.parse(printData);
            } catch (e) {
              console.error("Error parsing printData", e);
            }
          }
        },
        methods: {
          get_day(dateStr){
            return new Date(dateStr).getDate();
          },
          hasDuty(userDates, targetDate){
            if (!userDates || !Array.isArray(userDates)) return false;
            return userDates.some(d => {
              const dDate = typeof d === 'string' ? d : d.date;
              const dName = typeof d === 'object' ? (d.ven_name || '') : '';
              if (dDate !== targetDate) return false;
              // เฉพาะเวรหมายจับ/หมายค้น
              return dName.includes('หมายจับ') || dName.includes('หมายค้น');
            });
          }
        }
      }).mount('#app')
    </script>
  </body>
</html>
