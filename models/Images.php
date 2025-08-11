<?php
require_once 'BaseModel.php';

class project_images extends BaseModel
{
   protected function getTableName()
   {
       return "project_images";
   }

   protected function addNewRec()
   {
       // function body here...
   }

   protected function updateRec()
   {
       // function body here...
   }

   function getImagebyProjectId($project_id)
   {
    return ($project_id);
   }
}
?>
