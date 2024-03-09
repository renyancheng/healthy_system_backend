<?php
namespace App\Model;

use PhalApi\Model\DataModel;

class Upload extends DataModel
{
    public function upload($data)
    {
        return $this->insert($data);
    }

    public function getUploadByUserId($user_id)
    {
        return $this->getORM()->select('*')->where('user_id', $user_id)->fetchAll();
    }

    public function getUploadById($id)
    {
        return $this->getORM()->select('*')->where('id', $id)->fetchOne();
    }

    public function deleteUpload($id)
    {
        return $this->delete(array('id' => $id));
    }
}