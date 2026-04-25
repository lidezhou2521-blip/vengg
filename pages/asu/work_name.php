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
                <h3>ชื่อเวร/กลุ่มหน้าที่</h3>
                <!-- Button trigger modal user update form-->
            </div>
            <div class="page-content" id="workName" v-cloak>

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
                
                <div class="mb-2">
                    <button type="button" class="btn btn-success btn-sm" @click="show_ven_nfi" >
                        เพิ่มชื่อเวร
                    </button>
                </div>
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <!-- {{ven_names}} -->
                        <div class="row">
                            <div class="col col-6" v-for="vn in ven_names">                                
                                <div class="card" >
                                    <div class="card-body">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th colspan="2" class="text-center">
                                                        {{vn.vn_srt + ' '+ vn.vn_name}}
                                                        <span >({{vn.DN == 'กลางวัน' ? '☀️' : ''}}{{vn.DN == 'กลางคืน' ? '🌙' : ''}} {{vn.DN}}) </span>
                                                    </th>
                                                    <th class="text-center">
                                                        <button class="btn btn-warning btn-sm" @click="ven_name_usf(vn.vn_id)">แก้ไขชื่อ</button>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody v-for="vns in vn.ven_name_subs" >
                                                <tr >
                                                    <th scope="row">{{vns.vns_srt}}</th>
                                                    <td :style="'background-color: '+vns.color+'; color:white;'" v-if="vns.color">
                                                        {{vns.vns_name}} ({{vns.price ? '💰'+vns.price : '' }}) {{vns.color ? vns.color : ''}} 
                                                    </td>
                                                    <td  v-else>
                                                        {{vns.name}} ({{vns.price ? '💰'+vns.price : '' }}) {{vns.color ? vns.color : ''}} 
                                                    </td>
                                                    <td class="text-center">
                                                        <button @click="ven_name_s_up(vns.vns_id)" class="btn btn-warning btn-sm me-1">แก้ไข</button>
                                                        <button class="btn btn-danger btn-sm" @click="ven_name_s_del(vns.vns_id)">ลบ</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" class="text-center">
                                                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#ven_name_sub" @click="vns_insert(vn.vn_id)">
                                                            เพิ่มชื่อเวร
                                                        </button>
                                                        <!-- <button class="btn btn-success btn-sm">เพิ่มหน้าที่</button> -->
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- {{ven_name_subs}}                             -->
                        </div>    
                    </div>
                </section>

                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#ven_name" ref="show_ven_name_form" hidden >
                        เพิ่มชื่อเวร
                </button>
                <!-- Modal VenName Form -->
                <div class="modal fade" id="ven_name" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel"v-if="ven_name_form.act == 'insert'">เพิ่มชื่อเวร</h5>
                                <h5 class="modal-title" id="staticBackdropLabel" v-else>แก้ไขชื่อเวร</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="clear_ven_name_form" ref="close_ven_name_form"></button>
                            </div>
                            <div class="modal-body">
                                <form @submit.prevent="ven_name_save">                                    
                                    <div class="row mb-3">                                        
                                        <div class="col mb-3">
                                            <label for="srt" class="form-label">ลำดับ</label>
                                            <input type="number" min="0"  max="9" class="form-control" id="srt" v-model="ven_name_form.srt">
                                        </div>
                                        <div class="col mb-3">
                                            <label for="namef" class="form-label">ชื่อเวร</label>
                                            <input type="text" class="form-control" id="namef" v-model="ven_name_form.name">
                                        </div>
                                        <div class="col mb-3">
                                            <label for="DN" class="form-label">กลางวัน/กลางคืน</label>
                                            <!-- <input type="text" class="form-control" id="DN" v-model="ven_name_form.DN"> -->
                                            <select class="form-select" aria-label="Default select example" v-model="ven_name_form.DN">
                                                <option value="กลางวัน">กลางวัน(08.30-16.30)</option>
                                                <option value="กลางคืน">กลางคืน(16.30-08.30)</option>
                                                <option value="nightCourt">nightCourt(16.30-20.00)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="d-grid gap-2">
                                        
                                        <button type="button" class="col-auto me-auto btn btn-danger" v-if="ven_name_form_act !='insert'" @click.prevent="ven_name_del()">ลบ {{ven_name_form.id}}</button>
                                        <button type="submit" class="col-auto btn btn-primary">บันทึก</button>
                                    </div>
                                </form>
                                <!-- {{ven_name_form}} -->
                                <!-- {{ven_name_form_act}} -->
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#ven_name_sub" ref="ven_name_sub" hidden>
                    เพิ่มชื่อเวร
                </button>
                <!-- Modal VenName Form -->
                <div class="modal fade" id="ven_name_sub" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel">{{ven_name_form_act == 'insert' ? 'เพิ่ม' : 'แก้ไข'}}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="clear_vnsf" ref="close_vnsf"></button>
                            </div>
                            <div class="modal-body">
                                <form @submit.prevent="ven_name_sub_save">                                    
                                    <div class="row mb-2">                                        
                                        <div class="col">
                                            <label for="srt" class="form-label">ลำดับ</label>
                                            <input type="number" min="0"  max="9" class="form-control" id="srt" v-model="ven_name_sub_form.srt">
                                        </div>
                                        <div class="col">
                                            <label for="namef" class="form-label">ชื่อตำแหน่ง/หน้าที่</label>
                                            <input type="text" class="form-control" id="namef" v-model="ven_name_sub_form.name">
                                        </div>
                                        <div class="col">
                                            <label for="price" class="form-label">ค่าเวร</label>
                                            <input type="number" min="0" class="form-control" id="price" v-model="ven_name_sub_form.price">
                                        </div>
                                        <!-- <div class="col">
                                            <label for="namefcolor" class="form-label">สี</label>
                                            <input type="text" class="form-control" id="namefcolor" v-model="ven_name_sub_form.color">
                                        </div> -->
                                        <div class="col">
                                            <label for="inputColor" >สี</label>
                                            <select id="inputColor" class="form-control" v-model="ven_name_sub_form.color" :style="'background-color:'+  ven_name_sub_form.color +'; color:white;'">
                                                <option v-for="color in colors" :value="color" :style="'background-color:'+color+'; color:white;'">{{color}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="pull-end">
                                        
                                        <button type="submit" class="btn btn-primary">บันทึก</button>
                                    </div>
                                </form>
                                <!-- {{ven_name_sub_form}} -->
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
    <script src="./js/work_name.js"></script>
</body>

</html>