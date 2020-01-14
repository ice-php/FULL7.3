<?php
declare(strict_types=1);

namespace Program\Record\Base;

use icePHP\Frame\Table\Record;

/**
 * 表{$description}({$tableName})的行记录基类
 */
class {$baseName} extends Record
{
    //主键
    protected $_primaryKey='{$primaryKey}';

    //表名(别名)
    protected $_tableName='{$tableName}';

    //表的类名(格式化后)
    protected $_baseName='{$baseName}';

    //表{$description}的所有字段名
    protected static $_fields={$fieldsName};

    //本表所有字段名的常量
    {$fieldsNameContent}

    //本表所有枚举的常量
    {$enumContent}
{$fields}
}
