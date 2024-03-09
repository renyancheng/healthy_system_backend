<?php
namespace App\Model\User;

use PhalApi\Model\DataModel;

class Token extends DataModel
{
    public function validate($token)
    {
        return $this->getORM()->select('*')->where('token', $token)->fetchOne();
    }
    public function deleteToken($id)
    {
        return $this->delete(array('id' => $id));
    }

    public function getToken($id)
    {
        return $this->getORM()->select('*')->where('id', $id)->fetchOne();
    }
    public function createToken($data)
    {
        return $this->insert($data);
    }
}