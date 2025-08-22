<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

try {
    $pdo->query("SELECT 1"); 

    if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
        $Fullname = $_POST['FullName'] ?? '';
        $Username = $_POST['UserName'] ?? '';
        $Email = $_POST['Email'] ?? '';
        $Password = $_POST['Password'] ?? '';

       
        if (empty($Fullname) || empty($Username) || empty($Email) || empty($Password)) {
            echo json_encode([
                'success' => false,
                'message' => 'All fields are required!'
            ]);
            exit;
        }

        $HashedPassword = password_hash($Password, PASSWORD_DEFAULT);


        $stmtEmail = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmtEmail->bindParam(':email', $Email, PDO::PARAM_STR);
        $stmtEmail->execute();
        if ($stmtEmail->fetchColumn() > 0) {
            echo json_encode([
                'success' => false,
                'field' => 'email',
                'message' => 'This email is already registered!'
            ]);
            exit;
        }

        
        $stmtUser = $pdo->prepare("SELECT COUNT(*) FROM users WHERE user_name = :username");
        $stmtUser->bindParam(':username', $Username, PDO::PARAM_STR);
        $stmtUser->execute();
        if ($stmtUser->fetchColumn() > 0) {
            echo json_encode([
                'success' => false,
                'field' => 'username',
                'message' => 'This username is already taken!'
            ]);
            exit;
        }

        if (strlen($Password) < 6) {
            echo json_encode([
            'success' => false,
            'field' => 'password',
            'message' => 'Password must be at least 6 characters long.'
            ]);
            exit;
            }
        
        $sql = "INSERT INTO users (full_name, user_name, email, Password) 
                VALUES (:FullName, :UserName, :Email, :Password)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':FullName', $Fullname);
        $stmt->bindParam(':UserName', $Username);
        $stmt->bindParam(':Email', $Email);
        $stmt->bindParam(':Password', $HashedPassword);
        $stmt->execute();

        echo json_encode([
            'success' => true,
            'message' => 'Account created successfully!'
        ]);
        exit; 
    }

            

            
//             if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).+$/', $Password)) {
//             echo json_encode([
//             'success' => false,
//             'field' => 'password',
//             'message' => 'Password must contain uppercase, lowercase, number, and special character.'
//             ]);
//             exit;
// }


    } catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
    exit;
}
