<?php
namespace App\Domain;

class Article
{
    private $model;
    public function __construct(){
        $this->model = new \App\Model\Article();
    }
    // 检查当前用户对该文章是否有权限
    public function checkArticlePermission($userId, $articleId){
        $article = $this->model->getArticle($articleId);
        if(empty($article)){
            throw new \PhalApi\Exception\BadRequestException('文章不存在');
        }
        if($article['user_id'] != $userId){
            throw new \PhalApi\Exception\BadRequestException('没有权限操作该文章');
        }
    }

    // 浏览量+1
    public function countViews($articleId){
        $res = $this->model->countViews($articleId);
        if(empty($res)){
            throw new \PhalApi\Exception\InternalServerErrorException('更新失败');
        }
        return $res;
    }
}