<?php
require_once '../config.php';
require_once '../helpers/AppManager.php';
require_once '../models/Users.php';
require_once '../models/Books.php';
require_once '../models/BorrowedBooks.php';




//create user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_user') {

    try {
        $username = $_POST['UserName'];
        $firstname = $_POST['FirstName'];
        $lastname = $_POST['LastName'];
        $email = $_POST['Email'];
        $password = $_POST['Password'];
        $permission = $_POST['Role'];

        $userModel = new User();
        $created =  $userModel->createUser($username, $firstname, $lastname, $email, $password, $permission,);
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



// Create book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_book') {

    try {
        $Title = $_POST['Title'];
        $Author = $_POST['Author'];
        $Category = $_POST['category'];
        $Quantity = $_POST['Quantity'];
        $ISBN = $_POST['ISBN'];

        $bookModel = new Book();
        $created =  $bookModel->createBook($Title, $Author, $Category, $ISBN, $Quantity);
        if ($created) {
            echo json_encode(['success' => true, 'message' => "Book Added successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add book. May be book already added!']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
//Get Book by id
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['book_id']) && isset($_GET['action']) &&  $_GET['action'] == 'get_book') {

    try {
        $book_id = $_GET['book_id'];
        $bookModel = new Book();
        $book = $bookModel->getBookById($book_id);
        if ($book) {
            echo json_encode(['success' => true, 'message' => "Book Selected Successfully !!", 'data' => $book]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to select book. May be book is not exist!']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
// Update book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_book') {

    try {
        $book_id = $_POST['BookID'];
        $Title = $_POST['Title'];
        $Author = $_POST['Author'];
        $Category = $_POST['category'];
        $Quantity = $_POST['Quantity'];
        $ISBN = $_POST['ISBN'];

        $bookModel = new book();
        $updated =  $bookModel->updateBook($book_id, $Title, $Author, $Category, $ISBN, $Quantity);
        if ($updated) {
            echo json_encode(['success' => true, 'message' => "Book updated successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update Book. May be book is not  exist!']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
//Delete by book id
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['user_id']) && isset($_GET['action']) && $_GET['action'] == 'delete_book') {
    try {
        $BookID = $_GET['user_id'];

        $BookModel = new Book();


        $bookDeleted = $BookModel->deleteBookById($BookID);

        if ($bookDeleted) {
            echo json_encode(['success' => true, 'message' => ' book deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to Delete book.']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}



// Create Borrowed Book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_borrowed_book') {

    try {
        $book_id = $_POST['BookID'];
        $user_id = $_POST['UserID'];
        $borrow_date = $_POST['BorrowDate'];
        $due_date = $_POST['DueDate'];

        $bookborrowedModel = new BorrowedBooks();
        $created =  $bookborrowedModel->createBorrowedBook($book_id, $user_id, $borrow_date, $due_date,);
        if ($created) {
            echo json_encode(['success' => true, 'message' => "User Borrowed Book successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User Already Borrowed This Book!']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
//Get Borrowed Book by id
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['BorrowedBookID']) && isset($_GET['action']) &&  $_GET['action'] == 'get_borrowed_book') {

    try {
        $borrowed_book_id = $_GET['BorrowedBookID'];
        $bookModel = new BorrowedBooks();
        $book = $bookModel->getBookById($borrowed_book_id);
        if ($book) {
            echo json_encode(['success' => true, 'message' => "Borrowed Book ID selected successfully!", 'data' => $book]);
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_borrowed_book') {

    try {
        $borrowed_book_id = $_POST['BorrowedBookID'];
        $book_id = $_POST['BookID'];
        $user_id = $_POST['UserID'];
        $borrow_date = $_POST['BorrowDate'];
        $due_date = $_POST['DueDate'];
        $return_date = $_POST['ReturnDate'];
        $fine_status = $_POST['FineStatus'];

        $bookborrowedModel = new BorrowedBooks();
        $updated =  $bookborrowedModel->updateBorrowedBook($borrowed_book_id, $book_id, $user_id, $borrow_date, $due_date, $return_date, $fine_status);
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
