## 快速安装

### 克隆此仓库

```
git clone https://github.com/renyancheng/healthy_system_backend.git

cd healthy_system_backend
```


### 安装依赖
 
```bash
cd phalapi
composer update
```

## 部署

### Nginx配置
如果使用的是Nginx，可参考以下配置。  
```nginx
server {
    listen 80;
    server_name dev.phalapi.net;
    # 将根目录设置到public目录
    root /path/to/phalapi/public;
    charset utf-8;

    location / {
        index index.php;
    }

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        # 根据当前环境，选择合适的通讯方式
        # fastcgi_pass unix:/var/run/php-fpm/php-fpm.sock;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```
配置时需要将网站根目录设置到public目录，配置保存后重启nginx。  

> 温馨提示：推荐将访问根路径指向/path/to/phalapi/public。  

### 数据库配置
如何使用的是MySQL数据库，参考修改```./config/dbs.php```数据库配置。  

```php
return array(
    /**
     * DB数据库服务器集群 / database cluster
     */
    'servers' => array(
        'db_master' => array(                       // 服务器标记 / database identify
            'type'      => 'mysql',                 // 数据库类型，暂时只支持：mysql, sqlserver / database type
            'host'      => '127.0.0.1',             // 数据库域名 / database host
            'name'      => 'phalapi',               // 数据库名字 / database name
            'user'      => 'root',                  // 数据库用户名 / database user
            'password'  => '',	                    // 数据库密码 / database password
            'port'      => 3306,                    // 数据库端口 / database port
            'charset'   => 'UTF8',                  // 数据库字符集 / database charset
            'pdo_attr_string'   => false,           // 数据库查询结果统一使用字符串，true是，false否
            'driver_options' => array(              // PDO初始化时的连接选项配置
                // 若需要更多配置，请参考官方文档：https://www.php.net/manual/zh/pdo.constants.php
            ),
        ),
    ),

    // 更多代码省略……
);
```

最后，需要给runtime目录添加写入权限。更多安装说明请参考文档[下载与安装](http://docs.phalapi.net/#/v2.0/download-and-setup)。  

## 使用

查看[API文档](https://github.com/renyancheng/healthy_system_backend/tree/master/API.md)
