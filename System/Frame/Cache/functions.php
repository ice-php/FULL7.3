<?php
declare(strict_types=1);

namespace icePHP\Frame\Cache;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Psr16Cache;

/**
 * 获取一个缓存实例
 * 这是一个SCache的快捷入口
 * @param string $config Page/Data: 页面缓存/数据缓存
 * @return AdapterInterface
 */
function cache(string $config): AdapterInterface
{
    return CacheFactory::instance($config);
}

/**
 * 根据缓存类型创建缓存对象:file/redis/apc/mem/array/(other=null)
 * @param string $type
 * @return AdapterInterface
 */
function cacheByType(string $type): AdapterInterface
{
    return CacheFactory::createByType($type);
}

function simple(string $type): SimpleCache
{
    return CacheFactory::simple($type);
}

/**
 * 清除全部缓存
 */
function clearCache(): void
{
    CacheFactory::clearAll();
}

