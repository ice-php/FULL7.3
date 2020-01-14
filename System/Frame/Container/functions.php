<?php
declare(strict_types=1);

namespace icePHP\Frame\Container;

/**
 * 注册一个服务
 * @param string $id
 * @param object $service
 */
function serviceRegister(string $id, object $service)
{
    Container::instance()->set($id, $service);
}

/**
 * 检查服务是否存在
 * @param string $id
 * @return bool
 */
function serviceHas(string $id): bool
{
    return Container::instance()->has($id);
}

/**
 * 取回一个服务
 * @param string $id
 * @return object
 */
function serviceGet(string $id): object
{
    return Container::instance()->get($id);
}