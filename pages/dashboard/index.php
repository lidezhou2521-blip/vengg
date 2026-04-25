<?php 

require_once('../../server/authen.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />    
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>    
    <?php require_once('../includes/_header.php') ?>
    
    <link rel="stylesheet" href="../../assets/fullcalendar/main.css">
    <script src="../../assets/fullcalendar/main.min.js"></script>
  <style>
    .modalCenter{
        top:10% !important;
        /* tramsform:translateY(-25%) !important; */
    }
    /* .list-group-item-secondary{ cursor: pointer; } */
    .list-group-item-secondary:hover{ 
        cursor: pointer; 
        background: #FFEBCD;
    }
    .duty-stat-card {
        min-width: 180px;
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
    }
    .duty-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
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
                    <h3>หน้าแรก </h3>
                    <a href="./list.php" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-list-ul me-1"></i> มุมมองแบบรายการ
                    </a>
                </div>
            </div> 

                <!-- Content wrapper -->
                <div class="content-wrapper" id="dashboard">
                    
                    <!-- ========================= preloader start ========================= -->
                    <div class="preloader"  ref="loading" v-if="isLoading">
                        <div class="loader">
                            <div class="spinner">
                                <div class="spinner-container">
                                    <div class="spinner-rotator">
                                        <div class="spinner-left">
                                            <div class="spinner-circle"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text">
                                กำลังประมวลผล...
                            </div>
                        </div>                        
                    </div>
                    <!-- preloader end -->

                    <!-- {{ssid}} -->
                    <div class="container-xxl flex-grow-1 container-p-y" >                        
                        


                        <div class="row">
                            <div class="col-12 ">
                                <div class="card shadow-lg border-0">
                                    <div class="card-body">
                                        <div id='calendar' ref="calendar"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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
                                                <img v-else :src="data_event.img" class="img-fluid rounded-start" alt="data_event.img">
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
                                                <!-- <button type="button" class="btn btn-primary" :disabled="!(my_v.length > 0) || (d_now > data_event.ven_date)" @click="my_v_show = true">
                                                    ขอเปลี่ยน
                                                </button> -->
                                            </div>
                                        </div>
                                    </div>
                                    <ul class="list-group mt-3" >
                                        <li class="list-group-item list-group-item-primary" v-for="v,vi in vh">                                           
                                            {{v.id}} | {{v.u_name}} 
                                            <!-- <span class="badge bg-warning" v-if="data_event.status == 2">รออนุมัติ {{data_event.status}}</span>  -->
                                            
                                            <!-- {{vh}} -->
                                        </li>
                                    </ul>
                                    
                                    <div class="list-group mt-3" v-if="data_event.user_id == ssid && (data_event.ven_date >= d_now) && (data_event.status == 1)" >
                                        <button class="btn btn-warning" @click="ch_b == true ? ch_b = flase : ch_b = true">ยกให้ </button>  
                                    </div>
                                    <div class="list-group mt-3" v-if="my_v.length > 0 && !(data_event.user_id == ssid) && (data_event.ven_date >= d_now) && data_event.status == 1" >
                                        <button class="btn btn-primary" @click="ch_a == true ? ch_a = false : ch_a = true ">ขอเปลี่ยน</button>  
                                    </div>
                                    <ul class="list-group mt-3" v-if="ch_a" >
                                        <li class="list-group-item active" aria-current="true">เวรที่สามารถเปลี่ยนได้</li>  
                                        <li class="list-group-item list-group-item-secondary" v-for="m,mi in my_v" @click="change_a(mi)">                                           
                                            {{m.ven_date_th}}  | {{m.u_name}} | {{m.u_role}} <br> {{m.ven_com_name}} <br> {{m.DN}} | {{m.id}}
                                            
                                        </li>                                        
                                    </ul>
                                    <ul class="list-group mt-3" v-if="ch_b">
                                        <li v-if="users.length > 0"class="list-group-item active" aria-current="true">ยกให้</li>  
                                        <li v-else class="list-group-item" aria-current="true">ไม่พบข้อมูล.</li>  
                                        <div v-for="u in users" >
                                            <li class="list-group-item list-group-item-secondary" v-if="u.user_id != ssid"  @click="change_b(u.user_id,u.u_name,u.img)">                                           
                                                <span > {{u.u_name}}  </span>
                                                <!-- {{u.user_id +' '+ ' '+ssid}} -->
                                            </li>
                                        </div>
                                    </ul>
                                </div>



                                    <!-- {{d_now}} -->
                                    <!-- {{data_event}} -->
                                    <!-- {{my_v ? my_v.length :''}} -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalB" ref="show_modal_b" hidden>
                        Launch static backdrop modalB
                    </button>
  
                    <!-- Modal -->
                    <div class="modal fade" id="modalB" data-bs-keyboard="false" data-bs-backdrop="static"  tabindex="-2" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modalCenter">
                            <div class="modal-content">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title" id="staticBackdropLabel">  </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="close_m_b" ref="close_modal_b"></button>
                                </div>
                                <!-- {{my_v}} -->
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-5">
                                            <div class="card">
                                                <img :src="ch_v1.img" class="img-fluid rounded-start" alt="ch_v1.img">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ch_v1.u_name}}</h5>
                                                    <p class="card-text">
                                                        {{ch_v1.ven_date_th}} ({{ch_v1.ven_time}})<br>
                                                        {{ch_v1.DN}}<br>
                                                        {{ch_v1.ven_com_num_all}}<br>
                                                        {{ch_v1.ven_name}}<br>
                                                        {{ch_v1.u_role}}
                                                        {{ch_v1.price}}
                                                        <!-- {{ch_v1}} -->
                                                    </p>
                                                   
                                                </div>
                                            </div>                                            
                                        </div>
                                        <div class="col-2 text-center">
                                            <div class="mt-5" v-if="act=='a'">
                                            <<<  >>>
                                            </div>
                                            <div class="mt-5" v-if="act=='b'">
                                            ---  >>>
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="card" v-if="act=='a'">
                                                <img :src="ch_v2.img" class="img-fluid rounded-start" alt="ch_v2.img">
                                                <div class="card-body" >
                                                    <h5 class="card-title">{{ch_v2.u_name}}</h5>
                                                    <p class="card-text">
                                                        {{ch_v2.ven_date_th}} ({{ch_v2.ven_time}})<br>
                                                        {{ch_v2.DN}}<br>                                                        
                                                        {{ch_v1.ven_com_num_all}}<br>
                                                        {{ch_v2.ven_name}}<br>
                                                        {{ch_v2.u_role}}
                                                        {{ch_v2.price}}
                                                    </p>                                                    
                                                </div>
                                            </div>
                                            <div class="card" v-if="act=='b'">
                                                <!-- <img v-if="u_img2 == '' || u_img2 == null" src="../../assets/images/profiles/nopic.png" class="img-fluid rounded-start" alt="data_event.img"> -->
                                                <img :src="u_img2" class="img-fluid rounded-start" alt="u_img2">
                                                
                                                <div class="card-body">
                                                    <h5 class="card-title">{{u_name2}}</h5>
                                                    <p class="card-text">
                                                        {{ch_v1.ven_date_th}} ({{ch_v1.ven_time}})<br>
                                                        {{ch_v1.DN}}<br>
                                                        {{ch_v1.ven_com_num_all}}<br>
                                                        {{ch_v1.ven_name}}<br>
                                                        {{ch_v1.u_role}}
                                                        {{ch_v1.price}}
                                                        <!-- {{ch_v1}} -->
                                                    </p>                                                    
                                                </div>
                                                
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <button class="btn btn-primary" @click="change_save()" :disabled="isLoading" v-if="act=='a'">
                                            {{isLoading ? 'Loading..':'ยืนยันการเปลี่ยน'}}
                                        </button> 
                                        <button class="btn btn-warning" @click="change_save_bb()" :disabled="isLoading" v-if="act=='b'">
                                            {{isLoading ? 'Loading..':'ยืนยันการยก'}}
                                        </button> 
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
    
                    <div class="content-backdrop fade"></div>

                </div>
                    
    

                <?php require_once('../includes/_footer.php') ?>
        </div>
        

    </div>

    <?php require_once('../includes/_footer_sc.php') ?>
    
    <!-- <script src="../../assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script> -->
    <!-- <script src="../../assets/js/bootstrap.bundle.min.js"></script> -->

    <!-- <script src="../../assets/js/main.js"></script> -->
    <!--  -->
    <script src="../../node_modules/vue/dist/vue.global.js"></script>
    <script src="../../node_modules/axios/dist/axios.js"></script>
    <script src="../../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="./index.js"></script>
</body>
</html>

