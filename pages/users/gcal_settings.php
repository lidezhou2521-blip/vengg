<?php
require_once('../../server/authen.php');
if($_SESSION['AD_ROLE'] != 9){
    header('Location: ../../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('../includes/_header.php') ?>
    <style>
        [v-cloak] > * { display: none; }
        [v-cloak]::before { content: "กำลังโหลด..."; display: block; text-align: center; padding: 60px; font-size: 18px; color: #666; }
    </style>
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
                <h3>ตั้งค่า Google Calendar</h3>
            </div>
            
            <div class="page-content" v-cloak>
                <div class="row">
                    <div class="col-12 col-lg-10">
                        <div class="card">
                            <div class="card-header bg-white border-bottom">
                                <h4 class="card-title text-primary"><i class="bi bi-gear-fill"></i> สถานะการทำงาน Google Calendar</h4>
                            </div>
                            <div class="card-body pt-4">
                                <div class="form-check form-switch fs-4 mb-3">
                                    <input class="form-check-input" type="checkbox" id="gcalSwitch" v-model="gcal_enabled">
                                    <label class="form-check-label fw-bold ms-2" for="gcalSwitch" :class="gcal_enabled ? 'text-success' : 'text-danger'">
                                        {{ gcal_enabled ? 'เปิดใช้งานระบบ Calendar (Sync เปิดอยู่)' : 'ปิดระบบ (หยุดส่งข้อมูลเข้า Calendar)' }}
                                    </label>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Google Calendar API Service URL</label>
                                    <input type="text" class="form-control" v-model="api_url" placeholder="http://127.0.0.1/service/google/calendar/calendar.php">
                                    <div class="form-text text-muted">URL ของเซิร์ฟเวอร์ที่รัน Python/PHP Service สำหรับจัดการปฏิทิน</div>
                                </div>
                                
                                <button class="btn btn-primary mt-2" @click="saveConfig"><i class="bi bi-save"></i> บันทึกการตั้งค่า</button>

                                <p class="text-muted mt-3 ms-2 fs-6">
                                    หาก <b>เปิดการใช้งาน</b> ระบบจะสามารถส่งข้อมูลเวร (ที่อนุมัติแล้ว) ไปยังปฏิทิน Google Calendar ที่เชื่อมต่อได้ผ่านปุ่ม "ซิงค์" ด้านล่าง<br>
                                </p>
                            </div>
                        </div>

                        <div class="card mt-4 shadow-sm">
                            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                                <h4 class="card-title text-dark m-0"><i class="bi bi-calendar-event"></i> อัปเดตข้อมูลเวรเข้าปฏิทิน (Sync to Google Calendar)</h4>
                                <span class="badge bg-light text-dark border"><i class="bi bi-info-circle"></i> ส่งเฉพาะเวรที่ "อนุมัติแล้ว" (Status=1) เท่านั้น</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered m-0 align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center" width="120">ประจำเดือน</th>
                                                <th>รายละเอียดคำสั่งเวร</th>
                                                <th class="text-center" width="160">การจัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="vc in ven_coms" :key="vc.id">
                                                <td class="text-center fw-bold text-primary">{{ vc.ven_month_th }}</td>
                                                <td>
                                                    <span class="badge bg-secondary mb-1">เลขที่ {{ vc.ven_com_num }}</span> <small class="text-muted">ลงวันที่ {{ vc.ven_com_date_th }}</small><br>
                                                    <span class="fw-bold text-dark">{{ vc.ven_com_name }}</span><br>
                                                    <small class="text-info">{{ vc.ven_name }}</small>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-warning btn-sm fw-bold shadow-sm" @click="syncToGcal(vc)" :disabled="!gcal_enabled || isSyncing">
                                                        <i class="bi bi-arrow-repeat"></i> ซิงค์ (Sync)
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr v-if="ven_coms.length === 0 && !isLoading">
                                                <td colspan="3" class="text-center text-muted py-5">
                                                    <i class="bi bi-folder-x fs-1"></i><br>
                                                    ไม่พบข้อมูลคำสั่งเวรในระบบ
                                                </td>
                                            </tr>
                                            <tr v-if="isLoading">
                                                <td colspan="3" class="text-center py-5">
                                                    <div class="spinner-border text-primary" role="status"></div>
                                                    <div class="mt-2 text-muted">กำลังโหลดข้อมูล...</div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
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
    <script src="../../node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
    
    <script>
        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    gcal_enabled: false,
                    api_url: '',
                    ven_coms: [],
                    ven_coms_g: [],
                    isSyncing: false,
                    isLoading: true
                }
            },
            mounted() {
                this.loadConfig();
                this.getVenComs();
            },
            methods: {
                loadConfig() {
                    axios.get('../../server/users/update_gcal_config.php')
                        .then(res => {
                            if(res.data.status) {
                                this.gcal_enabled = res.data.state;
                                this.api_url = res.data.api_url || 'http://127.0.0.1/service/google/calendar/calendar.php';
                            }
                        })
                        .catch(err => console.error(err));
                },
                saveConfig() {
                    axios.post('../../server/users/update_gcal_config.php', { 
                        state: this.gcal_enabled,
                        api_url: this.api_url 
                    })
                        .then(res => {
                            if(res.data.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'บันทึกสำเร็จ',
                                    text: res.data.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('ข้อผิดพลาด', 'ไม่สามารถบันทึกได้', 'error');
                        });
                },
                getVenComs() {
                    this.isLoading = true;
                    axios.post('../../server/asu/report/get_ven_coms.php')
                        .then(res => {
                            if (res.data.status) {
                                let allComs = res.data.respJSON || [];
                                this.ven_coms_g = res.data.respJSON_G || [];
                                
                                // Mapping month names
                                allComs.forEach(vc => {
                                    const g = this.ven_coms_g.find(g => g.ven_month === vc.ven_month);
                                    if(g) {
                                        vc.ven_month_th = g.ven_month_th;
                                    }
                                });
                                this.ven_coms = allComs;
                            }
                        })
                        .catch(err => console.error(err))
                        .finally(() => {
                            this.isLoading = false;
                        });
                },
                syncToGcal(vc) {
                    Swal.fire({
                        title: 'ยืนยันการส่งข้อมูล?',
                        html: `กำลังจะซิงค์ข้อมูลคำสั่ง:<br><b class="text-primary">${vc.ven_com_num} ${vc.ven_name}</b><br><br><span class="text-danger">หมายเหตุ: จะส่งเฉพาะรายการเวรที่ถูก "อนุมัติ" ในหน้ารายงานแล้วเท่านั้น</span>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#f57c00',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'ตกลง, ซิงค์เลย',
                        cancelButtonText: 'ยกเลิก'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.isSyncing = true;
                            Swal.fire({
                                title: 'กำลังซิงค์ข้อมูลเข้า Calendar...',
                                html: 'กรุณารอสักครู่ อาจใช้เวลาหลายวินาทีขึ้นอยู่กับจำนวนข้อมูล',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            
                            axios.post('../../server/asu/report/send_to_gcal.php', { ven_com_id: vc.id })
                                .then(res => {
                                    if (res.data.status) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'ซิงค์ข้อมูลสำเร็จ',
                                            text: 'เพิ่มข้อมูลเวรลงในปฏิทินของศาลแล้ว',
                                            timer: 2000,
                                            showConfirmButton: false
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'warning',
                                            title: 'สำเร็จ (ไม่มีอัปเดต)',
                                            text: res.data.message || 'ไม่มีเวรสถานะอนุมัติที่ต้องอัปเดต'
                                        });
                                    }
                                })
                                .catch(err => {
                                    console.error(err);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'ข้อผิดพลาด',
                                        text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ หรือ Service Google Calendar ไม่ทำงาน'
                                    });
                                })
                                .finally(() => {
                                    this.isSyncing = false;
                                });
                        }
                    });
                }
            }
        }).mount('#app');
    </script>
</body>
</html>
