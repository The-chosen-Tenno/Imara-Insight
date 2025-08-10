<?php
require_once 'BaseModel.php';

class Logs extends BaseModel
{
    public $ProjectID;
    public $UserID;
    public $ProjectName;
    public $ProjectStatus;
    public $LastUpdated;


    protected function getTableName()
    {
        return "projects";
    }

    protected function addNewRec()
    {
        $param = array(
            ':user_id' => $this->UserID,
            ':project_name' => $this->ProjectName,
        );

        return $this->pm->run("INSERT INTO " . $this->getTableName() . "(user_id, project_name) 
                                VALUES(:user_id, :project_name)", 
                                $param);
    }

    protected function updateRec()
    {
        $param = array(
            ':user_id' => $this->UserID,
            ':book_id' => $this->BookID,
            ':borrow_date' => $this->BorrowDate,
            ':due_date' => $this->DueDate,
            ':return_date' => $this->ReturnDate,
            ':fine' => $this->Fine, // Include Fine in parameters
            ':borrowed_book_id' => $this->BorrowedBookID,
            ':fine_status' => $this->FineStatus
        );
    
        return $this->pm->run(
            "UPDATE " . $this->getTableName() . " 
             SET BookID = :book_id, UserID = :user_id, BorrowDate = :borrow_date, DueDate = :due_date, 
                 ReturnDate = :return_date, Fine = :fine, FineStatus = :fine_status
             WHERE BorrowedBookID = :borrowed_book_id",
            $param
        );
    }
    

    function createProject($user_id,$project_name)
    {
        // Create a new instance of the Book model
        $LogModel = new Logs();
        
        // Assign the provided values to the model's properties
        $LogModel->UserID = $user_id;
        $LogModel->ProjectName = $project_name;
        
        // Call the save method to insert the new book into the database
        $LogModel->addNewRec();
    
        // Return true if the book was successfully saved, otherwise return false
        return $LogModel ? true : false; // Simplified the return
    }

    function updateBorrowedBook($borrowed_book_id,$book_id, $user_id,$borrow_date,$due_date, $return_date = null,$fine_status)
    {
        // Initialize the Book model
        $bookModel = new Book();
    
        // Retrieve the book by BookID
        $existingBook = $bookModel->getBookById($book_id); // Assuming getById method exists
    

        $book = new BorrowedBooks();
        $book->BorrowedBookID = $borrowed_book_id;
        $book->BookID = $book_id;
        $book->UserID = $user_id;
        $book->BorrowDate = $borrow_date;
        $book->DueDate = $due_date;
        $book->ReturnDate = !empty($return_date) && $return_date !== '0000-00-00' ? $return_date : null;
        $book->FineStatus = $fine_status;

        $fine = 0;

        // Calculate fine only if return_date is provided and is after the due_date
        if (!is_null($return_date) && $return_date > $due_date) {
            $dueDateTime = new DateTime($due_date);
            $returnDateTime = new DateTime($return_date);
    
            $interval = $dueDateTime->diff($returnDateTime);
            $daysLate = $interval->days; // Number of days overdue
            $fine = $daysLate * 0.5; // Fine is 0.5 dollars per day
        }
    
        // Assign the calculated fine to the model
        $book->Fine = $fine;

        $book->updateRec();

        if ($book) {
            return $book; // User created successfully
        } else {
            return false; // User creation failed (likely due to database error)
        }
    
        // Save the changes
        // $updated = $existingBook->updateRec(); // Assuming save method exists
    
        // return $updated ? true : false;
    }

    public function getBookById($borrowed_book_id)
    {
        $param = array(':borrowed_book_id' => $borrowed_book_id);
        return $this->pm->run("SELECT * FROM " . $this->getTableName() . " WHERE BorrowedBookID = :borrowed_book_id", $param, true);
    }

    public function getByUserId($userId)
    {
        $param = array(':user_id' => $userId);
        return $this->pm->run("SELECT * FROM " . $this->getTableName() . " WHERE UserID = :user_id", $param, true);
    }

    public function getByBookId($bookId)
    {
        $param = array(':book_id' => $bookId);
        return $this->pm->run("SELECT * FROM " . $this->getTableName() . " WHERE BookID = :book_id", $param, true);
    }

    public function deleteRec($id)
    {
        $param = array(':id' => $id);
        return $this->pm->run("DELETE FROM " . $this->getTableName() . " WHERE BorrowedBookID = :id", $param);
    }
}
