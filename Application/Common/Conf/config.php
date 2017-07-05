<?php

return array(
    'URL_MODEL' => 3,

    'APP_VERSION' => 'v2.0',
    'APP_NAME'    => 'apiAdmin',


    //用于首页展示game
    'DEFAULT_CONTROLLER'    =>  'Games', // 默认控制器名称

    'USER_ADMINISTRATOR' => array(1),
    'AUTH_KEY' => 'I&TC{pft>L,C`wFQ>&#ROW>k{Kxlt1>ryW(>r<#R',

    'COMPANY_NAME' => 'ApiAdmin开发维护团队',

    'URL_ROUTER_ON'   => true,
    'URL_ROUTE_RULES' => array(
        'wiki/:hash'  => 'Home/Wiki/apiField',
        'api/:hash'   => 'Home/Api/index',
        'wikiList'    => 'Home/Wiki/apiList',
        'errorList'   => 'Home/Wiki/errorCode',
        'calculation' => 'Home/Wiki/calculation'
    ),

    'LANG_SWITCH_ON' => true,   // 开启语言包功能
    'LANG_LIST'      => 'zh-cn', // 允许切换的语言列表 用逗号分隔
    'VAR_LANGUAGE'   => 'l', // 默认语言切换变量

    /* 数据库设置 */
    'DB_TYPE'        => 'mysql',     // 数据库类型
    'DB_HOST'        => '127.0.0.1',     // 服务器地址
    'DB_NAME'        => 'apiadmin',          // 数据库名
    'DB_USER'        => 'root',      // 用户名
    'DB_PWD'         => '' ,     // 密码
    'GEETEST_ID'             => '034b9cc862456adf05398821cefc94eb',//极验id  仅供测试使用
    'GEETEST_KEY'            => 'b7f064b9ae813699de794303f0b0e76f',//极验key 仅供测试使用

);

