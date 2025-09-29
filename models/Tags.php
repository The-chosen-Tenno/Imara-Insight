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
            return false;
        }
        $tag = new Tags();
        $tag->name = $name;
        $tag->addNewRec();
        if ($tag) {
            return $tag;
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
        return  $result;
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
