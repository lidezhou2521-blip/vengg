<?php 

require_once('../../server/authen.php');
$sqlUser = 'SELECT u.id, p.name, p.sname, u.username, p.img, u.role, u.status 
            FROM user as u
            INNER JOIN `profile` as p ON p.user_id = u.id
            WHERE u.id = ?
            LIMIT 1';
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->execute([cleanData($_SESSION['AD_ID'])]);
$rowUser = $stmtUser->fetch(PDO::FETCH_OBJ);
if(!$stmtUser->rowCount()){
    header('Location: ../../login.php');
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
                                    <h5 class="card-header">Profile Details</h5>
                                    <div class="card-body">
                                        <form action="../../server/account/upload-image.php" method="POST" enctype="multipart/form-data">
                                            <div class="d-flex align-items-start align-items-sm-center gap-4">
                                                <img src="../../assets/images/uploads/<?php echo $rowUser->image ?:  'avatar.png'; ?>" class="d-block rounded" height="100" width="100" id="uploadedAvatar">
                                                <div class="button-wrapper">
                                                    <label for="upload" class="btn btn-primary me-2 mb-2" tabindex="0">
                                                        <span class="d-none d-sm-block">Upload new photo</span>
                                                        <i class="bx bx-upload d-block d-sm-none"></i>
                                                        <input
                                                            type="file"
                                                            id="upload"
                                                            name="file"
                                                            class="account-file-input"
                                                            hidden
                                                            accept="image/png, image/jpeg"
                                                        >
                                                        <input type="hidden" name="image" value="<?php echo $rowUser->image; ?>">
                                                    </label>
                                                    <div class="btn-group d-none" id="group-action">
                                                        <button type="submit" class="btn btn-success btn-sm d-inline" name="upload-image"> save </button>
                                                        <button type="button" class="btn btn-danger btn-sm d-inline" onClick="window.location.reload();"> cancel </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <hr class="my-0" >
                                    <div class="card-body">
                                        <form  method="POST" action="../../server/account/update-profile.php">
                                            <div class="row">
                                                <div class="row mb-4">
                                                    <label class="col-sm-2 col-xxl-1 col-form-label" for="firstname">firstname</label>
                                                    <div class="col-sm-10 col-xxl-11">
                                                        <div class="input-group input-group-merge">
                                                            <span id="firstname" class="input-group-text"><i class="bx bxs-user-pin"></i></span>
                                                            <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo $rowUser->firstname; ?>" placeholder="firstname" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-4">
                                                    <label class="col-sm-2 col-xxl-1 col-form-label" for="lastname">lastname</label>
                                                    <div class="col-sm-10 col-xxl-11">
                                                        <div class="input-group input-group-merge">
                                                            <span id="lastname" class="input-group-text"><i class="bx bxs-user-pin"></i></span>
                                                            <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $rowUser->lastname; ?>" placeholder="lastname" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-4">
                                                    <label class="col-sm-2 col-xxl-1 col-form-label" for="username">username</label>
                                                    <div class="col-sm-10 col-xxl-11">
                                                        <div class="input-group input-group-merge">
                                                            <p class="pt-1">
                                                                <span class="badge bg-primary"> <?php echo $rowUser->username; ?> </span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-4">
                                                    <label class="col-sm-2 col-xxl-1 col-form-label" for="role">role</label>
                                                    <div class="col-sm-10 col-xxl-11">
                                                        <div class="input-group input-group-merge">
                                                            <p class="pt-1">
                                                                <span class="badge bg-primary"> <?php echo $rowUser->role; ?> </span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-4">
                                                    <label class="col-sm-2 col-xxl-1 col-form-label" for="role">status</label>
                                                    <div class="col-sm-10 col-xxl-11">
                                                        <?php echo $rowUser->status == '10' ? '<span class="badge bg-success"> Active </span>':'<span class="badge bg-danger"> Block </span>' ; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <button type="submit" class="btn btn-primary me-2" name="profile">Save changes</button>
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
    <script src="../../assets/vendor/js/menu.js"></script>
    <script src="../../assets/js/main.js"></script>

    <script>
        $('.account-file-input').on('change', function(){
            if (this.files[0]) {
                var reader = new FileReader()
                $('#group-action').removeClass('d-none')
                $('#group-action').addClass('d-block')
                reader.onload = function (e) {
                    $('#uploadedAvatar').attr('src', e.target.result)
                }
                reader.readAsDataURL(this.files[0])
            }
        })
    </script>
</body>
</html>



