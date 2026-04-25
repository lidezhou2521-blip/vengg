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
                <h3>เตรียมคนอยู่เวร</h3>
            </div>
            <div class="page-content" id="asuIndex" v-cloak>
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <div class="row">
                            <div class="col col-4">                                
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">ผู้พิพากษา</h5>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item font-monospace" >นายพเยาว์ สนพลาย ลบ</li>
                                            <li class="list-group-item" v-for="n in 10">{{ n }} A second item</li>
                                            <li class="list-group-item"><button class="btn btn-success">เพิ่ม</button></li>
                                        </ul>
                                            
                                    </div>
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
    
    <!-- <script src="../../assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script> -->
    <!--  -->
    <script src="../../node_modules/vue/dist/vue.global.js"></script>
    <script src="../../node_modules/vue/dist/vue.global.prod.js"></script>
    <script src="../../node_modules/axios/dist/axios.js"></script>
    <script src="../../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="./js/index.js"></script>
</body>

</html>