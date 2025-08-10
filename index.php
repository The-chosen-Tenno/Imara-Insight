<?php

include __DIR__ . '/config.php';
include BASE_PATH . '/helpers/AppManager.php';

$sm = AppManager::getSM();
$username = $sm->getAttribute("userName");

if (isset($username)) {
    header('location: views/admin/dashboard.php');
} else {
    header('location: views/auth/login.php');
}
