<?php 

require_once('../../server/authen.php'); 
$sql = 'SELECT u.id, p.name, p.name, u.username, p.img, u.role, u.status, u.created_at 
        FROM user as u 
        INNER JOIN `profile` as p ON p.user_id = u.id 
        WHERE u.id NOT IN ( 1 )';
$stmt = $conn->prepare($sql);
$stmt->execute();
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
    <!-- dataTables.bootstrap5 -->
    <link rel="stylesheet" href="../../node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <!-- responsive.bootstrap5 -->
    <link rel="stylesheet" href="../../node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css">
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
                                        Admin Lists                                  
                                    </h5>
                                    <div class="card-body">
                                        <table id="table" class="table mb-3">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>image</th>
                                                    <th>firstname</th>
                                                    <th>lastname</th>
                                                    <th>username</th>
                                                    <th>role</th>
                                                    <th>status</th>
                                                    <th>Start date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                    $i = 0;
                                                    while($row = $stmt->fetch(PDO::FETCH_OBJ)){ 
                                                    $i++;
                                                ?>
                                                <tr>
                                                    <td><?php echo $i; ?></td>
                                                    <td><img src="../../assets/images/uploads/<?php echo $row->img ?: 'avatar.png'; ?>" class="img-fluid" width="50px" alt="appzstory"> </td>
                                                    <td><?php echo $row->name; ?></td>
                                                    <td><?php echo $row->sname; ?></td>
                                                    <td><?php echo $row->username; ?></td>
                                                    <td><span class="badge bg-primary"> <?php echo $row->role; ?></span></td>
                                                    <td>
                                                        <input class="toggle-event" type="checkbox" name="status" <?php echo $row->status == '10' ? 'checked':'' ; ?> 
                                                        data-id="<?php echo $row->id; ?>" data-toggle="toggle" data-on="active" data-off="block" 
                                                        data-onstyle="success" data-style="ios" data-size="sm">
                                                    </td>
                                                    <td><?php echo DateThai($row->created_at); ?></td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="form-edit.php?u_id=<?php echo $row->id; ?>"  class="btn btn-warning text-white">
                                                                <i class='bx bxs-edit-alt'></i> 
                                                            </a>
                                                            <a href="../../server/account/delete.php?u_id=<?php echo $row->u_id; ?>" onclick="return confirm('Are you sure you want to delete?')" class="btn btn-danger text-white">
                                                                <i class='bx bxs-trash' ></i> 
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
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

    <!-- jquery.dataTables -->
    <script src="../../node_modules/datatables.net/js/jquery.dataTables.min.js"></script>
    <!-- dataTables.bootstrap5 -->
	<script src="../../node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <!-- dataTables.responsive -->
    <script src="../../node_modules/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <!-- bootstrap4-toggle -->
    <script src="../../node_modules/bootstrap4-toggle/js/bootstrap4-toggle.min.js"></script>

    <script>
        $('#table').DataTable({
            pageLength : 5, // กำหนดจำนวนแถวที่จะแสดงใน table
            lengthMenu: [5, 10, 20, 50], //เปลี่ยนแปลงค่าตัวเลือกที่จะให้แสดงจำนวนแถว
            fnDrawCallback: function() {
                /** เรียกใช้งาน bootstrapToggle เมื่อมีการเปลี่ยนหน้าใหม่ */
                $('.toggle-event').bootstrapToggle()
                $('.toggle-event').change(function(){
                    $.ajax({
                        method: "POST",
                        url: "../../server/account/active.php",
                        data: { 
                            id: $(this).data('id'), 
                            value: $(this).is(':checked') 
                        }
                    })
                    .done(function( resp, status, xhr) {
                        setTimeout(() => {
                            alert('แก้ไขข้อมูลสำเร็จ')
                        }, 300);
                    })
                    .fail(function ( xhr, status, error) { 
                        alert('ไม่สามารถแก้ไขข้อมูลได้')
                    })
                })
            },
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal( {
                        header: function ( row ) {
                            var data = row.data()
                            return 'ผู้ใช้งาน: ' + data[1]
                        }
                    }),
                    renderer: $.fn.dataTable.Responsive.renderer.tableAll( {
                        tableClass: 'table'
                    })
                }
            }
        })
    </script>
</body>
</html>



