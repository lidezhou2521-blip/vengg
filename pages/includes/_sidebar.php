<div id="sidebar" class="active d-print-none">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <a href="#" class="fs-5"> <i class="bi bi-person-circle"></i> <?=$_SESSION['AD_FIRSTNAME'] .' '. $_SESSION['AD_LASTNAME']?></a>
                    <a href="../logout.php" onclick="return confirm('ต้องการออกจากระบบ..')" class="btn btn-danger btn-sm">logout</a>
                </div>
                <div class="toggler">
                    <a class="sidebar-hide d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Menu</li>

                <li class="sidebar-item <?php echo isActive('dashboard') ?>">
                    <a href="../dashboard/" class='sidebar-link'>
                        <i class="bi bi-grid-fill"></i>
                        <span>หน้าแรก</span>
                    </a>
                </li>
                <li class="sidebar-item <?php echo isActive('history') ?>">
                    <a href="../history/" class='sidebar-link'>
                        <i class="bi bi-grid-fill"></i>
                        <span>ประวัติการเปลี่ยน</span>
                    </a>
                </li>
                <?php if($_SESSION['AD_ROLE'] == '9'){ ?>
                    <li class="sidebar-item  has-sub <?php echo isActive('asu') ?>">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-stack"></i>
                            <span>อำนวยการ</span>
                        </a>
                        <ul class="submenu <?php echo isActive('asu') ?>">                        
                            <li class="submenu-item <?php echo isActiveFile('ven_approve') ?>">
                                <a href="../asu/ven_approve.php">
                                    <i class="bi bi-arrow-left-right"></i>
                                    <span>อนุมัติใบเปลี่ยนเวร</span>
                                </a>
                            </li>                        
                            <li class="submenu-item <?php echo isActiveFile('report') ?>">
                            <a href="../asu/report.php">
                                <i class="bi bi-clipboard-check"></i>
                                <span>รายงานการจัดเวร</span>
                            </a>
                        </li>                        
                        <li class="submenu-item <?php echo isActiveFile('ven_set') ?>">
                            <a href="../asu/ven_set.php" target="_blank">จัดเวร</a>
                        </li>
                        <li class="submenu-item <?php echo isActiveFile('ven_quick') ?>">
                            <a href="../asu/ven_quick.php">⚡ จัดเวรอย่างรวดเร็ว</a>
                        </li>
                        <li class="submenu-item <?php echo isActiveFile('ven_com') ?>">
                            <a href="../asu/ven_com.php">เพิ่มคำสั่ง</a>
                        </li>
                        <li class="submenu-item <?php echo isActiveFile('ven_user') ?>">
                            <a href="../asu/ven_user.php">เตรียม-ผู้อยู่เวร</a>
                        </li>
                        <li class="submenu-item <?php echo isActiveFile('work_name') ?>">
                            <a href="../asu/work_name.php">เตรียม-เวร/กลุ่มหน้าที่</a>
                        </li>
                        <li class="submenu-item <?php echo isActiveFile('holiday') ?>">
                            <a href="../asu/holiday.php">📅 วันหยุดราชการ</a>
                        </li>
                        
                    </ul>
                </li>
                <li class="sidebar-item <?php echo isActive('fnu') ?>">
                    <a href="../fnu/ven/" class='sidebar-link' target="_blank">
                        <i class="bi bi-cash"></i>
                        <span>การเงิน</span>
                    </a>
                </li>    
                
                <li class="sidebar-item  has-sub <?php echo isActive('users') ?>">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-stack"></i>
                        <span>admin</span>
                    </a>
                    <ul class="submenu <?php echo isActive('users') ?>">
                        <?php if($_SESSION['LOGIN_BY'] == 'gdms'){ ?>
                            <li class="submenu-item <?php echo isActive('users') ?>">
                                <a href="../users/index_gdms.php">จัดการสมาชิก</a>
                            </li>
                        <?php }else{ ?>
                            <li class="submenu-item <?php echo isActive('users') ?>">
                            <a href="../users/">จัดการสมาชิก</a>
                        </li>
                        <?php } ?>
                        <li class="submenu-item <?php echo isActiveFile('line') ?>">
                            <a href="../users/line.php">ตั่งค่า Line</a>
                        </li>
                        <li class="submenu-item <?php echo isActiveFile('fname') ?>">
                            <a href="../users/fname.php">คำนำหน้าชื่อ</a>
                        </li>
                        <li class="submenu-item <?php echo isActiveFile('dep') ?>">
                            <a href="../users/dep.php">ตำแหน่ง</a>
                        </li>
                        <li class="submenu-item <?php echo isActiveFile('group') ?>">
                            <a href="../users/group.php">กลุ่มงาน</a>
                        </li>
                        
                        
                    </ul>
                </li>
                <?php } ?>
                
                
            </ul>
        </div>
        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
</div>

