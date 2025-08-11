<?php
require_once '../config.php';
require_once '../helpers/AppManager.php';
require_once '../models/Users.php';
require_once '../models/Logs.php';
require_once '../models/Images.php';




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
        $user_id = $_POST['user_id'];
        $project_name = $_POST['project_name'];

        $bookborrowedModel = new Logs();
        $created =  $bookborrowedModel->createProject($user_id, $project_name);
        if ($created) {
            echo json_encode(['success' => true, 'message' => "New Project successfully Created"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'some error occurred']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
//Get Project by id
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['project_id']) && isset($_GET['action']) &&  $_GET['action'] == 'get_project') {

    try {
        $project_id = $_GET['project_id'];
        $LogModel = new Logs();
        $Log = $LogModel->getProjectById($project_id);
        if ($Log) {
            echo json_encode(['success' => true, 'message' => "Project selected successfully!", 'data' => $Log]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error Selecting Project ID']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
// Update project
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_project') {

    try {
        $project_id = $_POST['project_id'];
        $user_id = $_POST['user_id'];
        $ProjectName = $_POST['project_name'];
        $project_status = $_POST['status'];

        $LogModel = new Logs();
        $updated =  $LogModel->updateProject($project_id, $user_id, $ProjectName, $project_status);
        if ($updated) {
            echo json_encode(['success' => true, 'message' => "Project updated successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'project not found or update failed!']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
// update project user by admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_project_user') {

    try {
        $project_id = $_POST['project_id'];
        $user_id = $_POST['user_id'];
        $ProjectName = $_POST['project_name'];
        $project_status = $_POST['status'];
        $projectImage = $_POST['project_images'];

        $LogModel = new Logs();
        $updated =  $LogModel->updateProject($project_id, $user_id, $ProjectName, $project_status);
        if ($updated) {
            echo json_encode(['success' => true, 'message' => "Project updated successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update Borrowed Book!']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// get images by project id
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['project_id']) && isset($_GET['action']) &&  $_GET['action'] == 'get_images') {

    try {
        $project_id = $_GET['project_id'];
        $ImageModel = new project_images();
        $Image = $ImageModel->getImagebyProjectId($project_id);
        if ($Image) {
            echo json_encode(['success' => true, 'message' => "Images are Successfully retrieved!", 'data' => $Image]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error retrieved Images']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
dd('Access denied..!');
