<?php
declare(strict_types=1);

namespace icePHP\Frame\MVC;

/**
 * 所有业务逻辑类的基类,静态
 */
class Model
{
    //加载装饰器
    use Decorator;

    /**
     * 禁止实例化
     */
    private function __construct()
    {
    }
}