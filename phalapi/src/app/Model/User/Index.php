<?php
namespace App\Model\User;

use PhalApi\Model\DataModel;

class Index extends DataModel
{
    const EXCLUDE_PASSWORD_FIELDS = 'id,username,nickname,avatar,email,phone_number,created_time,updated_time,last_login_time,admin';
    const SAFETY_FIELDS = 'id,username,nickname,avatar,email,created_time,updated_time,last_login_time';
    public function getTableName()
    {
        return 'user';
    }

    public function login($username, $password_hash)
    {
        return $this->getData(
            array('username' => $username, 'password_hash' => $password_hash),
            array(),
                $this::EXCLUDE_PASSWORD_FIELDS
        );
    }

    public function register($data)
    {
        return $this->insert($data);
    }

    public function updateLoginTime($id)
    {
        return $this->update($id, array('last_login_time' => time()));
    }

    public function checkUsername($username)
    {
        return $this->getData(array('username' => $username));
    }
    public function getUserByUsername($username)
    {
        return $this->getDataBy('username', $username, $this::EXCLUDE_PASSWORD_FIELDS);
    }
    public function getUserById($id)
    {
        return $this->get($id, $this::SAFETY_FIELDS);
    }
    public function updateProfile($id, $data)
    {
        return $this->update($id, $data);
    }

}