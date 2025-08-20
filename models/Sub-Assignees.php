<?php
require_once 'BaseModel.php';

class SubAssignee extends BaseModel
{
    public $project_id;
    public $sub_assignee_id;

    function getTableName()
    {
        return 'project_sub_assignees';
    }

    function createSubAssignee($project_id, $sub_assignee_id)
    {
        $subAssigneeModel = new SubAssignee();
        $existingSubAssignee = $subAssigneeModel->getSubAssigneeByProjectIDAndUserID($project_id, $sub_assignee_id);
        if ($existingSubAssignee) {
            return false;
        }
        $SubAssignee = new SubAssignee();
        $SubAssignee->project_id = $project_id;
        $SubAssignee->sub_assignee_id = $sub_assignee_id;
        $SubAssignee->addNewRec();
        if ($SubAssignee) {
            return $SubAssignee;
        } else {
            return false;
        }
    }

    protected function addNewRec()
    {
        $param = [
            ':project_id' => $this->project_id,
            ':sub_assignee_id' => $this->sub_assignee_id
        ];
        $result = $this->pm->run(
            "INSERT INTO " . $this->getTableName() . "(project_id,sub_assignee_id) values(:project_id,:sub_assignee_id)",
            $param
        );
        return  $result;
    }
    
    protected function updateRec() {}

    public function getSubAssigneeByProjectIDAndUserID($project_id, $sub_assignee_id)
    {
        $param = [':project_id' => $project_id, ':sub_assignee_id' => $sub_assignee_id];
        $query = "SELECT * FROM " . $this->getTableName() . " WHERE (sub_assignee_id = :sub_assignee_id AND project_id = :project_id)";
        return $this->pm->run($query, $param);
    }
public function getAllByProjectId($project_id)
{
    $param = [':project_id' => $project_id];
    $query = "SELECT sub_assignee_id FROM " . $this->getTableName() . " WHERE project_id = :project_id";
    
    // Don't pass 'true' as third argument if it makes pm->run return only one row
    $result = $this->pm->run($query, $param); // remove the 'true'

    if (!$result || !is_array($result)) return [];

    $ids = [];
    foreach ($result as $row) {
        // Make sure each row has 'sub_assignee_id'
        if (isset($row['sub_assignee_id'])) {
            $ids[] = (int)$row['sub_assignee_id'];
        }
    }

    return $ids;
}
}
