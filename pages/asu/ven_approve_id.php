<?php 

require_once('../../server/authen.php'); 

?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require_once('../includes/_header.php') ?>

</head>
<body>
<div id="app">
        <?php require_once('../includes/_sidebar.php') ?>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <h3>ใบเปลี่ยนเวร</h3>
            </div>
            
            <div class="page-content" id="venApp" v-cloak> 
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <div class="row ">
                        <div class="col-md-10 mx-auto">
                            <div class="input-group">
                                <input class="form-control border rounded-pill" type="search" value="search" v-model="q" placeholder="ค้นหา" id="example-search-input">
                            </div>
                        </div>
                    </div>                    
                </div>
                {{!datas || datas.length == 0 ? 'NO-DATA':''}}
                    <div class="container-xxl flex-grow-1 container-p-y" v-if="datas.length > 0" >  
                        <!-- {{datas}}                       -->
                        <div class="row" v-for="d in datas">

                            <!-- <div class="row"> -->
                                <div class="card">
                                    <div class="card-body">
                                        <h4>{{d.id}}  
                                        <span class="badge bg-warning" v-if="d.status ==2">รออนุมัติ</span>
                                        </h4>
                                    <div class="row">
                                        <div class="col-5">
                                            <div class="card">
                                                <img :src="d.img1" class="img-thumbnail rounded-start" alt="data_event.img" height="100" >
                                                <div class="card-body">
                                                    <h5 class="card-title">{{d.user1}}</h5>
                                                    <p class="card-text">
                                                        {{date_thai(d.ven_date1)}}<br>
                                                        คำสั่งที่ {{d.ven_com_num_all}} เวรเดือน {{date_thai_my(d.ven_month)}}  <br> 
                                                        {{d.DN}} | {{d.u_role}} {{d.price}}                                                        
                                                    </p>
                                                    <!-- {{d.img1}}  -->
                                                </div>
                                            </div>                                            
                                        </div>
                                        <div class="col-2 text-center">
                                            <div class="mt-5">
                                            <<<  >>>
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="card">
                                                <img :src="d.img2" class="img-thumbnail rounded-start" alt="data_event.img" height="100">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{d.user2}}</h5>
                                                    <p class="card-text">
                                                    {{date_thai(d.ven_date2)}}<br>
                                                    คำสั่งที่ {{d.ven_com_num_all}} เวรเดือน {{date_thai_my(d.ven_month)}}  <br> 
                                                        {{d.DN}} | {{d.u_role}} {{d.price}}
                                                    </p>
                                                    <!-- {{d.img2}} -->
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div>
                                            <button class="btn btn-primary me-2" @click="print(d.id)" :disabled="isLoading">{{isLoading ? 'loading..' : 'บันทึกเปลี่ยนเวร'}}</button>
                                            <button class="btn btn-warning me-2" @click="print_2(d.id)" :disabled="isLoading">{{isLoading ? 'loading..' : 'ใบเปลี่ยนเวร'}}</button>

                                        </div>
                                            
                                        <div class="col text-center" v-if="d.status == 2">
                                            
                                            <button class="btn btn-warning btn-sm me-2" @click="ven_ch_app(d.id)" v-if="d.status != 1">อนุมัติ</button>
                                            <button class="btn btn-danger btn-sm" @click="ven_ch_cancle(d.id)" v-else>ยกเลิก</button>
                                        </div>
                                        <div class="col text-center" v-else>
                                            สถานะ อนุมัติแล้ว
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            <!-- </div> -->
                            

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
                                    <h5 class="modal-title" id="staticBackdropLabel"> </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ref="close_modal"></button>
                                </div>
                                <div class="modal-body">

                                    <div class="card mb-3" style="max-width: 540px;">
                                        <div class="row g-0">
                                            <div class="col-md-4">
                                                <img :src="'../../assets/images/profiles/nopic.png'" class="img-fluid rounded-start" alt="data_event.img">
                                                <!-- <img :src="'../../assets/images/profiles/'+data_event.img" class="img-fluid rounded-start" alt="data_event.img"> -->
                                            </div>
                                            <div class="col-md-8">
                                            <div class="card-body">
                                                <h4 class="card-title"></h4>
                                                <h6><span class="badge bg-secondary"> </span></h6>
                                                <p class="card-text">
                                                    
                                                    
                                                </p>
                                                <!-- <button type="button" class="btn btn-primary" :disabled="!(my_v.length > 0) || (d_now > data_event.ven_date)" @click="my_v_show = true">
                                                    ขอเปลี่ยน
                                                </button> -->
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>



                                    <!-- {{d_now}} -->
                                    <!-- {{data_event}} -->
                                    <!-- {{my_v ? my_v.length :''}} -->
                                </div>
                            </div>
                        </div>
                    </div>

    
                    
                    <div class="content-backdrop fade"></div>

                <!-- Modal venUser Form -->
                <!-- <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#ven_Ch" ref="show_va_form" hidden >
                        เพิ่มคำสั่ง
                </button> -->
                <!-- Modal venUser Form -->
                <!-- <div class="modal fade" id="ven_Ch" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel" > </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ref="close_va"></button>
                            </div>
                            <div class="modal-body">
                                
                                
                            </div>
                        </div>
                    </div>
                </div> -->


            </div>

            <?php require_once('../includes/_footer.php') ?>
        </div>
    </div>
    <?php require_once('../includes/_footer_sc.php') ?>

    <!-- <script src="../../assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script> -->
    <!--  -->
    <script src="../../node_modules/vue/dist/vue.global.js"></script>
    <script src="../../node_modules/vue/dist/vue.global.prod.js"></script>
    <script src="../../node_modules/axios/dist/axios.js"></script>
    <script src="../../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="./js/ven_approve_id.js"></script>
</body>

</html>