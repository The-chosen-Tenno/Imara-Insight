<?php
include __DIR__ . '/config.php';
include BASE_PATH . '/helpers/AppManager.php';

$sm = AppManager::getSM();
$username = $sm->getAttribute("userName");
$role = $sm->getAttribute("role");

if (empty($username)) {
    $sm->destroy();  // use SessionManager to destroy session
    header('Location: views/auth/login.php');
    exit;
} else {
    header('Location: views/system/dashboard.php');
    exit;
}
