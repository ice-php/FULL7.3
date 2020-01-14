<?php
declare(strict_types=1);

namespace icePHP\Frame\Crud;

/**
 * 列表字段类:enum/set
 */
class CrudList extends CrudField
{

    /**
     * 返回获取本类型输入参数的控制器代码模板信息
     * @return string 生成的模板代码
     */
    public function _input(): string
    {
        return self::tpl('inputEnum', [
            'name' => $this->_name,
            'title' => $this->_description,
            'list' => self::phpize()
        ]);
    }

    /**
     * 返回枚举集合类型精确匹配搜索的模板信息
     * @return array [conditionTpl=>控制器中获取搜索参数的代码模板名称,列表页中搜索条视图模板名称,参数数组]
     */
    public function _searchEqual(): array
    {
        return [
            'conditionTpl' => 'conditionList',
            'tpl' => 'searchList',
            'params' => [
                'title' => $this->_description,
                'name' => $this->_name,
                'list' => self::phpize(),
            ]
        ];
    }

}
