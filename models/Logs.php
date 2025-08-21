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
        $LogModel = new Logs();
        $LogModel->UserID = $user_id;
        $LogModel->ProjectName = $project_name;
        $LogModel->addNewRec();
        return $LogModel ? true : false;
    }

    function updateProject($project_id, $user_id, $project_name, $status)
    {
        $project = new Logs();
        $project->ProjectID = $project_id;
        $project->UserID = $user_id;
        $project->ProjectName = $project_name;
        $project->ProjectStatus = $status;
        $project->updateRec();

        if ($project) {
            return $project;
        } else {
            return false;
        }
    }

    public function getProjectById($project_id)
    {
        $param = array(':project_id' => $project_id);
        return $this->pm->run("SELECT * FROM " . $this->getTableName() . " WHERE id = :project_id", $param, true);
    }

    public function getByUserId($userId)
    {
        $param = array(':user_id' => $userId);
        return $this->pm->run("SELECT * FROM " . $this->getTableName() . " WHERE user_id = :user_id", $param);
    }

    public function deleteRec($id)
    {
        $param = array(':id' => $id);
        return $this->pm->run("DELETE FROM " . $this->getTableName() . " WHERE BorrowedBookID = :id", $param);
    }
    public function getCompleted()
{
    return $this->pm->run("
        SELECT * 
        FROM " . $this->getTableName() . " 
        WHERE status = 'finished'
        ORDER BY last_updated DESC
    ");
}

}
