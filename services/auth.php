<?php
include __DIR__ . '/../config.php';
include __DIR__ . '/../helpers/AppManager.php';

$pm = AppManager::getPM();
$sm = AppManager::getSM();

$email = $_POST['Email'] ?? '';
$password = $_POST['Password'] ?? '';

$param = array(':Email' => $email);
$user = $pm->run("SELECT * FROM users WHERE email = :Email", $param, true);

if ($user != null) {

    if ($user['status'] !== 'confirmed') {
        if(password_verify($password, $user['password'])){
            echo json_encode([
                "success" => false,
                "message" => "pending"
            ]);
            exit; // stop here! no redirect
        }
    }


    $correct = password_verify($password, $user['password']);
    if ($correct) {

        $sm->setAttribute("userId", $user['id']);
        $sm->setAttribute("fullName", $user['full_name']);
        $sm->setAttribute("userName", $user['user_name']);
        $sm->setAttribute("role", $user['role']);
        $sm->setAttribute("userPhoto", $user['photo']);

        header('location: ../index.php');
        echo 'Login confirmed';
        exit;
    } else {
        $sm->setAttribute("error", 'Invalid username or password!');
    }
} else {
    $sm->setAttribute("error", 'Invalid username or password!');
}
header("Location: " . $_SERVER['HTTP_REFERER']);

exit;
