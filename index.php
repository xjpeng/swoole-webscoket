<?php
session_start();
header( 'Content-Type:text/html;charset=utf-8'); 

//定义类路径
define('LIB_PATH','./lib/');

//定义模板路径
define('TPL_PATH','./tpl/');


//定义websocekt服务器端口
define('WEBSOCKET_PORT',5008);

//注册类自动__autoload
spl_autoload_register(function($class){
	include LIB_PATH.$class.'.class.php';
});

Route::run();