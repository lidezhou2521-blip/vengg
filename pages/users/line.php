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
                <h3>จัดการ LINE TOKEN</h3>
            </div>
            <div class="page-content" id="usersLine" v-cloak>
                <div class="row">
                    <div class="col-12 text-center">
                        <a :href="url_base+'/vengg/server/service/line/sendline.php'" target="_blank">Link สำหรับแจ้งเตือน GET {{url_base}}/vengg/server/service/line/sendline.php</a>

                    </div>
                </div>
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                                <div class="row mb-4">
                                                    <div class="col-md-12">
                                                        <div class="alert alert-info">
                                                            <h5 class="alert-heading"><i class="bi bi-info-circle"></i> ประกาศอัปเดตระบบ LINE</h5>
                                                            <p>เนื่องจากระบบ LINE Notify เดิมได้ถูกยกเลิกการให้บริการ (สิ้นสุดมี.ค. 2025) ระบบจึงได้อัปเกรดเป็น <b>LINE Messaging API</b> แทน<br>
                                                            การตั้งค่าใหม่จะต้องใช้ <b>Channel Access Token</b> เป็นตัวกลางหลัก และใช้ <b>User ID</b> หรือ <b>Group ID</b> เป็นปลายทางสำหรับผู้รับ</p>
                                                        </div>
                                                        <div class="card border border-primary shadow-sm">
                                                            <div class="card-header bg-primary text-white pb-2">
                                                                <h5 class="card-title text-white mb-0"><i class="bi bi-gear-fill"></i> ตั้งค่า LINE Channel Access Token (Global)</h5>
                                                            </div>
                                                            <div class="card-body pt-3">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Channel Access Token (Long-lived)</label>
                                                                    <textarea class="form-control" v-model="channel_access_token" rows="2" placeholder="ใส่ Channel Access Token จาก LINE Developers ที่นี่..."></textarea>
                                                                    <div class="form-text text-muted">Token นี้จะถูกใช้งานเป็นบอทหลักในการส่งข้อความ (หากไม่ใส่ ระบบจะพยายามใช้ LINE Notify แบบเดิมซึ่งอาจจะไม่ทำงานแล้ว)</div>
                                                                </div>
                                                                <button class="btn btn-primary" @click="save_line_config"><i class="bi bi-save"></i> บันทึก Token หลัก</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <h5 class="mb-3"><i class="bi bi-people-fill"></i> จัดการผู้รับแจ้งเตือน (ปลายทาง)</h5>
                                                
                                                <div class="row align-items-center">
                                                    <div class="col-md-8">
                                                        <div class="input-group">
                                                            <input class="form-control border rounded-pill" type="search" value="search" v-model="q" placeholder="ค้นหาชื่อผู้รับ" id="example-search-input">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 text-end">
                                                        <button class="btn btn-success btn-md" @click="line_insert()"><i class="bi bi-plus-circle"></i> เพิ่มปลายทางใหม่</button>
                                                    </div>
                                                </div>
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">name</th>
                                                    <th scope="col">สถานะ</th>
                                                    <th scope="col">act</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="d,index in datas">
                                                    <th scope="row">{{index+1}}</th>

                                                    <td>
                                                        <p @click="view(d.id)">
                                                            <i class="bi bi-broadcast me-2"></i>
                                                            <b class="me-1"> {{d.name}} </b>
                                                            <small :class="'badge text-sm ' + (d.status == 1 ? 'bg-primary' : 'bg-danger')">{{d.status == 1 ? '(ปกติ)' : '(ระงับการใช้งาน)'}}</small>
                                                            <br>token : {{d.token}}

                                                        </p>
                                                    </td>
                                                    <td>
                                                        <div class="form-check form-switch" v-if="d.status == 1">
                                                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" @click="line_status(d.id,'0')" checked>
                                                            <!-- <label class="form-check-label" for="flexSwitchCheckChecked">Checked switch checkbox input</label> -->
                                                        </div>
                                                        <div class="form-check form-switch" v-else>
                                                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" @click="line_status(d.id,'1')">
                                                            <!-- <label class="form-check-label" for="flexSwitchCheckChecked">Checked switch checkbox input</label> -->
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-primary btn-sm me-2 mb-1" @click="line_send_test(d.token,d.name)">ทดสอบ</button>
                                                        <button class="btn btn-warning btn-sm me-2 mb-1" @click="line_update(d.id)">แก้ไข</button>
                                                        <button class="btn btn-danger btn-sm me-2 mb-1" @click="line_del(d.id)">ลบ</button>

                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <!-- {{datas}} -->

                                    </div>
                                </div>
                            </div>
                        </div>



                    </div>
                </section>
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop_form" ref="show_modal_line_form" hidden>
                    Launch static backdrop modal
                </button>

                <!-- Modal view -->
                <div class="modal fade" id="staticBackdrop_form" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel">Line</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="close_modal_line_form()" ref="close_modal_line_form"></button>
                            </div>
                            <form @submit.prevent="line_save">
                                <div class="modal-body">

                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label for="username1" class="form-label fw-bold">ชื่อกลุ่ม/บุคคล (อ้างอิง)</label>
                                            <input type="text" class="form-control" id="username1" v-model="line_form.name" placeholder="เช่น แจ้งเตือนผู้พิพากษา">
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label for="example1" class="form-label fw-bold">User ID / Group ID / Room ID</label>
                                            <input type="text" class="form-control" id="example1" v-model="line_form.token" placeholder="เช่น U4af4980629... หรือ C...">
                                            <div class="form-text text-danger">นำมาจาก LINE Official Account (Webhook/Webhook Event) หรือ LINE Developers</div>
                                        </div>
                                    </div>



                                    <!-- {{line_form}} -->
                                    <!-- {{act}} -->
                                </div>
                                <div class="modal-footer">
                                    <div class="row">
                                        <button type="submit" class="btn btn-primary">บันทึก</button>
                                    </div>
                                </div>
                            </form>
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
    <script src="./js/line.js"></script>
</body>

</html>