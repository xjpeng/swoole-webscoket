# swoole-webscoket
使用swoole+websocket实现即时通信

### 演示
链接地址: [demo](http://static.cqleju.net/swoole/index.php) 演示密码: 666666

### 演示环境
- PHP5.3+
- PHP swoole扩展
- Chrome,Firefox,及支持websocket的现代浏览器
- Redis3.1

### 部署使用
1.配置websocke端口号
```php
//swoole.php
//5008为我设置的websocket服务器的端口号
$ws = new swoole_websocket_server("0.0.0.0", 5008);

//index.php
//定义websocekt服务器端口
define('WEBSOCKET_PORT',5008);

```
2.运行服务器
```php
//会常住内存运行,如运行失败检查端口是否被占用,并kill -15 pid
php swoole.php

```