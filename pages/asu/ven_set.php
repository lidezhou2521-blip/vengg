<?php 

require_once('../../server/authen.php'); 

?>
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8' />
  <title>
    ven-set
  </title>
  <style>
    [v-cloak] > * { display:none; }
      [v-cloak]::before { content: "loading..."; }
  
      script {
          display: none;
      }

  </style>

<!-- <link href='./dist/css/index.css' rel='stylesheet' />


<link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="./node_modules/sweetalert2/dist/sweetalert2.min.css">
<link rel="stylesheet" href="./dist/css/app.css">
<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300&display=swap" rel="stylesheet"> -->
<?php require_once('../includes/_header.php') ?>

<!-- <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script> -->
<!-- <script src="./node_modules/bootstrap/dist/js/bootstrap.min.js"></script> -->
<!-- <script src='./dist/js/demo-to-codepen.js'></script> -->
<link rel="stylesheet" href="../../assets/fullcalendar/main.min.css">
<script src="../../assets/fullcalendar/main.min.js"></script>

  <script>

  document.addEventListener('DOMContentLoaded', function() {
    var Calendar = FullCalendar.Calendar;
    var Draggable = FullCalendar.Draggable;

    var containerEl = document.getElementById('external-events');
    var calendarEl = document.getElementById('calendar');
    var checkbox = document.getElementById('drop-remove');

    // initialize the external events
    // -----------------------------------------------------------------

    new Draggable(containerEl, {
      itemSelector: '.fc-event',
      eventData: function(eventEl) {
        return {
          title: eventEl.innerText,
          extendedProps: eventEl.data-event
        };
      }
    });

    // initialize the calendar
    // -----------------------------------------------------------------

});

</script>
<style>

  html, body {
    margin: 0;
    padding: 0;
    font-family: 'Prompt', sans-serif;
    /* font-family: Arial, Helvetica Neue, Helvetica, sans-serif; */
    font-size: 15px;
  }

  #external-events {
    position: fixed;
    /* position: relative; */
    z-index: 2;
    top: 10px;
    left: 15px;
    width: 175px;
    height: 100%;
    padding: 0 10px;
    border: 1px solid rgb(236, 40, 40);
    background: rgb(144, 212, 18);
    overflow-y: auto;
    /* overflow-y: scroll; */
  }

  #external-events2 {
    position: fixed;
    font-size: 18px;
    z-index: 2;
    top: 10px;
    left: 220px;
    width: 800px;
    padding: 0 10px;
    border: 1px solid rgb(236, 40, 40);
    background: rgb(178, 206, 233);
  }

  .demo-topbar + #external-events { /* will get stripped out */
    top: 60px;
  }

  #external-events .fc-event {
    cursor: move;
    margin: 3px 0;
  }

  #calendar-container {
    position: relative;
    z-index: 1;
    margin-left: 200px;
    margin-top: 50px;
    width:auto;
  }
  
  #vc {
    position: relative;
    z-index: 3;
    margin-left: 200px;
  }
  #calendar {
    max-width: 1100px;
    margin: 20px auto;
    height: auto;
  }
</style>
</head>
<body>
<div id="venSet" v-cloak>
  <div class="clearfix" ref="loading" v-if="isLoading">
    <strong>Loading...</strong>
    <div class="spinner-border float-end" role="status" aria-hidden="true"></div>
  </div>
  
  <div id='external-events2'>
  {{ven_month}} | 
  <!-- {{ven_com}}  -->
  {{ven_com.ven_com_num}} {{ven_com.name}} {{ven_com.id}} |
  {{ven_name_sub.name}} {{ven_name_sub.vn_id}} : {{ven_name_sub.name}} {{ven_name_sub.vns_id}}
  <!-- {{ven_name_sub}}  -->
  </div>
  <div id='external-events'>
    <form >
      <!-- เลือกเดือน -->
      <select class="form-select mt-1 co-10" id="u_role" v-model="ven_month" placeholder="เดือน" @change="ch_sel_ven_month()">
          <option v-for="svm in months" :value="svm.ven_month" >{{svm.name}} </option>        
      </select>

      <!-- เลือกคำสั่งเวร -->
      <select class="form-select mt-1 co-10" id="select_vc" v-model="vc_index" placeholder="เลือกคำสั่งเวร" @change="ch_sel_ven_name(vc_index)">
                
          <option v-for="vc,vci in ven_coms" :value="vci" >{{vc.ven_com_num}} {{vc.name}} </option>        
      </select>

      <!-- เลือกตำแหน่ง/หน้าที่ -->
      <select class="form-select mt-1 co-10" id="vns" v-model="vns_index" placeholder="เลือกตำแหน่ง" @change="ch_sel_vns(vns_index)">
         
        <option v-for="vns,vnsi in ven_name_subs" :value="vnsi" > {{vns.name}} </option>        
      </select>

      <!-- <select class="form-select mt-1 co-10" id="u_role" v-model="ven_coms_index" placeholder="กรุณาเลือกคำสั่ง / หน้าที่" @change="sel_vem_com(ven_coms_index)">
        <option v-for="vc,index in ven_coms" :value="index" >{{vc.u_role}} -> {{vc.DN}} -> {{vc.ven_com_name}} -> {{vc.ven_com_num}} ->  {{vc.price}} </option>        
      </select> -->

    </form>
    <p>
      <strong>{{ven_month}} </strong><br>
      
    </p>
    
    <div class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event' v-for="pf,index in profiles" :data-event="pf.data_event" :data-uid="pf.uid">
      <div class='fc-event-main'>{{index + 1}} {{pf.u_name}}</div>
    </div>    
       <!-- {{profiles}} -->

  </div>
  
  <div id='calendar-container'>
     <div id='calendar' ref="calendar"></div>
     
  </div>
  <!-- {{datas}} -->
  <!-- {{profiles}} -->

  <!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop" ref="show_modal" hidden>
    Launch static backdrop modal
