<?php
namespace App\Domain;

use Ramsey\Uuid\Uuid;

class Upload
{

    private $model;
    public function __construct()
    {
        $this->model = new \App\Model\Upload();
    }
    public function saveFile($file_temp)
    {
        $path = '/upload/' . date('Ym') . '/';
        $file_name = $file_temp['name'];
        $file_size = $file_temp['size'];
        $file_error = $file_temp['error'];
        $file_tmp_name = $file_temp['tmp_name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        if ($file_error > 0) {
            return false;
        }
        if (!is_dir(API_ROOT . '/public' . $path)) {
            mkdir(API_ROOT . '/public' . $path, 0777, true);
        }
        $file_name = md5(Uuid::uuid4()->toString() . microtime() . $file_name) . '.' . $file_ext;
        $res = move_uploaded_file($file_tmp_name, API_ROOT . '/public' . $path . $file_name);
        if ($res === false) {
            return false;
        }
        $this->model->upload(
            array(
                'filename' => $file_name,
                'filepath' => $path . $file_name,
                'size' => $file_size,
                'description' => '头像',
                'upload_time' => time()
            )
        );
        return \App\APP_URL . $path . $file_name;

    }
    public function saveFileFromUrl($url)
    {
        $path = '/upload/' . date('Ym') . '/';
        if (!is_dir(API_ROOT . '/public' . $path)) {
            mkdir(API_ROOT . '/public' . $path, 0777, true);
        }
        $file_name = md5(Uuid::uuid4()->toString() . microtime() . $url) . '.png';
        $res = file_put_contents(API_ROOT . '/public' . $path . $file_name, file_get_contents($url));
        if ($res === false) {
            return false;
        }
        $res = $this->model->upload(
            array(
                'filename' => $file_name,
                'filepath' => $path . $file_name,
                'size' => strlen(file_get_contents($url)),
                'description' => '头像',
                'upload_time' => time()
            )
        );
        if ($res) {
            return \App\APP_URL . $path . $file_name;
        }
        return false;
    }
    public function generateAvatar($username)
    {
        $identicon = new \Identicon\Identicon();
        $imageData = $identicon->getImageData($username);
        $path = '/upload/' . date('Ym') . '/';
        if (!is_dir(API_ROOT . '/public' . $path)) {
            mkdir(API_ROOT . '/public' . $path, 0777, true);
        }
        $file_name = md5(Uuid::uuid4()->toString() . microtime() . $username) . '.png';
        $res = file_put_contents(API_ROOT . '/public' . $path . $file_name, $imageData);
        if ($res === false) {
            return false;
        }
        $res = $this->model->upload(
            array(
                'filename' => $file_name,
                'filepath' => $path . $file_name,
                'size' => strlen($imageData),
                'description' => '头像',
                'upload_time' => time()
            )
        );
        if ($res) {
            return \App\APP_URL . $path . $file_name;
        }
        return false;
    }
}