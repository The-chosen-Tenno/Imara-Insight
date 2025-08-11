<?php
require_once '../config.php';
require_once '../helpers/AppManager.php';
require_once '../models/Users.php';
require_once '../models/Logs.php';




//create user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_user') {

    try {
        $user_name = $_POST['user_name'];
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = $_POST['role'];

        $userModel = new User();
        $created =  $userModel->createUser($full_name, $user_name, $email,  $password, $role);
        if ($created) {
            echo json_encode(['success' => true, 'message' => "User created successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create user. May be user already exist!']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
//Get user by id
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id']) && isset($_GET['action']) &&  $_GET['action'] == 'get_user') {

    try {
        $user_id = $_GET['user_id'];
        $userModel = new User();
        $user = $userModel->getUserById($user_id);
        if ($user) {
            echo json_encode(['success' => true, 'message' => "User selected successfully!", 'data' => $user]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not Found']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
//update user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_user') {

    try {
        $username = $_POST['UserName'] ?? '';
        $email = $_POST['Email'] ?? '';

        $id = $_POST['ID'];

        // Validate inputs
        if (empty($username) || empty($email)) {
            echo json_encode(['success' => false, 'message' => 'Required fields are missing!']);
            exit;
        }


        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email address']);
            exit;
        }

        $userModel = new User();
        $updated =  $userModel->updateUser($id, $username, $email);
        if ($updated) {
            echo json_encode(['success' => true, 'message' => "User updated successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update user. May be user already exist!']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
//Delete by user id
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['user_id']) && isset($_GET['action']) && $_GET['action'] == 'delete_user') {
    try {
        $ID = $_GET['user_id'];

        $userModel = new User();

        // if ($permission == 'admin') {
        //     $userDeleted = $userModel->deleteUser($ID);
        //     if ($userDeleted === false) {
        //         echo json_encode(['success' => false, 'message' => 'Doctor has appointments and cannot be deleted.']);
        //         exit;
        //     }
        // }
        // Proceed to delete the user if doctor deletion was successful or not needed
        $userDeleted = $userModel->deleteUser($ID);

        if ($userDeleted) {
            echo json_encode(['success' => true, 'message' => 'User deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete user.']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}





// Create New Project
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_project') {

    try {
        $user_id = $_POST['UserID'];
        $project_name = $_POST['projectName'];

        $bookborrowedModel = new Logs();
        $created =  $bookborrowedModel->createProject($user_id, $project_name);
        if ($created) {
            echo json_encode(['success' => true, 'message' => "New Project successfully Created"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User Already Borrowed This Book!']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
//Get Project by id
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ProjectId']) && isset($_GET['action']) &&  $_GET['action'] == 'get_project') {

    try {
        $project_id = $_GET['ProjectId'];
        $LogModel = new Logs();
        $Log = $LogModel->getProjectById($project_id);
        if ($Log) {
            echo json_encode(['success' => true, 'message' => "Borrowed Book ID selected successfully!", 'data' => $Log]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error Selecting Borrowed Book ID']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
// Update Borrowed Book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_project') {

    try {
        $project_id = $_POST['ProjectId'];
        $user_id = $_POST['UserID'];
        $ProjectName = $_POST['ProjectName'];
        $project_status = $_POST['ProjectStatus'];

        $LogModel = new Logs();
        $updated =  $LogModel->updateProject($project_id, $user_id, $ProjectName, $project_status);
        if ($updated) {
            echo json_encode(['success' => true, 'message' => "User Borrowed Book Updated successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update Borrowed Book!']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
dd('Access denied..!');
