<?php
declare(strict_types=1);

namespace icePHP\Frame\Crud;

/**
 * 日期时间字段类
 * 包括date,datetime,time,timestamp
 */
class CrudDate extends CrudField
{
    /**
     * 返回获取位/布尔类型输入参数的控制器代码模板信息
     * @return string 生成的模板代码
     */
    public function _input():string
    {
        //自动填充的字段不参与添加和编辑
        if ($this->_isAutoField()) {
            return '';
        }

        $type = self::getType();
        return self::tpl('input', [
            'name' => $this->_name,
            'title' => $this->_description,
            'type' => $type['title'],
            'action' => $type['action']
        ]);
    }

    /**
     * 获取本字段类型的 数据格式,获取参数的方法名,日期格式,中文名称
     * @return array
     */
    private function getType():array
    {
        //日期时间
        if ($this->_type == 'datetime' or $this->_type == 'timestamp') {
            return [
                'name' => 'datetime',
                'action' => 'getDatetime',
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'title' => '日期时间',
                'preg' => '/^\d{4}\-\d{2}\-\d{2}\s*\d{2}\:\d{2}\:\d{2}$/'
            ];
        }

        //只有日期
        if ($this->_type == 'date') {
            return [
                'name' => 'date',
                'action' => 'getDate',
                'format' => 'yyyy-MM-dd',
                'title' => '日期',
                'preg' => '/^\d{4}\-\d{2}\-\d{2}$/'
            ];
        }

        //只有时间
        if ($this->_type == 'time') {
            return [
                'name' => 'time',
                'action' => 'getTime',
                'format' => 'HH:mm:ss',
                'title' => '时间',
                'preg' => '/^\d{2}\:\d{2}\:\d{2}$/'
            ];
        }
        trigger_error('日期类型错误:'.$this->_type,E_USER_ERROR);
        exit;
    }

    /**
     * 返回日期类型精确匹配搜索的模板信息
     * @return array [conditionTpl=>控制器中获取搜索参数的代码模板名称,列表页中搜索条视图模板名称,参数数组]
     */
    public function _searchEqual():array
    {
        //自动填充的字段不参与搜索
        if ($this->_isAutoField()) {
            trigger_error('CRUD搜索条件中,自动日期不可参与搜索');
        }

        $type = $this->getType();

        return [
            'conditionTpl' => 'conditionDate',
            'tpl' => 'searchDate',
            'params' => [
                'action' => $type['action'],
                'title' => $this->_description,
                'name' => $this->_name,
                'format' => $type['format'],
            ]
        ];
    }

    /**
     * 返回日期类型范围匹配搜索的模板信息
     * @return array [conditionTpl=>控制器中获取搜索参数的代码模板名称,列表页中搜索条视图模板名称,参数数组]
     */
    public function _searchRange():array
    {
        //自动填充的字段不参与搜索
        if ($this->_isAutoField()) {
            trigger_error('CRUD搜索条件中,自动日期不可参与搜索');
        }

        $type = $this->getType();
        return [
            'conditionTpl' => 'conditionDateRange',
            'tpl' => 'searchDateRange',
            'params' => [
                'action' => $type['action'],
                'title' => $this->_description,
                'name' => $this->_name,
                'format' => $type['format']
            ]
        ];
    }

    /**
     * 生成本类型字段的添加页面模板
     * @return string 生成的模板内容
     */
    public function _add():string
    {
        return self::tpl('addDate', [
            'title' => $this->_description,
            'name' => $this->_name,
            'format' => self::getType()['format'],
            'notNull' => $this->_notNull ? 'star' : '',
            'default' => $this->_hasDefault ? $this->_defaultValue : ''
        ]);
    }

    /**
     * 生成本类型字段的修改页面模板
     * @return string 模板内容
     */
    public function _edit():string
    {
        return self::tpl('editDate', [
            'title' => $this->_description,
            'name' => $this->_name,
            'notNull' => $this->_notNull ? 'star' : '',
            'format' => self::getType()['format']
        ]);
    }
}