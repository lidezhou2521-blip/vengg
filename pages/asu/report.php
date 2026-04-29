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
            <header class="mb-3 d-print-none">
                <a href="#" class="burger-btn d-block">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading d-print-none">
                <h3>Report</h3>
            </div>
            <div class="page-content" id="asuReport" v-cloak>

                <!-- ========================= preloader start ========================= -->
                <div class="preloader" ref="loading" v-if="isLoading">
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

                <section class="row">
                    <div class="col-12 col-lg-12">
                        <!-- {{ven_coms}} -->
                        <!-- {{ven_coms_g}} -->
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="card">
                                    <div class="card-body pb-3">
                                        <div class="row align-items-center">
                                            <div class="col-md-auto">
                                                <label for="date_start" class="col-form-label text-bold">กรองช่วงวันที่ (เฉพาะปุ่มเวรแขวงฯ, ฟื้นฟูฯ, หมายจับฯ)</label>
                                            </div>
                                        </div>
                                        <div class="row align-items-center mt-2">
                                            <div class="col-md-auto">
                                                <label for="date_start" class="col-form-label">ตั้งแต่วันที่ :</label>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="date" id="date_start" class="form-control" v-model="date_start">
                                            </div>
                                            <div class="col-md-auto">
                                                <label for="date_end" class="col-form-label">ถึงวันที่ :</label>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="date" id="date_end" class="form-control" v-model="date_end">
                                            </div>
                                        </div>
                                        <div class="row align-items-center mt-2">
                                            <div class="col-md-auto">
                                                <label class="col-form-label">เลือกประจำเดือน :</label>
                                            </div>
                                            <div class="col-md-3">
                                                <select class="form-select" v-model="sel_month">
                                                    <option value="">-- แสดงทุกเดือน --</option>
                                                    <option v-for="m in ven_coms_g" :value="m.ven_month">{{m.ven_month_th}}</option>
                                                </select>
                                            </div>
                                            <div class="col-md-auto">
                                                <button class="btn btn-outline-secondary" @click="date_start=''; date_end=''; sel_month='';">ล้างค่า</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col col-12">
                                <template v-for='cvg in ven_coms_g'>
                                    <div class="card" v-if="sel_month === '' || sel_month === cvg.ven_month">
                                        <div class="card-body">

                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th colspan="2" class="text-start">
                                                            เวรเดือน {{cvg.ven_month_th}}
                                                            <button class="btn btn-info btn-sm ms-2" @click="print_master(cvg.ven_month)">สรุปรายเดือน (รูป5)</button>
                                                            <button class="btn btn-success btn-sm ms-2" @click="print_groups(cvg.ven_month)">แยกกลุ่มงาน</button>
                                                            <button class="btn btn-outline-success btn-sm ms-2" @click="print_dutytype(cvg.ven_month)"><i class="bi bi-tags-fill me-1"></i>ตรวจสอบเวร</button>
                                                            <button class="btn btn-dark btn-sm ms-2" @click="print_single(cvg.ven_month, 'หมายจับ-ค้น')">สรุปหมายค้น-จับ</button>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody v-for="vc in ven_coms">
                                                    <tr v-if="vc.ven_month == cvg.ven_month">
                                                        <td>
                                                            เลขคำสั่งที่ {{vc.ven_com_num}} | ลงวันที่ {{vc.ven_com_date_th}} | {{vc.ven_com_name}} ({{vc.ven_name}})
                                                            <button class="btn btn-success btn-sm me-2" @click="approve_ven(vc.id)"><i class="bi bi-check-circle"></i> อนุมัติเวร</button>
                                                            <button class="btn btn-primary btn-sm me-2" @click="print5(vc.id, vc.ven_month)">เวรแขวง+เวรปล่อยฯ</button>
                                                            <button class="btn btn-secondary btn-sm me-2" @click="print6(vc.id, vc.ven_month)">เวรฟื้นฟู/ตรวจสอบการจับ</button>
                                                            <button class="btn btn-info btn-sm me-2 text-white" @click="print7(vc.id, vc.ven_month)">เวรหมายจับ-ค้น</button>
                                                            <button class="btn btn-warning btn-sm" @click="print3(cvg.ven_month)">ใบขวางสรุป</button>
                                                        </td>


                                                        <td class="text-end col " style="width: 250px;">
                                                            <button v-if="vc.ven_name.includes('ตรวจสอบการจับ') || vc.ven_name.includes('ฟื้นฟู')" class="btn btn-primary btn-sm m-2" @click="print2(vc.id, vc.ven_month)">แนบท้าย({{vc.ven_name}})</button>
                                                            <button v-if="vc.ven_name.includes('หมายจับ-ค้น')" class="btn btn-primary btn-sm m-2" @click="print(vc.id, vc.ven_month)">แนบท้าย({{vc.ven_name}})</button>
                                                            <button v-if="vc.ven_name.includes('ผู้ตรวจ')" class="btn btn-primary btn-sm m-2" @click="print4(vc.ven_month,vc.ven_com_num,vc.ven_com_date)">แนบท้าย รักษาการณ์({{vc.ven_name}})</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>

                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                </section>

                <!-- Button to trigger modal -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-target="#view" ref="show_modal" hidden>
                    Open Modal
                </button>

                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">รายการ</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-center">
                                    เลขคำสั่งที่ {{heads.vc_num}} ลงวันที่ {{heads.vc_date}} | {{heads.vc_name}}
                                </p>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">วันที่</th>
                                            <th scope="col">เวลา</th>
                                            <th scope="col">ผู้พิพากษา</th>
                                            <th scope="col">ชื่อผู้เข้าพิจารณา</th>
                                            <th scope="col">หมายเหตุ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(d, index) in datas" :key="index">
                                            <td class="align-top">{{ date_thai_dt(d.ven_date) }}</td>
                                            <td class="align-top">
                                                <ul class="list-group">
                                                    <li class="list-group-item mt-0" v-for="dvt in d.ven_time">
                                                        {{ dvt === '08:30' ? '08.30 - 16.30 น.' : '16.30 - 08.30 น.' }}
                                                    </li>
                                                </ul>
                                            </td>
                                            <td class="align-top">
                                                <ul class="list-group">
                                                    <li class="list-group-item mt-0" v-for="dunj in d.u_namej">
                                                        {{ dunj }}
                                                    </li>
                                                </ul>
                                            </td>
                                            <td class="align-top">
                                                <ul class="list-group">
                                                    <li class="list-group-item mt-0" v-for="dun in d.u_name">
                                                        {{ dun }}
                                                    </li>
                                                </ul>
                                            </td>
                                            <td class="align-top">
                                                <ul class="list-group">
                                                    <li class="list-group-item mt-0" v-for="dur in d.cmt">
                                                        {{ dur }}
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
    <script src="./js/report.js?v=<?= time() ?>"></script>
</body>

</html>