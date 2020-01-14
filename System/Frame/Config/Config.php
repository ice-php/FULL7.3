<?php
/**
 * icePHP框架的一部分,以实现读取多个配置目录下多个配置文件的功能
 */

declare(strict_types=1);

namespace icePHP\Frame\Config;

use function icePHP\Frame\Functions\requireFile;

use icePHP\Frame\Functions\RequireFileException;

/**
 * 在多个目录下读取配置文件
 * Class Config
 * @package icePHP
 */
class Config
{
    //全部配置目录
    private static $dirs = [];

    /**
     * 设置一个优先的配置目录
     * @param string $dir
     */
    public static function insert(string $dir): void
    {
        array_unshift(self::$dirs, $dir);
    }

    /**
     * 设置一个低优先的配置目录
     * @param string $dir
     */
    public static function append(string $dir): void
    {
        self::$dirs[] = $dir;
    }

    /**
     * 获取配置文件内容(从文件中获取)
     *
     * @param string $fileName 配置文件名
     * @return mixed 配置内容
     */
    private static function readFile(string $fileName)
    {
        //配置文件名
        $fullName = $fileName . '.php';

        //逐个配置文件目录查找
        foreach (self::$dirs as $dir) {
            try {
                //这里 要忽略 大小写
                $content = requireFile($dir . DIRECTORY_SEPARATOR . $fullName);
            } catch (RequireFileException $e) {
                continue;
            }

            //如果缺少权限或内容为空/无效,则继续查找
            if ($content) {
                return $content;
            }
        }

        //未找到
        return null;
    }

    /**
     * 获取其它配置文件的内容
     * 来源于配置文件路径下的指定 文件
     * 使用可变参数, 文件名,配置项,子项,...
     * 开发人员通常使用 全局函数 config 来调用本功能
     * @param string[] $argv
     * @return   mixed
     * @throws ConfigException
     */
    public static function get(string ... $argv)
    {
        // 动态参数列表
        $args = count($argv);

        // 缓存
        static $all = [];

        // 一个参数都未给出
        if ($args < 1) {
            throw new ConfigException('请求配置时缺少参数', ConfigException::MISS_ARGUMENT);
        }

        // 第一个参数是配置文件名
        $file = $argv[0];

        // 如果尚未获取,则获取
        if (!isset($all[$file])) {
            $all[$file] = self::readFile($file);
        }

        // 根据请求参数,获取子项
        $config = $all[$file];

        // 根据函数参数,获取继续的子项
        for ($i = 1; $i < $args; $i++) {
            $itemName = $argv[$i];

            // 没有指定的配置项
            if (!isset($config[$itemName])) {
                throw new ConfigException('请求配置时,未找到相应的配置', ConfigException::NOT_FOUND);
            }

            $config = $config[$itemName];
        }

        return $config;
    }

    /**
     * 判断是否是调试运行模式,只有当系统配置中的config==debug时,才是调试运行模式,不考虑临时指定调试运行模式
     * @param $name string 调试参数的名称
     * @return boolean 是/否
     */
    public static function isDebug(string $name = 'Debug'): bool
    {
        return self::mode() == $name;
    }

    /**
     * 获取当前运行模式: debug/run/demo, 根据项目需要,也可增加运行模式
     */
    public static function mode(): string
    {
        try {
            return self::get('System', 'Config');
        } catch (ConfigException $e) {
            trigger_error('system/config配置必须指定', E_USER_ERROR);
            return '';
        }
    }
}