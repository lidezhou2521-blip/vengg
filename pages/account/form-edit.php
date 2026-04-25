<?php 

require_once('../../server/authen.php');
if (isset($_GET['u_id'])) {
    $sqlUser = 'SELECT u_id, firstname, lastname, role, status FROM users WHERE u_id = :u_id AND u_id NOT IN ( 1 ) LIMIT 1';
    $stmtUser = $conn->prepare($sqlUser);
    $stmtUser->execute(array( 'u_id' => cleanData($_GET['u_id']) ));
    $rowUser = $stmtUser->fetch(PDO::FETCH_ASSOC);
    if(!$stmtUser->rowCount()){
        header('Location: admin.php');
    }
} else {
    header('Location: admin.php');
}
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
    <!-- bootstrap4-toggle -->
    <link rel="stylesheet" href="../../node_modules/bootstrap4-toggle/css/bootstrap4-toggle.min.css">

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
                                        Edit Admin                                   
                                    </h5>
                                    <div class="card-body">
                                        <form method="post" action="../../server/account/update.php">
                                            <div class="row mb-4">
                                                <label class="col-sm-2 col-xxl-1 col-form-label" for="firstname">firstname</label>
                                                <div class="col-sm-10 col-xxl-11">
                                                    <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo $rowUser['firstname']; ?>" placeholder="firstname" required>
                                                </div>
                                            </div>
                                            <div class="row mb-4">
                                                <label class="col-sm-2 col-xxl-1 col-form-label" for="lastname">lastname</label>
                                                <div class="col-sm-10 col-xxl-11">
                                                    <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $rowUser['lastname']; ?>" placeholder="lastname" required>
                                                </div>
                                            </div>
                                            <div class="row mb-4">
                                                <label class="col-sm-2 col-xxl-1 col-form-label" for="role">role</label>
                                                <div class="col-sm-10 col-xxl-11">
                                                    <select class="form-control" name="role" id="role" required>
                                                        <option value="superadmin" <?php echo $rowUser['role'] == 'superadmin' ? 'selected' : '';  ?>>Super Admin</option>
                                                        <option value="admin" <?php echo $rowUser['role'] == 'admin' ? 'selected' : '';  ?>>Admin</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-4">
                                                <label class="col-sm-2 col-xxl-1 col-form-label" for="role">status</label>
                                                <div class="col-sm-10 col-xxl-11">
                                                    <input class="toggle-event" type="checkbox" name="status" <?php echo $rowUser['status'] == 'true' ? 'checked':'' ; ?> data-toggle="toggle" data-on="active" data-off="block" 
                                                        data-onstyle="success" data-style="ios" data-size="sm">
                                                </div>
                                            </div>
                                            <input type="hidden" name="u_id" value="<?php echo $_GET['u_id']; ?>" required>
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
    <!-- bootstrap4-toggle -->
    <script src="../../node_modules/bootstrap4-toggle/js/bootstrap4-toggle.min.js"></script>
    <script>
        $('.toggle-event').bootstrapToggle()
		if ( window.history.replaceState ) {
			window.history.replaceState( null, null, window.location.href );
		}
    </script>
</body>
</html>



