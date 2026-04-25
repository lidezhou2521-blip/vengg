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
                <h3>จัดการสมาชิก(GDMS)</h3>
            </div>
            <div class="page-content" id="usersIndex" v-cloak>
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
                                                <!-- <button class="btn btn-success btn-md" @click="user_form_insert_show()">เพิ่ม</button> -->
                                                <!-- <button class="btn btn-success btn-md">เพิ่ม user</button> -->

                                            </div>
                                        </div>
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">#</th>
                                                <th scope="col">ชื่อ-สกุล</th>
                                                <th scope="col">สถานะ</th>
                                                <th scope="col">act</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="d,index in datas">
                                                    <th scope="row">{{d.st}}</th>
                                                    <th scope="row" @click="b_user_img(d.uid,index)">
                                                        <img :src="d.img" alt="Photo"  height="160">
                                                    </th>
                                                    <td>
                                                        <p @click="view(d.uid)">
                                                            <i class="bi bi-person-circle"></i> {{d.name}}</b>({{d.uid}}) 
                                                            <span :class="'badge text-sm ' + (d.status == 10 ? 'bg-primary' : 'bg-danger')">{{d.status == 10 ? '(ปกติ)' : '(ระงับการใช้งาน)'}}</span>
                                                            <br><i class="bi bi-person-badge"></i> <span class="text-sm">{{d.dep}}</span> 
                                                        </p>
                                                    </td>
                                                    <td>
                                                        <div class="form-check form-switch"  >
                                                            <input class="form-check-input" type="checkbox" :id="'flexSwitchCheckChecked' + index" @click="user_status(d.uid)" :checked="d.status == 10 ? true : false" >
                                                        </div>
                                                        
                                                    </td>
                                                    <td>
                                                        <!-- <button class="btn btn-primary btn-sm me-2 mb-1" @click="view(d.uid)">view</button>     -->
                                                        <button class="btn btn-warning btn-sm me-2 mb-1" @click="user_update(d.uid)">แก้ไข</button>    
                                                        <button v-if="d.status == 0" class="btn btn-danger btn-sm me-2 mb-1" @click="profile_del(d.uid)">ลบ</button>    
                                                        <!-- <button class="btn btn-primary btn-sm me-2 mb-1" @click="user_update_role(d.uid)">แก้ไขสิทธ์</button>     -->
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
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop" ref="show_modal_user" hidden>
                    Launch static backdrop modal
                </button>

                <!-- Modal view -->
                <div class="modal fade" id="staticBackdrop" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel">User</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="reset_user()" ref="close_modal_user"></button>
                            </div>
                            <div class="modal-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">{{user.id}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th scope="row">name</th>
                                            <td>{{user.name}}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">ตำแหน่ง</th>
                                            <td>{{user.dep}}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">กลุ่มงาน</th>
                                            <td>{{user.workgroup}}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Phone</th>
                                            <td>{{user.phone}}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">เลขที่บัญชี</th>
                                            <td>{{user.bank_account}}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">ธนาคาร/สาขา</th>
                                            <td>{{user.bank_comment}}</td>
                                        </tr>                                            
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2">
                                                <button class="btn btn-warning btn-sm me-2 mb-1" @click="user_update(user.id)">แก้ไข</button>
                                            </td>
                                        </tr>
                                        
                                    </tfoot>
                                </table>
                                <!-- {{user}} -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop_form" ref="show_modal_user_form" hidden>
                    Launch static backdrop modal
                </button>

                <!-- Modal view -->
                <div class="modal fade" id="staticBackdrop_form" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel">User</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="close_modal_user_form()" ref="close_modal_user_form"></button>
                            </div>
                            <div class="modal-body">
                                <form @submit.prevent="user_insert">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="username1" class="form-label" >username</label>
                                            <input type="text" class="form-control" id="username1" aria-describedby="emailHelp" v-model="user_form.username">
                                            <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                                        </div>
                                        <div class="col mb-3">
                                            <label for="exampleInputPassword1" class="form-label">Password</label>
                                            <input type="password" class="form-control" id="example1" v-model="user_form.password">
                                        </div>
                                        <div class="col mb-3">
                                            <label for="exampleInputPassword2" class="form-label">RePassword</label>
                                            <input type="password" class="form-control" id="example2" v-model="user_form.repassword">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-3 mb-3">
                                            <label for="fname" class="form-label" >คำนำหน้าชื่อ</label>
                                            <!-- <input type="text" class="form-control" id="fneme" aria-describedby="emailHelp" v-model="user_form.fname" require> -->
                                            <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                                            <select class="form-select" aria-label="Default select example" v-model="user_form.fname">
                                                <option v-for="sf in sel_fname" :value="sf.name">{{sf.name}}</option>
                                            </select>
                                        </div>
                                        <div class="col mb-3">
                                            <label for="exampleInputPassword1" class="form-label">ชื่อ</label>
                                            <input type="text" class="form-control" id="name" v-model="user_form.name">
                                        </div>
                                        <div class="col mb-3">
                                            <label for="sname_uf" class="form-label">สกุล</label>
                                            <input type="text" class="form-control" id="sname_uf" v-model="user_form.sname">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="dep" class="form-label" >ตำแหน่ง</label>
                                            <!-- <input type="text" class="form-control" id="dep" aria-describedby="emailHelp" v-model="user_form.dep"> -->
                                            <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                                            <select class="form-select" aria-label="Default select example" v-model="user_form.dep">
                                                <option v-for="sd in sel_dep" :value="sd.name">{{sd.name}}</option>
                                            </select>
                                        </div>
                                        <div class="col mb-3">
                                            <label for="workgroup" class="form-label">กลุ่มงาน</label>
                                            <!-- <input type="text" class="form-control" id="workgroup" v-model="user_form.workgroup"> -->
                                            <select class="form-select" aria-label="Default select example" v-model="user_form.workgroup">
                                                <option v-for="sw in sel_workgroup" :value="sw.name">{{sw.name}}</option>
                                            </select>
                                        </div>
                                        <div class="col-3 mb-3">
                                            <label for="phone" class="form-label">โทรศัพท์</label>
                                            <input type="text" class="form-control" id="phone" v-model="user_form.phone">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col mb-3">
                                            <label for="bank_account" class="form-label" >เลขที่บัญชี</label>
                                            <input type="text" class="form-control" id="bank_account" aria-describedby="emailHelp" v-model="user_form.bank_account">
                                            <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                                        </div>
                                        <div class="col mb-3">
                                            <label for="bank_comment" class="form-label">ธนาคาร/สาขา</label>
                                            <input type="text" class="form-control" id="bank_comment" v-model="user_form.bank_comment">
                                        </div>                                           
                                        <div class="col mb-3">
                                            <label for="st" class="form-label">ลำดับที่</label>
                                            <input type="text" class="form-control" id="st" v-model="user_form.st">
                                        </div>                                            
                                    </div>
                                    <div class="row">
                                        <button type="submit" class="btn btn-primary">บันทึก</button>
                                    </div>
                                </form>
                                <!-- {{user_form}} -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Button trigger modal user update form-->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#user_update_form" ref="show_modal_user_update_form" hidden>
                    Launch static backdrop modal
                </button>

                <!-- Modal view -->
                <div class="modal fade" id="user_update_form" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel">User</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ref="close_modal_user_update_form"></button>
                            </div>
                            <div class="modal-body">
                                <form @submit.prevent="user_update_save">
                                    
                                    <div class="row">
                                        <div class="col-3 mb-3">
                                            <label for="fname" class="form-label" >คำนำหน้าชื่อ</label>
                                            <!-- <input type="text" class="form-control" id="fneme" aria-describedby="emailHelp" v-model="user_form.fname" require> -->
                                            <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                                            <select class="form-select" aria-label="Default select example" v-model="user_form.fname">
                                                <option v-for="sf in sel_fname" :value="sf.name">{{sf.name}}</option>
                                            </select>
                                        </div>
                                        <div class="col mb-3">
                                            <label for="name_uf" class="form-label">ชื่อ</label>
                                            <input type="text" class="form-control" id="name_uf" v-model="user_form.name">
                                        </div>
                                        <div class="col mb-3">
                                            <label for="sname_uuf" class="form-label">สกุล</label>
                                            <input type="text" class="form-control" id="sname_uuf" v-model="user_form.sname">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="dep_uf" class="form-label" >ตำแหน่ง</label>
                                            <!-- <input type="text" class="form-control" id="dep" aria-describedby="emailHelp" v-model="user_form.dep"> -->
                                            <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                                            <select class="form-select" aria-label="Default select example" v-model="user_form.dep">
                                                <option v-for="sd in sel_dep" :value="sd.name">{{sd.name}}</option>
                                            </select>
                                        </div>
                                        <div class="col mb-3">
                                            <label for="workgroup_uf" class="form-label">กลุ่มงาน</label>
                                            <!-- <input type="text" class="form-control" id="workgroup" v-model="user_form.workgroup"> -->
                                            <select class="form-select" aria-label="Default select example" v-model="user_form.workgroup">
                                                <option v-for="sw in sel_workgroup" :value="sw.name">{{sw.name}}</option>
                                            </select>
                                        </div>
                                        <div class="col-3 mb-3">
                                            <label for="phone_uf" class="form-label">โทรศัพท์</label>
                                            <input type="text" class="form-control" id="phone_uf" v-model="user_form.phone">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col mb-3">
                                            <label for="bank_account_uf" class="form-label" >เลขที่บัญชี</label>
                                            <input type="text" class="form-control" id="bank_account_uf" aria-describedby="emailHelp" v-model="user_form.bank_account">
                                            <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                                        </div>
                                        <div class="col mb-3">
                                            <label for="bank_comment_uuf" class="form-label">ธนาคาร/สาขา</label>
                                            <input type="text" class="form-control" id="bank_comment_uff" v-model="user_form.bank_comment">
                                        </div>                                           
                                        <div class="col mb-3">
                                            <label for="st" class="form-label">ลำดับที่</label>
                                            <input type="text" class="form-control" id="st" v-model="user_form.st">
                                        </div>                                           
                                    </div>
                                    <div class="row">
                                        <button type="submit" class="btn btn-primary">บันทึก</button>
                                    </div>
                                </form>
                                <!-- {{user_form}} -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Button trigger modal user update form-->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#user_update_role_form" ref="show_modal_user_u_r_form" hidden>
                    Launch static backdrop modal
                </button>

                <!-- Modal view -->
                <div class="modal fade" id="user_update_role_form" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel">User</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ref="close_modal_user_u_r_form"></button>
                            </div>
                            <div class="modal-body">
                                <form @submit.prevent="user_update_role_save">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="username" class="form-label" >username</label>
                                            <input type="text" class="form-control" id="username" aria-describedby="emailHelp" v-model="user_role_form.username">
                                            <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                                        </div>
                                        <div class="col mb-3">
                                            <label for="exampleInputPassword1" class="form-label">Password</label>
                                            <input type="password" class="form-control" id="exampleInputPassword1" v-model="user_role_form.password">
                                        </div>
                                        <div class="col mb-3">
                                            <label for="exampleInputPassword2" class="form-label">RePassword</label>
                                            <input type="password" class="form-control" id="exampleInputPassword2" v-model="user_role_form.repassword">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <select class="form-select" aria-label="Default select example" v-model="user_role_form.role">
                                                <option value="1">MEMBER</option>
                                                <option value="9">ADMIN</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <button type="submit" class="btn btn-primary">บันทึก</button>
                                    </div>
                                </form>
                                <!-- {{user_form}} -->
                            </div>
                        </div>
                    </div>
                </div>

                 <!-- Button trigger modal user update form-->
                 <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal" ref="show_user_img" hidden>
                    Launch static backdrop modal
                </button>
                <!-- Modal -->
                <div  class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">

                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">{{user_img.title}}</h5>
                                
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ref="close_user_img"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row mb-3" >
                                    <div class="col-sm-12" >
                                        <img class="img-fluid" :src="user_img.img" alt="Photo" v-if="user_img.img">
                                    </div>
                                </div>
                                                
                                <form @submit="onUpload">
                                    <!-- <input type="file" name="file" id="file" @change="previewFiles"> -->
                                    <input type="hidden" name="pro_id" :value="user_img.id">
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="file" name="file" @change="onChangeInput()" ref="myFiles" :value="user_img.val">
                                            <label class="custom-file-label" for="exampleInputFile" > </label>
                                        </div>
                                        <div class="input-group-append">
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-bs-dismiss="modal" >Close</button>
                            </div>
                        </div>

                    </div>
                </div>
                
            </div>
            <?php require_once('../includes/_footer.php') ?>

        </div>
    </div>
    <script src="../../assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="../../assets/js/bootstrap.bundle.min.js"></script>

    <script src="../../assets/js/main.js"></script>
    <!--  -->
    <script src="../../node_modules/vue/dist/vue.global.js"></script>
    <script src="../../node_modules/vue/dist/vue.global.prod.js"></script>
    <script src="../../node_modules/axios/dist/axios.js"></script>
    <script src="../../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="./index_gdms.js"></script>
</body>

</html>