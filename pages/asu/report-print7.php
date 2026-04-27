<?php 
require_once('../../server/authen.php'); 
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>รายงานเวรหมายจับ-ค้น</title>
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
        .table-bordered { border: 1px solid black !important; margin-bottom: 20px;}
        .table-bordered th, .table-bordered td { border: 1px solid black !important; padding: 10px 5px; vertical-align: top; }
        .text-bold { font-weight: bold; }
        @page { size: A4 portrait; margin: 15mm 10mm; }
        .page-break { page-break-after: always; }
        .page-break:last-child { page-break-after: auto; }
        @media print { .no-print { display: none; } }
        .list-item { margin-bottom: 5px; }
    </style>  
    </head>
  <body>
    <div id="app" v-cloak class="container mt-2">
        <div id="print-area">
            <template v-for="(page, pIndex) in chunkedData" :key="pIndex">
                <div class="page-break">
                    <div class="text-center mb-4">
                        <h5 class="text-bold">บัญชีรายชื่อข้าราชการฝ่ายตุลาการศาลยุติธรรม พนักงานราชการและลูกจ้าง</h5>
                        <h5 class="text-bold">แนบท้ายคำสั่งศาล ที่ {{toThaiNum(datas.vc.ven_com_num)}} ลงวันที่ {{date_thai(datas.vc.ven_com_date)}}</h5>
                        <h7 class="text-bold">เรื่อง ให้ข้าราชการฝ่ายตุลาการศาลยุติธรรม พนักงานราชการและลูกจ้างปฏิบัติงานนอกเวลาราชการ </h7>
                        <h7 class="text-bold">และในวันหยุดราชการ เวรหมายจับ หมายค้น เวลา ๑๖.๓๐ น. - ๐๘.๓๐ น. ของวันรุ่งขึ้น (เวรกลางคืน)</h7>
                        
                        <h6 class="text-bold">ประจำเดือน {{toThaiNum(datas.vc.ven_month_th)}}</h6>
                    </div>

                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th style="width: 20%;">วันที่</th>
                                <th style="width: 40%;">เวลา ๑๖.๓๐ - ๐๘.๓๐ น.<br>ข้าราชการตุลาการ</th>
                                <th style="width: 40%;">เวลา ๑๖.๓๐ - ๐๘.๓๐ น.<br>ข้าราชการศาลยุติธรรม</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(day, index) in page" :key="index">
                                <td class="align-middle text-center" style="white-space: nowrap;">
                                    {{ date_thai_day_only(day.ven_date) }} ที่ {{ date_thai_num(day.ven_date) }}
                                </td>
                                <td class="text-start px-3">
                                    <div v-for="(j, jIndex) in day.u_namej" :key="'j'+jIndex" class="list-item">
                                        {{ toThaiNum(jIndex + 1) }}. {{ j }}
                                    </div>
                                </td>
                                <td class="text-start px-3">
                                    <div v-for="(s, sIndex) in day.u_staff" :key="'s'+sIndex" class="list-item">
                                        {{ toThaiNum(sIndex + 3) }}. {{ s.name }}
                                    </div>
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
    <script>
      Vue.createApp({
        data() { return { datas: { respJSON: [], vc: {} } } },
        computed: {
            chunkedData() {
                if(!this.datas.respJSON) return [];
                const data = this.datas.respJSON;
                const chunks = [];
                for (let i = 0; i < data.length; i += 12) {
                    chunks.push(data.slice(i, i + 12));
                }
                return chunks;
            }
        },
        mounted(){
          const printData = localStorage.getItem("print7")
          if (printData) { this.datas = JSON.parse(printData) }
        },
        methods: {
          exportWord() {
            const element = document.getElementById('print-area').cloneNode(true);
            
            // Fix page breaks for MS Word
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
                    body { font-family: 'Sarabun', 'TH Sarabun New', sans-serif; font-size: 16pt; }
                    table { border-collapse: collapse; width: 100%; text-align: center; margin-bottom: 20px; } 
                    table, th, td { border: 1pt solid black; padding: 10px 5px; vertical-align: top; } 
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
            fileDownload.download = 'รายงานเวรหมายจับค้น.doc';
            fileDownload.click();
            document.body.removeChild(fileDownload);
          },
          toThaiNum(num) {
            if (num === null || num === undefined) return '';
            const thaiNums = ['๐', '๑', '๒', '๓', '๔', '๕', '๖', '๗', '๘', '๙'];
            return num.toString().replace(/\d/g, match => thaiNums[match]);
          },
          date_thai(day){
            if(!day) return '';
            var monthNamesThai = ["มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม"];
            var d = new Date(day);
            return this.toThaiNum(d.getDate()) + ' ' + monthNamesThai[d.getMonth()] + ' ' + this.toThaiNum(d.getFullYear() + 543);
          },
          date_thai_num(day){
            if(!day) return '';
            var monthNamesThai = ["ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค."];
            var d = new Date(day);
            return this.toThaiNum(d.getDate()) + ' ' + monthNamesThai[d.getMonth()] + ' ' + this.toThaiNum((d.getFullYear() + 543).toString().substring(2));
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
