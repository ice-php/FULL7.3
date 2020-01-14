<?php

declare(strict_types=1);

namespace icePHP\Frame\Cache;

use icePHP\Frame\FileLog\FileLog;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Exception\CacheException;

use function icePHP\Frame\Config\configDefault;
use function icePHP\Frame\Config\configMust;
use function icePHP\Frame\Debug\isDebug;
use function icePHP\Frame\Functions\dump;
use Symfony\Component\Cache\Psr16Cache;

/**
 * 缓存 工厂
 * @author 蓝冰大侠
 */
final class CacheFactory
{
    /**
     * 获取一个缓存实例
     * @param string $config 类型:Page(页面缓存)/Data(数据缓存)/Must(必须)
     * @return AdapterInterface
     */
    public static function instance($config): AdapterInterface
    {
        //防止单词大小写错误
        $config = strtolower(trim($config));

        //多例句柄
        static $instances = [];

        //如果尚未实例化,则创建
        if (!isset($instances[$config])) {
            $instances[$config] = self::createInstance($config);
        }

        //返回实例
        return $instances[$config];
    }

    /**
     * 创建一个简单的缓存对象
     * @param string $type
     * @return SimpleCache
     */
    public static function simple(string $type): SimpleCache
    {
        return new SimpleCache(self::createByType($type));
    }

    /**
     * 根据缓存类型创建缓存对象:redis/file/apc/mem/none
     * @param string $type
     * @return AdapterInterface
     */
    public static function createByType(string $type): AdapterInterface
    {
        $type = strtolower(trim($type));

        // 返回文件缓存对象实例
        if ($type == 'file') {
            $dir = DIR_ROOT . configDefault('Run/Cache', 'FileCache', 'dir');
            return new FilesystemAdapter('', 0, $dir);
        }

        //返回 Redis缓存的对象实例
        if ($type == 'redis') {
            $config = configMust('Redis');
            $redis = new \Redis();
            $redis->connect($config['host'], $config['port'] ?? 6379, $config['timeout'] ?? 0);
            return new RedisAdapter($redis);
        }

        //返回APC共享内存的缓存对象
        if ($type == 'apc') {
            try {
                return new ApcuAdapter();
            } catch (CacheException $e) {
                self::logException($e);
                return new NullAdapter();
            }
        }

        // 在进程内部用数组来缓存
        if ($type == 'array') {
            return new ArrayAdapter();
        }

        // 返回内存缓存对象实例
        if ($type == 'mem') {
            $config = configMust('Memcache');
            $memcache = new \Memcached();

            for ($i = 1; $i < $config['servers']; $i++) {
                $memcache->addServer($config[$i]['host'], $config[$i]['port'] ?? 11211);
            }
            try {
                return new MemcachedAdapter($memcache);
            } catch (CacheException $e) {
                self::logException($e);
                return new NullAdapter();
            }
        }

        // 返回无缓存对象实例
        return new NullAdapter();
    }

    /**
     * 创建一个指定配置的缓存实例
     * @param $config string
     * @return AdapterInterface
     */
    private static function createInstance(string $config): AdapterInterface
    {
        // 取相应类型的缓存配置要求
        if ($config == 'page') {
            $type = configDefault('none', 'System', 'cachePage');
        } elseif ($config == 'data') {
            $type = configDefault('none', 'System', 'cacheData');
        } elseif ($config == 'must') {
            $type = configDefault('file', 'System', 'cacheMust');
        } else {
            $type = 'none';
        }

        return self::createByType($type);
    }

    /**
     * 清除所有缓存
     */
    public static function clearAll(): void
    {
        // 构造 文件缓存 对象
        $cache = self::instance('file');

        // 清除文件缓存内容
        $cache->clear();

        // 构造Memcache缓存对象
        $cache = self::instance('data');

        // 清除Memcache缓存内容
        $cache->clear();

        //清除MUST类型的缓存
        $cache = self::instance('must');
        $cache->clear();
    }


    /**
     * 记录缓存系统的异常日志
     * @param CacheException $e
     */
    public static function logException(CacheException $e)
    {
        //调试模式,直接报错
        if (isDebug()) {
            dump($e);
            exit;
        }

        //运行模式,记录到日志文件中
        FileLog::instance()->cache($e);
    }
}