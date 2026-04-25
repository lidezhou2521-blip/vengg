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
                <h3>คำสั่ง</h3>
            </div>
            
            <div class="page-content" id="venCom" v-cloak> 
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
                
                <section class="row" >           
                    <div class="col-12 col-lg-12">
                        <!-- {{ven_coms}} -->
                        <!-- {{ven_coms_g}} -->
                        <div class="row">
                            <div class="col-12 text-end mb-2">
                                <button class="btn btn-success btn-sm" @click="ven_com_add()">เพิ่มคำสั่ง</button>
                            </div>
                            <div class="col col-12" v-if="ven_coms_g">                                
                                <div class="card" v-for='cvg in ven_coms_g'>
                                    <div class="card-body" >
                                        
                                        <table class="table" v-if="ven_coms.length > 0">
                                            <thead>
                                                <tr>
                                                    <th colspan="3" class="text-start">
                                                    เวรเดือน {{cvg.ven_month_th}} 
                                                    </th>

                                                </tr>
                                            </thead>
                                            <tbody  v-for="vc in ven_coms">
                                                <tr v-if="vc.ven_month == cvg.ven_month">
                                                        <td >  
                                                            เลขคำสั่งที่ {{vc.ven_com_num}} | ลงวันที่ {{vc.ven_com_date_th}} | {{vc.ven_com_name}} ({{vc.ven_name}})
                                                            <!-- | {{vc.ref}} | {{vc.status}}  -->
    
                                                        </td>
                                                        <td class="text-end" style="width: 50px;">
                                                            <div class="form-check form-switch" v-if="vc.status == 1" >
                                                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" @click="vc_status(vc.id,77)" checked>
                                                            </div>
                                                            <div class="form-check form-switch" v-else>
                                                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" @click="vc_status(vc.id,1)" >
                                                            </div>
                                                        </td>
                                                        <td class="text-end col " style="width: 120px;">
                                                            <button class="btn btn-warning btn-sm me-2" @click="ven_com_up(vc.id)">แก้ไข</button>
                                                            <button class="btn btn-danger btn-sm" @click="ven_com_del(vc.id)">ลบ</button>
                                                        </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>                           
                        </div>    
                    </div>
                 
                </section>

                <!-- Modal venUser Form -->
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#ven_com" ref="show_vc_form" hidden >
                        เพิ่มคำสั่ง
                </button>
                <!-- Modal venUser Form -->
                <div class="modal fade" id="ven_com" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel" >{{vc_form_act == 'insert' ? 'เพิ่มคำสั่ง' : 'แก้ไขคำสั่ง'}}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="clear_vc_form" ref="close_vc"></button>
                            </div>
                            <div class="modal-body">
                                <!-- {{vc_form}} -->
                                <!-- {{vc_form_act}} -->
                                <form @submit.prevent="vc_save">                                    
                                    <div class="row mb-3">                                        
                                        <div class="col mb-3">
                                            <label for="srt" class="form-label">เลขคำสั่ง</label>
                                            <input type="text" class="form-control" id="srt" v-model="vc_form.ven_com_num">
                                        </div>
                                        <div class="col mb-3">
                                            <label for="ven_com_date" class="form-label">ลงวันที่</label>
                                            <input type="date" class="form-control" id="ven_com_date" v-model="vc_form.ven_com_date">
                                        </div>
                                        <div class="col mb-3">
                                            <label for="ven_month33" class="form-label">เวรเดือน</label>
                                            <select class="form-select" aria-label="Default select example" v-model="vc_form.ven_month" >
                                                <option v-for="svm in sel_ven_month" :value="svm.ven_month" >{{svm.name}}</option>
                                            </select>
                                        </div>                                        
                                    </div>
                                    <div class="row mb-3">
                                        <!-- <div class="col ">
                                            <label for="ven_com_name" class="form-label">ชื่อเวรเต็ม</label>
                                            <input type="text" class="form-control" id="ven_com_name" v-model="vc_form.ven_com_name">
                                        </div> -->
                                        <div class="col-12 ">
                                            <label for="ven_name" class="form-label">ชื่อเวร</label>
                                            <!-- <input type="text" class="form-control" id="ven_name" v-model="vc_form.ven_name"> -->
                                            <select class="form-select" aria-label="Default select example" v-model="vc_form.vn_id" >
                                                <option v-for="vn in ven_names" :value="vn.id" >{{vn.name}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- {{ven_names}} -->
                                    <div class="d-grid gap-2">
                                         <button type="submit" class="col-auto btn btn-primary">บันทึก</button>
                                    </div>
                                </form>
                                
                            </div>
                        </div>
                    </div>
                </div>


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
    <script src="./js/ven_com.js"></script>
</body>

</html>