<?php
/**
 * 用于检查PHP版本,并包含框架核心类
 * User: ice
 * Date: 2019/11/5
 * Time: 11:28
 */


use icePHP\Frame\Core;

// 定义框架所有目录常量,就在当前目录的上一级,例: wamp/www/
define('DIR_ROOT', dirname(__DIR__) . '/');

// 版本检查,至少要求7.2
if (PHP_VERSION < '7.2') {
    exit('icePHP framework requires PHP version 7.2 or above.');
}

// composer 的自动加载
require_once('vendor/autoload.php');

// 导入框架类
require_once('Frame/Core.php');

//初始化框架
Core::construct();