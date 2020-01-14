<?php
/**
 * 作为 命令行模式使用的包含文件
 */

//检查PHP版本,并包含框架核心类
use icePHP\Frame\Core;

require_once('plugin.php');

//处理命令行请求
Core::program();
