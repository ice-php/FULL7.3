<?php
/**
 * Created by PhpStorm.
 * User: ice
 * Date: 2019/11/20
 * Time: 14:32
 */

namespace icePHP\Frame;


trait Singleton
{
    /**
     * 禁止实例化
     */
    private function __construct()
    {
    }

    /**
     * 获取本类单例的方法,公开
     */
    public static function instance(): self
    {
        // 单例句柄
        static $instance;

        // 初次创建对象
        if (!$instance) {
            $instance = new self();
        }

        // 返回对象句柄
        return $instance;
    }
}