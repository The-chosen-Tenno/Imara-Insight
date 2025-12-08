<?php
require_once 'BaseModel.php';

class Logs extends BaseModel
{
    public $ProjectID;
    public $UserID;
    public $ProjectName;
    public $ProjectType;
    public $Description;
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
            ':description' => $this->Description,
            ':project_type' => $this->ProjectType,
            ':status' => $this->ProjectStatus,
        );
        return $this->pm->run(
            "INSERT INTO " . $this->getTableName() . " (user_id, project_name, description, project_type, status) 
         VALUES (:user_id, :project_name, :description, :project_type, :status)",
            $param
        );
    }

    protected function updateRec()
    {
        $param = array(
            ':user_id' => $this->UserID,
            ':project_id' => $this->ProjectID,
            ':project_name' => $this->ProjectName,
            ':description' => $this->Description,
            ':status' => $this->ProjectStatus,
        );

        return $this->pm->run(
            "UPDATE " . $this->getTableName() . " 
             SET project_name = :project_name, status = :status, description = :description
             WHERE id = :project_id AND user_id = :user_id",
            $param
        );
    }

    function createProject($user_id, $project_name, $description, $project_type, $status = 'in_progress') // ✅ fixed signature
    {
        $LogModel = new Logs();
        $LogModel->UserID = $user_id;
        $LogModel->ProjectName = $project_name;
        $LogModel->Description = $description;
        $LogModel->ProjectType = $project_type;
        $LogModel->ProjectStatus = $status;
        $LogModel->addNewRec();

        return $LogModel ? true : false;
    }

    function updateProject($project_id, $user_id, $project_name, $description, $status)
    {
        $project = new Logs();
        $project->ProjectID = $project_id;
        $project->UserID = $user_id;
        $project->ProjectName = $project_name;
        $project->Description = $description;
        $project->ProjectStatus = $status;
        $project->updateRec();

        return $project ?: false;
    }

    public function updateProjectStatus($project_id, $status)
    {
        $sql = "UPDATE " . $this->getTableName() . " 
            SET status = :status
            WHERE id = :project_id";

        $params = [
            ':project_id' => $project_id,
            ':status' => $status
        ];
        return $this->pm->run($sql, $params);
    }

    public function getProjectById($project_id)
    {
        $param = array(':project_id' => $project_id);
        return $this->pm->run("SELECT * FROM " . $this->getTableName() . " WHERE id = :project_id", $param, true);
    }

    public function getByUserId($userId)
    {
        $param = array(':user_id' => $userId);
        return $this->pm->run("SELECT * FROM " . $this->getTableName() . " WHERE user_id = :user_id ORDER BY id DESC", $param);
    }
    public function getByAllProjectId($userId)
    {
        $param = array(':user_id' => $userId);
        return $this->pm->run("SELECT * FROM " . $this->getTableName() . " WHERE user_id = :user_id ORDER BY id DESC", $param);
    }
    public function getAllByDesc()
    {
        return $this->pm->run("SELECT * FROM " . $this->getTableName() . " ORDER BY id DESC");
    }

    public function filterProject($assignee = null, $status = null, $created_at = null, $updated_at = null)
    {
        $filters = [
            'user_id'    => $assignee,
            'status'     => $status,
            'created_at' => $created_at,
            'updated_at' => $updated_at
        ];

        $sql = "SELECT id FROM " . $this->getTableName() . " WHERE 1=1";
        $params = [];

        foreach ($filters as $col => $val) {
            if ($val !== null && $val !== '') {
                if ($col === 'created_at' || $col === 'updated_at') {
                    $sql .= " AND DATE($col) = :$col";
                    $params[":$col"] = date('Y-m-d', strtotime($val));
                } else {
                    $sql .= " AND $col = :$col";
                    $params[":$col"] = $val;
                }
            }
        }
        $sql .= " ORDER BY id DESC";
        $result = $this->pm->run($sql, $params);
        if (!$result || !is_array($result)) return [];

        $ids = [];
        foreach ($result as $row) {
            if (isset($row['id'])) {
                $ids[] = (int)$row['id'];
            }
        }

        return $ids;
    }



    public function deleteRec($id)
    {
        $param = array(':id' => $id);
        return $this->pm->run("DELETE FROM " . $this->getTableName() . " WHERE id = :id", $param);
    }

    public function getLastInsertId()
    {
        return $this->pm->lastInsertId();
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
