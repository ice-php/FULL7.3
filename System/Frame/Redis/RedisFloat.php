<?php
declare(strict_types=1);

namespace icePHP\Frame\Redis;

/**
 * 位类型,String类型的子类型
 */
final class RedisFloat extends RedisElement
{
    /**
     * 获取当前存储对象的类型(字符串格式)
     * @return string
     */
    public function getType(): string
    {
        return 'Float';
    }

    /**
     * 数值增减(浮点)
     * @param $diff float 1/-1/N/-N
     * @return float 操作过后的值
     */
    public function increase(float $diff = 1): float
    {
        return $this->handle->incrByFloat($this->name, $diff);
    }

    /**
     * 减量操作
     * @param float $diff
     * @return float
     */
    public function decrease(float $diff = 1): float
    {
        return $this->increase(-$diff);
    }

    /**
     * 获取当前缓存值,转换成浮点
     * @return float
     */
    public function get(): float
    {
        return floatval(parent::getRaw());
    }

    /**
     * 设置一个键值
     * @param $value float 值
     * @param int $expire 生存期
     * @return bool 成功否
     */
    public function set(float $value, int $expire = 0): bool
    {
        return parent::setString(strval($value), $expire);
    }

    /**
     * 将当前对象的值设为value，并返回旧值。
     * @param $value float 新值
     * @return float 原值
     */
    public function getAndSet(float $value): float
    {
        return floatval($this->handle->getSet($this->name, strval($value)));
    }
}