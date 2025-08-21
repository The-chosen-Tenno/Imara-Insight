<?php
require_once 'BaseModel.php';

class ProjectImageModel extends BaseModel
{
    protected function getTableName()
    {
        return "project_images";
    }

    // REQUIRED: empty methods to satisfy BaseModel
    protected function addNewRec()
    {
        // Not used here
        return false;
    }

    protected function updateRec()
    {
        // Not used here
        return false;
    }

    public function saveProjectImages($project_id, $images)
    {
        foreach ($images as $img) {
            $params = [
                ':project_id' => $project_id,
                ':title' => $img['title'],
                ':description' => $img['description'],
                ':file_path' => $img['file'],
            ];

            $this->pm->run(
                "INSERT INTO " . $this->getTableName() . " 
                 (project_id, title, description, file_path) 
                 VALUES (:project_id, :title, :description, :file_path)",
                $params
            );
        }
        return true;
    }

    public function getImagebyProjectId($project_id)
    {
        $params = [':project_id' => $project_id];
        return $this->pm->run("SELECT * FROM " . $this->getTableName() . " WHERE project_id = :project_id", $params);
    }

    function getAll()
    {
        $sql = "SELECT * FROM project_images";
        return $this->pm->run($sql);
    }
}

