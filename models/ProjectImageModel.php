<?php

require_once 'BaseModel.php';

class ProjectImageModel extends BaseModel
{

    public $ProjectID;
    public $file_path;

    protected function getTableName()
    {
        return "project_images";
    }

    protected function addNewRec() {}
    protected function updateRec() {}
    public function saveProjectImages(int $projectId, array $fileNames)
    {
        $sql = "INSERT INTO project_images (project_id, file_path) VALUES (:project_id, :file_path)";

        foreach ($fileNames as $fileName) {
            $params = [
                ':project_id' => $projectId,
                ':file_path' => $fileName,
            ];
            $this->pm->run($sql, $params);
        }
    }
    
}
