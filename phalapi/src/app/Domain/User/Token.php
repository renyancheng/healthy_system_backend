<?php
namespace App\Domain\User;

class Token
{
    private $model;
    public function __construct()
    {
        $this->model = new \App\Model\User\Token();
    }

    /**
     * 获取jwt的payload并检查登录状态
     * @return array
     */
    function getPayloadAndCheckLogin()
    {
        $payload = \PhalApi\DI()->jwt->decodeJwt();
        if (empty($payload['user_id'])) {
            throw new \PhalApi\Exception\BadRequestException('无效token');
        }
        $token = \PhalApi\DI()->jwt->encodeJwt($payload);
        $validation = $this->model->validate($token);
        if ($validation === false) {
            throw new \PhalApi\Exception\BadRequestException('该token不存在');
        }
        if ($validation['exp'] < time()) {
            // 删除该token
            $this->model->deleteToken($payload['id']);
            throw new \PhalApi\Exception\BadRequestException('该token已过期');
        }
        return $payload;
    }

}