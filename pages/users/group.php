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
                <h3>จัดการ กลุ่มงาน</h3>
            </div>
            <div class="page-content" id="usersGroup" v-cloak>
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
                                                <button class="btn btn-success btn-md" @click="group_insert()">เพิ่ม</button>

                                            </div>
                                        </div>
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">name</th>
                                                <th scope="col">act</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="d,index in datas">
                                                    <th scope="row">{{index+1}}</th>                                                    
                                                    <td>
                                                        <p >
                                                            <b class="me-1"> {{d.name}} </b>                                                            
                                                            
                                                        </p>
                                                    </td>
                                                    
                                                    <td>
                                                        <button class="btn btn-warning btn-sm me-2 mb-1" @click="group_update(d.id)">แก้ไข</button>    
                                                        <button class="btn btn-danger btn-sm me-2 mb-1" @click="group_del(d.id)">ลบ</button>    
                                                        
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
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop_form" ref="show_modal_form" hidden>
                    Launch static backdrop modal
                </button>

                <!-- Modal view -->
                <div class="modal fade" id="staticBackdrop_form" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel">กลุ่มงาน</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="close_modal_form()" ref="close_modal_form"></button>
                            </div>
                            <form @submit.prevent="group_save">
                            <div class="modal-body">
                                
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label for="name" class="form-label" >ชื่อกลุ่มงาน</label>
                                            <input type="text" class="form-control" id="name" aria-describedby="emailHelp" v-model="form.name">
                                            <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                                        </div>                                       
                                        
                                    </div>                          
                                
                                <!-- {{form}} -->
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
    <script src="./js/group.js"></script>
</body>

</html>