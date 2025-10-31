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
    public $photo;
    public $user_status;

    function getTableName()
    {
        return 'users';
    }

    function createUser($full_name, $user_name, $email, $password, $role)
    {
        $userModel = new User();
        $existingUser = $userModel->getUserByUsernameOrEmail($user_name, $email);
        if ($existingUser) {
            return false;
        }
        $user = new User();
        $user->full_name = $full_name;
        $user->user_name = $user_name;
        $user->password = $password;
        $user->role = $role;
        $user->email = $email;
        $user->addNewRec();
        if ($user) {
            return $user;
        } else {
            return false;
        }
    }

    function createUserByAdmin($full_name, $user_name, $email, $password, $role)
    {
        $userModel = new User();
        $existingUser = $userModel->getUserByUsernameOrEmail($user_name, $email);
        if ($existingUser) {
            return false;
        }
        $user = new User();
        $user->full_name = $full_name;
        $user->user_name = $user_name;
        $user->password = $password;
        $user->role = $role;
        $user->email = $email;
        $user->status = 'confirmed';
        $addResult = $user->addNewRec();
        if ($addResult !== false) {
            $userId = $userModel->getLastInsertedUserId();
            require_once 'LeaveLimit.php';
            $leaveLimit = new LeaveLimit();
            $leaveLimit->createLeaveForUser($userId);
            return $user;
        }
    }

    function updateUser($id, $user_name, $email, $photoPath = null)
    {
        $userModel = new User();
        $oldUser = $userModel->getUserById($id);
        if (!$oldUser) {
            return false;
        }

        if ($user_name !== $oldUser['user_name'] || $email !== $oldUser['email']) {
            $existingUser = $userModel->getUserByUsernameOrEmailWithId($user_name, $email, $id);
            if ($existingUser) {
                return false;
            }
        }

        $user = new User();
        $user->id = $id;
        $user->user_name = $user_name;
        $user->email = $email;

        if (isset($photoPath) && isset($oldUser['photo'])) {
            if (file_exists(__DIR__ . '/../' . $oldUser['photo'])) {
                unlink(__DIR__ . '/../' . $oldUser['photo']);
            }
        }

        $user->photo = $photoPath !== null ? $photoPath : null;

        $result = $user->updateRec();
        return $result !== false;
    }



    function deleteUser($id)
    {
        $user = new User();
        $user->deleteRec($id);
        if ($user) {
            return true;
        } else {
            return false;
        }
    }

    protected function addNewRec()
    {
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $param = [
            ':full_name'   => $this->full_name,
            ':user_name'   => $this->user_name,
            ':email'       => $this->email,
            ':password'    => $this->password,
            ':role'        => $this->role,
            ':status'      => $this->status,
            ':user_status' => $this->user_status ?? 'active',
        ];
        return $this->pm->run(
            "INSERT INTO " . $this->getTableName() . " 
        (full_name,user_name,password,role,email,status,user_status) 
        VALUES (:full_name,:user_name,:password,:role,:email,:status,:user_status)",
            $param
        );
    }


    protected function updateRec()
    {
        $fields = [
            'user_name = :user_name',
            'email = :email'
        ];

        $param = [
            ':id' => $this->id,
            ':user_name' => $this->user_name,
            ':email' => $this->email
        ];

        if ($this->photo !== null) {
            $fields[] = 'photo = :photo';
            $param[':photo'] = $this->photo;
        }

        $sql = "UPDATE " . $this->getTableName() . " SET " . implode(', ', $fields) . " WHERE id = :id";

        return $this->pm->run($sql, $param);
    }

    function acceptUser($id)
    {
        $user = new User();
        $user->id = $id;
        $user->status = 'confirmed';
        $result = $user->updateStatus();
        if ($result !== false) {
            require_once 'LeaveLimit.php';
            $leaveLimit = new LeaveLimit();
            $leaveLimit->createLeaveForUser($id);
            return true;
        }

        return false;
    }

    function declineUser($id)
    {
        $user = new User();
        $user->id = $id;
        $user->status = 'declined';
        $result = $user->updateStatus();
        return $result !== false;
    }

    public function updateUserStatus($user_id, $status)
    {
        $sql = "UPDATE " . $this->getTableName() . " SET user_status = :status WHERE id = :id";
        $params = [
            ':status' => $status,
            ':id'     => $user_id
        ];
        $result = $this->pm->run($sql, $params);
        return $result > 0;
    }



    function updateStatus()
    {
        $param = [
            ':id' => $this->id,
            ':status' => $this->status
        ];

        return $this->pm->run(
            "UPDATE " . $this->getTableName() . " SET status = :status WHERE ID = :id",
            $param
        );
    }

    public function getUserByUsernameOrEmailWithId($user_name, $email, $excludeUserId = null)
    {
        $param = [
            ':user_name' => $user_name,
            ':email'     => $email
        ];

        $query = "SELECT * FROM " . $this->getTableName() . " 
              WHERE (user_name = :user_name OR email = :email)";

        if ($excludeUserId !== null) {
            $query .= " AND id != :excludeUserId";
            $param[':excludeUserId'] = $excludeUserId;
        }

        $result = $this->pm->run($query, $param);

        if (is_array($result) && count($result) > 0) {
            return $result;
        }

        return false;
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

    public function getUserbyStatus($status = 'pending')
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
    public function getAllActive()
    {
        return $this->pm->run(
            "SELECT * FROM " . $this->getTableName() . " 
     WHERE user_status = 'active' 
     ORDER BY id DESC"
        );
    }
}
