<?php
require_once 'BaseModel.php';

class Book extends BaseModel
{
    public $Title;
    public $Author;
    public $Category;
    public $Quantity;
    public $ISBN;
    public $BookID;
    
    protected function getTableName()
    {
        return "books";
    }
    
    protected function addNewRec()
    {
        $param = array(
            ':title' => $this->Title,
            ':author' => $this->Author,
            ':category' => $this->Category,
            ':quantity' => $this->Quantity,
            ':isbn' => $this->ISBN,
        );
    
        return $this->pm->run("INSERT INTO " . $this->getTableName() . "(Title, Author, Category, Quantity, ISBN) 
                                VALUES(:title, :author, :category, :quantity, :isbn)", 
                                $param);
    }
    
    protected function updateRec()
    {
        $param = array(
            ':title' => $this->Title,
            ':author' => $this->Author,
            ':category' => $this->Category,
            ':quantity' => $this->Quantity,
            ':isbn' => $this->ISBN,
            ':book_id' => $this->BookID
        );
    
        return $this->pm->run("UPDATE books SET Title = :title, Author = :author, Category = :category, Quantity = :quantity, ISBN = :isbn WHERE BookID = :book_id", $param); // Changed from ID to BookID
    }

    function createBook($title, $author, $category, $isbn, $quantity)
    {
        // Create a new instance of the Book model
        $bookModel = new Book();
        
        // Assign the provided values to the model's properties
        $bookModel->Title = $title;
        $bookModel->Author = $author;
        $bookModel->Category = $category;
        $bookModel->ISBN = $isbn;
        $bookModel->Quantity = $quantity;
        
        // Call the save method to insert the new book into the database
        $bookModel->save();
    
        // Return true if the book was successfully saved, otherwise return false
        return $bookModel ? true : false; // Simplified the return
    }

    function updateBook($bookid, $title, $author, $category, $isbn, $quantity)
    {
        // Initialize the Book model
        $bookModel = new Book();
    
        // Retrieve the book by BookID
        $existingBook = $bookModel->getBookById($bookid); // Assuming getById method exists
    

        $book = new book();
        $book->Title = $title;
        $book->Author = $author;
        $book->Category = $category;
        $book->ISBN = $isbn;
        $book->Quantity = $quantity;
        $book->BookID = $bookid;

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
    
    function deleteBookById($BookID)
    {
        // Find the book by BookID
        $book = $this->getBookById($BookID);
        if (!$book) {
            return true; // No book found, proceed with deletion
        }
    
        if (empty($book['BookID'])) return false;
        $BookID = $book['BookID'];
    
        // // Delete the book record
        return $this->deleteRecBook($BookID);
    }
    
    public function getBookById($BookID)
    {
        $param = array(':book_id' => $BookID); // Changed from :id to :book_id
        return $this->pm->run("SELECT * FROM " . $this->getTableName() . " WHERE BookID = :book_id", $param, true); // Changed ID to BookID
    }
    
    public function deleteRecBook($ID)
    {
        // Delete the book record from the database using BookID
        $param = array(':book_id' => $ID); // Changed from :id to :book_id
        $rowsDeleted = $this->pm->run("DELETE FROM " . $this->getTableName() . " WHERE BookID = :book_id", $param); // Changed ID to BookID
        return $rowsDeleted;
    }
}
?>
