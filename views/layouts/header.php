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
    <!-- Json -->
    <?php
    $styleMap = json_decode(file_get_contents(('../../assets/json/color_pattern.json')), true);
    ?>
</head>

<body data-menu-collapsible="true">
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Sidebar -->
            <aside id="layout-menu" class="layout-menu menu-vertical   menu bg-menu-theme p-3">
                <div class="app-brand mb-4 text-center">
                    <a href="<?= url('/views/system/dashboard.php') ?>" class="d-flex flex-column align-items-center text-decoration-none">
                        <img src="<?= asset('assets/img/favicon/favicon.png') ?>" alt="icon" width="50" height="50" class="mb-2">
                        <span class="fw-bold fs-5 text-capitalize">Imara-Insight</span>
                    </a>
                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto" style="<?= $styleMap['bg-imara-purple'] ?>">
                        <i class="bx bx-chevron-left d-block d-xl-none align-middle"></i>
                    </a>
                </div>
                <div class="d-flex flex-column gap-1 menu-inner">
                    <?php
                    $menuItems = [
                        ['label' => 'Dashboard', 'url' => 'views/system/dashboard.php', 'icon' => 'bx bx-home-alt', 'bg' => 'bg-imara-yellow-light', 'color' => 'imara-purple'],
                        ['label' => 'Project Logs', 'url' => 'views/system/Logs.php', 'icon' => 'bx bxs-coin-stack', 'bg' => 'bg-imara-yellow-light', 'color' => 'imara-purple'],
                        ['label' => 'Project Logs (New)', 'url' => 'views/system/DraggableLogsTest.php', 'icon' => 'bx bxs-coin-stack', 'bg' => 'bg-imara-yellow-light', 'color' => 'imara-purple'],
                        ['label' => 'Request Leave', 'url' => 'views/system/LeaveRequest.php', 'icon' => 'bx bxs-calendar-x', 'bg' => 'bg-imara-yellow-light', 'color' => 'imara-purple'],
                        ['label' => 'Leave Approvals', 'url' => 'views/system/LeaveApprove.php', 'icon' => 'bx bx-calendar-check', 'permission' => 'admin', 'bg' => 'bg-imara-yellow-light', 'color' => 'imara-purple'],
                        ['label' => 'Employees', 'url' => 'views/system/users.php', 'icon' => 'bx bx-user', 'bg' => 'bg-imara-yellow-light', 'color' => 'imara-purple'],
                        ['label' => 'Register', 'url' => 'views/system/Register.php', 'icon' => 'bx bx-user-check', 'permission' => 'admin', 'bg' => 'bg-imara-yellow-light', 'color' => 'imara-purple'],
                    ];
                    foreach ($menuItems as $item) {
                        if (isset($item['permission']) && $item['permission'] !== $permission) continue;
                        $active = basename($item['url']) === $currentFilename;
                        $classes = 'd-flex align-items-center p-2 rounded ' . ($active ? 'fw-bold' : 'text-dark');
                        $bgStyle = ($active && isset($item['bg'])) ? $styleMap[$item['bg']] : '';
                        $colorStyle = ($active && isset($item['color'])) ? $styleMap[$item['color']] : '';
                        $hoverColor = $styleMap['imara-purple'];
                    ?>
                        <a href="<?= url($item['url']) ?>"
                            class="<?= $classes ?>"
                            style="<?= $bgStyle ?>"
                            onmouseover="this.querySelector('span').style.cssText='<?= $hoverColor ?>'; this.querySelector('i').style.cssText='<?= $hoverColor ?>';"
                            onmouseout="this.querySelector('span').style.cssText='<?= $colorStyle ?>'; this.querySelector('i').style.cssText='<?= $colorStyle ?>';">
                            <i class="<?= $item['icon'] ?> me-2" style="<?= $colorStyle ?>"></i>
                            <span style="<?= $colorStyle ?>"><?= $item['label'] ?></span>
                        </a>
                    <?php
                    }
                    ?>
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
                            <div class="dropdown ms-auto">
                                <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
                                    <!-- <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                        <span class="position-relative">
                                            <i class="bx bx-bell" style="font-size: 24px; <?= $styleMap['imara-yellow'] ?>"></i>
                                            <span class="badge rounded-pill bg-danger badge-dot badge-notifications border"></span>
                                        </span>
                                    </a> -->
                                    <ul class="dropdown-menu dropdown-menu-end p-0">
                                        <li class="dropdown-menu-header border-bottom">
                                            <div class="dropdown-header d-flex align-items-center py-3">
                                                <h6 class="mb-0 me-auto">Notification</h6>
                                                <div class="d-flex align-items-center h6 mb-0">
                                                    <span class="badge bg-label-primary me-2">8 New</span>
                                                    <a href="javascript:void(0)" class="dropdown-notifications-all p-2" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Mark all as read" data-bs-original-title="Mark all as read"><i class="icon-base bx bx-envelope-open text-heading"></i></a>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="dropdown-notifications-list scrollable-container ps">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0 me-3">
                                                            <div class="avatar">
                                                                <img src="../../assets/img/avatars/1.png" alt="" class="rounded-circle">
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="small mb-0">Congratulation Lettie 🎉</h6>
                                                            <small class="mb-1 d-block text-body">Won the monthly best seller gold badge</small>
                                                            <small class="text-body-secondary">1h ago</small>
                                                        </div>
                                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                                                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="icon-base bx bx-x"></span></a>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                            <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                                                <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                                            </div>
                                            <div class="ps__rail-y" style="top: 0px; right: 0px;">
                                                <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
                                            </div>
                                        </li>
                                        <li class="border-top">
                                            <div class="d-grid p-4">
                                                <a class="btn btn-primary btn-sm d-flex" href="javascript:void(0);">
                                                    <small class="align-middle">View all notifications</small>
                                                </a>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center" href="<?= url('views/system/profile.php') ?>">
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