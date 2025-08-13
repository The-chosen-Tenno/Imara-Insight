<?php

include __DIR__ . '/config.php';
include BASE_PATH . '/helpers/AppManager.php';

$sm = AppManager::getSM();
$username = $sm->getAttribute("userName");
$role = $sm->getAttribute("role");

if (!empty($username)) {
    if ($role === "admin") {
        header('Location: views/admin/Authorization.php');
        exit;
    } else {
        header('Location: views/admin/dashboard.php');
        exit;
    }
} else {
    header('Location: views/auth/login.php');
    exit;
}
