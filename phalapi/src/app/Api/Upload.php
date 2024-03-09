<?php
namespace App\Api;

use PhalApi\Api;

/**
 * 上传接口
 */
class Upload extends Api
{
    public function __construct()
    {
        $this->domain = new \App\Domain\Upload();
        $this->tokenDomain = new \App\Domain\User\Token();
    }
    public function getRules()
    {
        return array(
            'index' => array(
                'file' => array(
                    'name' => 'file',
                    'type' => 'file',
                    'max' => 1024 * 1024 * 2,
                    'range' => array('image/jpeg', 'image/png'),
                    'ext' => 'jpg,jpeg,png',
                    'require' => true,
                    'desc' => '文件'
                )
            )
        );
    }
    /**
     * 上传文件
     * @desc 上传文件
     * @return string url 文件地址
     * @method POST
     */
    public function index()
    {
        $payload = $this->tokenDomain->getPayloadAndCheckLogin();
        $file = $this->file;
        $url = $this->domain->saveFile($file);
        if (empty($url)) {
            throw new \PhalApi\Exception\BadRequestException('文件上传失败');
        }
        return $url;
    }
}