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
        return  $this->pm->run($sql, $param);
    }

    protected function addNewRec() {}

    protected function updateRec() {}

    public function useLeaveDays($user_id, $leave_day_count, $reason_type)
    {
        $reason_type_balance = $reason_type . '_balance';

        if ($leave_day_count >= 1) {
            $all_leave_details  = $this->getAllRemainingLeave($user_id);
            $leave_details = $all_leave_details[0];
            if ($leave_day_count <= $leave_details[$reason_type_balance]) {

                $this->updateLeaveBalance($leave_day_count, $user_id, $reason_type_balance);
                return true;
            } elseif ($leave_day_count > $leave_details[$reason_type_balance]) {

                $reason_type_extra = $reason_type . '_extra';
                $extra_leave_count = $leave_day_count - $leave_details[$reason_type_balance];
                $already_extra_leave = $leave_details[$reason_type_extra];
                $new_leave_count = $already_extra_leave + $extra_leave_count;
                $this->updateExtraLeaveBalance($new_leave_count, $user_id, $reason_type_balance, $reason_type_extra, $reason_type);
                return true;
            } else {

                return false;
            }
        }

        return false;
    }

    public function updateExtraLeaveBalance($new_leave_count, $user_id, $reason_type_balance, $reason_type_extra, $reason_type)
    {
        $reason_type_status = $reason_type . '_status';

        $params = [
            ':new_leave_count' => $new_leave_count,
            ':user_id' => $user_id
        ];

        $sql = "UPDATE " . $this->getTableName() . " SET 
            $reason_type_balance = 0,
            $reason_type_extra = :new_leave_count,
            $reason_type_status = 'overused'
            WHERE user_id = :user_id";

        return $this->pm->run($sql, $params);
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

    public function calculateDays($start_date, $end_date)
    { {

            $start = new DateTime($start_date);
            $end = new DateTime($end_date);
            $diff = $start->diff($end);

            return $diff->days;
        }
    }

    public function trackHalfDay($user_id, $reason_type)
    {
        $reason_type = $reason_type . '_half_day_count';
        $params = [
            ':user_id' => $user_id
        ];

        $sql = $this->pm->run("SELECT $reason_type FROM " . $this->getTableName() . " WHERE user_id = :user_id", $params);

        if ($sql[0][$reason_type] == 1) {

            $this->pm->run("UPDATE " . $this->getTableName() . " SET 
            $reason_type = 0
            WHERE user_id = :user_id", $params);

            return 1;
        } else {
            $this->pm->run("UPDATE " . $this->getTableName() . " SET 
            $reason_type = 1
            WHERE user_id = :user_id", $params);
            return 0;
        }
    }

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
