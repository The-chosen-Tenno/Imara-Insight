<?php
require_once 'BaseModel.php';

class Notifications extends BaseModel
{

    public $id;
    public $user_id;
    public $notifi_details;
    public $status;

    function getTableName()
    {
        return 'notifications';
    }
    function createNotifications($notifi_details, $user_id)
    {
        // $tagModel = new Notifications();
        // $existingTag = $tagModel->getTagByName($notifi_details);
        // if ($existingTag) {
        //     return $existingTag->id; // return existing tag ID
        // }

        $notifications = new Notifications();
        $notifications->notifi_details = $notifi_details;
        $notifications->user_id = $user_id;
        $new_notifications = $notifications->addNewRec();

        if ($new_notifications) {
            return $new_notifications; // return the ID, not the object
        } else {
            return false;
        }
    }

    protected function addNewRec()
    {
        $param = [
            ':notifi_details' => $this->notifi_details
        ];
        $result = $this->pm->run(
            "INSERT INTO " . $this->getTableName() . "(name) values(:notifi_details)",
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
    public function getAllUnreadNotifications($user_id)
    {
        $param = [
            ':user_id' => $this->user_id
        ];
        $query = "SELECT * FROM " . $this->getTableName() . " WHERE status ='unread' AND user_id = :user_id";
        $result = $this->pm->run($query, $param);

        if (!$result || !is_array($result)) return [];

        $notifications = [];
        foreach ($result as $row) {
            if (isset($row['name'])) {
                $notifications[] = $row['name'];
            }
        }

        return $notifications;
    }

    public function getAllTags()
    {
        return $this->pm->run(
            "SELECT * FROM " . $this->getTableName()
        );
    }
}
