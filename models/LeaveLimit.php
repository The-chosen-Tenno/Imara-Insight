<?php
require_once 'BaseModel.php';

class LeaveLimit extends BaseModel
{
    public $reason_type;
    public $leave_note;
    public $leave_duration;
    public $half_day;
    public $date_off;
    public $start_date;
    public $end_date;
    public $description;
    public $user_id;
    public $status;
    public $leave_day_count;
    public $id;


    protected function getTableName()
    {
        return 'leave_limits';
    }

    function createLeaveForUser($user_id)
    {
        $param = [':user_id' => $user_id];
        $sql = "INSERT INTO " . $this->getTableName() . " (user_id) VALUES (:user_id)";
        return $this->pm->run($sql, $param);
    }

    function getAllRemainingLeave($user_id)
    {
        $param = [':user_id' => $user_id];
        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE user_id = :user_id";
        return $this->pm->run($sql, $param);
    }

    protected function addNewRec() {}

    protected function updateRec() {}

    public function useLeaveDays($user_id, $leave_day_count, $reason_type)
    {
        $reason_type_balance = $reason_type . '_balance';

        if ($leave_day_count == 1) {
            $all_leave_details  = $this->getAllRemainingLeave($user_id);
            $leave_details = $all_leave_details[0];

            if (isset($leave_details[$reason_type_balance]) && $leave_details[$reason_type_balance] > 0) {
                $this->updateLeaveBalance(1, $user_id, $reason_type_balance);

                return true;
            } else {
                return false;
            }
        }
    }

    public function updateLeaveBalance($day_count, $user_id, $reason_type_balance)
    {
        $sql = "UPDATE " . $this->getTableName() . " 
            SET $reason_type_balance = $reason_type_balance - :day_count
            WHERE user_id = :user_id";

        $params = [
            ':day_count' => $day_count,
            ':user_id' => $user_id
        ];

        return $this->pm->run($sql, $params);
    }

    public function calculateDays() {}

    public function trackHalfDay() {}

    // protected function getLeaveStatusByIdAndType($user_id, $reason_type)
    // {
    //     $param = [':user_id' => $user_id];
    //     $sql = "SELECT * FROM " . $this->getTableName() . " WHERE user_id = :user_id";
    //     return $this->pm->run($sql, $param);
    // }

    // public function getUserById($id)
    // {
    //     $param = [':id' => $id];
    //     return $this->pm->run(
    //         "SELECT * FROM " . $this->getTableName() . " WHERE id = :id",
    //         $param,
    //         true
    //     );
    // }

    public function getLastInsertedUserId()
    {
        $result = $this->pm->run('SELECT MAX(id) as lastInsertedId FROM users', null, true);
        return $result['lastInsertedId'] ?? 100;
    }
}
