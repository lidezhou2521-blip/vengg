<?php 
require_once('../../server/authen.php'); 
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Report 5 (Attachment)</title>
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
        .table-bordered { border: 1px solid black !important; margin-bottom: 20px;}
        .table-bordered th, .table-bordered td { border: 1px solid black !important; padding: 5px; vertical-align: middle; }
        .text-bold { font-weight: bold; }
        @page { size: A4; margin: 15mm 10mm; }
        .page-break { page-break-after: always; }
        .page-break:last-child { page-break-after: auto; }
        @media print { .no-print { display: none; } }
    </style>  
    </head>
  <body>
    <div id="app" v-cloak class="container mt-2">
        <div id="print-area">
            <template v-for="(page, pIndex) in chunkedData" :key="pIndex">
                <div class="page-break">
                    <div class="text-center mb-4">
                        <h5 class="text-bold">บัญชีรายชื่อข้าราชการฝ่ายตุลาการศาลยุติธรรมและพนักงานราชการ</h5>
                        <h5 class="text-bold">แนบท้ายคำสั่งศาล ที่ {{datas.vc.ven_com_num}} ลงวันที่ {{date_thai(datas.vc.ven_com_date)}}</h5>
                        <h5 class="text-bold">เรื่อง ให้ข้าราชการฝ่ายตุลาการศาลยุติธรรม พนักงานราชการและลูกจ้างปฏิบัติงานนอกเวลาราชการ และในวันหยุดราชการ</h5>
                        <h5 class="text-bold">ประจำเดือน {{datas.vc.ven_month_th}}</h5>
                    </div>

                    <table class="table table-bordered text-center" v-for="(day, index) in page" :key="index">
                        <thead>
                            <tr class="table-light">
                                <th rowspan="2" style="width: 150px;">วันที่</th>
                                <th rowspan="2" style="width: 250px;">ข้าราชการตุลาการ</th>
                                <th colspan="2">ข้าราชการศาลยุติธรรม พนักงานราชการและลูกจ้าง</th>
                                <th rowspan="2" style="width: 150px;">หมายเหตุ</th>
                            </tr>
                            <tr class="table-light">
                                <th style="width: 250px;">ชื่อ-นามสกุล</th>
                                <th style="width: 200px;">ปฏิบัติหน้าที่</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, rIndex) in day.rows" :key="rIndex">
                                <td v-if="rIndex === 0" :rowspan="day.rows.length">
                                    <span class="text-bold">{{date_thai_day_only(day.ven_date)}}</span><br>
                                    ที่ {{date_thai_num(day.ven_date)}}<br>
                                    (เวลา {{day.ven_time}}-16.30 น.)
                                </td>
                                <td class="text-start">{{row.judge_name}}</td>
                                <td class="text-start">{{row.staff_name}}</td>
                                <td class="text-start">{{row.staff_duty}}</td>
                                <td v-if="rIndex === 0" :rowspan="day.rows.length">
                                    {{day.note}}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </template>
        </div>

        <div class="mt-4 mb-5 no-print text-center">
            <button class="btn btn-primary mx-2" onclick="window.print()">🖨️ พิมพ์เอกสาร</button>
            <button class="btn btn-info mx-2" @click="exportWord">📝 แปลงเป็น Word</button>
        </div>
    </div>

    <script src="../../node_modules/vue/dist/vue.global.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
      Vue.createApp({
        data() { return { datas: { respJSON: [], vc: {} } } },
        computed: {
            chunkedData() {
                if(!this.datas.respJSON) return [];
                const data = this.datas.respJSON.map(d => {
                    const jLen = d.u_namej.length;
                    const sLen = d.u_staff.length;
                    const maxRows = Math.max(jLen, sLen, 1);
                    const rows = [];
                    for(let i=0; i<maxRows; i++){
                        rows.push({
                            judge_name: i < jLen ? d.u_namej[i] : '',
                            staff_name: i < sLen ? d.u_staff[i].name : '',
                            staff_duty: i < sLen ? d.u_staff[i].duty : ''
                        });
                    }
                    d.rows = rows;
                    return d;
                });
                
                const chunks = [];
                for (let i = 0; i < data.length; i += 5) {
                    chunks.push(data.slice(i, i + 5));
                }
                return chunks;
            }
        },
        mounted(){
          const printData = localStorage.getItem("print6")
          if (printData) { this.datas = JSON.parse(printData) }
        },
        methods: {
          exportPDF() {
            const element = document.getElementById('print-area');
            const opt = {
                margin:       [15, 10, 15, 10],
                filename:     'รายงานแนบท้ายเวร.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' },
                pagebreak:    { mode: ['css', 'legacy'] }
            };
            html2pdf().set(opt).from(element).save();
          },
          exportWord() {
            const element = document.getElementById('print-area').cloneNode(true);
            
            // Fix page breaks for MS Word by replacing the CSS page-break div with an mso page break
            const pageBreaks = element.querySelectorAll('.page-break');
            for(let i=0; i < pageBreaks.length - 1; i++) {
                const br = document.createElement('br');
                br.setAttribute('clear', 'all');
                br.setAttribute('style', 'mso-special-character:line-break;page-break-before:always');
                pageBreaks[i].parentNode.insertBefore(br, pageBreaks[i].nextSibling);
            }

            const header = `<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
            <head>
                <meta charset='utf-8'>
                <title>Report</title>
                <style>
                    @page WordSection1 {
                        size: 210mm 297mm;
                        margin: 15mm 10mm 15mm 10mm;
                        mso-header-margin: 10mm;
                        mso-footer-margin: 10mm;
                        mso-paper-source: 0;
                    }
                    div.WordSection1 { page: WordSection1; }
                    body { font-family: 'Sarabun', 'TH Sarabun New', sans-serif; font-size: 14pt; }
                    table { border-collapse: collapse; width: 100%; text-align: center; margin-bottom: 20px; } 
                    table, th, td { border: 1pt solid black; padding: 5px; vertical-align: middle; } 
                    .table-light th { background-color: #f8f9fa; }
                    .text-start { text-align: left; } 
                    .text-center { text-align: center; } 
                    .text-bold { font-weight: bold; }
                    h5 { margin-top: 5px; margin-bottom: 5px; font-size: 16pt; }
                </style>
            </head>
            <body><div class="WordSection1">`;
            const footer = "</div></body></html>";
            const sourceHTML = header + element.innerHTML + footer;
            
            const source = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(sourceHTML);
            const fileDownload = document.createElement("a");
            document.body.appendChild(fileDownload);
            fileDownload.href = source;
            fileDownload.download = 'รายงานแนบท้ายเวร.doc';
            fileDownload.click();
            document.body.removeChild(fileDownload);
          },
          date_thai(day){
            if(!day) return '';
            var monthNamesThai = ["มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม"];
            var d = new Date(day);
            return d.getDate() + ' ' + monthNamesThai[d.getMonth()] + ' ' + (d.getFullYear() + 543);
          },
          date_thai_num(day){
            if(!day) return '';
            var monthNamesThai = ["ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค."];
            var d = new Date(day);
            return d.getDate() + ' ' + monthNamesThai[d.getMonth()] + ' ' + ((d.getFullYear() + 543).toString().substring(2));
          },
          date_thai_day_only(day){
            if(!day) return '';
            var dayNames = ["อาทิตย์","จันทร์","อังคาร","พุธ","พฤหัสบดี","ศุกร์","เสาร์"];
            var d = new Date(day);
            return dayNames[d.getDay()];
          }
        }
      }).mount('#app')
    </script>
  </body>
</html>
