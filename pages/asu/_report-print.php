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
    </style>  
    </head>
  <body>
    <div id="appReports" v-cloak>
        <div class="text-center">
            <h3>{{datas.heads.vc_name}}</h3>
            <h4>แนบท้ายคำสั่งที่ {{datas.heads.vc_num}} ลงวันที่ {{datas.heads.vc_date}}</h4>
            <h5>ประจำเดือน {{datas.heads.ven_month_th}}</h5>
            <!-- {{datas.vc}} -->
        </div>
        <table class="table table-bordered d-print-inline d-print-table ">
            <thead>
                <tr class="text-center">
                    <th>วัน เดือน ปี</th>
                    <th>เวลา</th>
                    <th>รายชื่อผู้พิพากษา</th>
                    <th>รายชื่อเจ้าหน้าที่</th>
                    <th>หมายเหตุ</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="d in datas.respJSON">
                    <td>{{d.ven_date_th}}</td>
                    <td>
                        <li class="list-group-item" v-for="dvt in d.ven_time">
                            {{dvt == '08:30' ? '8.30 - 16.30 น.' : '16.30 - 8.30 น.'}}
                        </li>
                    </td>
                    <td>
                        <li class="list-group-item" v-for="dunj in d.u_namej"> {{dunj}}</li>
                    </td>
                    <td> 
                        <li class="list-group-item" v-for="dun in d.u_name">{{dun}}</li>
                    </td>
                    <td>
                       <li class="list-group-item" v-for="dur in d.cmt">{{dur}}</li>
                    </td>
                </tr>
            </tbody>
        </table> 
        <div class="text-end mt-5 me-5 ">
          <br>
          <br>
          <br>
          <h5>(ชื่อ - สกุล)</h5>
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
      window.print()
    },
    methods: {    
           

    },
  }).mount('#appReports');
</script>
  </body>
</html>