<?php
namespace App;

// 4到16位（字母，数字，下划线，减号）
const PASSWORD_REGEX = '/^\S*(?=\S{6,})(?=\S*\d)(?=\S*[A-Z])(?=\S*[a-z])(?=\S*[!@#$%^&*? ])\S*$/';

// 密码强度校验，最少6位，包括至少1个大写字母，1个小写字母，1个数字，1个特殊字符
const USERNAME_REGEX = '/^[\w-]{4,16}$/';

// email邮箱
const EMAIL_REGEX = '/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';

// 手机号
const PHONE_NUMBER_REGEX = '/^(?:(?:\+|00)86)?1[3-9]\d{9}$/';

const APP_URL = 'http://dev.com';

function md5_password($password)
{
    return md5(sha1($password) . 'mxgxxx');
}
function getDate()
{
    return date('Y-m-d H:i:s', time());
}
