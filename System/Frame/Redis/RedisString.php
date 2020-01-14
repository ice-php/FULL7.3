<?php
declare(strict_types=1);

namespace icePHP\Frame\Redis;

/**
 * 字符串(String)类型
 */
class RedisString extends RedisElement
{
    /**
     * 设置一个键值
     * @param $value string 值
     * @param int $expire 生存期
     * @return bool 成功否
     */
    public function set(string $value, int $expire = 0): bool
    {
        return parent::setString($value, $expire);
    }

    /**
     * 获取当前存储对象的类型(字符串格式)
     * @return string
     */
    public function getType(): string
    {
        return 'String';
    }

    /**
     * 用value参数覆写(Overwrite)当前对象所储存的字符串值，从偏移量offset开始
     * php中有一个函数substr_replace
     * @param $offset int 偏移
     * @param $value string 填充内容
     * @return int 被SETRANGE修改之后，字符串的长度。
     */
    public function substr_replace(int $offset, string $value): int
    {
        return intval($this->handle->setRange($this->name, $offset, $value));
    }

    /**
     * 将value追加到当前对象原来的值之后
     * @param $value string 要追加的字符串
     * @return int 完成后的字符串长度
     */
    public function append(string $value): int
    {
        return intval($this->handle->append($this->name, $value));
    }

    /**
     * 返回当前存储对象所关联的字符串值
     * @return string
     */
    public function get(): string
    {
        return parent::getRaw();
    }

    /**
     * 返回key中字符串值的子字符串，字符串的截取范围由start和end两个偏移量决定(包括start和end在内)。
     * 负数偏移量表示从字符串最后开始计数，-1表示最后一个字符，-2表示倒数第二个，以此类推。
     * @param $start int
     * @param $end int
     * @return string
     */
    public function substr(int $start, int $end): string
    {
        return $this->handle->getRange($this->name, $start, $end);
    }

    /**
     * 将当前对象的值设为value，并返回旧值。
     * @param $value string 新值
     * @return string 原值
     */
    public function getAndSet(string $value): string
    {
        return $this->handle->getSet($this->name, $value);
    }

    /**
     * 返回key所储存的字符串值的长度。
     * @return int
     */
    public function length(): int
    {
        return $this->handle->strlen($this->name);
    }

    /**
     * 转换成BIT类型
     * @return RedisBit
     */
    public function toBit(): RedisBit
    {
        return new RedisBit($this->handle, $this->name);
    }

    /**
     * 转换成Int类型
     * @return RedisInt
     */
    public function toInt(): RedisInt
    {
        return new RedisInt($this->handle, $this->name);
    }

    /**
     * 转换成Float类型
     * @return RedisFloat
     */
    public function toFloat(): RedisFloat
    {
        return new RedisFloat($this->handle, $this->name);
    }
}