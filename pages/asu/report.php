<?php

require_once('../../server/authen.php');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('../includes/_header.php') ?>

</head>
<style>
    .report-row {
        transition: all 0.2s ease;
        border-bottom: 1px solid #f0f2f5;
    }
    .report-row:hover {
        background-color: #f8fafc;
    }
    .bg-light-primary {
        background-color: #f1f5f9 !important;
    }
    /* Softer Button Palette */
    .btn-soft-indigo { background-color: #6366f1; color: white; border: none; }
    .btn-soft-indigo:hover { background-color: #4f46e5; color: white; }
    
    .btn-soft-sage { background-color: #10b981; color: white; border: none; }
    .btn-soft-sage:hover { background-color: #059669; color: white; }
    
    .btn-soft-sand { background-color: #f59e0b; color: white; border: none; }
    .btn-soft-sand:hover { background-color: #d97706; color: white; }
    
    .btn-soft-slate { background-color: #64748b; color: white; border: none; }
    .btn-soft-slate:hover { background-color: #475569; color: white; }
    
    .btn-soft-sky { background-color: #0ea5e9; color: white; border: none; }
    .btn-soft-sky:hover { background-color: #0284c7; color: white; }

    .avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
    }
    .avatar-lg {
        width: 48px;
        height: 48px;
    }
    .badge-soft-info {
        background-color: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }
    /* Dimensional Buttons */
    .btn-approve {
        background: linear-gradient(145deg, #22c55e, #16a34a);
        color: white;
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
        transition: all 0.2s ease;
    }
    .btn-approve:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(34, 197, 94, 0.4);
        color: white;
    }
    .btn-approve:active {
        transform: translateY(0);
    }
    
    .btn-revert {
        background: #ffffff;
        color: #ef4444;
        border: 1px solid #fee2e2;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: all 0.2s ease;
    }
    .btn-revert:hover {
        background-color: #fef2f2;
        border-color: #fecaca;
        color: #dc2626;
        transform: scale(1.02);
    }
</style>
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
                                                        <th colspan="3" class="text-start">
                                                            เวรเดือน {{cvg.ven_month_th}}
                                                            <button class="btn btn-info btn-sm ms-2" @click="print_master(cvg.ven_month)">สรุปรายเดือน (รูป5)</button>
                                                            <button class="btn btn-success btn-sm ms-2" @click="print_groups(cvg.ven_month)">แยกกลุ่มงาน</button>
                                                            <button class="btn btn-outline-success btn-sm ms-2" @click="print_dutytype(cvg.ven_month)"><i class="bi bi-tags-fill me-1"></i>ตรวจสอบเวร</button>
                                                                                                  <tbody v-for="vc in ven_coms">
                                                    <tr v-if="vc.ven_month == cvg.ven_month" class="report-row">
                                                        <td class="py-3">
                                                            <div class="d-flex align-items-start">
                                                                <div class="avatar avatar-lg bg-light-primary me-3 mt-1">
                                                                    <span class="avatar-content"><i class="bi bi-file-earmark-ruled text-primary fs-4"></i></span>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                                        <h5 class="mb-0 text-primary fw-bold">เลขคำสั่งที่ {{vc.ven_com_num}}</h5>
                                                                        <span class="text-muted small"><i class="bi bi-calendar-event me-1"></i>ลงวันที่ {{vc.ven_com_date_th}}</span>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <span class="badge badge-soft-info mb-1">{{vc.ven_name}}</span>
                                                                        <div class="text-secondary small">{{vc.ven_com_name}}</div>
                                                                    </div>
                                                                    <div class="d-flex flex-wrap gap-2">
                                                                        <button v-if="vc.ven_name.includes('ศาลแขวง') || vc.ven_name.includes('เวรเปิดทำการ')" class="btn btn-soft-indigo btn-sm px-3 shadow-sm" @click="print5(vc.id, vc.ven_month)"><i class="bi bi-printer me-1"></i>เวรแขวง+เวรปล่อยฯ</button>
                                                                        <button v-if="vc.ven_name.includes('ศาลแขวง')" class="btn btn-soft-sage btn-sm px-3 shadow-sm" @click="print5_district(vc.id, vc.ven_month)"><i class="bi bi-house-door me-1"></i>เฉพาะเวรแขวงฯ</button>
                                                                        <button v-if="vc.ven_name.includes('เวรเปิดทำการ')" class="btn btn-soft-sand btn-sm px-3 shadow-sm" @click="print5_release(vc.id, vc.ven_month)"><i class="bi bi-unlock me-1"></i>เฉพาะเวรปล่อยฯ</button>
                                                                        <button v-if="vc.ven_name.includes('ฟื้นฟู') || vc.ven_name.includes('ตรวจสอบการจับ')" class="btn btn-soft-slate btn-sm px-3 shadow-sm" @click="print6(vc.id, vc.ven_month)"><i class="bi bi-shield-check me-1"></i>เวรฟื้นฟู/ตรวจสอบการจับ</button>
                                                                        <button v-if="vc.ven_name.includes('หมายจับ-ค้น')" class="btn btn-soft-sky btn-sm px-3 shadow-sm" @click="print7(vc.id, vc.ven_month)"><i class="bi bi-search me-1"></i>เวรหมายจับ-ค้น</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>

                                                        <td class="text-end align-middle" style="width: 250px;">
                                                            <div class="d-flex flex-column gap-2 pe-3">
                                                                <button v-if="vc.ven_name.includes('ผู้ตรวจ')" class="btn btn-outline-primary btn-sm" @click="print4(vc.ven_month,vc.ven_com_num,vc.ven_com_date)">
                                                                    <i class="bi bi-file-earmark-pdf me-1"></i>แนบท้าย รักษาการณ์
                                                                </button>
                                                                <button v-else class="btn btn-outline-secondary btn-sm" disabled>
                                                                    ไม่มีไฟล์แนบท้ายพิเศษ
                                                                </button>
                                                            </div>
                                                        </td>

                                                        <td class="text-end border-start bg-light align-middle" style="width: 160px;">
                                                            <div class="px-2">
                                                                <div v-if="vc.pending_count > 0" class="text-center">
                                                                    <div class="mb-2">
                                                                        <span class="badge bg-warning rounded-pill px-3">{{ vc.pending_count }} รออนุมัติ</span>
                                                                    </div>
                                                                    <button class="btn btn-approve btn-sm w-100 py-2" @click="approve_ven(vc.id)">
                                                                        <i class="bi bi-check-circle-fill d-block mb-1 fs-5"></i> 
                                                                        อนุมัติเวร
                                                                    </button>
                                                                </div>
                                                                <div v-else-if="vc.active_count > 0" class="text-center">
                                                                    <div class="mb-2">
                                                                        <span class="badge bg-success rounded-pill px-3">อนุมัติแล้ว</span>
                                                                    </div>
                                                                    <button class="btn btn-revert btn-sm w-100 py-1" @click="unapprove_ven(vc.id)">
                                                                        <i class="bi bi-arrow-counterclockwise me-1"></i>ยกเลิก
                                                                    </button>
                                                                </div>
                                                                <div v-else class="text-muted small text-center py-3">
                                                                    <i class="bi bi-dash-circle d-block mb-1 fs-4 opacity-25"></i>
                                                                    ไม่มีข้อมูล
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
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