<?php

return array(

	//默认每页显示数量
	'DEFAULT_PER_PAGE'  => 10,

    //AccessToken有效期（单位：秒）
    'ACCESS_TOKEN_EXPIRES' => 7200,

    //UserToken有效期（单位：秒）
    'USER_TOKEN_EXPIRES' => null,

	/* 数据库设置 */
	'DB_TYPE'        => 'mysql',     // 数据库类型
	'DB_HOST'        => '127.0.0.1',     // 服务器地址
	'DB_NAME'        => 'app_game',          // 数据库名
	'DB_USER'        => 'root',      // 用户名
	'DB_PWD'         => '' ,     // 密码

    'URL_ROUTER_ON'         =>  true,   // 是否开启URL路由
    'URL_ROUTE_RULES'       =>  array(  // 默认路由规则 针对模块
       // 'index'         => '/Public/', // 静态地址路由
    ),



);