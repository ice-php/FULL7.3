<?php
declare(strict_types=1);

namespace icePHP\Frame\Crud;

/**
 * 字符串:char,varchar
 */
class CrudString extends CrudField
{
    /**
     * 返回获取本类型输入参数的控制器代码模板信息
     * @return string 生成的模板代码
     */
    public function _input(): string
    {
        $params = [
            'name' => $this->_name,
            'title' => $this->_description,
            'type' => '字符串',
            'action' => 'getString'
        ];

        //不带缩略图的图片上传
        if ($this->_isUploadImage === true) {
            return self::tpl('inputImage', $params);
        }

        //普通字符串的输入获取
        if (!$this->_isUploadImage) {
            return self::tpl('input', $params);
        }

        //带缩略图的图片上传
        return self::tpl('inputThumb', array_merge($params, $this->_isUploadImage));
    }

    /**
     * 返回字符串类型精确匹配搜索的模板信息
     * @return array [conditionTpl=>控制器中获取搜索参数的代码模板名称,列表页中搜索条视图模板名称,参数数组]
     */
    public function _searchEqual(): array
    {
        return parent::searchText('conditionString');
    }


    /**
     * 设置本字段是否是上传图片
     * @param bool $config true/false/配置数组
     * @return CrudField
     */
    public function isUploadImage($config = true): CrudField
    {
        //false表示不是上传图片,true表示是不需要缩略图的上传图片
        if ($config === false or $config === true) {
            $this->_isUploadImage = $config;
            return $this;
        }

        //检查宽度参数
        if (!isset($config['width']) or !is_int($config['width'])) {
            trigger_error('图片上传的配置信息中width必须设置为整数');
        }

        //检查高度参数
        if (!isset($config['height']) or !is_int($config['height'])) {
            trigger_error('图片上传的配置信息中height必须设置为整数');
        }

        //检查前缀参数
        if (!isset($config['prefix']) or !is_string($config['prefix'])) {
            trigger_error('图片上传的配置信息中prefix必须设置为字符串',E_USER_ERROR);
        }

        //检查缩放类型参数
        if (!isset($config['type']) or !in_array($config['type'], ['等比缩放填充', '等比缩放留空', '等比裁剪', '拉伸'])) {
            trigger_error('图片上传的配置信息中type必须设置为"等比缩放填充|等比缩放留空|等比裁剪|拉伸"',E_USER_ERROR);
        }

        //记录配置信息
        $this->_isUploadImage = $config;
        return $this;
    }
}
