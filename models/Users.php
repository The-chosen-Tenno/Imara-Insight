<?php
require_once 'BaseModel.php';

class User extends BaseModel
{
    public $user_name;
    public $full_name;
    public $role;
    public $status;
    private $email;
    private $password;

    function getTableName()
    {
        return 'users';
    }

    // ================= CREATE USER =================
    function createUser($full_name, $user_name, $email, $password, $role)
    {
        // Check if user/email already exists
        if ($this->getUserByUsernameOrEmail($user_name, $email)) {
            return false;
        }

        $this->full_name = $full_name;
        $this->user_name = $user_name;
        $this->password = $password;
        $this->role = $role;
        $this->email = $email;

        return $this->addNewRec() ? true : false;
    }

    // ================= UPDATE USER =================
    function updateUser($id, $user_name, $email)
    {
        if ($this->getUserByUsernameOrEmailWithId($user_name, $email, $id)) {
            return false;
        }

        $this->id = $id;
        $this->user_name = $user_name;
        $this->email = $email;

        return $this->updateRec() ? true : false;
    }

    // ================= DELETE USER =================
    function deleteUser($id)
    {
        $this->id = $id;
        return $this->deleteRec($id) ? true : false;
    }

    // ================= ADD RECORD =================
    protected function addNewRec()
    {
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        $param = [
            ':full_name' => $this->full_name,
            ':user_name' => $this->user_name,
            ':email' => $this->email,
            ':password' => $this->password,
            ':role' => $this->role,
        ];

        return $this->pm->run(
            "INSERT INTO " . $this->getTableName() . " (full_name,user_name,password,role,email) 
             VALUES (:full_name,:user_name,:password,:role,:email)",
            $param
        );
    }

    // ================= UPDATE RECORD =================
    protected function updateRec()
    {
        $param = [
            ':user_name' => $this->user_name,
            ':email' => $this->email,
            ':id' => $this->id
        ];

        return $this->pm->run(
            "UPDATE " . $this->getTableName() . " SET user_name = :user_name, email = :email WHERE id = :id",
            $param
        );
    }

    // ================= STATUS =================
    function acceptUser($id)
    {
        $this->id = $id;
        $this->status = 'confirmed';
        return $this->updateStatus() !== false;
    }

    function declineUser($id)
    {
        $this->id = $id;
        $this->status = 'declined';
        return $this->updateStatus() !== false;
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

    // ================= GET USER =================
    public function getUserByUsernameOrEmail($user_name, $email)
    {
        $param = [':user_name' => $user_name, ':email' => $email];
        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE user_name = :user_name OR email = :email";
        $result = $this->pm->run($sql, $param);
        return !empty($result) ? $result[0] : null;
    }

    public function getUserByUsernameOrEmailWithId($user_name, $email, $excludeUserId = null)
    {
        $param = [':user_name' => $user_name, ':email' => $email];
        $query = "SELECT * FROM " . $this->getTableName() . " WHERE (user_name = :user_name OR email = :email)";
        if ($excludeUserId !== null) {
            $query .= " AND id != :excludeUserId";
            $param[':excludeUserId'] = $excludeUserId;
        }
        $result = $this->pm->run($query, $param);
        return !empty($result) ? $result[0] : null;
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

    public function getUserByStatus($status = 'pending')
    {
        $query = "SELECT * FROM " . $this->getTableName() . " WHERE status = :status";
        $param = [':status' => $status];
        return $this->pm->run($query, $param);
    }

    public function getLastInsertedUserId()
    {
        $result = $this->pm->run('SELECT MAX(id) as lastInsertedId FROM ' . $this->getTableName(), null, true);
        return $result['lastInsertedId'] ?? null;
    }

    // ================= EMAIL EXISTS =================
    public function emailExists($email, $excludeId = null)
    {
        $param = [':email' => $email];
        $sql = "SELECT COUNT(*) as count FROM " . $this->getTableName() . " WHERE email = :email";
        if ($excludeId) {
            $sql .= " AND id != :id";
            $param[':id'] = $excludeId;
        }
        $result = $this->pm->run($sql, $param, true);
        return $result['count'] > 0;
    }

        public function user_nameExists($user_name, $excludeId = null)
    {
        $param = [':user_name' => $user_name];
        $sql = "SELECT COUNT(*) as count FROM " . $this->getTableName() . " WHERE user_name = :user_name";
        if ($excludeId) {
            $sql .= " AND id != :id";
            $param[':id'] = $excludeId;
        }
        $result = $this->pm->run($sql, $param, true);
        return $result['count'] > 0;
    }
}
