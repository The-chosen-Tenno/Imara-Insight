<?php
include __DIR__ . '/../config.php';
include __DIR__ . '/../helpers/AppManager.php';

$pm = AppManager::getPM();
$sm = AppManager::getSM();

$email = $_POST['Email'] ?? '';
$password = $_POST['Password'] ?? '';

$param = [':Email' => $email];
$user = $pm->run("SELECT * FROM users WHERE email = :Email", $param, true);

if ($user) {
    if (password_verify($password, $user['password'])) {
        if ($user['status'] !== 'confirmed') {
            echo json_encode([
                "success" => false,
                "message" => "pending"
            ]);
            exit;
        }

        // login success
        $sm->setAttribute("userId", $user['id']);
        $sm->setAttribute("fullName", $user['full_name']);
        $sm->setAttribute("userName", $user['user_name']);
        $sm->setAttribute("role", $user['role']);
        $sm->setAttribute("userPhoto", $user['photo']);

        echo json_encode([
            "success" => true,
            "redirect" => "../../index.php"
        ]);
        exit;
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Invalid email or password"
        ]);
        exit;
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password"
    ]);
    exit;
}
