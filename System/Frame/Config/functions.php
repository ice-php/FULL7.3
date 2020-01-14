<?php
declare(strict_types=1);

namespace icePHP\Frame\Config;

/**
 * 取指定配置信息,如果不传递参数,则获取配置类的单例实例
 * 这是Config的一个快捷入口
 * 所有参数原样传递
 * @param $items string[] 配置文件名称以及配置项名称列表
 * @return string|array|bool
 * @throws ConfigException
 */
function config(string ...$items)
{
    return Config::get(...$items);
}

/**
 * 取配置值,取不到时返回缺省值
 * @param mixed $default
 * @param string[] $items
 * @return mixed
 */
function configDefault($default, string ...$items)
{
    try {
        return config(...$items);
    } catch (ConfigException $e) {
        return $default;
    }
}

/**
 * 获取一个必须存在的配置项
 * @param string[] $items
 * @return array|bool|string
 */
function configMust(string ...$items)
{
    try {
        return config(...$items);
    } catch (ConfigException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
        return null;
    }
}

/**
 * 获取一个数组类型的配置项
 * @param string[] $items
 * @return array
 */
function configMustArray(string ...$items): array
{
    $config = configMust(...$items);
    if (!is_array($config)) {
        trigger_error('配置:' . implode('|', $items) . ' 必须是数组', E_USER_ERROR);
    }
    return $config;
}