<?php
namespace App\Api;

use PhalApi\Api;

/**
 * 饮食日记接口
 */
class DietDiary extends Api
{
    public function getRules()
    {
        return array(
            'createData' => array(
                'meals' => array('name' => 'meals', 'type' => 'string', 'require' => true, 'desc' => '饮食内容'),
            ),
            'deleteData' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '饮食日记id'),
            ),
            'updateData' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '饮食日记id'),
                'meals' => array('name' => 'meals', 'type' => 'string', 'require' => true, 'desc' => '饮食内容'),
            ),
            'getData' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '饮食日记id'),
            ),
            'getList' => array(
                'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default' => 1, 'desc' => '页码'),
                'pageSize' => array('name' => 'pageSize', 'type' => 'int', 'min' => 1, 'default' => 10, 'desc' => '每页数量'),
            ),
        );
    }
    public function __construct()
    {
        $this->model = new \App\Model\DietDiary();
        $this->tokenDomain = new \App\Domain\User\Token();
        $this->dietDiaryDomain = new \App\Domain\DietDiary();
    }

    /**
     * 创建饮食日记
     * @desc 创建饮食日记
     * @method POST
     */
    public function createData()
    {
        $payload = $this->tokenDomain->getPayloadAndCheckLogin();
        $userId = $payload['user_id'];
        $created_time = time();
        $this->dietDiaryDomain->checkUserDietDiary($userId, $created_time);
        $data = array(
            'user_id' => $userId,
            'meals' => $this->meals,
            'views' => 0,
            'created_time' => $created_time,
        );
        $res = $this->model->insert($data);
        if (empty($res)) {
            throw new \PhalApi\Exception\BadRequestException('创建失败');
        }
        $data = $this->model->getDataBy('id', (int) $res);
        return $data;
    }

    /**
     * 更新饮食日记
     * @desc 更新饮食日记
     * @method POST
     */
    public function updateData()
    {
        $payload = $this->tokenDomain->getPayloadAndCheckLogin();
        $userId = $payload['user_id'];
        $this->dietDiaryDomain->checkUserDietDiaryPermission($userId, $this->id);
        $data = array(
            'meals' => $this->meals,
        );
        $res = $this->model->update($this->id, $data);
        if ($res) {
            return true;
        } else {
            throw new \PhalApi\Exception\BadRequestException('更新失败');
        }
    }

    /**
     * 获取饮食日记详情
     * @desc 获取饮食日记详情
     * @method GET
     */
    public function getData()
    {
        $payload = $this->tokenDomain->getPayloadAndCheckLogin();
        $this->dietDiaryDomain->checkUserDietDiaryPermission($payload['user_id'], $this->id);
        $data = $this->model->getDataBy('id', $this->id);
        if (empty($data)) {
            throw new \PhalApi\Exception\BadRequestException('饮食日记不存在');
        }
        $this->dietDiaryDomain->countViews($data['id']);
        return $data;
    }

    /**
     * 获取饮食日记列表
     * @desc 获取饮食日记列表
     * @method GET
     */
    public function getList()
    {
        $payload = $this->tokenDomain->getPayloadAndCheckLogin();
        $page = $this->page;
        $pageSize = $this->pageSize;
        $where = 'user_id = ?';
        $params = [$payload['user_id']];
        $total = $this->model->count(array('user_id' => $payload['user_id']));
        $totalPage = ceil($total / $pageSize);
        $data = $this->model->getList($where, $params, '*', "created_time DESC", $page, $pageSize);
        // 获取列表没必要更新浏览量
        /* foreach ($data as $key => $value) {
            // 更新浏览量
            $this->dietDiaryDomain->countViews($value['id']);
        } */
        // $data['totalPage'] = $totalPage;
        return [
            'totalPage' => $totalPage,
            'list' => $data,
        ];
    }

    /**
     * 删除饮食日记
     * @desc 删除饮食日记
     * @method POST
     */
    public function deleteData()
    {
        $payload = $this->tokenDomain->getPayloadAndCheckLogin();
        $userId = $payload['user_id'];
        $this->dietDiaryDomain->checkUserDietDiaryPermission($userId, $this->id);
        $res = $this->model->delete($this->id);
        if ($res) {
            return true;
        } else {
            throw new \PhalApi\Exception\BadRequestException('删除失败');
        }
    }


}