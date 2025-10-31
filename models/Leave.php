<?php
require_once 'BaseModel.php';
require_once 'LeaveLimit.php';

class Leave extends BaseModel
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
    public $id;


    function getTableName()
    {
        return 'leave_requests';
    }

    function createLeaveReq($reason_type, $leave_note, $leave_duration, $half_day, $date_off, $start_date, $end_date, $description, $user_id)
    {
        $leaveModel = new Leave();
        if ($leave_duration === 'multi') {
            $existingLeave = $leaveModel->getLeaveByDateAndUserIDMultiDays($user_id, $start_date, $end_date);
            if ($existingLeave) {
                return false;
            }
        } else{
            $existingLeave = $leaveModel->getLeaveByDateAndUserID($user_id, $date_off);
            if ($existingLeave) {
                return false;
            }
        }


        $leave = new Leave();
        $leave->reason_type = $reason_type;
        $leave->leave_note = $leave_note;
        $leave->leave_duration = $leave_duration;
        $leave->half_day = ($leave_duration === 'half') ? $half_day : null;
        $leave->date_off = ($leave_duration === 'full') ? $date_off : null;
        $leave->start_date = ($leave_duration === 'multi') ? $start_date : null;
        $leave->end_date = ($leave_duration === 'multi') ? $end_date : null;
        $leave->description = $description;
        $leave->user_id = $user_id;
        $leave->addNewRec();

        return $leave ? $leave : false;
    }

    protected function addNewRec()
    {
        $param = [
            ':reason_type'    => $this->reason_type,
            ':leave_note'   => $this->leave_note,
            ':leave_duration' => $this->leave_duration,
            ':half_day'       => $this->half_day,
            ':date_off'       => $this->date_off,
            ':start_date'       => $this->start_date,
            ':end_date'       => $this->end_date,
            ':description'    => $this->description,
            ':user_id'        => $this->user_id,
        ];

        $query = "INSERT INTO " . $this->getTableName() . "
        (reason_type, leave_note, leave_duration, half_day, date_off, start_date, end_date, description, user_id)
        VALUES
        (:reason_type, :leave_note, :leave_duration, :half_day, :date_off, :start_date, :end_date, :description, :user_id)";

        return $this->pm->run($query, $param);
    }



    protected function updateRec() {}

    function approveLeave($id)
    {
        $leave = new Leave();
        $leave->id = $id;
        $leave->status = 'approved';
        $result = $leave->updateStatus();

        if ($result !== false && $leave->status === 'approved') {
            $AllleaveData = $this->getLeaveByID($id);
            $leaveData = $AllleaveData;
            $leaveLimits = new LeaveLimit();

            if ($leaveData['leave_duration'] === 'full') {

                $leaveLimits->useLeaveDays($leaveData['user_id'], 1, $leaveData['reason_type']);
            } elseif ($leaveData['leave_duration'] === 'multi') {

                $numDays = $leaveLimits->calculateDays(
                    $leaveData['start_date'],
                    $leaveData['end_date']
                );
                $leaveLimits->useLeaveDays($leaveData['user_id'], $numDays, $leaveData['reason_type']);
            } elseif ($leaveData['leave_duration'] === 'half') {

                $numHalfDays = $leaveLimits->trackHalfDay($leaveData['user_id'], $leaveData['reason_type']);
                if ($numHalfDays == 1) {
                    $leaveLimits->useLeaveDays($leaveData['user_id'], $numHalfDays, $leaveData['reason_type']);
                } else {
                }
            }
        }
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

        $result = $this->pm->run(
            "UPDATE " . $this->getTableName() . " SET status = :status WHERE id = :id",
            $param
        );
    }

    public function getLeavebyID($id)
    {
        $param = [':id' => $id];
        return $this->pm->run(
            "SELECT * FROM " . $this->getTableName() . " WHERE id = :id",
            $param,
            true
        );
    }

    public function getLeaveByDateAndUserID($user_id, $date_off)
    {
        $param = [
            ':user_id' => $user_id,
            ':date_off' => $date_off,

        ];
        $query = "SELECT * FROM " . $this->getTableName() . " 
              WHERE user_id = :user_id 
              AND date_off = :date_off";

        return $this->pm->run($query, $param);
    }

    public function getLeaveByDateAndUserIDMultiDays($user_id, $start_date, $end_date)
    {
        $param = [
            ':user_id' => $user_id,
            ':target_start' => $start_date,
            ':target_end' => $end_date
        ];

        $query = "SELECT * FROM " . $this->getTableName() . " 
              WHERE user_id = :user_id
              AND start_date <= :target_end
              AND end_date >= :target_start";

        $result = $this->pm->run($query, $param);
        return $result;
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
