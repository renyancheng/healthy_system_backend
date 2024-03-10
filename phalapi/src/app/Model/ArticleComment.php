<?php
namespace App\Model;

use PhalApi\Model\DataModel;

class ArticleComment extends DataModel
{
    public function getTableName()
    {
        return 'article_comment';
    }

    public function createComment($data)
    {
        return $this->getORM()->insert($data);
    }

    public function getCommentById($commentId)
    {
        return $this->getDataBy('id', $commentId);
    }

    public function getTopCommentList($articleId, $page, $pageSize)
    {
        return $this->getORM()
            ->where('article_id', $articleId)
            ->where('parent_comment_id', 0)
            ->page($page, $pageSize)
            ->fetchAll();
    }

    public function getSubComment($commentId)
    {
        return $this->getORM()
            ->where('parent_comment_id', $commentId)
            ->fetchAll();
    }

    public function getTotalPage($articleId, $pageSize)
    {
        $total = $this->getORM()
            ->where('article_id', $articleId)
            ->count();
        return ceil($total / $pageSize);
    }

    public function deleteComment($commentId)
    {
        $subComment = $this->getSubComment($commentId);
        if (!empty($subComment)) {
            foreach ($subComment as $comment) {
                $this->deleteComment($comment['id']);
            }
        }
        return $this->getORM()
            ->where('id', $commentId)
            ->delete();
    }
}