<?php 

require_once('../../server/authen.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" >
    <title>Account | App</title>
    <link rel="shortcut icon" type="image/x-icon" href="../../assets/images/uploads/icon.ico">
    <link rel="stylesheet" href="../../assets/vendor/fonts/boxicons.css" >
    <link rel="stylesheet" href="../../assets/vendor/css/core.css" class="template-customizer-core-css" >
    <link rel="stylesheet" href="../../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" >
    <link rel="stylesheet" href="../../assets/css/demo.css" >
    <!-- Helpers -->
    <script src="../../assets/vendor/js/helpers.js"></script>
    <script src="../../assets/js/config.js"></script>
</head>
<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php require_once('../includes/_sidebar.php') ?>
            <div class="layout-page">
                <?php require_once('../includes/_navbar.php') ?>
                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row">
                            <div class="col-md-12">
                                <?php require_once('../includes/nav-account.php') ?>
                                <div class="card mb-4">
                                    <h5 class="card-header">
                                        Create New Admin                                   
                                    </h5>
                                    <div class="card-body">
                                        <form method="post" action="../../server/account/create.php" enctype="multipart/form-data">
                                            <div class="row mb-4">
                                                <label class="col-sm-2 col-xxl-1 col-form-label" for="firstname">firstname</label>
                                                <div class="col-sm-10 col-xxl-11">
                                                    <div class="input-group input-group-merge">
                                                        <span id="firstname" class="input-group-text"><i class="bx bx-user"></i></span>
                                                        <input type="text" class="form-control" id="firstname" name="firstname" placeholder="firstname" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-4">
                                                <label class="col-sm-2 col-xxl-1 col-form-label" for="lastname">lastname</label>
                                                <div class="col-sm-10 col-xxl-11">
                                                    <div class="input-group input-group-merge">
                                                        <span id="lastname" class="input-group-text"><i class="bx bx-user"></i></span>
                                                        <input type="text" class="form-control" id="lastname" name="lastname" placeholder="lastname" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-4">
                                                <label class="col-sm-2 col-xxl-1 col-form-label" for="username">username</label>
                                                <div class="col-sm-10 col-xxl-11">
                                                    <div class="input-group input-group-merge">
                                                        <span id="username" class="input-group-text"><i class="bx bxs-user-pin"></i></span>
                                                        <input type="text" class="form-control" id="username" name="username" placeholder="username" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-4">
                                                <label class="col-sm-2 col-xxl-1 col-form-label" for="password">password</label>
                                                <div class="col-sm-10 col-xxl-11">
                                                    <div class="input-group input-group-merge">
                                                        <span id="password2" class="input-group-text"><i class="bx bx-key"></i></i></span>
                                                        <input type="password" class="form-control" id="password" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-4">
                                                <label class="col-sm-2 col-xxl-1 col-form-label" for="role">role</label>
                                                <div class="col-sm-10 col-xxl-11">
                                                    <select class="form-control" name="role" id="role" required>
                                                        <option value disabled selected>Select Role</option>
                                                        <option value="superadmin">Super Admin</option>
                                                        <option value="admin">Admin</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-4">
                                                <label class="col-sm-2 col-xxl-1 col-form-label" for="permission">Image Profile</label>
                                                <div class="col-sm-10 col-xxl-11">
                                                    <div class="form-group">
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input" name="file" accept="image/*">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row justify-content-end">
                                                <div class="col-sm-10 col-xxl-11">
                                                    <input type="submit" name="submit" class="btn btn-primary" value="Submit">
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php require_once('../includes/_footer.php') ?>
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>

    <script src="../../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../../assets/vendor/libs/popper/popper.js"></script>
    <script src="../../assets/vendor/js/bootstrap.js"></script>
    <script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../../assets/vendor/js/menu.js"></script>
    <script src="../../assets/vendor/libs/apex-charts/apexcharts.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/dashboards-analytics.js"></script>
    <script>
		if ( window.history.replaceState ) {
			window.history.replaceState( null, null, window.location.href );
		}
	</script>

</body>
</html>



