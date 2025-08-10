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

        return $this->pm->run(
            "INSERT INTO " . $this->getTableName() . "(user_id, project_name) 
                                VALUES(:user_id, :project_name)",
            $param
        );
    }

    protected function updateRec()
    {
        $param = array(
            ':project_id' => $this->ProjectID,
            // ':user_id' => $this->UserID,
            ':project_name' => $this->ProjectName,
            ':project_status' => $this->ProjectStatus,
        );

        return $this->pm->run(
            "UPDATE " . $this->getTableName() . " 
             SET project_name = :project_name, status = :project_status
             WHERE id = :project_id",
            $param
        );
    }


    function createProject($user_id, $project_name)
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

    function updateProject($project_id, $user_id, $ProjectName, $project_status)
    {
        // Initialize the Book model
        $LogModel = new Logs();

        // Retrieve the book by BookID
        $existingProject = $LogModel->getProjectById($project_id);


        $project = new Logs();
        $project->ProjectID = $project_id;
        $project->UserID = $user_id;
        $project->ProjectName = $ProjectName;
        $project->ProjectStatus = $project_status;
        $fine = 0;

        $project->updateRec();

        if ($project) {
            return $project; // User created successfully
        } else {
            return false; // User creation failed (likely due to database error)
        }

        // Save the changes
        // $updated = $existingBook->updateRec(); // Assuming save method exists

        // return $updated ? true : false;
    }

    public function getProjectById($project_id)
    {
        $param = array(':project_id' => $project_id);
        return $this->pm->run("SELECT * FROM " . $this->getTableName() . " WHERE id = :project_id", $param, true);
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
