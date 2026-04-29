<?php 

require_once('../../server/authen.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('../includes/_header.php') ?>
    <style>
        .duty-item {
            transition: all 0.2s;
            border-left: 5px solid transparent;
        }
        .duty-item:hover {
            background-color: #f8fafc;
            transform: translateX(5px);
        }
        .date-badge {
            min-width: 100px;
            text-align: center;
        }
        .status-badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }
        /* Match dashboard search style */
        .search-box .input-group {
            transition: all 0.3s ease;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid transparent !important;
        }
        .search-box .input-group:focus-within {
            box-shadow: 0 0 0 0.25rem rgba(67, 94, 190, 0.25);
            border-color: #435ebe !important;
            background: #fff !important;
        }
        .search-box input:focus {
            background: #fff !important;
        }
        .avatar.avatar-md .avatar-content {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        .modalCenter {
            top: 10% !important;
        }
    </style>
</head>
<body class="theme-dark">
    <div id="app">
        <?php require_once('../includes/_sidebar.php') ?>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <div class="d-flex justify-content-between align-items-center">
                    <h3>รายการเวร (มุมมองแบบรายการ)</h3>
                    <a href="./index.php" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-calendar3 me-1"></i> สลับเป็นมุมมองปฏิทิน
                    </a>
                </div>
            </div> 

            <div class="content-wrapper" id="list-view">
                <!-- Preloader -->
                <div class="preloader" v-if="isLoading">
                    <div class="loader">
                        <div class="spinner"></div>
                        <div class="text">กำลังโหลดข้อมูล...</div>
                    </div>
                </div>

                <div class="container-fluid">
                    <!-- Filters & Search (same layout as dashboard/index.php) -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <!-- Row 1: Search + เวรของฉัน (matches dashboard) -->
                            <div class="row align-items-center g-3">
                                <div class="col-md-9 search-box">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-primary"></i></span>
                                        <input type="text" v-model="search" class="form-control border-start-0" placeholder="ค้นหาชื่อ, ตำแหน่ง, ประเภทเวร...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button @click="filterMyDuty = !filterMyDuty" 
                                            class="btn w-100" 
                                            :class="filterMyDuty ? 'btn-primary' : 'btn-outline-primary'">
                                        <i class="bi" :class="filterMyDuty ? 'bi-person-check-fill' : 'bi-person-circle'"></i>
                                        เวรของฉัน
                                    </button>
                                </div>
                            </div>

                            <!-- Row 2: Month/Year filter (compact, list-view specific) -->
                            <div class="row g-2 mt-2">
                                <div class="col-md-3">
                                    <select v-model="filter_month" class="form-select form-select-sm">
                                        <option value="">ทุกเดือน</option>
                                        <option v-for="(m, i) in months" :key="i" :value="i + 1">{{ m }}</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select v-model="filter_year" class="form-select form-select-sm">
                                        <option value="">ทุกปี</option>
                                        <option v-for="y in years" :key="y" :value="y">{{ y + 543 }}</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Duty Types Legend with Colors -->
                            <div class="mt-4" v-if="dutyStats.length > 0">
                                <h6 class="mb-3 text-muted small"><i class="bi bi-tag-fill me-2"></i>เลือกแสดงตามชื่อเวร:</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    <div v-for="type in dutyStats" :key="type.key" 
                                         @click="toggleType(type.key)"
                                         class="px-3 py-2 rounded-3 border d-flex align-items-center shadow-sm"
                                         :style="{ 
                                             borderLeft: '5px solid ' + type.color, 
                                             backgroundColor: type.active ? '#f8fafc' : '#ffffff',
                                             opacity: type.active ? 1 : 0.4,
                                             filter: type.active ? 'none' : 'grayscale(0.5)',
                                             cursor: 'pointer',
                                             transition: 'all 0.2s'
                                         }">
                                        <div class="me-3">
                                            <div class="fw-bold small">{{ type.name }}</div>
                                        </div>
                                        <div class="ms-auto d-flex align-items-center">
                                            <div class="fw-bold me-2" :class="type.active ? 'text-primary' : 'text-muted'">{{ type.count }}</div>
                                            <i class="bi" :class="type.active ? 'bi-check-circle-fill text-success' : 'bi-circle text-muted'"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- List Content -->
                    <div v-if="groupedEvents.length > 0">
                        <div v-for="group in groupedEvents" :key="group.date" class="mb-5">
                            <div class="d-flex align-items-center mb-3">
                                <h4 class="mb-0 text-primary fw-bold">{{ group.dateText }}</h4>
                                <hr class="flex-grow-1 ms-3 opacity-25">
                            </div>
                            
                            <div v-for="dg in group.dutyGroups" :key="dg.color + dg.name" class="mb-4 ps-md-4">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge rounded-pill me-2" :style="{ backgroundColor: dg.color, width: '12px', height: '12px', padding: 0 }">&nbsp;</span>
                                    <h6 class="mb-0 text-secondary">{{ dg.name }}</h6>
                                </div>
                                <div class="row row-cols-1 row-cols-lg-2 g-3">
                                    <div v-for="event in dg.events" :key="event.id" class="col" @click="showDetail(event.id)">
                                        <div class="card h-100 shadow-sm duty-item border-0" :style="{ borderLeft: '5px solid ' + event.backgroundColor, cursor: 'pointer' }">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-md me-3">
                                                            <div v-if="!event.extendedProps.img || event.extendedProps.img.includes('1.jpg')" 
                                                                 class="avatar-content bg-primary bg-opacity-10 text-primary shadow-sm">
                                                                <i class="bi bi-person-circle fs-4"></i>
                                                            </div>
                                                            <img v-else :src="event.extendedProps.img" alt="Face" class="shadow-sm">
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ event.extendedProps.u_name }}</h6>
                                                            <small class="text-muted">{{ event.extendedProps.u_role }}</small>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge" :class="event.extendedProps.DN == 'กลางคืน' ? 'bg-dark' : 'bg-warning text-dark'">
                                                            {{ event.extendedProps.DN == 'กลางคืน' ? '🌙 กลางคืน' : '☀️ กลางวัน' }}
                                                         </span>
                                                         <div v-if="event.extendedProps.user_id == currentUserId" class="mt-1">
                                                             <span class="badge bg-info text-dark small">
                                                                 <i class="bi bi-person-check-fill me-1"></i>เวรของฉัน
                                                             </span>
                                                         </div>
                                                         <div class="small text-muted mt-1">{{ event.extendedProps.ven_time }}</div>
                                                     </div>
                                                </div>
                                                <div v-if="event.comment" class="bg-light p-2 rounded-2 mt-2">
                                                    <div class="small text-danger">
                                                        <i class="bi bi-exclamation-circle me-1"></i>{{ event.comment }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-5">
                        <i class="bi bi-clipboard-x fs-1 text-muted"></i>
                        <p class="mt-3 text-muted">ไม่พบข้อมูลเวรที่ตรงกับเงื่อนไข</p>
                    </div>
                </div>
            <!-- Modal Section (from index.php) -->
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop" ref="show_modal" hidden>
                Launch static backdrop modal
            </button>

            <!-- Modal -->
            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel"> {{data_event.id}} 
                                <span class="badge bg-warning" v-if="data_event.status ==2">รออนุมัติ</span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="close_m" ref="close_modal"></button>
                        </div>
                        <div class="modal-body">

                            <div class="card mb-3" style="max-width: 540px;">
                                <div class="row g-0">
                                    <div class="col-md-4">
                                        <img :src="data_event.img" class="img-fluid rounded-start" alt="data_event.img">
                                    </div>
                                    <div class="col-md-8">
                                    <div class="card-body">
                                        <h4 class="card-title">{{data_event.u_name}}</h4>
                                        <h6>
                                            <span class="badge bg-secondary me-2">{{data_event.u_role}} </span>
                                            <span class="badge bg-warning" v-if="data_event.status ==2">รออนุมัติ</span>
                                        </h6>
                                        <p class="card-text">
                                            {{data_event.ven_date_th}} ({{data_event.ven_time}})<br>
                                            {{data_event.DN}} {{data_event.ven_com_name}} <br>
                                            {{data_event.ven_com_num_all ? 'คำสั่งที่ '+data_event.ven_com_num_all: ''}} 
                                            <span class="badge bg-info text-dark">{{data_event.price ? data_event.price : ''}}</span>
                                            <button v-if="data_event.DN == 'กลางคืน'" @click="report_jk(data_event.ven_date,data_event.DN)"  class="btn btn-success btn-sm mt-2">รายงานเวรหมายจับหมายค้น</button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <ul class="list-group mt-3" >
                                <li class="list-group-item list-group-item-primary" v-for="v,vi in vh">                                           
                                    {{v.id}} | {{v.u_name}} 
                                </li>
                            </ul>
                            
                            <div class="list-group mt-3" v-if="data_event.user_id == ssid && (data_event.ven_date >= d_now) && (data_event.status == 1)" >
                                <button class="btn btn-warning" @click="ch_b == true ? ch_b = false : ch_b = true">ยกให้ </button>  
                            </div>
                            <div class="list-group mt-3" v-if="my_v.length > 0 && !(data_event.user_id == ssid) && (data_event.ven_date >= d_now) && data_event.status == 1" >
                                <button class="btn btn-primary" @click="ch_a == true ? ch_a = false : ch_a = true ">ขอเปลี่ยน</button>  
                            </div>
                            <ul class="list-group mt-3" v-if="ch_a" >
                                <li class="list-group-item active" aria-current="true">เวรที่สามารถเปลี่ยนได้</li>  
                                <li class="list-group-item list-group-item-secondary" v-for="m,mi in my_v" @click="change_a(mi)" style="cursor: pointer;">                                           
                                    {{m.ven_date_th}}  | {{m.u_name}} | {{m.u_role}} <br> {{m.ven_com_name}} <br> {{m.DN}} | {{m.id}}
                                </li>                                        
                            </ul>
                            <ul class="list-group mt-3" v-if="ch_b">
                                <li v-if="users.length > 0"class="list-group-item active" aria-current="true">ยกให้</li>  
                                <li v-else class="list-group-item" aria-current="true">ไม่พบข้อมูล.</li>  
                                <div v-for="u in users" >
                                    <li class="list-group-item list-group-item-secondary" v-if="u.user_id != ssid"  @click="change_b(u.user_id,u.u_name,u.img)" style="cursor: pointer;">                                           
                                        <span > {{u.u_name}}  </span>
                                    </li>
                                </div>
                            </ul>
                        </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal B -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalB" ref="show_modal_b" hidden>
                Launch static backdrop modalB
            </button>

            <div class="modal fade" id="modalB" data-bs-keyboard="false" data-bs-backdrop="static"  tabindex="-2" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modalCenter">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title" id="staticBackdropLabel"> ยืนยันการดำเนินการ </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="close_m_b" ref="close_modal_b"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row text-center">
                                <div class="col-5">
                                    <div class="card">
                                        <img :src="ch_v1.img" class="img-fluid rounded-start" alt="ch_v1.img">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ch_v1.u_name}}</h5>
                                            <p class="card-text small">
                                                {{ch_v1.ven_date_th}}<br>
                                                {{ch_v1.DN}}<br>
                                                {{ch_v1.ven_name}}
                                            </p>
                                        </div>
                                    </div>                                            
                                </div>
                                <div class="col-2 d-flex align-items-center justify-content-center">
                                    <div v-if="act=='a'" class="fs-2 text-primary">
                                        <i class="bi bi-arrow-left-right"></i>
                                    </div>
                                    <div v-if="act=='b'" class="fs-2 text-warning">
                                        <i class="bi bi-arrow-right"></i>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <div class="card" v-if="act=='a'">
                                        <img :src="ch_v2.img" class="img-fluid rounded-start" alt="ch_v2.img">
                                        <div class="card-body" >
                                            <h5 class="card-title">{{ch_v2.u_name}}</h5>
                                            <p class="card-text small">
                                                {{ch_v2.ven_date_th}}<br>
                                                {{ch_v2.DN}}<br>
                                                {{ch_v2.ven_name}}
                                            </p>                                                    
                                        </div>
                                    </div>
                                    <div class="card" v-if="act=='b'">
                                        <img :src="u_img2" class="img-fluid rounded-start" alt="u_img2">
                                        <div class="card-body">
                                            <h5 class="card-title">{{u_name2}}</h5>
                                            <p class="card-text small">
                                                (รับเวรต่อ)
                                            </p>                                                    
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3 px-3">
                                <button class="btn btn-primary" @click="change_save()" :disabled="isLoading" v-if="act=='a'">
                                    {{isLoading ? 'กำลังโหลด..':'ยืนยันการเปลี่ยนเวร'}}
                                </button> 
                                <button class="btn btn-warning" @click="change_save_bb()" :disabled="isLoading" v-if="act=='b'">
                                    {{isLoading ? 'กำลังโหลด..':'ยืนยันการยกเวรให้'}}
                                </button> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once('../includes/_footer.php') ?>
        </div>
    </div>

    <?php require_once('../includes/_footer_sc.php') ?>
    <script src="../../node_modules/vue/dist/vue.global.js"></script>
    <script src="../../node_modules/axios/dist/axios.js"></script>
    <script src="../../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="./list.js?v=<?php echo time(); ?>"></script>
</body>
</html>
