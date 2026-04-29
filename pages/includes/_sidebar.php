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

                <li class="sidebar-item <?php echo isActiveFile('index') && isActive('dashboard') ? 'active' : '' ?>">
                    <a href="../dashboard/" class='sidebar-link'>
                        <i class="bi bi-calendar-event"></i>
                        <span>ปฏิทินเวร</span>
                    </a>
                </li>
                <li class="sidebar-item <?php echo isActiveFile('list') ?>">
                    <a href="../dashboard/list.php" class='sidebar-link'>
                        <i class="bi bi-list-ul"></i>
                        <span>รายการเวร</span>
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
                                    <i class="bi bi-arrow-left-right me-2"></i>
                                    <span>อนุมัติใบเปลี่ยนเวร</span>
                                </a>
                            </li>                        
                            <li class="submenu-item <?php echo isActiveFile('report') ?>">
                            <a href="../asu/report.php">
                                <i class="bi bi-clipboard-check me-2"></i>
                                <span>รายงานการจัดเวร</span>
                            </a>
                        </li>                        
                        <li class="submenu-item <?php echo isActiveFile('ven_set') ?>">
                            <a href="../asu/ven_set.php" target="_blank">
                                <i class="bi bi-calendar-plus me-2"></i>
                                <span>จัดเวร</span>
                            </a>
                        </li>
                        <li class="submenu-item <?php echo isActiveFile('ven_quick') ?>">
                            <a href="../asu/ven_quick.php">
                                <i class="bi bi-lightning-fill me-2"></i>
                                <span>จัดเวรอย่างรวดเร็ว</span>
                            </a>
                        </li>
                        <li class="submenu-item <?php echo isActiveFile('ven_com') ?>">
                            <a href="../asu/ven_com.php">
                                <i class="bi bi-file-earmark-plus me-2"></i>
                                <span>เพิ่มคำสั่ง</span>
                            </a>
                        </li>
                        <li class="submenu-item <?php echo isActiveFile('ven_user') ?>">
                            <a href="../asu/ven_user.php">
                                <i class="bi bi-person-lines-fill me-2"></i>
                                <span>เตรียม-ผู้อยู่เวร</span>
                            </a>
                        </li>
                        <li class="submenu-item <?php echo isActiveFile('work_name') ?>">
                            <a href="../asu/work_name.php">
                                <i class="bi bi-briefcase me-2"></i>
                                <span>เตรียม-เวร/กลุ่มหน้าที่</span>
                            </a>
                        </li>
                        <li class="submenu-item <?php echo isActiveFile('holiday') ?>">
                            <a href="../asu/holiday.php">
                                <i class="bi bi-calendar-date me-2"></i>
                                <span>วันหยุดราชการ</span>
                            </a>
                        </li>
                        
                    </ul>
                </li>
                <li class="sidebar-item has-sub <?php echo isActive('fnu') ?>">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-cash"></i>
                        <span>การเงิน</span>
                    </a>
                    <ul class="submenu <?php echo isActive('fnu') ?>">
                        <li class="submenu-item <?php echo isActiveFile('index') ?>">
                            <a href="../fnu/index.php">
                                <i class="bi bi-cash-stack me-2"></i>
                                <span>หน้าหลักการเงิน</span>
                            </a>
                        </li>
                        <li class="submenu-item <?php echo isActiveFile('report-check-overlap') ?>">
                            <a href="../fnu/report-check-overlap.php">
                                <i class="bi bi-calendar-range me-2"></i>
                                <span>เช็คเวรชน</span>
                            </a>
                        </li>
                    </ul>
                </li>    
                
                <li class="sidebar-item  has-sub <?php echo isActive('users') ?>">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-shield-lock"></i>
                        <span>admin</span>
                    </a>
                    <ul class="submenu <?php echo isActive('users') ?>">
                        <?php if($_SESSION['LOGIN_BY'] == 'gdms'){ ?>
                            <li class="submenu-item <?php echo isActive('users') ?>">
                                <a href="../users/index_gdms.php">
                                    <i class="bi bi-people me-2"></i>
                                    <span>จัดการสมาชิก</span>
                                </a>
                            </li>
                        <?php }else{ ?>
                            <li class="submenu-item <?php echo isActive('users') ?>">
                                <a href="../users/">
                                    <i class="bi bi-people me-2"></i>
                                    <span>จัดการสมาชิก</span>
                                </a>
                            </li>
                        <?php } ?>
                        <li class="submenu-item <?php echo isActiveFile('line') ?>">
                            <a href="../users/line.php">
                                <i class="bi bi-line text-success me-2"></i>
                                <span>ตั่งค่า Line</span>
                            </a>
                        </li>
                        <li class="submenu-item <?php echo isActiveFile('fname') ?>">
                            <a href="../users/fname.php">
                                <i class="bi bi-person-badge me-2"></i>
                                <span>คำนำหน้าชื่อ</span>
                            </a>
                        </li>
                        <li class="submenu-item <?php echo isActiveFile('dep') ?>">
                            <a href="../users/dep.php">
                                <i class="bi bi-person-vcard me-2"></i>
                                <span>ตำแหน่ง</span>
                            </a>
                        </li>
                        <li class="submenu-item <?php echo isActiveFile('group') ?>">
                            <a href="../users/group.php">
                                <i class="bi bi-diagram-3 me-2"></i>
                                <span>กลุ่มงาน</span>
                            </a>
                        </li>
                        <li class="submenu-item <?php echo isActiveFile('gcal_settings') ?>">
                            <a href="../users/gcal_settings.php">
                                <i class="bi bi-calendar-check text-warning me-2"></i>
                                <span>ตั้งค่า Google Calendar</span>
                            </a>
                        </li>
                        
                        
                    </ul>
                </li>
                <?php } ?>
                
                
            </ul>
        </div>
        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
</div>

