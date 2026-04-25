<ul class="nav nav-pills flex-column flex-md-row mb-3 d-print-none">
    <li class="nav-item">
        <a class="nav-link <?php echo isActiveFile('profile') ?>" href="../account/profile.php"><i class="bx bx-user me-1"></i> My Profile </a>
    </li>
    <?php if($_SESSION['AD_ROLE'] == "superadmin"){  ?>
    <li class="nav-item">
        <a class="nav-link <?php echo pathCurrent() == 'account/index' || pathCurrent() == 'account/form-edit' ? 'active': '' ?>" href="../account/"><i class="bx bx-user me-1"></i> Admin </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo pathCurrent() == 'account/form-create' ? 'active': '' ?>" href="../account/form-create.php"><i class="bx bx-user-plus me-1"></i> New Admin </a>
    </li>
    <?php } ?>
</ul>