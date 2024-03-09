<?php
namespace App\Domain;

class DietDiary
{
    public $model;
    public function __construct()
    {
        $this->model = new \App\Model\DietDiary();
    }
    /**
     * 检查用户是否已经记录了某天的饮食
     * @param $userId 用户id
     * @param $date 时间戳
     */
    public function checkUserDietDiary($userId, $date)
    {
        // $date是时间戳, 数据库里的也是时间戳，字段名是created_time,看是不是一天
        $start = strtotime(date('Y-m-d', $date));
        $end = $start + 86400;
        $res = $this->model->getData(
            "user_id = ? AND created_time >= ? AND created_time < ?",
            [$userId, $start, $end]
        );
        if ($res) {
            throw new \PhalApi\Exception\BadRequestException('今天已经记录过饮食了');
        } else {
            return true;
        }
    }

    /**
     * 检查jwt的payload里的user_id是否有权限操作这条饮食记录
     * @param $userId 用户id
     * @param $diaryId 饮食记录id
     */
    public function checkUserDietDiaryPermission($userId, $diaryId)
    {
        // 先查这个饮食记录存不存在
        $res = $this->model->getDataBy('id', $diaryId);
        if (!$res) {
            throw new \PhalApi\Exception\BadRequestException('饮食记录不存在');
        }
        // 再查这个饮食记录是不是这个用户的
        if ($res['user_id'] != $userId) {
            throw new \PhalApi\Exception\BadRequestException('没有权限操作这条饮食记录');
        }
        return true;
    }

    /**
     * 流量量统计
     * @param $diaryId 饮食记录id
     * 
     */
    public function countViews($diaryId)
    {
        $res = $this->model->getDataBy('id', $diaryId);
        if (!$res) {
            throw new \PhalApi\Exception\BadRequestException('饮食记录不存在');
        }
        $this->model->updateCounter(array('id' => $diaryId), array('views' => 1));
        return true;
    }
}