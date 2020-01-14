<?php
/**
 * 框架核心要用到的几个必要函数
 * User: ice
 * Date: 2019/11/20
 * Time: 17:54
 */
namespace icePHP\Frame;

/**
 * 当本框架作为插件使用时的入口
 * 这是Frame的一个快捷入口
 * 没有经过Index.php单一入口,而是使用此方法接入
 *
 * @param string $module 模块名称
 * @param string $controller 控制器名称
 * @param string $action 动作名称
 * @param array $params 参数数组
 */
function plugin(string $module = null, string $controller = null, string $action = null, array $params = []): void
{
    Core::plugin($module, $controller, $action, $params);
}

/**
 * 显示一个页面片段,通常由View来调用
 * 这是Frame的一个快捷入口
 * @param string $module 模块名称
 * @param string $controller 控制器名称
 * @param string $action 动作名称
 * @param array $params 参数数组
 * @param int $cached 文件缓存的有效期(秒)
 * @return string 空 模板中显示时需要一个返回值
 */
function fragment(string $module, string $controller, string $action, array $params = [], int $cached = 7200): string
{
    Core::fragment($module, $controller, $action, $params, $cached);
    return '';
}
