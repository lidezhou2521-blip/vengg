<?php
require_once('../../server/authen.php');
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Open Court Duty Schedule</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    [v-cloak]>* {
      display: none;
    }

    [v-cloak]::before {
      content: "loading...";
    }

    @font-face {
      font-family: Sarabun;
      src: url(../../assets/fonts/Sarabun/Sarabun-Regular.ttf);
    }

    * {
      font-family: Sarabun;
      font-size: 14px;
    }

    body {
      background-color: #f8f9fa;
      padding: 20px;
    }

    .report-container {
      background-color: white;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
      padding: 0;
      margin: auto;
      border: 1px solid #dee2e6;
      max-width: 297mm;
    }

    .report-header-top {
      background-color: #f39c12; /* Different color for distinct report */
      color: white;
      padding: 5px;
      text-align: center;
      font-weight: bold;
      font-size: 16px;
      border-bottom: 1px solid #000;
    }

    .report-header-sub {
      text-align: center;
      padding: 5px;
      font-size: 14px;
      border-bottom: 1px solid #000;
      line-height: 1.4;
    }

    .table {
      margin-bottom: 0;
      border-collapse: collapse;
    }

    .table-bordered {
      border: 1px solid #000 !important;
    }

    .table-bordered th,
    .table-bordered td {
      border: 1px solid #000 !important;
      padding: 4px 5px;
      vertical-align: middle;
    }

    .table th {
      text-align: center;
      background-color: white;
    }

    .group-header {
      background-color: #fef5e7;
      text-align: center !important;
      font-weight: bold;
    }

    .col-no {
      width: 35px;
      text-align: center;
    }

    .col-name {
      width: 200px;
    }

    .col-day {
      width: 30px;
      text-align: center;
      font-size: 12px;
      font-weight: bold;
      background-color: #f39c12 !important;
      color: white !important;
    }

    .col-duty {
      width: 300px;
      font-size: 11px;
      line-height: 1.2;
      text-align: left;
    }

    .col-remark {
      width: 120px;
      font-size: 11px;
      text-align: left;
    }

    .tick {
      color: black;
      font-weight: bold;
      font-size: 16px;
      text-align: center;
      display: block;
    }

    @media print {
      .no-print {
        display: none;
      }

      body {
        padding: 0;
        background-color: white;
      }

      .report-container {
        box-shadow: none;
        border: none;
        width: 100%;
        max-width: none;
      }

      @page {
        size: portrait;
        margin: 0.5cm;
      }

      .report-header-top {
        background-color: #f39c12 !important;
        -webkit-print-color-adjust: exact;
      }

      .col-day {
        background-color: #f39c12 !important;
        -webkit-print-color-adjust: exact;
      }
    }
  </style>
</head>

