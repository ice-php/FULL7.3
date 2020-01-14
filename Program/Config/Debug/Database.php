<?php
/**
 * 调试模式下的数据库服务器配置
 */
return [
    //是否允许多表关联查询
    '_enable_multi' => true,

    //是否自动添加created/updated
    '_auto_field' => true,

    '_defaultTable'=>'user',

    //以下是各个数据库的连接
    '某数据库连接' => [
        'connect' => [
            'host' => 'xxx.com',  //主机
            'user' => 'root', //数据库用户
            'password' => '123456', //密码
            'database' => 'sample', //数据库名称
            'port' => '3306' //端口
        ],
        'mode' => '读写', //访问模式: 读/写/读写, 用于以后读写分离
        'default' => true, //是否是默认数据库,即未指定的表,都从这里访问
    ],

];