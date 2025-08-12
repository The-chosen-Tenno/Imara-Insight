<?php

require_once 'BaseModel.php';

class User extends BaseModel
{
    public $user_name;
    public $full_name;
    public $role;
    private $email;
    private $password;

    function getTableName()
    {
        return 'users';
    }

    protected function addNewRec()
    {
        // Hash the password before storing it
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        $param = array(
            ':full_name' => $this->full_name,
            ':user_name' => $this->user_name,
            ':email' => $this->email,
            ':password' => $this->password,
            ':role' => $this->role,

        );

        return $this->pm->run("INSERT INTO " . $this->getTableName() . "(full_name,user_name,password, role, email) values(:full_name,:user_name,:password,:role,:email)", $param);
    }

    protected function updateRec()
    {
       $existingUser = $this->getUserByUsernameOrEmailWithId($this->user_name, $this->email, $this->id);
        if ($existingUser) {
            return false; 
        }


        $param = array(
            ':user_name' => $this->user_name,
            ':email' => $this->email,
            ':id' => $this->id
        );
        return $this->pm->run(
            "UPDATE " . $this->getTableName() . " 
            SET 
                user_name = :user_name,  
                email = :email
            WHERE ID = :id",
            $param
        );
    }

    public function getUserByUsernameOrEmailWithId($user_name, $email, $excludeUserId = null)
    {
        $param = array(':user_name' => $user_name, ':email' => $email);

        $query = "SELECT * FROM " . $this->getTableName() . " 
                  WHERE (user_name = :user_name OR email = :email)";

        if ($excludeUserId !== null) {
            $query .= " AND id != :excludeUserId";
            $param[':excludeUserId'] = $excludeUserId;
        }

        $result = $this->pm->run($query, $param);

        return $result; 
    }

    public function getUserByUsernameOrEmail($user_name, $email)
    {
        $param = array(
            ':user_name' => $user_name,
            ':email' => $email
        );

        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE user_name = :user_name OR email = :email";
        $result = $this->pm->run($sql, $param);

        if (!empty($result)) { 
            $user = $result[0];
            return $user;
        } else {
            return null;
        }
    }

    function createUser($full_name, $user_name, $email, $password,$role) 
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

    function updateUser($id, $user_name, $email,)
    {
        $userModel = new User();

        $existingUser = $userModel->getUserByUsernameOrEmailWithId($user_name, $email, $id);
        if ($existingUser) {
            return false; 
        }

        $user = new User();
        $user->id = $id;
        $user->user_name = $user_name;
        $user->email = $email;
      
        $user->updateRec();

        if ($user) {
            return true; 
        } else {
            return false; 
        }
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

    public function getLastInsertedUserId()
    {
        $result = $this->pm->run('SELECT MAX(id) as lastInsertedId FROM users', null, true);
        return $result['lastInsertedId'] ?? 100;
    }


    public function getUserById($id)
    {
        $param = array(':id' => $id);
        return $this->pm->run(" SELECT *
            FROM " . $this->getTableName() . "
            WHERE id = :id
        "
           , $param, true);
    }

    public function getUserbyStatus()
    {
       return $this->pm->run("SELECT * FROM " . $this->getTableName() . " WHERE status = 'pending'", [], true);
    }
}