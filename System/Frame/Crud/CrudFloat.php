<?php
declare(strict_types=1);

namespace icePHP\Frame\Crud;

/**
 * 浮点类型,包括:decimal/double/float/numeric
 */
class CrudFloat extends CrudField
{
    /**
     * 返回获取浮点类型输入参数的控制器代码模板信息
     * @return string 生成的模板代码
     */
    public function _input(): string
    {
        return self::tpl('input', [
            'name' => $this->_name,
            'title' => $this->_description,
            'type' => '浮点',
            'action' => 'getFloat'
        ]);
    }

    /**
     * 返回浮点类型范围精确搜索的模板信息
     * @return array [conditionTpl=>控制器中获取搜索参数的代码模板名称,列表页中搜索条视图模板名称,参数数组]
     */
    public function _searchEqual(): array
    {
        return parent::searchText('conditionFloat');
    }
}
