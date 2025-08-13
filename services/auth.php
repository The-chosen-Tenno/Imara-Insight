<?php
include __DIR__ . '/../config.php';
include __DIR__ . '/../helpers/AppManager.php';

header('Content-Type: application/json'); 

$pm = AppManager::getPM();
$sm = AppManager::getSM();

$email = $_POST['Email'] ?? '';
$password = $_POST['Password'] ?? '';

$param = [':Email' => $email];
$user = $pm->run("SELECT * FROM users WHERE email = :Email", $param, true);

if ($user != null) {
    if ($user['status'] !== 'confirmed') {
        echo json_encode([
            "success" => false,
            "message" => "pending"
        ]);
        exit;
    }

    if (password_verify($password, $user['password'])) {
        $sm->setAttribute("userId", $user['id']);
        $sm->setAttribute("fullName", $user['full_name']);
        $sm->setAttribute("userName", $user['user_name']);
        $sm->setAttribute("role", $user['role']);

        echo json_encode([
            "success" => true,
            "message" => "confirmed"
        ]);
        exit;
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Invalid username or password!"
        ]);
        exit;
    }

} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid username or password!"
    ]);
    exit;
}
