<?php
require_once 'BaseModel.php';

class ProjectTags extends BaseModel
{
    public $project_id;
    public $tag_id;

    function getTableName()
    {
        return 'project_tags';
    }

    function createProjectTags($project_id, $tag_id)
    {
        $projectTagsModel = new ProjectTags();
        $existingProjectTags = $projectTagsModel->getProjectTagsByProjectIDAndTagID($project_id, $tag_id);
        if ($existingProjectTags) {
            return false;
        }
        $ProjectTags = new ProjectTags();
        $ProjectTags->project_id = $project_id;
        $ProjectTags->tag_id = $tag_id;
        $ProjectTags->addNewRec();
        if ($ProjectTags) {
            return $ProjectTags;
        } else {
            return false;
        }
    }

    function updateProjectTags($project_id, $tag_id)
    {
        $projectTagsModel = new ProjectTags();
        $existingProjectTags = $projectTagsModel->getProjectTagsByProjectIDAndTagID($project_id, $tag_id);

        if ($existingProjectTags) {
            $existingProjectTags->project_id = $project_id;
            $existingProjectTags->tag_id = $tag_id;

            if ($existingProjectTags->updateRec()) {
                return $existingProjectTags;
            } else {
                return false;
            }
        }

        return false;
    }

    protected function addNewRec()
    {
        $param = [
            ':project_id' => $this->project_id,
            ':tag_id' => $this->tag_id
        ];
        $result = $this->pm->run(
            "INSERT INTO " . $this->getTableName() . "(project_id,tag_id) values(:project_id,:tag_id)",
            $param
        );
        return  $result;
    }

    protected function updateRec()
    {
        $param = [
            ':project_id' => $this->project_id,
            ':tag_id'     => $this->tag_id
        ];

        $result = $this->pm->run(
            "UPDATE " . $this->getTableName() . " 
         SET tag_id = :tag_id 
         WHERE project_id = :project_id",
            $param
        );

        return $result;
    }

    public function getProjectTagsByProjectIDAndTagID($project_id, $tag_id)
    {
        $param = [':project_id' => $project_id, ':tag_id' => $tag_id];
        $query = "SELECT * FROM " . $this->getTableName() . " WHERE (project_id = :project_id AND tag_id = :tag_id)";
        return $this->pm->run($query, $param);
    }
    public function getAllTagByProjectId($project_id)
    {
        $param = [':project_id' => $project_id];
        $query = "SELECT tag_id FROM " . $this->getTableName() . " WHERE project_id = :project_id";
        $result = $this->pm->run($query, $param);

        if (!$result || !is_array($result)) return [];
        $ids = [];
        foreach ($result as $row) {
            if (isset($row['tag_id'])) {
                $ids[] = (int)$row['tag_id'];
            }
        }

        return $ids;
    }

    public function removeProjectTag($project_id, $tag_id)
    {
        $param = [
            ':project_id' => $project_id,
            ':tag_id'     => $tag_id
        ];

        $result = $this->pm->run(
            "DELETE FROM " . $this->getTableName() . " 
         WHERE project_id = :project_id AND tag_id = :tag_id",
            $param
        );

        return $result;
    }
    public function getByUserId($userId)
    {
        $param = array(':user_id' => $userId);
        return $this->pm->run("SELECT * FROM " . $this->getTableName() . " WHERE sub_assignee_id = :user_id", $param);
    }
}
