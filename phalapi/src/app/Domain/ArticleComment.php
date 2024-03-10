<?php
namespace App\Domain;

class ArticleComment
{
    private $model;
    private $userModel;
    public function __construct()
    {
        $this->model = new \App\Model\ArticleComment();
        $this->userModel = new \App\Model\User\Index();
    }

    // 获取文章评论，拿到顶级评论，再获取二级评论，再获取回复评论, 回复评论和二级评论是同一级的，所以可以合并在一起为顶级评论的二级评论，reply_id不为0的评论中添加被回复的评论的信息
    public function getCommentList($articleId, $page, $pageSize)
    {
        $commentList = $this->getTopCommentList($articleId, $page, $pageSize);
        foreach ($commentList as $key => $value) {
            $commentList[$key]['sub_comment'] = $this->getSubComment($value['id']);
            foreach ($commentList[$key]['sub_comment'] as $k => $v) {
                if ($v['reply_id'] != 0) {
                    $commentList[$key]['sub_comment'][$k]['reply_comment'] = $this->getCommentById($v['reply_id']);
                }
            }
        }
        return $commentList;
    }
    public function getTopCommentList($articleId, $page, $pageSize)
    {
        $comment = $this->model->getTopCommentList($articleId, $page, $pageSize);
        foreach ($comment as $key => $value) {
            $user = $this->userModel->getUserById($value['user_id']);
            $comment[$key]['user'] = $user;
        }
        return $comment;
    }

    public function getSubComment($commentId)
    {
        $comment = $this->model->getSubComment($commentId);
        foreach ($comment as $key => $value) {
            $user = $this->userModel->getUserById($value['user_id']);
            $comment[$key]['user'] = $user;
        }
        return $comment;
    }

    public function getCommentById($commentId)
    {
        $comment=  $this->model->getCommentById($commentId);
        $user = $this->userModel->getUserById($comment['user_id']);
        $comment['user'] = $user;
        return $comment;
    }

}