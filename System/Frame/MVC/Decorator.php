<?php
declare(strict_types=1);

namespace icePHP\Frame\MVC;

use function icePHP\Frame\Functions\left;

/**
 * 装饰器
 * User: ice
 * Date: 2018/12/19
 * Time: 17:08
 */
trait Decorator
{
    /**
     * 获取注解
     * @param string $class 类名
     * @param string $method 方法名
     * @param string $name 注解名
     * @return array 注解内容数组
     */
    private static function getAnnotation(string $class, string $method, string $name): array
    {
        //尝试反射
        try {
            $reflect = new \ReflectionMethod($class, $method);
        } catch (\ReflectionException $e) {
            trigger_error('获取类[' . $class . ']中方法[' . $name . ']的反射失败');
            exit;
        }

        //取反射中的注释
        $comment = $reflect->getDocComment();

        //没有注释
        if (!$comment) {
            return [];
        }

        //获取到的注解
        $annotations = [];

        //注解名称长度
        $len = strlen($name);
        $comment = str_replace("\r\n", "\r", $comment);
        $comment = str_replace("\n", "\r", $comment);

        //逐行分析
        foreach (explode("\r", $comment) as $line) {
            //去除空格,及开始的*
            $line = trim(trim(trim($line), '*'));

            //是否匹配注解名称
            if (left($line, $len + 1) == '@' . $name) {
                $annotations[] = explode(' ', substr($line, $len + 2))[0];
            }
        }

        //返回全部指定注解的数组
        return $annotations;
    }

    /**
     * 根据公开的方法名称,换算成内部实现时的私有方法名称
     * @param string $name 对外的方法名称
     * @return string 内部实现的方法名称
     */
    public static function buildName(string $name): string
    {
        return '_' . $name;
    }

    /**
     * 调用一个不存在的静态方法时
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        //实际方法名
        $method = self::buildName($name);

        $class=get_called_class();

        //如果方法不存在,则报错
        if (!method_exists($class, $method)) {
            trigger_error('调用类[' . $class . ']中不存在的方法[' . $name . ']');
            exit;
        }

        //处理前置注解,前置注解对请求参数进行修改
        $before = self::getAnnotation($class, $method, 'before');
        foreach ($before as $annotation) {
            $arguments = static::$annotation(...$arguments);
        }

        //处理包围注解,包围注解可覆盖原调用
        $round = self::getAnnotation($class, $method, 'round');
        $arguments = [[$class, $method], $arguments];
        if ($round) {
            foreach (array_reverse($round) as $annotation) {
                $arguments = [[$class, $annotation], $arguments];
            }
        }
        if (is_array($arguments[1])) {
            $result = $arguments[0](...$arguments[1]);
        } elseif (is_null($arguments[1])) {
            $result = $arguments[0]();
        } else {
            $result = $arguments[0]($arguments[1]);
        }

        //处理后置注解,后置注解对处理结果进行修正
        $after = self::getAnnotation($class, $method, 'after');
        foreach ($after as $annotation) {
            $result = static::$annotation($result);
        }

        return $result;
    }

    /**
     * 魔术调用方法
     * @param string $name 方法名
     * @param array $arguments 方法参数
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        //实际方法名
        $method = self::buildName($name);

        $class = get_called_class();

        //如果方法不存在,则报错
        if (!method_exists($class, $method)) {
            return false;
        }

        //处理前置注解,前置注解对请求参数进行修改
        $before = $this->getAnnotation($class, $method, 'before');
        foreach ($before as $annotation) {
            $arguments = static::$annotation(...$arguments);
        }

        //处理包围注解,包围注解可覆盖原调用
        $round = $this->getAnnotation($class, $method, 'round');
        $arguments = [[$this, $method], $arguments];
        if ($round) {
            foreach (array_reverse($round) as $annotation) {
                $arguments = [[$this, $annotation], $arguments];
            }
        }
        if (is_array($arguments[1])) {
            $result = $arguments[0](...$arguments[1]);
        } elseif (is_null($arguments[1])) {
            $result = $arguments[0]();
        } else {
            $result = $arguments[0]($arguments[1]);
        }

        //处理后置注解,后置注解对处理结果进行修正
        $after = $this->getAnnotation($class, $method, 'after');
        foreach ($after as $annotation) {
            $result = static::$annotation($result);
        }

        return $result;
    }
}