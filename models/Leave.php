<?php
require_once 'BaseModel.php';

class Leave extends BaseModel
{
    public $reason_type;
    public $other_reason;
    public $date_off;
    public $description;
    public $user_id;
    public $status;
    function getTableName()
    {
        return 'leave_requests';
    }

    function createLeaveReq($reason_type, $other_reason, $date_off, $description, $user_id)
    {
        $leaveModel = new Leave();
        $existingLeave = $leaveModel->getLeaveByDateAndUserID($user_id, $date_off);
        if ($existingLeave) {
            return false;
        }
        $leave = new Leave();
        $leave->reason_type = $reason_type;
        $leave->other_reason = $other_reason;
        $leave->date_off = $date_off;
        $leave->description = $description;
        $leave->user_id = $user_id;
        $leave->addNewRec();
        if ($leave) {
            return $leave;
        } else {
            return false;
        }
    }

    function updateUser()
    {
    }


    protected function addNewRec()
    {
        $param = [
            ':reason_type' => $this->reason_type,
            ':other_reason' => $this->other_reason,
            ':date_off' => $this->date_off,
            ':description' => $this->description,
            ':user_id' => $this->user_id,
        ];
        $result = $this->pm->run(
            "INSERT INTO " . $this->getTableName() . "(reason_type,other_reason,date_off, description, user_id) values(:reason_type,:other_reason,:date_off,:description,:user_id)",
            $param
        );
        return  $result;
    }

    protected function updateRec()
    {
    }

    function approveLeave($id)
    {
        $leave = new Leave();
        $leave->id = $id;
        $leave->status = 'approved';
        $result = $leave->updateStatus();
        return $result !== false;
    }

    function denyLeave($id)
    {
        $user = new Leave();
        $user->id = $id;
        $user->status = 'denied';
        $result = $user->updateStatus();
        return $result !== false;
    }

    function updateStatus()
    {
        $param = [
            ':id' => $this->id,
            ':status' => $this->status
        ];

        return $this->pm->run(
            "UPDATE " . $this->getTableName() . " SET status = :status WHERE id = :id",
            $param
        );
    }

    public function getLeaveByDateAndUserID($user_id, $date_off)
    {
        $param = [':user_id' => $user_id, ':date_off' => $date_off];
        $query = "SELECT * FROM " . $this->getTableName() . " WHERE (user_id = :user_id AND date_off = :date_off)";
        return $this->pm->run($query, $param);
    }

    public function getUserByUsernameOrEmail($user_name, $email)
    {
        $param = [':user_name' => $user_name, ':email' => $email];
        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE user_name = :user_name OR email = :email";
        $result = $this->pm->run($sql, $param);
        if (!empty($result)) {
            return $result[0];
        }
        return null;
    }

    public function getUserById($id)
    {
        $param = [':id' => $id];
        return $this->pm->run(
            "SELECT * FROM " . $this->getTableName() . " WHERE id = :id",
            $param,
            true
        );
    }

    public function getLeavebyStatus($status = 'pending')
    {
        $query = "SELECT * FROM " . $this->getTableName() . " WHERE status = :status";
        $param = [':status' => $status];
        return $this->pm->run($query, $param);
    }

    public function getLastInsertedUserId()
    {
        $result = $this->pm->run('SELECT MAX(id) as lastInsertedId FROM users', null, true);
        return $result['lastInsertedId'] ?? 100;
    }
}
