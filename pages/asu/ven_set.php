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

<?php require_once('../includes/_header.php') ?>

<link rel="stylesheet" href="../../assets/fullcalendar/main.min.css">
<script src="../../assets/fullcalendar/main.min.js"></script>

  <script>

  document.addEventListener('DOMContentLoaded', function() {
    var Calendar = FullCalendar.Calendar;
    var Draggable = FullCalendar.Draggable;

    var containerEl = document.getElementById('external-events-list');
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

});

</script>
<style>
  html, body {
    margin: 0;
    padding: 0;
    font-family: 'Prompt', sans-serif;
    background-color: #f8fafc;
    font-size: 14px;
  }

  #external-events {
    position: fixed;
    z-index: 10;
    top: 0;
    left: 0;
    width: 240px;
    height: 100vh;
    padding: 25px 15px;
    background: #ffffff;
    border-right: 1px solid #edf2f7;
    box-shadow: 5px 0 25px rgba(0,0,0,0.03);
    overflow-y: auto;
  }

  #external-events h5 {
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 20px;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
  }

  #external-events select {
    margin-bottom: 12px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    padding: 10px;
    font-size: 0.9rem;
    background-color: #f8fafc;
    color: #2d3748;
    transition: all 0.2s;
  }

  #external-events select:focus {
    border-color: #435ebe;
    box-shadow: 0 0 0 3px rgba(67, 94, 190, 0.1);
    outline: none;
  }

  .sidebar-section-title {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #a0aec0;
    font-weight: 700;
    margin: 15px 0 8px 5px;
  }

  #external-events .fc-event,
  #external-events .fc-event-main {
    color: #2d3748 !important;
  }

  #external-events .fc-event {
    cursor: move;
    margin: 8px 0 !important;
    padding: 12px !important;
    border-radius: 10px !important;
    border: none !important;
    background: #ffffff !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04) !important;
    border-left: 4px solid #435ebe !important;
    font-weight: 600 !important;
    transition: all 0.2s !important;
  }

  #external-events .fc-event:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
    background: #f1f5f9 !important;
  }

  #external-events2 {
    position: fixed;
    z-index: 5;
    top: 0;
    left: 240px;
    right: 0;
    height: 60px;
    background: #ffffff;
    border-bottom: 1px solid #edf2f7;
    display: flex;
    align-items: center;
    padding: 0 30px;
    font-weight: 600;
    color: #4a5568;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    transition: all 0.3s ease;
  }

  #calendar-container {
    position: relative;
    z-index: 1;
    margin-left: 240px;
    padding: 80px 30px 30px 30px;
    transition: all 0.3s ease;
  }
  
  #calendar {
    max-width: 100%;
    background: #ffffff;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.04);
  }

  /* Custom Scrollbar for Sidebar */
  #external-events::-webkit-scrollbar { width: 4px; }
  #external-events::-webkit-scrollbar-track { background: transparent; }
  #external-events::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
