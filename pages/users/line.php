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
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">

                                            <div class="row ">
                                                <div class="col-md-10 mx-auto">
                                                    <div class="input-group">
                                                        <input class="form-control border rounded-pill" type="search" value="search" v-model="q" placeholder="ค้นหา" id="example-search-input">
                                                    </div>
                                                </div>
                                            </div>

                                            <div>
                                                <button class="btn btn-success btn-md" @click="line_insert()">เพิ่ม</button>

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
                                            <label for="username1" class="form-label">username</label>
                                            <input type="text" class="form-control" id="username1" aria-describedby="emailHelp" v-model="line_form.name">
                                            <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label for="exampleInputPassword1" class="form-label">token</label>
                                            <input type="text" class="form-control" id="example1" v-model="line_form.token">
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