<body>
  <div id="app" v-cloak class="report-container">
    <div class="report-header-top">
      บัญชีรายชื่อข้าราชการฝ่ายตุลาการศาลยุติธรรมศาลจังหวัดเบตง
    </div>
    <div class="report-header-sub">
      เวรพิจารณาคำร้องขอปล่อยตัวชั่วคราว และเวรเปิดทำการวันหยุดของศาลเยาวชนฯ<br>

      ประจำเดือน {{datas.ven_month_th}} (เวรวันอาทิตย์หรือวันหยุดราชการ 08.30-16.30 น.)
    </div>

    <div class="table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th rowspan="2" class="col-no">ที่</th>
            <th rowspan="2" class="col-name">ชื่อ-นามสกุล</th>
            <th :colspan="filteredDates.length">วันที่</th>
            <th rowspan="2" class="col-duty">หน้าที่ความรับผิดชอบ</th>
            <th rowspan="2" class="col-remark">หมายเหตุ</th>
          </tr>
          <tr>
            <th v-for="d in filteredDates" :key="d.ven_date" class="col-day">
              {{get_day(d.ven_date)}}
            </th>
          </tr>
        </thead>
        <tbody>
          <template v-for="(group, gName, gIndex) in groupedUsers" :key="gName">
            <!-- Group Header Row -->
            <tr>
              <td class="col-no"></td>
              <td class="group-header">{{getGroupLabel(gIndex + 1, gName)}}</td>
              <td v-for="d in filteredDates"></td>
              <td class="col-duty"></td>
              <td class="col-remark"></td>
            </tr>
            <!-- User Rows -->
            <tr v-for="(u, uIndex) in group" :key="u.uid">
              <td class="text-center">{{uIndex + 1}}</td>
              <td>{{u.name}}</td>
              <td v-for="d in filteredDates" :key="d.ven_date">
                <span v-if="hasDuty(u.dates, d.ven_date)" class="tick">✓</span>
              </td>
              <!-- Responsibility spans all rows of group -->
              <td v-if="uIndex === 0" :rowspan="group.length" class="col-duty">
                {{getResponsibility(gName)}}
              </td>
              <td v-if="uIndex === 0" :rowspan="group.length" class="col-remark">
                {{getRemark(gName)}}
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <div class="p-4 no-print text-center">
      <button class="btn btn-warning btn-lg px-5 shadow text-white" onclick="window.print()">
        <i class="bi bi-printer"></i> พิมพ์เอกสาร
      </button>
      <button class="btn btn-outline-secondary btn-lg ms-3" onclick="window.history.back()">
        กลับ
      </button>
    </div>
  </div>

  <script src="../../node_modules/vue/dist/vue.global.js"></script>
  <script>
    const {
      createApp
    } = Vue
    createApp({
      data() {
        return {
          datas: {
            dates: [],
            groups: {}
          },
          responsibilities: {
            'หัวหน้ากลุ่ม': 'ทำหน้าที่เป็นหัวหน้ากลุ่ม ปฏิบัติหน้าที่หัวหน้าชุดผู้ควบคุมการปฏิบัติงาน',
            'งานการเงิน': 'งานการเงิน',
            'งานหมาย-รับคำร้อง': 'ปฏิบัติหน้าที่งานออกหมาย ค้นสำนวน รวบรวมข้อมูลเพื่อประเมินความเสี่ยงการปล่อยตัวชั่วคราว (บ.ค.3) รวบรวมข้อมูล บันทึกแบบแสดงความประสงค์ขอใช้อุปกรณ์อิเล็กทรอนิกส์ (EM) จัดเตรียมและติดตั้งอุปกรณ์ EM บันทึกการรับ-จ่ายอุปกรณ์ EM ให้ผู้ต้องหาหรือจำเลย จัดทำคำสั่งตั้งผู้กำกับดูแล พิมพ์หนังสือแจ้งผู้กำกับดูแล จัดทำหนังสือแจ้งคำสั่งห้ามออกนอกประเทศ ส่งคำสั่งผ่านระบบ WLIS เตรียมความพร้อมระบบจอภาพระหว่างศาล-เรือนจำ จัดทำหมายเบิก ประสานงานเรือนจำเบิกตัวผู้ต้องหาฟังคำสั่งปล่อยชั่วคราว/ติดตั้ง EM สแกนสำนวนในส่วนที่เกี่ยวข้อง จัดทำ คำร้องขอทำงานบริการสังคมแทนค่าปรับ (บ.ส.1, บ.ส.2) จัดทำใบนัดรายงานตัว จัดทำหนังสือแจ้งสำนักงานคุมประพฤติ บันทึกสมุดคุมการทำงานบริการสังคมแทนค่าปรับ',
            'งานประชาสัมพันธ์': 'งานประชาสัมพันธ์ (ปล่อยตัวชั่วคราว)'
          }
        }
      },
      computed: {
        groupedUsers() {
          if (!this.datas || !this.datas.groups) return {};
          let result = {};
          // Order based on the image
          const order = ['หัวหน้ากลุ่ม', 'งานการเงิน', 'งานหมาย-รับคำร้อง', 'งานประชาสัมพันธ์'];

          for (const gName in this.datas.groups) {
            if (gName === 'ผู้พิพากษา') continue;

            const users = Object.values(this.datas.groups[gName]).filter(u => {
              // Filter only for duty ID 28
              return u.dates && u.dates.some(d => d.vn_id == 28);
            });

            if (users.length === 0) continue;

            let matchName = gName;
            for (let key of order) {
              if (gName.includes(key)) {
                matchName = key;
                break;
              }
            }

            if (!result[matchName]) result[matchName] = [];
            result[matchName] = result[matchName].concat(users);
          }

          for (let key in result) {
            result[key].sort((a, b) => (parseInt(a.order) || 999) - (parseInt(b.order) || 999));
          }
          return result;
        },
        filteredDates() {
          if (!this.datas || !this.datas.dates) return [];
          // Filter only dates that have duty ID 28
          let activeDates = new Set();
          for (const groupName in this.datas.groups) {
            const group = this.datas.groups[groupName];
            for (const uid in group) {
              const user = group[uid];
              if (user.dates) {
                user.dates.forEach(d => {
                  if (d.vn_id == 28) activeDates.add(d.date);
                });
              }
            }
          }
          return this.datas.dates.filter(d => activeDates.has(d.ven_date));
        }
      },
      mounted() {
        const printData = localStorage.getItem("print_open_court_schedule")
        if (printData) {
          try {
            this.datas = JSON.parse(printData);
          } catch (e) {
            console.error("Error parsing printData", e);
          }
        }
      },
      methods: {
        get_day(dateStr) {
          return new Date(dateStr).getDate();
        },
        getGroupLabel(num, name) {
          return `กลุ่มที่ ${num} ${name}`;
        },
        getResponsibility(gName) {
          for (let key in this.responsibilities) {
            if (gName.includes(key)) return this.responsibilities[key];
          }
          return '';
        },
        getRemark(gName) {
          if (gName.includes('ประชาสัมพันธ์')) return '';
          return 'ทำหน้าที่เวรของศาลเยาวชนฯ อีกตำแหน่งหนึ่ง';
        },
        hasDuty(userDates, targetDate) {
          if (!userDates || !Array.isArray(userDates)) return false;
          return userDates.some(d => {
            const dDate = typeof d === 'string' ? d : d.date;
            if (dDate !== targetDate) return false;
            return d.vn_id == 28;
          });
        }
      }
    }).mount('#app')
  </script>
</body>

</html>
