<?php
//自动载入 Config和RequireInsensitivity
require '../vendor/autoload.php';

//本示例中使用Config
//RequireInsensitivity会被Config类自动调用
use icePHP\Frame\Config\Config;

try {
    /**
     * 此时尚未设置目录,没有值
     */
    var_dump(Config::get('config1'));

    /**
     * 包含配置文件目录 dir2
     * 显示 this is config 2 in dir 2
     */
    Config::insert(__DIR__ . DIRECTORY_SEPARATOR . 'dir2');
    var_dump(Config::get('config2'));

    /**
     * 低优先级附加目录 dir3
     * 显示的仍旧是 dir2中的配置信息
     * config3 不在 dir2中, 读取dir3 中的config3内容
     */
    Config::append('dir3');
    var_dump(Config::get('config2'));
    var_dump(Config::get('config3', 'dog'));

    /**
     * 高优先级附加目录  dir1
     * 显示dir1 中的配置信息
     */
    Config::insert('dir1');
    var_dump(Config::get('config2'));
}catch (Exception $e){
    var_dump($e);
}
