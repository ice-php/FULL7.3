<?php
/**
 * 框架核心配置文件
 */
return [
    //数据库连接的过期时间,秒
    'database_timeout'=>30,
    
    // 默认的模块
    'default_module' => 'home',
    
    // 默认的控制器
    'default_controller' => 'index',
    
    // 默认的动作
    'default_action' => 'index',
    
    // 默认的URL表现模式: '传统模式'/'路径模式'
    'url_mode' => '传统模式',
    
    // 配置模式,当此处使用Debug时,自动开启调试模式
    'Config' => 'Debug',

    //操作系统中运行本系统的用户
    'OS_USER'=>'www',

    // 用来判断是否调试状态的COOKIE名称
    'cookieDebug' => 'ICEDEBUG',
    
    // 模板自动编译开关
    'template' => true,
    
    // 缓存方式:file/redis/apc/mem/array/(other=null)
    
    // 视图缓存
    'cachePage' => 'none',
    
    // 全数据缓存,不再使用此配置项,以tablecache配置文件代替
    'cacheData' => 'file',
    
    // 主动强制缓存
    'cacheMust' => 'file',
    
    // 使用的数据库类型:mysql/sqlserver/oracle 后两种暂不支持
    'sql' => 'mysql',
    
    // 版本号
    'version' => '2019-12-27',

    //Web访问地址的路径
    'host'=>'/',
    
    // Cookie的域 示例: tuanweihui.com
    'cookieDomain' => '/',
    
    // Cookie的过期时间,秒,当前设置为30分钟
    'cookiePersist' => 24 * 30 * 60
];