</button>
  <!-- Modal -->
  <div class="modal fade" id="staticBackdrop"  data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ref="close_modal" @click="close_m()"></button>
        </div>
        <div class="modal-body">
          <div>
            <table class="table table-bordered">
              <!-- <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col"></th>
                </tr>
              </thead> -->
              <tbody>
                <tr>
                  <th scope="row">id</th>
                  <td>
                    {{data_event.id}}
                    {{data_event.status == 5 ? 'ปิดการใช้งานชั่วคราว':''}}

                    <button v-if="data_event.status == 5 || data_event.status == 1" @click="ven_dis_open(data_event.id)" class="btn btn-danger">
                    {{data_event.status == 5 ? 'เปิดการใช้งาน':'ปิดการใช้งานชั่วคราว'}}
                    </button>
                  </td>
                </tr>
                <tr>
                  <th scope="row">วันที่ เวลา</th>
                  <td>{{data_event.ven_date}} เวลา {{data_event.ven_time}} น.</td>
                </tr>
                <tr>
                  <th scope="row">เบิกเงินในคำสั่ง</th>
                  <td>
                    <!-- {{data_event}} -->
                    <select class="form-select" aria-label="Default select example" v-model="data_event.ven_com_idb" v-if="data_event_ven_coms" @change.prevent="ven_save2()">
                        <option v-for="vc in data_event_ven_coms" :value="vc.vc_id" >{{' คำสั่งที่ ' + vc.ven_com_num + ' เวร ' +vc.name}}</option>
                    </select>
                    <!-- {{data_event_ven_coms}} -->
                  </td>
                </tr>
                <tr>
                  <th scope="row">คำสั่ง</th>
                  <td>
                    {{data_event.u_role}} | {{data_event.DN}} | {{data_event.ven_com_name}} | {{data_event.price}}
                  </td>
                </tr>
                <tr v-for="vc,i in data_event_ven_coms">
                  <td></td>
                  <td>
                    <input type="checkbox"  :id="i" name="ckb" :value="vc.vc_id" v-model="data_event.ven_com_id" @change.prevent="ven_save()"> 
                    <label :for="i"> {{' คำสั่งที่ ' + vc.ven_com_num + ' เวร ' +vc.name}}</label><br>
                  </td>
                </tr>
                <tr>
                  <th scope="row">ชื่อผู้อยู่</th>
                  <td>{{data_event.fname}}{{data_event.name}} {{data_event.sname}}</td>
                </tr>
                
              </tbody>
            </table>
            <!-- {{ven_coms}} -->
            <!-- {{data_event}} -->
          </div>
          <div class="row">
            <div>
              <button @click="ven_del(data_event.id)" class="btn btn-danger" :disabled='isLoading'>
                {{isLoading ? 'Londing..': 'ลบ'}}
              </button>
            </div>
            <div>
              <!-- <button @click="ven_save()" class="btn btn-primary">save</button> -->
            </div>
            <!-- <div class="float-right"> -->
              <!-- <button type="button" class="btn btn-success float-right" data-bs-dismiss="modal" aria-label="Close" ref="close_modal" @click="close_m()">ยกเลิก</button> -->
            <!-- </div> -->

          </div>
        </div>

      <!-- {{data_event}} -->
    </div>
  </div>
</div>
  <script src="../../assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="../../assets/js/bootstrap.bundle.min.js"></script>

    <script src="../../assets/js/main.js"></script>

    <script src="../../node_modules/vue/dist/vue.global.js"></script>
    <script src="../../node_modules/vue/dist/vue.global.prod.js"></script>
    <script src="../../node_modules/axios/dist/axios.js"></script>
    <script src="../../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="./js/ven_set.js"></script>
    <!-- <script src="../plugins/toastr/toastr.min.js"></script> -->
    <!-- <script src="./node_modules/fullcalendar/main.min.js"></script> -->

    
</body>
</html>
