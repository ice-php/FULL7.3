<?php
/**
 * 框架统一入口
 */
//维护页面跳转
//header('Location: /Static/maintaining.html');

//检查PHP版本,并包含框架核心类
use icePHP\Frame\Core;

require_once('../System/plugin.php');

// 运行框架
Core::run();
