<?php
namespace App\Model\User;

use PhalApi\Model\DataModel;

class Info extends DataModel
{
    public function getInfo($userId)
    {
        $rs = $this->getORM()
            ->select('*')
            ->where('id = ?', $userId)
            ->fetchRow();

        return $rs;
    }

    public function updateInfo($userId, $data)
    {
        $rs = $this->getORM()
            ->where('id = ?', $userId)
            ->update($data);

        return $rs;
    }

    public function addInfo($data)
    {
        $rs = $this->getORM()
            ->insert($data);

        return $rs;
    }

    public function deleteInfo($userId)
    {
        $rs = $this->getORM()
            ->where('id = ?', $userId)
            ->delete();

        return $rs;
    }
}