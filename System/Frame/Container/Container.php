<?php
declare(strict_types=1);

namespace icePHP\Frame\Container;

use function icePHP\Frame\Debug\isDebug;
use function icePHP\Frame\Functions\json;

use icePHP\Frame\Singleton;
use Psr\Container\ContainerInterface;

/**
 * 满足 PSR11 的容器
 * @package icePHP\Frame\Container
 */
class Container implements ContainerInterface
{
    //单例
    use Singleton;

    //内部缓冲
    private $items = [];

    /**
     * 查看是否存在
     * @param string $id 键
     * @return bool
     */
    public function has($id)
    {
        //如果键不合法,且是运行状态,返回不存在
        if (!$this->assertKey($id)) {
            return false;
        }

        //返回是否存在
        return isset($this->items[$id]);
    }

    /**
     * 取出
     * @param string $id 键
     * @return mixed|null
     */
    public function get($id)
    {
        if (!$this->assertKey($id)) {
            return null;
        }
        if (!isset($this->items[$id])) {
            return null;
        }
        return $this->items[$id];
    }

    /**
     * 注册一个服务
     * @param string $id
     * @param object $item
     */
    public function set(string $id, object $item)
    {
        $this->items[$id] = $item;
    }

    /**
     * 判断内容项的键是否是字符串
     * @param $id mixed 键
     * @return bool 是否合法
     */
    private function assertKey($id): bool
    {
        if (!is_string($id)) {
            if (isDebug()) {
                trigger_error('item key of container must be string:' . json($id));
            }
            return false;
        }
        return true;
    }
}