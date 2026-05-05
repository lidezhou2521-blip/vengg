<?php
require_once('../../server/authen.php');
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>District Court Duty Schedule</title>
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
      background-color: #a29bfe;
      color: black;
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
      background-color: #f8f9fa;
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
      background-color: #a29bfe !important;
    }

    .col-duty {
      width: 300px;
      font-size: 11px;
      line-height: 1.2;
      text-align: left;
    }
    .col-duty-sm {
      font-size: 9.5px !important;
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
        background-color: #a29bfe !important;
        -webkit-print-color-adjust: exact;
      }

      .col-day {
        background-color: #a29bfe !important;
        -webkit-print-color-adjust: exact;
      }

      .bg-comment {
        background-color: #e0e0e0 !important;
        -webkit-print-color-adjust: exact;
      }
    }

    .bg-comment {
      background-color: #e0e0e0;
    }
  </style>
</head>

<body>
  <div id="app" v-cloak class="report-container">
    <div class="report-header-top">
      บัญชีรายชื่อข้าราชการฝ่ายตุลาการศาลยุติธรรมศาลจังหวัดเบตง
    </div>
    <div class="report-header-sub">
      เวรเปิดทำการศาลแขวงและพิจารณาคำร้องขอปล่อยตัวชั่วคราว<br>
      และเวรเปิดทำการวันหยุดของศาลเยาวชนฯ<br>
      ประจำเดือน {{datas.ven_month_th}} (เวรวันเสาร์หรือวันหยุดราชการ 08.30-16.30 น.)
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
          <template v-for="(group, gIndex) in groupedUsers" :key="group.name">
            <!-- Group Header Row -->
            <tr>
              <td class="col-no"></td>
              <td class="group-header">{{getGroupLabel(group.groupId || group.srt || (gIndex + 1), group.name)}}</td>
              <td v-for="d in filteredDates"></td>
              <td class="col-duty"></td>
              <td class="col-remark"></td>
            </tr>
            <!-- User Rows -->
            <tr v-for="(u, uIndex) in group.users" :key="u.uid">
              <td class="text-center">{{uIndex + 1}}</td>
              <td>{{u.name}}</td>
              <td v-if="u.comment" :colspan="filteredDates.length" class="bg-comment text-center" style="font-size: 11px;">
                {{u.comment}}
                <span v-if="getDutyDates(u.dates)" class="ms-2 text-primary">
                  (เข้าเวรวันที่: {{getDutyDates(u.dates)}})
                </span>
              </td>
              <template v-else>
                <td v-for="d in filteredDates" :key="d.ven_date">
                  <span v-if="hasDuty(u.dates, d.ven_date)" class="tick">✓</span>
                </td>
              </template>
              <!-- Responsibility spans all rows of group -->
              <td v-if="uIndex === 0" :rowspan="group.users.length" :class="['col-duty', {'col-duty-sm': group.name === 'งานประชาสัมพันธ์'}]">
                {{getResponsibility(group.name)}}
              </td>
              <td v-if="uIndex === 0" :rowspan="group.users.length" class="col-remark">
                {{getRemark(group.name)}}
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <div class="p-4 no-print text-center">
      <button class="btn btn-primary btn-lg px-5 shadow" onclick="window.print()">
        <i class="bi bi-printer"></i> พิมพ์เอกสาร
      </button>
      <button class="btn btn-outline-secondary btn-lg ms-3" onclick="window.close()">
        กลับ
      </button>
    </div>
  </div>

  <script src="../../node_modules/vue/dist/vue.global.js"></script>
  <script src="../../node_modules/axios/dist/axios.min.js"></script>
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
            'หัวหน้ากลุ่ม': 'ทำหน้าที่เป็นหัวหน้ากลุ่ม ตรวจสอบและช่วยเหลือทุกงาน เมื่อมีปัญหาข้อขัดข้องในการปฏิบัติงาน ตรวจสอบความถูกต้องของหมายต่างๆ และหนังสือแจ้งหน่วยงานภายนอก',
            'งานการเงิน': 'ปฏิบัติหน้าที่รับเงินค่าปรับพินิจ เงินกลาง ส่งมอบเงินให้กับคณะกรรมการเก็บรักษาเงิน และจัดทำเอกสารทางการเงินและบันทึกบัญชี',
            'งานรับฟ้อง': 'งานรับฟ้อง ปฏิบัติหน้าที่รับคำฟ้อง จัดทำสำนวน จัดทำสารบบความและสารบบคำพิพากษา ข4 สอบคำให้การ ออกหมายตามคำพิพากษา พิมพ์หนังสือแจ้งคำสั่งศาลสืบเสาะและพินิจ (คป.1) พิมพ์หนังสือแจ้งคำสั่งศาล (คป.4) คำร้องขอหมายจับและหมายค้น ส่งตัวตามหมายจับและ  เพิกถอนหมายจับในระบบ AWIS ค้นหาสำนวน ออกหมายตามคำสั่งศาล ตรวจสอบการจับและมอบตัว  พิมพ์หนังสือราชการแจ้งหน่วยงานที่เกี่ยวข้อง และสแกนเอกสารที่เกี่ยวข้องในระบบ',
            'งานผัดฟ้อง-ฝากขัง': 'งานผัดฟ้อง-ฝากขัง ปฏิบัติหน้าที่รับคำร้อง เสนอสำนวน จัดเตรียมความพร้อมของระบบทางไกลผ่านจอภาพ ประสานเจ้าหน้าที่เรือนจำรับหมาย และสแกนเอกสารที่เกี่ยวข้องในระบบ',
            'งานประชาสัมพันธ์': 'ปฏิบัติหน้าที่ให้คำแนะนำปรึกษาด้านกฎหมาย แจ้งสิทธิการปล่อยชั่วคราวให้แก่ผู้ต้องหาหรือจำเลยหรือญาติ แจ้งหน้าที่ผู้ขอประกันให้แก่ผู้ประกัน รวบรวม ตรวจสอบข้อมูลผู้ขอประกัน ข้อมูลคดี เพื่อประกอบการจัดทำคำร้องขอปล่อยชั่วคราว รวบรวมข้อมูลเพื่อประเมินความเสี่ยงการปล่อยชั่วคราว (บ.ส.3) ประสานงานผู้จะทำหน้าที่ผู้กำกับดูแลผู้ถูกปล่อยชั่วคราว จัดทำคำร้องขอปล่อยชั่วคราวก่อนวางหลักประกัน (คำร้องใบเดียว) ชั้นฝากขัง/พิจารณา/อุทธรณ์/ฎีกา ตรวจสอบเอกสารแสดงตัวตนผู้ขอประกัน และเอกสารหลักฐานเกี่ยวกับหลักประกัน จัดทำคำร้องขอปล่อยชั่วคราว คำสาบานตน สัญญาประกัน รวบรวมข้อมูล บันทึกแบบแสดงความประสงค์ขอใช้อุปกรณ์อิเล็กทรอนิกส์ (EM) นำข้อมูลเข้าระบบ EM ประสานงานศูนย์ EM จัดเตรียมและติดตั้งอุปกรณ์ EM บันทึกการรับ-จ่ายอุปกรณ์ EM ให้ผู้ต้องหาหรือจำเลย จัดทำคำสั่งแต่งตั้งผู้กำกับดูแล พิมพ์หนังสือแจ้งผู้กำกับดูแล จัดทำหนังสือแจ้งคำสั่งห้ามออกนอกประเทศ ส่งคำสั่งผ่านระบบ WLIS จัดทำหนังสือแจ้งอายัดหลักประกัน แจ้งหน่วยงานต้นสังกัดผู้ประกัน จัดทำหมายปล่อย จัดทำใบปล่อยตัว แจ้งการปล่อยตัวจำเลยให้ตำรวจผู้ควบคุมทราบ จัดทำบัตรนัดให้ผู้ต้องหาหรือจำเลยเพื่อรายงานตัว รับคำร้องอุทธรณ์คำสั่งศาลชั้นต้นที่ไม่อนุญาตให้ปล่อยชั่วคราว ส่งคำร้องอุทธรณ์ และรับคำสั่งศาลสูงผ่านระบบอิเล็กทรอนิสก์ แจ้งการรับคำสั่ง แจ้งการอ่านผ่านระบบอิเล็กทรอนิสก์ เตรียมความพร้อมระบบจอภาพระหว่างศาล-เรือนจำ จัดทำหมายเบิก ประสานงานเรือนจำเบิกตัวผู้ต้องขังเพื่อฟังคำสั่งปล่อยชั่วคราว/ติดตั้งEM สแกนสำนวนในส่วนที่เกี่ยวข้อง บันทึกรายการคำร้องขอปล่อยชั่วคราวในสมุดคุมปล่อยชั่วคราว/สมุดคุมติดตั้ง EM จัดทำรายงานการปล่อยชั่วคราวประจำวัน จัดทำคำร้องขอทำงานบริการสังคมแทนค่าปรับ (บ.ส.1, บ.ส.2) จัดทำใบนัดรายงานตัว จัดทำหนังสือแจ้งสำนักงานคุมประพฤติ บันทึกสมุดคุมการทำงานบริการสังคมแทนค่าปรับ จัดทำคำร้อง คำขอ คำแถลงอื่นใดที่เกี่ยวข้องกับการปล่อยชั่วคราวหรือเพื่อการคุ้มครองสิทธิและเสรีภาพของผู้ต้องหาหรือจำเลย'
          }
        }
      },
      computed: {
        groupedUsers() {
          if (!this.datas || !this.datas.groups) return [];
          let result = [];
          // วนลูปตามกลุ่มที่มีในข้อมูล
          const categories = ['หัวหน้ากลุ่ม', 'งานการเงิน', 'งานรับฟ้อง', 'งานผัดฟ้อง-ฝากขัง', 'งานประชาสัมพันธ์'];

          for (const gKey in this.datas.groups) {
            const groupData = this.datas.groups[gKey];
            const gName = groupData.name;

            if (gName === 'ผู้พิพากษา') continue;

            // แสดงเฉพาะกลุ่มงานที่เกี่ยวข้องกับรายงานนี้ และเฉพาะเวร ID 27 (เวรแขวงฯ)
            if (groupData.vn_id != 27) continue;
            if (!categories.some(key => gName.includes(key))) continue;
            const users = Object.values(groupData.users || {});
            if (users.length === 0) continue;

            result.push({
              name: gName,
              srt: parseInt(groupData.vns_srt) || 999,
              groupId: groupData.vns_group_id,
              vn_id: groupData.vn_id, // Store vn_id
              users: users.map(user => ({
                ...user,
                comment: user.comment // Ensure comment is passed
              }))
            });
          }

          // เรียงลำดับกลุ่มตาม srt
          result.sort((a, b) => a.srt - b.srt);

          // เรียงลำดับคนในแต่ละกลุ่ม
          result.forEach(group => {
            group.users.sort((a, b) => (parseInt(a.order) || 999) - (parseInt(b.order) || 999));
          });

          return result;
        },
        filteredDates() {
          if (!this.datas || !this.datas.dates) return [];
          // กรองเฉพาะวันที่มีเวร id 27
          let activeDates = new Set();
          for (const groupName in this.datas.groups) {
            const groupData = this.datas.groups[groupName];
            const groupUsers = groupData.users || {};
            for (const uid in groupUsers) {
              const user = groupUsers[uid];
              if (user.dates) {
                user.dates.forEach(d => {
                  if (d.vn_id == 27) activeDates.add(d.date);
                });
              }
            }
          }
          return this.datas.dates.filter(d => activeDates.has(d.ven_date));
        }
      },
      mounted() {
        const urlParams = new URLSearchParams(window.location.search);
        const venMonth = urlParams.get('ven_month');

        if (venMonth) {
          axios.post('../../server/asu/report/report_groups.php', {
              ven_month: venMonth
            })
            .then(response => {
              if (response.data.status) {
                this.datas = response.data;
              }
            })
            .catch(error => {
              console.error("Error fetching data:", error);
            });
        } else {
          const printData = localStorage.getItem("print_district_schedule")
          if (printData) {
            try {
              this.datas = JSON.parse(printData);
            } catch (e) {
              console.error("Error parsing printData", e);
            }
          }
        }
      },
      methods: {
        get_day(dateStr) {
          return new Date(dateStr).getDate();
        },
        getGroupLabel(num, name) {
          // If the name already contains "กลุ่มที่", use it as is
          if (name.includes('กลุ่มที่')) return name;
          return `กลุ่มที่ ${num} ${name}`;
        },
        getResponsibility(gName) {
          for (let key in this.responsibilities) {
            if (gName.includes(key)) return this.responsibilities[key];
          }
          return '';
        },
        getRemark(gName) {
          if (gName.includes('ผัดฟ้อง') || gName.includes('ประชาสัมพันธ์')) {
            return '';
          }
          return 'ทำหน้าที่เวรของศาลเยาวชนฯ อีกตำแหน่งหนึ่ง';
        },
        hasDuty(userDates, targetDate) {
          if (!userDates || !Array.isArray(userDates)) return false;
          return userDates.some(d => {
            const dDate = typeof d === 'string' ? d : d.date;
            if (dDate !== targetDate) return false;
            return d.vn_id == 27;
          });
        },
        getDutyDates(userDates) {
          if (!userDates || !Array.isArray(userDates)) return '';
          return userDates
            .filter(d => d.vn_id == 27)
            .map(d => {
              const dateStr = typeof d === 'string' ? d : d.date;
              return new Date(dateStr).getDate();
            })
            .sort((a, b) => a - b)
            .join(', ');
        }
      }
    }).mount('#app')
  </script>
</body>

</html>