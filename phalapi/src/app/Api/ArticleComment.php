<?php
namespace App\Api;

use PhalApi\Api;

/**
 * 文章评论接口
 */
class ArticleComment extends Api
{

    public function __construct()
    {
        $this->model = new \App\Model\ArticleComment();
        $this->domain = new \App\Domain\ArticleComment();
        $this->tokenDomain = new \App\Domain\User\Token();
    }
    public function getRules()
    {
        return array(
            'createData' => array(
                'article_id' => array('name' => 'article_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '文章ID'),
                'content' => array('name' => 'content', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '评论内容'),
                'parent_comment_id' => array('name' => 'parent_comment_id', 'type' => 'int', 'min' => 0, 'default' => 0, 'require' => false, 'desc' => '父评论ID'),
                'reply_id' => array('name' => 'reply_id', 'type' => 'int', 'min' => 0, 'default' => 0, 'require' => false, 'desc' => '回复评论ID'),
            ),
            'getList' => array(
                'article_id' => array('name' => 'article_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '文章ID'),
                'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default' => 1, 'require' => false, 'desc' => '页码'),
                'pageSize' => array('name' => 'pageSize', 'type' => 'int', 'min' => 1, 'default' => 10, 'require' => false, 'desc' => '每页数量'),
            ),
            'deleteData' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '评论ID'),
            ),
        );
    }

    /**
     * 创建文章评论
     * @desc 创建文章评论
     * @method POST
     */
    public function createData()
    {
        $payload = $this->tokenDomain->getPayloadAndCheckLogin();
        $userId = $payload['user_id'];
        $articleId = $this->article_id;
        $content = $this->content;
        $parentId = $this->parent_comment_id;
        $data = array(
            'user_id' => $userId,
            'article_id' => $articleId,
            'content' => $content,
            'parent_comment_id' => $parentId,
            'reply_id' => $this->reply_id,
            'comment_date' => time(),
        );
        $res = $this->model->createComment($data);
        if (empty($res)) {
            throw new \PhalApi\Exception\BadRequestException('评论失败');
        }
        return $res;
    }

    /**
     * 获取评论列表
     * @desc 获取文章评论列表
     * @method GET
     */
    public function getList()
    {
        $articleId = $this->article_id;
        $page = $this->page;
        $pageSize = $this->pageSize;
        $res = $this->domain->getCommentList($articleId, $page, $pageSize);
        if (empty($res)) {
            return array();
        }
        $totalPage = $this->model->getTotalPage($articleId, $pageSize);
        return [
            'totalPage' => $totalPage,
            'list' => $res,
        ];
    }

    /**
     * 删除评论
     * @desc 删除评论
     * @method POST
     */
    public function deleteData()
    {
        $payload = $this->tokenDomain->getPayloadAndCheckLogin();
        $userId = $payload['user_id'];
        $commentId = $this->id;
        $comment = $this->model->getCommentById($commentId);
        if (empty($comment)) {
            throw new \PhalApi\Exception\BadRequestException('评论不存在');
        }
        if ($comment['user_id'] != $userId) {
            throw new \PhalApi\Exception\BadRequestException('无权删除');
        }
        $res = $this->model->deleteComment($commentId);
        if (empty($res)) {
            throw new \PhalApi\Exception\BadRequestException('删除失败');
        }
        return true;
    }
}