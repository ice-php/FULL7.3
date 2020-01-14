<?php
declare(strict_types=1);

namespace icePHP\Frame\Crud;

/**
 * 整数相关的字段类
 * 包括int,bigint,mediumint,smallint,year,tinyint(>1)
 * Class SCrudInt
 */
class CrudInt extends CrudField
{
    /**
     * 构造字段配置对象
     * @param $name string 字段名
     * @param $meta array 字段的META信息
     * @param Crud $crudConfig 总表配置
     * @throws
     */
    public function __construct(string $name, array $meta, Crud $crudConfig)
    {
        parent::__construct($name, $meta, $crudConfig);

        //设置本类型字段的最大值和最小值范围
        self::minMax();
    }

    /**
     * 设置本类型字段的最大值和最小值范围
     */
    private function minMax(): void
    {
        $this->preg('/^[+\-]?\d*$/', '此处只允许输入整数');

        if ($this->_type == 'tinyint') {
            if ($this->_unsigned) {
                $this->max(255, '无符号的tinyint类型最大值为255');
                $this->min(0, '无符号的整数类型最小值为0');
            } else {
                $this->max(127, '带符号的tinyint类型最大值为127');
                $this->min(-128, '带符号的tinyint类型最小值为-128');
            }
        } elseif ($this->_type == 'smallint') {
            if ($this->_unsigned) {
                $this->max(65535, '无符号的smallint类型最大值为65535');
                $this->min(0, '无符号的整数类型最小值为0');
            } else {
                $this->max(32767, '带符号的smallint类型最大值为32767');
                $this->min(-32768, '带符号的smallint类型最小值为-32768');
            }
        } elseif ($this->_type == 'mediumint') {
            if ($this->_unsigned) {
                $this->max(16777215, '无符号的mediumint类型最大值为16777215');
                $this->min(0, '无符号的整数类型最小值为0');
            } else {
                $this->max(8388607, '带符号的mediumint类型最大值为8388607');
                $this->min(-8388608, '带符号的mediumint类型最小值为-8388608');
            }
        } elseif ($this->_type == 'int') {
            if ($this->_unsigned) {
                $this->max(4294967295, '无符号的int类型最大值为4294967295');
                $this->min(0, '无符号的整数类型最小值为0');
            } else {
                $this->max(2147483647, '带符号的int类型最大值为2147483647');
                $this->min(-2147483648, '带符号的int类型最小值为-2147483648');
            }
        } elseif ($this->_type == 'bigint') {
            if ($this->_unsigned) {
                $this->max('18446744073709551615', '输入超出范围,您真的需要表达这么大的整数么?');
                $this->min(0, '无符号的整数类型最小值为0');
            } else {
                $this->max('9223372036854775807', '输入超出范围,您真的需要表达这么大的整数么?');
                $this->min('-9223372036854775808', '输入超出范围,您真的需要表达这么长的整数么?');
            }
        } else {
            trigger_error('CRUD无法识别的整数类型:' . $this->_type);
        }
    }

    /**
     * 返回获取本类型输入参数的控制器代码模板信息
     * @return string 生成的模板代码
     */
    public function _input(): string
    {
        return self::tpl('input', [
            'name' => $this->_name,
            'title' => $this->_description,
            'type' => '整数',
            'action' => 'getInt'
        ]);
    }

    /**
     * 返回整数类型范围精确搜索的模板信息
     * @return array [conditionTpl=>控制器中获取搜索参数的代码模板名称,列表页中搜索条视图模板名称,参数数组]
     */
    public function _searchEqual(): array
    {
        return parent::searchText('conditionInt');
    }
}
