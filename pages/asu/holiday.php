<?php 
require_once('../../server/authen.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('../includes/_header.php') ?>
    <style>
        [v-cloak] > * { display: none; }
        [v-cloak]::before { content: "loading..."; }
    </style>
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
                <h3>📅 กำหนดวันหยุดราชการ</h3>
            </div>

            <div class="page-content" id="holidayPage" v-cloak>
                <section class="row">
                    <div class="col-12 col-lg-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0 text-white">เพิ่มวันหยุด</h5>
                            </div>
                            <div class="card-body mt-3">
                                <form @submit.prevent="addHoliday">
                                    <div class="mb-3">
                                        <label class="form-label">วันที่</label>
                                        <input type="date" class="form-control" v-model="form.holiday_date" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ชื่อวันหยุด</label>
                                        <input type="text" class="form-control" v-model="form.holiday_name" placeholder="เช่น วันสงกรานต์" required>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary" :disabled="isLoading">
                                            <span v-if="isLoading" class="spinner-border spinner-border-sm"></span>
                                            บันทึก
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-8">
                        <div class="card">
                            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-white">รายการวันหยุด</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center" style="width: 80px;">ลำดับ</th>
                                                <th>วันที่</th>
                                                <th>ชื่อวันหยุด</th>
                                                <th class="text-center" style="width: 100px;">จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(h, index) in holidays" :key="h.id">
                                                <td class="text-center">{{ index + 1 }}</td>
                                                <td>{{ formatDate(h.holiday_date) }}</td>
                                                <td>{{ h.holiday_name }}</td>
                                                <td class="text-center">
                                                    <button class="btn btn-danger btn-sm" @click="deleteHoliday(h.id)">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr v-if="holidays.length === 0">
                                                <td colspan="4" class="text-center text-muted py-4">ไม่พบข้อมูลวันหยุด</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <?php require_once('../includes/_footer.php') ?>
        </div>
    </div>

    <?php require_once('../includes/_footer_sc.php') ?>
    <script src="../../node_modules/vue/dist/vue.global.js"></script>
    <script src="../../node_modules/axios/dist/axios.js"></script>
    <script src="../../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script>
        Vue.createApp({
            data() {
                return {
                    holidays: [],
                    form: {
                        holiday_date: '',
                        holiday_name: ''
                    },
                    isLoading: false
                }
            },
            mounted() {
                this.getHolidays();
            },
            methods: {
                getHolidays() {
                    axios.get('../../server/asu/holiday/get_holidays.php')
                        .then(res => {
                            if (res.data.status) {
                                this.holidays = res.data.respJSON;
                            }
                        });
                },
                addHoliday() {
                    this.isLoading = true;
                    axios.post('../../server/asu/holiday/holiday_action.php', {
                        act: 'insert',
                        ...this.form
                    }).then(res => {
                        if (res.data.status) {
                            this.form.holiday_date = '';
                            this.form.holiday_name = '';
                            this.getHolidays();
                            Swal.fire('สำเร็จ', res.data.message, 'success');
                        }
                    }).finally(() => this.isLoading = false);
                },
                deleteHoliday(id) {
                    Swal.fire({
                        title: 'ยืนยันการลบ?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'ลบ',
                        cancelButtonText: 'ยกเลิก'
                    }).then(result => {
                        if (result.isConfirmed) {
                            axios.post('../../server/asu/holiday/holiday_action.php', {
                                act: 'delete',
                                id: id
                            }).then(res => {
                                if (res.data.status) {
                                    this.getHolidays();
                                    Swal.fire('สำเร็จ', res.data.message, 'success');
                                }
                            });
                        }
                    });
                },
                formatDate(dateStr) {
                    const d = new Date(dateStr);
                    const months = ["ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."];
                    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear() + 543}`;
                }
            }
        }).mount('#holidayPage');
    </script>
</body>
</html>
