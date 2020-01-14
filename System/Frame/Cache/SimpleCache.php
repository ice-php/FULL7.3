<?php
/**
 * Created by PhpStorm.
 * User: ice
 * Date: 2020/1/3
 * Time: 9:39
 */

namespace icePHP\Frame\Cache;


use Symfony\Component\Cache\Psr16Cache;
use Psr\SimpleCache\CacheException as SimpleCacheException;

class SimpleCache extends Psr16Cache
{
    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        try {
            return parent::get($key, $default);
        } catch (SimpleCacheException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
        exit;
    }

    /**
     * @param $key
     * @param $value
     * @param null $ttl
     * @return bool
     */
    public function set($key, $value, $ttl = null)
    {
        try {
            return parent::set($key, $value, $ttl);
        } catch (SimpleCacheException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            exit;
        }
    }
}