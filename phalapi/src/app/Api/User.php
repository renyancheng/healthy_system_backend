<?php
namespace App\Api;

use PhalApi\Api;
use Ramsey\Uuid\Uuid;

/**
 * 用户接口
 */

class User extends Api
{
    public function __construct()
    {
        $this->model = new \App\Model\User\Index();
        $this->tokenModel = new \App\Model\User\Token();
        $this->infoModel = new \App\Model\User\Info();
        $this->uploadDomain = new \App\Domain\Upload();
        $this->tokenDomain = new \App\Domain\User\Token();
    }
    public function getRules()
    {
        return array(
            'login' => array(
                'username' => array(
                    'name' => 'username',
                    'require' => true,
                    'regex' => \App\USERNAME_REGEX,
                    'desc' => '用户名'
                ),
                'password' => array(
                    'name' => 'password',
                    'require' => true,
                    'regex' => \App\PASSWORD_REGEX,
                    'desc' => '密码'
                ),
            ),
            'register' => array(
                // user
                'username' => array(
                    'name' => 'username',
                    'require' => true,
                    'regex' => \App\USERNAME_REGEX,
                    'desc' => '用户名'
                ),
                'nickname' => array(
                    'name' => 'nickname',
                    'require' => true,
                    'min' => 2,
                    'max' => 16,
                    'desc' => '昵称'
                ),
                'password' => array(
                    'name' => 'password',
                    'require' => true,
                    'regex' => \App\PASSWORD_REGEX,
                    'desc' => '密码'
                ),
                'email' => array(
                    'name' => 'email',
                    'require' => true,
                    'regex' => \App\EMAIL_REGEX,
                    'desc' => '邮箱'
                ),
                'phone_number' => array(
                    'name' => 'phone_number',
                    'require' => true,
                    'desc' => '手机号'
                ),

                // user_info
                'birth_date' => array(
                    'name' => 'birth_date',
                    'require' => true,
                    'type' => 'date',
                    'format' => 'timestamp',
                    'desc' => '生日'
                ),
                'gender' => array(
                    'name' => 'gender',
                    'require' => true,
                    'type' => 'enum',
                    'range' => array('男', '女', '其他'),
                    'desc' => '性别'
                ),
                'height' => array(
                    'name' => 'height',
                    'require' => true,
                    'type' => 'float',
                    'desc' => '身高'
                ),
                'weight' => array(
                    'name' => 'weight',
                    'require' => true,
                    'type' => 'float',
                    'desc' => '体重'
                ),
                'health_conditions' => array(
                    'name' => 'health_conditions',
                    'require' => true,
                    'desc' => '健康状况'
                ),
                /* 'activity' => array(
                    'name' => 'activity',
                    'require' => true,
                    'type' => 'enum',
                    'range' => array('Sedentary', 'Lightly Active', 'Moderately Active', 'Very Active', 'Super Active'),
                    'desc' => '活动量'
                ),
                'diet_type' => array(
                    'name' => 'diet_type',
                    'require' => true,
                    'type' => 'enum',
                    'range' => array('Vegetarian','Non-vegetarian','Vegan','Keto','Other'),
                    'desc' => '饮食类型'
                ),
                'allergies' => array(
                    'name' => 'allergies',
                    'require' => true,
                    'type' => 'array',
                    'desc' => '过敏源'
                ), */

            ),
            'updateProfile' => array(
                'nickname' => array(
                    'name' => 'nickname',
                    'require' => true,
                    'min' => 2,
                    'max' => 16,
                    'desc' => '昵称'
                ),
                'email' => array(
                    'name' => 'email',
                    'require' => true,
                    'regex' => \App\EMAIL_REGEX,
                    'desc' => '邮箱'
                ),
                'phone_number' => array(
                    'name' => 'phone_number',
                    'require' => true,
                    'regex' => \App\PHONE_NUMBER_REGEX,
                    'desc' => '手机号'
                ),
                'avatar' => array(
                    'name' => 'avatar',
                    'require' => true,
                    'type' => 'file',
                    'range' => array(
                        'image/jpeg',
                        'image/png',
                    ),
                    'ext' => 'jpg,jpeg,png,',
                    'max' => 2 * 1024 * 1024,
                    'desc' => '头像'
                ),
            ),
            'updateInfo' => array(
                'birth_date' => array(
                    'name' => 'birth_date',
                    'require' => true,
                    'type' => 'date',
                    'format' => 'timestamp',
                    'desc' => '生日'
                ),
                'gender' => array(
                    'name' => 'gender',
                    'require' => true,
                    'type' => 'enum',
                    'range' => array('男', '女', '其他'),
                    'desc' => '性别'
                ),
                'height' => array(
                    'name' => 'height',
                    'require' => true,
                    'type' => 'float',
                    'desc' => '身高'
                ),
                'weight' => array(
                    'name' => 'weight',
                    'require' => true,
                    'type' => 'float',
                    'desc' => '体重'
                ),
                'health_conditions' => array(
                    'name' => 'health_conditions',
                    'require' => true,
                    'desc' => '健康状况'
                ),
            ),
            'getUserProfile' => array(
                'id' => array(
                    'name' => 'id',
                    'require' => true,
                    'type' => 'int',
                    'min' => 1,
                    'desc' => '用户id'
                )
            )
        );
    }

