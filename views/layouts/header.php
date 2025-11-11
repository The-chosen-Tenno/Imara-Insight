<?php
include __DIR__ . '/../../config.php';
include BASE_PATH . '/helpers/AppManager.php';

$sm = AppManager::getSM();

$requireAuth = $requireAuth ?? true;

$userId     = $sm->getAttribute("userId");
$username   = $sm->getAttribute("userName");
$fullName   = $sm->getAttribute("fullName");
$permission = $sm->getAttribute("role");
$photo      = $sm->getAttribute("userPhoto");

if ($requireAuth && !$userId) {
    header("Location: " . url("views/auth/login.php"));
    exit();
}

$currentUrl      = $_SERVER['SCRIPT_NAME'];
$currentFilename = basename($currentUrl);
?>

<!DOCTYPE html>
<html
    lang="en"
    class="light-style layout-menu-fixed"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="assets/"
    data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Imara - Insight</title>

    <meta name="description" content="" />
    <meta name="domain" content="<?= current_domain() ?>" />

    <link rel="icon" type="image/x-icon" href="<?= asset('assets/img/favicon/favicon.png') ?>" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="<?= asset('assets/vendor/fonts/boxicons.css') ?>" />
    <link rel="stylesheet" href="<?= asset('assets/vendor/css/core.css') ?>" class="template-customizer-core-css" />
    <link rel="stylesheet" href="<?= asset('assets/vendor/css/theme-default.css') ?>" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="<?= asset('assets/css/demo.css') ?>" />
    <link rel="stylesheet" href="<?= asset('assets/css/Style.css') ?>" />
    <link rel="stylesheet" href="<?= asset('assets/css/Authorization.css') ?>" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- data table-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?= asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') ?>" />
    <link rel="stylesheet" href="<?= asset('assets/vendor/libs/apex-charts/apex-charts.css') ?>" />

    <script src="<?= asset('assets/vendor/js/helpers.js') ?>"></script>
    <script src="<?= asset('assets/js/config.js') ?>"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Sidebar -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme p-3">
                <div class="app-brand mb-4 text-center">
                    <a href="<?= url('/views/system/dashboard.php') ?>" class="d-flex flex-column align-items-center text-decoration-none">
                        <img src="<?= asset('assets/img/favicon/favicon.png') ?>" alt="icon" width="50" height="50" class="mb-2">
                        <span class="fw-bold fs-5 text-capitalize">Imara-Insight</span>
                    </a>
                </div>
                <div class="d-flex flex-column gap-1">
                    <a href="<?= url('views/system/dashboard.php') ?>" class="d-flex align-items-center p-2 rounded <?= $currentFilename === "dashboard.php" ? 'bg-light fw-bold text-primary' : 'text-dark hover-bg-light' ?>">
                        <i class="bx bx-home-alt me-2"></i> Dashboard
                    </a>
                    <a href="<?= url('views/system/Logs.php') ?>" class="d-flex align-items-center p-2 rounded <?= $currentFilename === "Logs.php" ? 'bg-light fw-bold text-primary' : 'text-dark hover-bg-light' ?>">
                        <i class="bx bxs-coin-stack me-2"></i>Project Logs
                    </a>

                    <a href="<?= url('views/system/LeaveRequest.php') ?>" class="d-flex align-items-center p-2 rounded <?= $currentFilename === "LeaveRequest.php" ? 'bg-light fw-bold text-primary' : 'text-dark hover-bg-light' ?>">
                        <i class="bx bxs-calendar-x me-2"></i> Request Leave
                    </a>
                    <?php if ($permission === "admin") : ?>
                        <a href="<?= url('views/system/LeaveApprove.php') ?>" class="d-flex align-items-center p-2 rounded <?= $currentFilename === "LeaveApprove.php" ? 'bg-light fw-bold text-primary' : 'text-dark hover-bg-light' ?>">
                            <i class="bx bx-calendar-check me-2"></i> Leave Approvals
                        </a>
                    <?php endif; ?>
                    <a href="<?= url('views/system/users.php') ?>" class="d-flex align-items-center p-2 rounded <?= $currentFilename === "users.php" ? 'bg-light fw-bold text-primary' : 'text-dark hover-bg-light' ?>">
                        <i class="bx bx-user me-2"></i> Employees
                    </a>
                    <?php if ($permission === "admin") : ?>
                        <a href="<?= url('views/system/Register.php') ?>" class="d-flex align-items-center p-2 rounded <?= $Register === "Register.php" ? 'bg-light fw-bold text-primary' : 'text-dark hover-bg-light' ?>">
                            <i class="bx bx-user-check me-2"></i> Register
                        </a>
                    <?php endif; ?>
                </div>
            </aside>
            <!-- /Sidebar -->

            <!-- Layout page -->
            <div class="layout-page">
                <!-- Navbar -->
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- User Dropdown -->
                            <!-- User Dropdown -->
                            <div class="dropdown ms-auto">
                                <a href="#" class="d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown">
                                    <img src="<?= $photo ? url($photo) : url('assets/img/illustrations/default-profile-picture.png') ?>"
                                        alt="<?= $username ?>" class="rounded-circle" width="40" height="40">
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center" href="<?= url('views/system/profile.php') ?>">
                                            <img src="<?= $photo ? url($photo) : url('assets/img/illustrations/default-profile-picture.png') ?>"
                                                alt="<?= $username ?>" class="rounded-circle me-2" width="35" height="35">
                                            <div>
                                                <div class="fw-semibold"><?= $username ?></div>
                                                <small class="text-muted text-capitalize"><?= $permission ?></small>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center" href="../personalportfolio.php?id=<?= $userId ?>" target="_blank">
                                            <i class="bx bx-briefcase me-2"></i> Portfolio
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="bx bx-power-off me-2"></i> Logout
                                            <form id="logout-form" action="<?= url('services/logout.php') ?>" method="POST" class="d-none"></form>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </ul>
                    </div>
                </nav>
</body>