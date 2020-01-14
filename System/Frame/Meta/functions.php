<?php
declare(strict_types=1);

namespace icePHP\Frame\Meta;

/**
 * 获取一个表的数据字典描述
 * @param $name string 表名
 * @return array
 */
function meta(string $name): array
{
    return Meta::dictTable($name);
}