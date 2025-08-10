<?php
require_once __DIR__ . '/../config.php'; // Include database config
try {
    // Test the database connection
    $pdo->query("SELECT 1");

    // If connected, proceed to the next step
    echo "Database connection successful! Proceeding...";
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Your existing code here
        $Fullname = $_GET['FullName'];
        $Username = $_GET['UserName'];
        $Email = $_GET['Email'];
        $Password = $_GET['Password'];
        $HashedPassword = password_hash($Password, PASSWORD_DEFAULT);

        try {
            $sql = "INSERT INTO users (FullName, UserName, Email, Password) 
                    VALUES (:FullName, :UserName, :Email, :Password)";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':FullName', $Fullname);
            $stmt->bindParam(':UserName', $Username);
            $stmt->bindParam(':Email', $Email);
            $stmt->bindParam(':Password', $HashedPassword);

            $stmt->execute();
            header("Location: ../index.php");  
            exit();  
        } catch (PDOException $e) {
            echo "Error inserting data: " . $e->getMessage();
        }
    }
} catch (PDOException $e) {
    // If the database connection fails
    die("Database connection failed: " . $e->getMessage());
}

?>
