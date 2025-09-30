<?php
require_once 'BaseModel.php';

class Tags extends BaseModel
{

    public $name;

    function getTableName()
    {
        return 'tags';
    }

  function createTag($name)
{
    $tagModel = new Tags();
    $existingTag = $tagModel->getTagByName($name);
    if ($existingTag) {
        return $existingTag->id; // return existing tag ID
    }

    $tag = new Tags();
    $tag->name = $name;
    $tagId = $tag->addNewRec(); // addNewRec() returns the new ID

    if ($tagId) {
        return $tagId; // return the ID, not the object
    } else {
        return false;
    }
}

    protected function addNewRec()
    {
        $param = [
            ':name' => $this->name
        ];
        $result = $this->pm->run(
            "INSERT INTO " . $this->getTableName() . "(name) values(:name)",
            $param
        );
         return $this->pm->lastInsertId();
    }

    protected function updateRec() {}

    public function getTagByName($name)
    {
        $param = [':name' => $name];
        $query = "SELECT * FROM " . $this->getTableName() . " WHERE (name = :name)";
        return $this->pm->run($query, $param);
    }
    public function getAllTagByName()
    {
        $query = "SELECT `name` FROM " . $this->getTableName();
        $result = $this->pm->run($query);

        if (!$result || !is_array($result)) return [];

        $names = [];
        foreach ($result as $row) {
            if (isset($row['name'])) {
                $names[] = $row['name'];
            }
        }

        return $names;
    }

    public function getAllTags()
    {
        return $this->pm->run(
            "SELECT * FROM " . $this->getTableName()
        );
    }
}