</style>
</head>
<body>
<div id="venSet" v-cloak>
  <div class="clearfix" ref="loading" v-if="isLoading">
    <strong>Loading...</strong>
    <div class="spinner-border float-end" role="status" aria-hidden="true"></div>
  </div>
  
  <div id='external-events2' :style="{ left: show_sidebar ? '240px' : '0' }">
    <button @click="show_sidebar = !show_sidebar" class="btn btn-sm btn-outline-primary me-3">
        <i class="bi" :class="show_sidebar ? 'bi-chevron-left' : 'bi-chevron-right'"></i>
    </button>
    <i class="bi bi-calendar-event me-2 text-primary"></i>
    <span class="me-3">{{ven_month}}</span>
    <span class="badge bg-light text-primary border me-3">คำสั่งที่ {{ven_com.ven_com_num}}</span>
    <span class="text-muted small">{{ven_com.name}}</span>
    <span class="ms-auto badge bg-primary rounded-pill px-3">{{ven_name_sub.name}}</span>
  </div>

  <div id='external-events' v-show="show_sidebar">
    <h5><i class="bi bi-person-plus-fill me-2 text-primary"></i> จัดการเวร</h5>
    
    <form>
      <div class="sidebar-section-title">1. เลือกเดือน</div>
      <select class="form-select" v-model="ven_month" @change="ch_sel_ven_month()">
          <option v-for="svm in months" :value="svm.ven_month">{{svm.name}}</option>        
      </select>

      <div class="sidebar-section-title">2. เลือกคำสั่งเวร</div>
      <select class="form-select" v-model="vc_index" @change="ch_sel_ven_name(vc_index)">
          <option v-for="(vc,vci) in ven_coms" :value="vci">{{vc.ven_com_num}} {{vc.name}}</option>        
      </select>

      <div class="sidebar-section-title">3. เลือกตำแหน่ง/หน้าที่</div>
      <select class="form-select" v-model="vns_index" @change="ch_sel_vns(vns_index)">
          <option v-for="(vns,vnsi) in ven_name_subs" :value="vnsi">{{vns.name}}</option>        
      </select>
    </form>

    <div class="sidebar-section-title mt-4 d-flex justify-content-between align-items-center">
        <span>รายชื่อเจ้าหน้าที่ ({{profiles_filtered.length}})</span>
    </div>
    
    <div class="mt-2 mb-3">
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-filter text-muted"></i></span>
            <input type="text" v-model="qp" class="form-control border-start-0 ps-0" placeholder="กรองชื่อในรายการ...">
        </div>
    </div>
    
    <div id="external-events-list">
        <div class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event shadow-sm' 
             v-for="pf in profiles_filtered" 
             :key="pf.uid"
             :data-event="JSON.stringify(pf.data_event)" 
             :data-uid="pf.uid">
          <div class='fc-event-main'>
            <i class="bi bi-grip-vertical me-1 opacity-50"></i>
            {{pf.u_name}}
          </div>
        </div>
    </div>
    
    <div v-if="profiles_filtered.length === 0" class="text-center py-5 text-muted small opacity-50">
      <i class="bi bi-person-dash fs-2 d-block mb-2"></i>
      ไม่พบข้อมูลเจ้าหน้าที่
    </div>
  </div>
  
  <div id='calendar-container' :style="{ marginLeft: show_sidebar ? '240px' : '0' }">
     <!-- Search and Legend -->
     <div class="card mb-3 shadow-sm border-0">
         <div class="card-body">
             <div class="row g-3 align-items-center">
                 <div class="col-md-12">
                     <div class="input-group">
                         <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-primary"></i></span>
                         <input type="text" v-model="search_query" class="form-control border-start-0" placeholder="ค้นหาชื่อ, ตำแหน่ง, ประเภทเวรในปฏิทิน...">
                     </div>
                 </div>
             </div>

             <!-- Duty Types Legend -->
             <div class="mt-3" v-if="dutyStats.length > 0">
                 <div class="d-flex flex-wrap gap-2">
                     <div v-for="type in dutyStats" :key="type.key" 
                          @click="toggleType(type.key)"
                          class="px-3 py-1 rounded-pill border d-flex align-items-center shadow-sm cursor-pointer"
                          :style="{ 
                              borderLeft: '4px solid ' + type.color, 
                              backgroundColor: type.active ? '#f8fafc' : '#ffffff',
                              opacity: type.active ? 1 : 0.4,
                              filter: type.active ? 'none' : 'grayscale(0.5)',
                              cursor: 'pointer',
                              transition: 'all 0.2s',
                              fontSize: '0.85rem'
                          }">
                         <div class="fw-bold me-2">{{ type.name }}</div>
                         <div class="badge bg-light text-dark border">{{ type.count }}</div>
                     </div>
                 </div>
             </div>
         </div>
     </div>

     <div id='calendar' ref="calendar"></div>
     
  </div>

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
                    <select class="form-select" aria-label="Default select example" v-model="data_event.ven_com_idb" v-if="data_event_ven_coms" @change.prevent="ven_save2()">
                        <option v-for="vc in data_event_ven_coms" :value="vc.vc_id" >{{' คำสั่งที่ ' + vc.ven_com_num + ' เวร ' +vc.name}}</option>
                    </select>
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
          </div>
          <div class="row">
            <div>
              <button @click="ven_del(data_event.id)" class="btn btn-danger" :disabled='isLoading'>
                {{isLoading ? 'Londing..': 'ลบ'}}
              </button>
            </div>
          </div>
        </div>

    </div>
  </div>
</div>
  <script src="../../assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="../../assets/js/bootstrap.bundle.min.js"></script>

    <script src="../../assets/js/main.js"></script>

    <script src="../../node_modules/vue/dist/vue.global.js"></script>
    <script src="../../node_modules/axios/dist/axios.js"></script>
    <script src="../../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="./js/ven_set.js?v=<?php echo time(); ?>"></script>
    
</body>
</html>