    /**
     * 用户登录
     * @desc 用户登录接口
     * @method POST
     */
    public function login()
    {
        if ($this->model->checkUsername($this->username)) {
            $password_hash = \App\md5_password($this->password);
            $rs = $this->model->login($this->username, $password_hash);
            if ($rs) {
                $this->model->updateLoginTime($rs['id']);
                $uuid = Uuid::uuid4()->toString();
                $payload = array(
                    'id' => $uuid,
                    'user_id' => $rs['id'],
                    'username' => $rs['username'],
                    'iat' => time(),
                    'exp' => time() + 60 * 60 * 24 * 3,
                    'admin' => boolval($rs['admin'])
                );
                $rs['token'] = \PhalApi\DI()->jwt->encodeJwt($payload);
                $this->tokenModel->createToken(
                    array(
                        'id' => $uuid,
                        'user_id' => $rs['id'],
                        'token' => $rs['token'],
                        'iat' => $payload['iat'],
                        'exp' => $payload['exp']
                    )
                );
                return $rs;
            } else {
                throw new \PhalApi\Exception\BadRequestException('密码错误');
            }
        } else {
            throw new \PhalApi\Exception\BadRequestException('用户名不存在');
        }
    }

    /**
     * 用户注册
     * @desc 用户注册接口
     * @method POST
     */
    public function register()
    {
        if ($this->model->checkUsername($this->username)) {
            throw new \PhalApi\Exception\BadRequestException('用户名已存在');
        }
        $default_avatar = $this->uploadDomain->generateAvatar($this->username);
        $date = time();
        $user = array(
            'username' => $this->username,
            'nickname' => $this->nickname,
            'password_hash' => \App\md5_password($this->password),
            'avatar' => $default_avatar,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'created_time' => $date,
            'updated_time' => $date,
            'last_login_time' => $date
        );
        $res = $this->model->register($user);
        if (empty($res)) {
            throw new \PhalApi\Exception\BadRequestException('注册失败');
        }
        $user = $this->model->getUserByUsername($user['username']);
        $user_info = array(
            'id' => $user['id'],
            'birth_date' => $this->birth_date,
            'gender' => $this->gender,
            'height' => $this->height,
            'weight' => $this->weight,
            'health_conditions' => $this->health_conditions
        );
        $res_info = $this->infoModel->addInfo($user_info);
        if (empty($res_info)) {
            throw new \PhalApi\Exception\BadRequestException('注册失败');
        }
        // 注册后不自动登陆
        /* $uuid = Uuid::uuid4()->toString();
        $payload = array(
            'id' => $uuid,
            'user_id' => $user['id'],
            'username' => $user['username'],
            'iat' => time(),
            'exp' => time() + 60 * 60 * 24 * 3,
            'admin' => boolval($user['admin'])
        );
        $user['token'] = \PhalApi\DI()->jwt->encodeJwt($payload);
        $this->tokenModel->createToken(
            array(
                'id' => $uuid,
                'user_id' => $user['id'],
                'token' => $user['token'],
                'iat' => $payload['iat'],
                'exp' => $payload['exp'],
                'admin'=> boolval($user['admin'])
            )
        ); */
        return $user;
    }

    /**
     * 用户退出
     * @desc 用户退出接口
     * @method POST
     */
    public function logout()
    {
        $payload = \PhalApi\DI()->jwt->decodeJwt();
        // $payload = \PhalApi\DI()->jwt->decodeJwtByParam($token);
        if ($payload['id']) {
            $rs = $this->tokenModel->deleteToken($payload['id']);
            if ($rs) {
                return true;
            } else {
                throw new \PhalApi\Exception\BadRequestException('退出失败');
            }
        } else {
            throw new \PhalApi\Exception\BadRequestException('无效token');
        }
    }
    /**
     * 更新用户资料
     * @desc 更新用户资料接口
     * @method POST
     */
    public function updateProfile()
    {
        $payload = $this->tokenDomain->getPayloadAndCheckLogin();
        // 将avatar保存到服务器
        $upload_res = $this->uploadDomain->saveFile($this->avatar);
        if (empty($upload_res)) {
            throw new \PhalApi\Exception\BadRequestException('头像上传失败');
        }
        $data = array(
            'nickname' => $this->nickname,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'avatar' => $upload_res
        );
        $update_res = $this->model->updateProfile($payload['user_id'], $data);
        if ($update_res) {
            return true;
        } else {
            throw new \PhalApi\Exception\BadRequestException('更新失败');
        }
    }

    /**
     * 更新用户信息
     * @desc 更新用户信息接口
     * @method POST
     */
    public function updateInfo()
    {
        $payload = $this->tokenDomain->getPayloadAndCheckLogin();
        $data = array(
            'birth_date' => $this->birth_date,
            'gender' => $this->gender,
            'height' => $this->height,
            'weight' => $this->weight,
            'health_conditions' => $this->health_conditions
        );
        $update_res = $this->infoModel->updateInfo($payload['user_id'], $data);
        if ($update_res) {
            return true;
        } else {
            throw new \PhalApi\Exception\BadRequestException('更新失败');
        }
    }

    /**
     * 获取用户信息
     * @desc 获取用户信息接口
     */
    public function getUserInfo()
    {
        $payload = $this->tokenDomain->getPayloadAndCheckLogin();
        $user_info = $this->infoModel->getInfo($payload['user_id']);
        if ($user_info) {
            return $user_info;
        } else {
            throw new \PhalApi\Exception\BadRequestException('获取用户信息失败');
        }
    }

    /**
     * 获取用户资料
     * @desc 获取用户资料接口
     */
    public function getUserProfile()
    {

        $user = $this->model->getUserById($this->id);
        if ($user) {
            return $user;
        } else {
            throw new \PhalApi\Exception\BadRequestException('用户不存在');
        }
    }
}