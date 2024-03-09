<?php
namespace App\Api;

use PhalApi\Api;

/**
 * 文章接口
 */
class Article extends Api
{
    public function __construct()
    {
        $this->model = new \App\Model\Article();
        $this->userModel = new \App\Model\User\Index();
        $this->tokenDomain = new \App\Domain\User\Token();
        $this->articleDomain = new \App\Domain\Article();
    }

    public function getRules()
    {
        return array(
            'getData' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '文章ID'),
            ),
            'getList' => array(
                'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default' => 1, 'desc' => '页码'),
                'pageSize' => array('name' => 'pageSize', 'type' => 'int', 'min' => 1, 'default' => 10, 'desc' => '每页数量'),
            ),
            'createData' => array(
                'title' => array('name' => 'title', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '文章标题'),
                'content' => array('name' => 'content', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '文章内容'),
            ),
            'updateData' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '文章ID'),
                'title' => array('name' => 'title', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '文章标题'),
                'content' => array('name' => 'content', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '文章内容'),
            ),
            'deleteData' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '文章ID'),
            ),
        );
    }

    /**
     * 创建文章
     * @desc 创建文章
     * @method POST
     * @return int id 文章ID
     */
    public function createData()
    {
        $payload = $this->tokenDomain->getPayloadAndCheckLogin();
        $userId = $payload['user_id'];
        $title = $this->title;
        $content = $this->content;
        $row = $this->model->createArticle($userId, $title, $content);
        if (empty($row)) {
            throw new \PhalApi\Exception\InternalServerErrorException('创建失败');
        }
        return $row;
    }

    /**
     * 获取文章
     * @desc 获取单一文章
     * @method GET
     * @return array data 文章数据
     */
    public function getData()
    {
        $id = $this->id;
        $article = $this->model->getArticle($id);
        if (empty($article)) {
            throw new \PhalApi\Exception\BadRequestException('文章不存在');
        }
        $user = $this->userModel->getUserById($article['user_id']);
        $this->articleDomain->countViews($id);
        $article['user'] = $user;
        return $article;
    }

    /**
     * 获取文章列表
     * @desc 获取文章列表
     * @method GET
     * @return array list 文章列表
     */
    public function getList()
    {
        $page = $this->page;
        $pageSize = $this->pageSize;
        $list = $this->model->getArticleList($page, $pageSize);
        foreach ($list as &$item) {
            $user = $this->userModel->getUserById($item['user_id']);
            $item['user'] = $user;
        }
        $totalPage = $this->model->getTotalPage($pageSize);
        return ['totalPage' => $totalPage, 'list' => $list];
    }

    /**
     * 更新文章
     * @desc 更新文章
     * @method POST
     */
    public function updateData()
    {
        $payload = $this->tokenDomain->getPayloadAndCheckLogin();
        $userId = $payload['user_id'];
        $this->articleDomain->checkArticlePermission($userId, $this->id);
        $id = $this->id;
        $title = $this->title;
        $content = $this->content;
        $res = $this->model->updateArticle($id, $title, $content);
        if (empty($res)) {
            throw new \PhalApi\Exception\InternalServerErrorException('更新失败');
        }
        return true;
    }

    /**
     * 删除文章
     * @desc 删除文章
     * @method POST
     */
    public function deleteData()
    {
        $payload = $this->tokenDomain->getPayloadAndCheckLogin();
        $userId = $payload['user_id'];
        $this->articleDomain->checkArticlePermission($userId, $this->id);
        $id = $this->id;
        $res = $this->model->deleteArticle($id);
        if (empty($res)) {
            throw new \PhalApi\Exception\InternalServerErrorException('删除失败');
        }
        return true;
    }
}