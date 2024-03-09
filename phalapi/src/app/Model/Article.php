<?php
namespace App\Model;

use PhalApi\Model\DataModel;

class Article extends DataModel
{
    const EXCLUDE_CONTENT_FIELDS = 'id, user_id, title, views, created_time';
    public function getArticle($id)
    {
        return $this->getORM()
            ->where('id = ?', $id)
            ->fetch();
    }

    public function createArticle($userId, $title, $content)
    {
        return $this->getORM()
            ->insert(array(
                'user_id' => $userId,
                'title' => $title,
                'content' => $content,
                'views' => 0,
                'created_time' => time(),
            ));
    }

    public function getArticleList($page, $pageSize)
    {
        return $this->getORM()
            ->select($this::EXCLUDE_CONTENT_FIELDS)
            ->page($page, $pageSize)
            ->fetchAll();
    }

    public function getTotalPage($pageSize){
        $total = $this->getORM()
            ->select('count(*) as total')
            ->fetch();
        return ceil($total['total'] / $pageSize);
    }

    public function updateArticle($id, $title, $content)
    {
        return $this->getORM()
            ->where('id = ?', $id)
            ->update(array(
                'title' => $title,
                'content' => $content,
            ));
    }

    public function deleteArticle($id)
    {
        return $this->getORM()
            ->where('id = ?', $id)
            ->delete();
    }

    public function countViews($id)
    {
        return $this->updateCounter(array('id'=>$id), array('views' => 1));
    }
}