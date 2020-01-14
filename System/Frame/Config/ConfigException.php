<?php
declare(strict_types=1);

namespace icePHP\Frame\Config;

class ConfigException extends \Exception
{
    //配置文件内容必须是数组
    const CONTENT_NOT_ARRAY = 1;

    //请求配置时缺少参数
    const MISS_ARGUMENT = 2;

    //没找到相应的配置项
    const NOT_FOUND = 3;
}