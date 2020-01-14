<?php
declare(strict_types=1);

namespace icePHP\Frame\Crud;

/**
 * 位类型:bit/tinyint(1)
 */
class CrudBit extends CrudField
{
    /**
     * 构造字段配置对象
     * @param $name string 字段名
     * @param $meta array 字段的META信息
     * @param Crud $crudConfig 总表配置
     */
    protected function __construct(string $name, array $meta, Crud $crudConfig)
    {
        $meta['enums'] = ['是' => '是', '否' => '否'];
        parent::__construct($name, $meta, $crudConfig);
    }

    /**
     * 返回获取位/布尔类型输入参数的控制器代码模板信息
     * @return string 生成的模板代码
     */
    public function _input(): string
    {
        return self::tpl('input', [
            'name' => $this->_name,
            'title' => $this->_description,
            'type' => '布尔',
            'action' => 'getBoolean'
        ]);
    }

    /**
     * 返回位/布尔类型精确匹配搜索的模板信息
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
