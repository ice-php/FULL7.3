<?php
/**
 * 日志相关配置(包括文件日志与数据库日志)
 */

return [
	//日志全局配置
	'enable' => false,                    //系统日志功能是否开启
	'writeLog' => true,    //开发人员用writeLog写的日志是否记录
	'dir_log' => DIR_ROOT . 'Run/Log/',        //日志文件根目录
	
	//数据库日志
	//'requestLog' => ['icePHP\Frame\TableLog\TableLog', 'request'], //由哪个方法记录系统请求日志
	//'operationLog' => ['icePHP\Frame\TableLog\TableLog', 'db'],//由哪个方法记录系统操作日志
	
	//由哪个方法获取当前后台管理员编号和管理员名称, 应该是个静态方法,且从SESSION或本地文件中读取,此时还不能访问数据库
	//'getAdminId' => ['MCurrentUser', 'getAdminId'],
	//'getAdminName' => ['MCurrentUser', 'getAdminName'],
	
	//数据库日志-免记录名单,日志表,对这些表的增删改,就不要再次记录日志了
	'noLogTables' => [
	],
	
	//数据库请求日志-免记录名单 ,以下MCA请求就不记录请求日志了
	'noLogRequest' => [
	],
	
	//请求日志-执行时间日志
	'dispatch' => [
		'limit' => 0, //执行时间长度限制(毫秒)
		'file' => 'web/dispatch', //日志文件
	],
	
	//请求日志-发生MCA异常的记录
	'mca' => 'anti/mca',
	
	//是否记录Cache系统产生的参数异常日志
	'cache'=>'cacheException',
	
	//请求日志-防入侵相关日志配置
	'anti' => [
		//IP黑名单
		'black_ip' => 'anti/black_ip',
		
		//参数名称不允许
		'param_name' => 'anti/param_name',
		
		//参数值轻度约束
		'light' => 'anti/light',
		
		//参数值高度约束
		'high' => 'anti/high',
		
		//不允许被Flash调用
		'flash' => 'anti/flash',
	],
	
	//请求日志-捕获抛出的异常时的记录
	'exception' => 'exception/exception',
	
	//请求日志-以下MCA,要记录请求体
	'requestBody' => [
	],
	
	//所有SQL文本日志
	'sql' => [
		//查询请求日志
		'query' => 'sql/query',
		'execute' => 'sql/execute',
		'queryHandle' => 'sql/queryHandle',
		
		'queryFast' => 'sql/queryFast',  //快速查询不记录查询结果
		
		'limit' => 0, //超时则记录,
		
		'afterQuery' => 'sql/afterQuery',
		'afterExecute' => 'sql/afterExecute',
		'afterQueryHandle' => 'sql/afterQueryHandle',
	],
];
