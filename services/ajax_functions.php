<?php
require_once '../config.php';
require_once '../helpers/AppManager.php';
require_once '../models/Users.php';
require_once '../models/Logs.php';
require_once '../models/Sub-Assignees.php';
require_once '../models/ProjectImageModel.php';
require_once '../models/Leave.php';
require_once '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Create user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'admin_create_user') {
    try {
        $user_name = $_POST['user_name'];
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = $_POST['role'];


        $userModel = new User();
        $created = $userModel->createUserByAdmin($full_name, $user_name, $email, $password, $role);
        if ($created) {
            echo json_encode(['success' => true, 'message' => "User created successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create user. User may already exist!']);
        }
    } catch (PDOException $e) {
        // Handle DB errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// Get user by ID
if (isset($_GET['action']) && $_GET['action'] === 'get_all_users') {
    $users = (new User())->getAllActive();
    echo json_encode(['success' => true, 'data' => $users]);
    exit;
}

// Update user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_user') {
    try {
        $username = $_POST['UserName'] ?? '';
        $email = $_POST['Email'] ?? '';
        $id = $_POST['ID'] ?? '';

        if (empty($username) || empty($email)) {
            echo json_encode(['success' => false, 'message' => 'Required fields missing!']);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email address']);
            exit;
        }

        $photoPath = null;
        if (!empty($_FILES['Photo']['name'])) {
            $uploadDir = __DIR__ . '/../uploads/profileImage/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = time() . '_' . basename($_FILES['Photo']['name']);
            $targetFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['Photo']['tmp_name'], $targetFile)) {
                $photoPath = 'uploads/profileImage/' . $fileName;
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
                exit;
            }
        }

        $userModel = new User();
        $updated = $userModel->updateUser($id, $username, $email, $photoPath);

        if ($updated) {
            echo json_encode(['success' => true, 'message' => "User updated successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update user. User may already exist!']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
// user status change 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_user_status') {
    try {
        $id = $_POST['user_id'] ?? null;
        $user_status = $_POST['user_status'] ?? null;
// print_r($_POST);
// die()
        if (!$id || !$user_status) {
            echo json_encode(['success' => false, 'message' => 'Missing user ID or status']);
            exit;
        }

        $userModel = new User();
        $updated   = $userModel->updateUserStatus($id, $user_status);
        if ($updated) {
            echo json_encode(['success' => true, 'message' => "User status updated successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found or update failed!']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
// Get user by ID (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id'], $_GET['action']) && $_GET['action'] == 'get_user_by_id') {
    try {
        $user_id = $_GET['user_id'];
        $userModel = new User();
        $user = $userModel->getUserById($user_id);

        if ($user) {
            echo json_encode([
                'success' => true,
                'message' => "User retrieved successfully!",
                'data' => $user   // ✅ return full user, not just the ID
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error selecting user ID'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'accept_user') {
    try {
        $id = $_POST['user_id'];
        $userModel = new User();
        $accepted = $userModel->acceptUser($id);

        if ($accepted) {
            $user = $userModel->getUserById($id);

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'sandbox.smtp.mailtrap.io';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'bd5ed557564c7b';
                $mail->Password   = '496353a167a69c';
                $mail->Port       = 2525;

                $mail->setFrom('hameemtrooper@gmail.com', 'Admin');
                $mail->addAddress($user['email'], $user['user_name']); // ✅ send to actual 

                $mail->isHTML(true);
                $mail->Subject = 'Your Account Has Been Accepted';
                $mail->Body    = "Hello <b>{$user['user_name']}</b>,<br>Your account has been <b>accepted</b> successfully!";

                $mail->send();

                echo json_encode(['success' => true, 'message' => "User accepted successfully & email sent!"]);
            } catch (Exception $e) {
                echo json_encode(['success' => true, 'message' => "User accepted successfully but email failed: {$mail->ErrorInfo}"]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to accept user. May already be accepted!']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// Decline user registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'decline_user') {
    try {
        $id = $_POST['user_id'];
        $userModel = new User();
        $declined = $userModel->declineUser($id);
        if ($declined) {
            echo json_encode(['success' => true, 'message' => "User accepted successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to accept user. May already be accepted!']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// Create new project
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_project') {
    try {
        $user_id = $_POST['user_id'];
        $project_name = $_POST['project_name'];
        $project_type = $_POST['type'] ?? 'coding';
        $project_status = $_POST['status'] ?? 'idle';

        $logsModel = new Logs();
        $projectCreated = $logsModel->createProject($user_id, $project_name, $project_type, $project_status);

        if ($projectCreated) {
            $project_id = $logsModel->getLastInsertId(); // ✅ always get last inserted ID

            // Handle sub-assignees
            if (!empty($_POST['sub_assignees'])) {
                $user_ids = $_POST['sub_assignees'];
                $subAssigneeModel = new SubAssignee();
                $successCount = 0;

                foreach ($user_ids as $sub_id) {
                    $added = $subAssigneeModel->createSubAssignee($project_id, $sub_id);
                    if ($added) $successCount++;
                }
            }

            // Handle images if provided
            $descriptions = $_POST['project_images_description'] ?? [];
            $uploadedData = [];

            if (isset($_FILES['project_images']) && !empty($_FILES['project_images']['name'][0])) {
                $uploadDir = __DIR__ . '/../uploads/projects/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

                for ($i = 0; $i < count($_FILES['project_images']['name']); $i++) {
                    $tmpName = $_FILES['project_images']['tmp_name'][$i];
                    $originalName = basename($_FILES['project_images']['name'][$i]);
                    $fileName = uniqid() . '_' . $originalName;
                    $targetFilePath = $uploadDir . $fileName;

                    $fileType = mime_content_type($tmpName);
                    if (strpos($fileType, 'image') === false) continue;

                    if (move_uploaded_file($tmpName, $targetFilePath)) {
                        $uploadedData[] = [
                            'file' => $fileName,
                            'description' => $descriptions[$i] ?? ''
                        ];
                    }
                }

                if (!empty($uploadedData)) {
                    $projectImageModel = new ProjectImageModel();
                    $projectImageModel->saveProjectImages($project_id, $uploadedData);
                }
            }

            echo json_encode(['success' => true, 'message' => "New project created successfully"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Some error occurred while creating project']);
        }

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}


// Get project by ID
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['project_id'], $_GET['action']) && $_GET['action'] == 'get_project') {
    try {
        $project_id = $_GET['project_id'];
        $logsModel = new Logs();
        $project = $logsModel->getProjectById($project_id);
        if ($project) {
            echo json_encode(['success' => true, 'message' => "Project retrieved successfully!", 'data' => $project]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error selecting project ID']);
        }
    } catch (PDOException $e) {
        // Handle DB errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// Get user by ID
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id'], $_GET['action']) && $_GET['action'] == 'get_user_by_id') {
    try {
        $user_id = $_GET['user_id'];
        $userModel = new User();
        $user = $userModel->getUserById($user_id);
        if ($user) {
            echo json_encode(['success' => true, 'message' => "User retrieved successfully!", 'data' => $user]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error selecting user ID']);
        }
    } catch (PDOException $e) {
        // Handle DB errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}


// Update project
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_project') {
    try {
        $project_id = $_POST['project_id'];
        $user_id = $_POST['user_id'];
        $projectName = $_POST['project_name'];
        $project_status = $_POST['status'];

        $logsModel = new Logs();
        $updated = $logsModel->updateProject($project_id, $user_id, $projectName, $project_status);
        if ($updated) {
            echo json_encode(['success' => true, 'message' => "Project updated successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Project not found or update failed!']);
        }
    } catch (PDOException $e) {
        // Handle DB errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// Update project user and upload images (admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_project_user') {
    try {
        $project_id = $_POST['project_id'];
        $user_id = $_POST['user_id'];
        $projectName = $_POST['project_name'];
        $project_status = $_POST['status'];

        $logsModel = new Logs();
        $updated = $logsModel->updateProject($project_id, $user_id, $projectName, $project_status);

        // Prepare arrays for titles and descriptions
        $titles = $_POST['project_images_title'] ?? [];
        $descriptions = $_POST['project_images_description'] ?? [];

        $uploadedData = [];
        if (isset($_FILES['project_images']) && !empty($_FILES['project_images']['name'][0])) {
            $uploadDir = __DIR__ . '/../uploads/projects/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            for ($i = 0; $i < count($_FILES['project_images']['name']); $i++) {
                $tmpName = $_FILES['project_images']['tmp_name'][$i];
                $originalName = basename($_FILES['project_images']['name'][$i]);
                $fileName = uniqid() . '_' . $originalName;
                $targetFilePath = $uploadDir . $fileName;

                $fileType = mime_content_type($tmpName);
                if (strpos($fileType, 'image') === false) continue;

                if (move_uploaded_file($tmpName, $targetFilePath)) {
                    $uploadedData[] = [
                        'file' => $fileName,
                        'title' => $titles[$i] ?? '',
                        'description' => $descriptions[$i] ?? ''
                    ];
                }
            }

            $projectImageModel = new ProjectImageModel();
            if (!empty($uploadedData)) {
                $projectImageModel->saveProjectImages($project_id, $uploadedData);
            }
        }

        if ($updated) {
            echo json_encode(['success' => true, 'message' => 'Project updated successfully', 'uploaded_images' => $uploadedData]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update project']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// Get images by project ID (POST method)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'get_images') {
    try {
        $project_id = $_POST['project_id'];
        $imageModel = new ProjectImageModel();
        $retrieved = $imageModel->getImagebyProjectId($project_id);
        if ($retrieved) {
            echo json_encode(['success' => true, 'message' => 'Images retrieved successfully', 'retrieved_images' => $retrieved]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to retrieve images']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
// new leave request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'request_leave') {
    try {
        $reason_type = $_POST['reason_type'];
        $leave_note = $_POST['leave_note'] ?? null;
        $leave_duration = $_POST['leave_duration'] ?? 'full'; // New: full/half
        $half_day = $_POST['half_day'] ?? null; // only relevant if leave_duration = half
        $date_off = $_POST['date_off'];
        $description = $_POST['description'];
        $user_id = $_POST['user_id'];

        // If leave_duration is full, ignore half_day
        if ($leave_duration === 'full') {
            $half_day = null;
        }

        $leaveModel = new Leave();
        $requested = $leaveModel->createLeaveReq(
            $reason_type,
            $leave_note,
            $leave_duration,
            $half_day,
            $date_off,
            $description,
            $user_id
        );

        if ($requested) {
            echo json_encode(['success' => true, 'message' => "Leave requested successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to request leave. Leave may already exist!']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// approve leave request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'approve_leave') {
    try {
        $id = $_POST['id'];
        $leaveModel = new Leave();
        $requested = $leaveModel->approveLeave($id);
        if ($requested) {
            echo json_encode(['success' => true, 'message' => "Leave requested successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to request leave. leave may already exist!']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// deny leave request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deny_leave') {
    try {
        $id = $_POST['id'];
        $leaveModel = new Leave();
        $requested = $leaveModel->denyLeave($id);
        if ($requested) {
            echo json_encode(['success' => true, 'message' => "Leave requested successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to request leave. leave may already exist!']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'get_sub_assignees') {
    header('Content-Type: application/json');

    $project_id = intval($_GET['project_id'] ?? 0);
    if (!$project_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid project ID.']);
        exit;
    }

    try {
        $subAssigneeModel = new SubAssignee();
        $subs = $subAssigneeModel->getAllByProjectId($project_id); // just IDs

        $userModel = new User();
        $allUsers = $userModel->getAll();
        $lookup = [];
        foreach ($allUsers as $u) {
            $lookup[$u['id']] = $u['full_name'];
        }

        $data = [];
        foreach ($subs as $id) {
            $data[] = [
                'id' => $id,
                'full_name' => $lookup[$id] ?? 'Unknown'
            ];
        }

        echo json_encode([
            'success' => true,
            'message' => 'Sub-assignees retrieved successfully!',
            'data' => $data
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_sub_assignees') {
    try {
        $project_id = $_POST['project_id'];
        $user_ids = $_POST['user_id'];

        $successCount = 0;
        $subAssigneeModel = new SubAssignee();
        foreach ($user_ids as $user_id) {
            $added = $subAssigneeModel->createSubAssignee($project_id, $user_id);
            if ($added) $successCount++;
        }

        if ($successCount > 0) {
            echo json_encode(['success' => true, 'message' => "$successCount sub-assignees added successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No sub-assignees were added (they may already exist).']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove_sub_assignee') {
    header('Content-Type: application/json');

    $project_id = intval($_POST['project_id'] ?? 0);
    $user_ids   = $_POST['user_id'] ?? [];

    if (!$project_id || empty($user_ids) || !is_array($user_ids)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid project ID or no sub-assignees selected.'
        ]);
        exit;
    }

    try {
        $subAssigneeModel = new SubAssignee();
        $ok = $subAssigneeModel->removeFromProject($project_id, $user_ids);

        echo json_encode([
            'success' => $ok,
            'message' => $ok
                ? 'Sub-assignees removed successfully!'
                : 'Failed to remove sub-assignees.'
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'get_available_sub_assignees') {
    header('Content-Type: application/json');

    $project_id = intval($_GET['project_id'] ?? 0);
    if (!$project_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid project ID.']);
        exit;
    }

    try {
        $subAssigneeModel = new SubAssignee();
        $assignedIds = $subAssigneeModel->getAllByProjectId($project_id); // existing sub-assignees

        $userModel = new User();
        $allUsers = $userModel->getAllActive();

        $data = [];
        foreach ($allUsers as $u) {
            if (!in_array($u['id'], $assignedIds)) { // exclude already assigned
                $data[] = [
                    'id' => $u['id'],
                    'full_name' => $u['full_name']
                ];
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'Available users retrieved successfully!',
            'data' => $data
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
    exit;
}

dd('Access denied..!');
