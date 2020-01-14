<?php
declare(strict_types=1);

namespace icePHP\Frame\Crud;

/**
 * 某一字段的CRUD配置对象
 * Date: 2017/3/10
 */
abstract class CrudField
{
    /**
     * 构造字段对象(工厂方法)
     * 本方法为友类提供
     * @param $name string 字段 名
     * @param array $meta 字段 META信息
     * @param Crud $config 表配置对象
     * @return CrudField CrudBit|CrudInt|CrudText|CrudString|CrudList|CrudDate|CrudFloat|CrudBinary
     */
    public static function _instance(string $name, array $meta, Crud $config): CrudField
    {
        //数据库字段类型转换成CRUD数据类型
        switch ($meta['type']) {
            //整数
            case 'int':
            case 'bigint':
            case 'mediumint':
            case 'smallint':
            case 'year':
                return new CrudInt($name, $meta, $config);

            //二进制
            case 'binary':
            case 'blob':
            case 'longblob':
            case 'mediumblob':
            case 'tinyblob':
            case 'varbinary':
                return new CrudBinary($name, $meta, $config);
            case 'bit':
                trigger_error('CRUD暂时无法处理bit类型',E_USER_ERROR);
                exit;
            case 'set':
                trigger_error('CRUD暂时无法处理set类型',E_USER_ERROR);
                exit;

            //日期时间
            case 'date':
            case 'datetime':
            case 'time':
            case 'timestamp':
                return new CrudDate($name, $meta, $config);

            //枚举列表
            case 'enum':
                return new CrudList($name, $meta, $config);

            //大文本
            case 'text';
            case 'longtext':
            case 'mediumtext':
            case 'tinytext':
                return new CrudText($name, $meta, $config);

            case 'tinyint':
                if ($meta['maxLength'] == 1) {
                    //布尔
                    return new CrudBit($name, $meta, $config);
                } else {
                    //整数
                    return new CrudInt($name, $meta, $config);
                }

            //字符串
            case 'char':
            case 'varchar':
                return new CrudString($name, $meta, $config);

            //浮点
            case 'decimal':
            case 'double':
            case 'float':
                return new CrudFloat($name, $meta, $config);
            default:
                trigger_error('CRUD不认识的字段类型:' . $meta['type'],E_USER_ERROR);
                exit;
        }
    }

    /**
     * 判断本字段是否是自动创建字段
     * @return bool
     */
    public function _isAutoField(): bool
    {
        return $this->_type == 'datetime' and ($this->_name == 'created' or $this->_name == 'updated');
    }

    /**
     * @var Crud 表配置
     */
    private $crudConfig;

    //字段名
    public $_name;

    /**
     * @var array 字段的META信息
     */
    public $_meta;

    //具体的字段 Meta信息
    public $_scale, $_type, $_maxLength, $_notNull, $_primaryKey, $_autoIncrement, $_binary, $_unsigned, $_hasDefault, $_description;

    //可能有的Meta信息
    public $_defaultValue, $_enums, $_sets;

    //验证相关属性
    public $_scaleMsg, $_maxLengthMsg, $_minLength, $_minLengthMsg, $_max, $_maxMsg, $_min, $_minMsg, $_preg, $_pregMsg;

    //外键配置
    public $_foreign;

    /**
     * 设置外键配置
     * @param $table string 外键表名
     * @param $key string 值字段名
     * @param $value string 显示字段名
     * @param $where string 外键表过滤条件
     * @param $order string 外键表排序
     * @return $this
     */
    public function foreign(string $table, string $key, string $value, string $where = null, string $order = 'id desc'): CrudField
    {
        $this->_foreign = ['table' => $table, 'key' => $key, 'value' => $value, 'where' => $where, 'order' => $order];
        return $this;
    }

    //字典配置
    public $_dict;

    /**
     * 设置字典配置
     * @param $name string 字典项目名称
     * @param string $table 字典表名称
     * @param string $nameField 字典表项目名称字段名
     * @param string $contentField 字典表项目值字段名
     * @param string $separator 分隔符
     * @return $this
     */
    public function dict(string $name, string $table = 'dict', string $nameField = 'name', string $contentField = 'content', string $separator = '#;'): CrudField
    {
        $this->_dict = [
            'fieldName' => $this->_name,
            'name' => $name,
            'table' => $table,
            'nameField' => $nameField,
            'contentField' => $contentField,
            'separator' => $separator
        ];
        return $this;
    }

    /**
     * 生成验证时的字段名部分
     * @return string
     */
    protected function fieldName(): string
    {
        return '[' . $this->_description . ']';
    }

    /**
     * 字段基类的结束处理方法
     */
    public function _over(): void
    {
        //完善 非空检查
        if ($this->_notNull === true) {
            $this->_notNull = self::fieldName() . '不允许为空,必须输入';
        }

        //完善无符号检查
        if ($this->_unsigned === true) {
            $this->_unsigned = self::fieldName() . '不允许有正负号';
        }

        //完善精度检查
        if ($this->_scale and !$this->_scaleMsg) {
            $this->_scaleMsg = self::fieldName() . '精度错误,最大允许精度为:' . $this->_scale;
        }

        //完善最大长度检查
        if ($this->_maxLength === -1) {
            $this->_maxLength = null;
        }
        if ($this->_maxLength and !$this->_maxLengthMsg) {
            $this->_maxLengthMsg = self::fieldName() . '长度错误,最大允许长度为:' . $this->_maxLength;
        }

        //完善最小长度检查
        if ($this->_minLength and !$this->_minLengthMsg) {
            $this->_minLengthMsg = self::fieldName() . '长度错误,最小允许长度为:' . $this->_minLength;
        }

        //完善最大值检查
        if ($this->_max and !$this->_maxMsg) {
            $this->_maxMsg = self::fieldName() . '超出范围,最大允许值为:' . $this->_max;
        }

        //完善最小值检查
        if ($this->_min and !$this->_minMsg) {
            $this->_minMsg = self::fieldName() . '超出范围,最小允许值为:' . $this->_min;
        }

        //完善正则检查
        if ($this->_preg and !$this->_pregMsg) {
            $this->_pregMsg = self::fieldName() . '格式错误';
        }
    }

    /**
     * 设置本字段的正则验证规则
     * @param $preg  string 正则表达式
     * @param string $msg 错误消息
     * @return $this
     */
    public function preg(string $preg, string $msg = ''): CrudField
    {
        $this->_preg = $preg;
        $this->_pregMsg = $msg;
        return $this;
    }

    /**
     * 设置本字段允许的最小值
     * @param $min mixed 最小值(整数/浮点/字符串)
     * @param $msg string 错误消息
     * @return $this
     */
    public function min($min, string $msg = ''): CrudField
    {
        $this->_min = $min;
        $this->_minMsg = $msg;
        return $this;
    }

    /**
     * 设置本字段允许的最大值
     * @param $max mixed 最大 值 (整数/浮点/字符串)
     * @param $msg string 错误消息
     * @return $this
     */
    public function max($max, string $msg = ''): CrudField
    {
        $this->_max = $max;
        $this->_maxMsg = $msg;
        return $this;
    }

    /**
     * 设置本字段的最小长度
     * @param $minLength int 长度
     * @param $msg string 错误消息
     * @return $this
     */
    public function minLength(int $minLength, string $msg = ''): CrudField
    {
        $this->_minLength = intval($minLength);
        $this->_minLengthMsg = $msg;
        return $this;
    }

    /**
     * 构造字段配置对象
     * @param $name string 字段名
     * @param $meta array 字段的META信息
     * @param Crud $crudConfig 总表配置
     */
    protected function __construct(string $name, array $meta, Crud $crudConfig)
    {
        if (in_array($meta['type'], ['set', 'bit', 'year'])) {
            trigger_error('CRUD系统暂不支持集合/位/年份 类型的字段');
        }

        //总表的配置对象
        $this->crudConfig = $crudConfig;

        //本字段名
        $this->_name = $name;

        //本字段原始meta信息
        $this->_meta = $meta;

        //本字段的每一个Meta信息
        $this->_scale = $meta['scale'];
        $this->_type = $meta['type'];
        $this->_maxLength = $meta['maxLength'];
        $this->_notNull = $meta['notNull'];
        $this->_primaryKey = $meta['primaryKey'];
        $this->_autoIncrement = $meta['autoIncrement'];
        $this->_binary = $meta['binary'];
        $this->_unsigned = $meta['unsigned'];
        $this->_description = $name == $this->_primaryKey ? '编号' : ($meta['description'] ?: $name);

        //以下是可能有的Meta信息
        $this->_hasDefault = isset($meta['hasDefault']) ? $meta['hasDefault'] : null;
        $this->_defaultValue = isset($meta['defaultValue']) ? $meta['defaultValue'] : null;
        $this->_enums = isset($meta['enums']) ? $meta['enums'] : null;
        $this->_sets = isset($meta['sets']) ? $meta['sets'] : null;
    }

    /**
     * 设置字段的精度
     * @param $scale int 小数点后位数
     * @param $msg string 错误消息
     * @return $this
     */
    public function scale(int $scale, string $msg = ''): CrudField
    {
        $this->_scale = intval($scale);
        $this->_scaleMsg = $msg;
        return $this;
    }

    /**
     * 设置字段的原始数据类型
     * @param $type string 原始数据类型
     * @return $this
     */
    public function type(string $type): CrudField
    {
        $this->_type = $type;
        return $this;
    }

    /**
     * 设置字段的最大长度
     * @param $maxLength int 最大长度
     * @param $msg string 错误消息
     * @return $this
     */
    public function maxLength(int $maxLength, string $msg = ''): CrudField
    {
        $this->_maxLength = intval($maxLength);
        $this->_maxLengthMsg = $msg;
        return $this;
    }

    /**
     * 设置字段的非空
     * @param $isNotNull bool|string 是否为非空 错误提示
     * @return $this
     */
    public function notNull(bool $isNotNull = true): CrudField
    {
        $this->_notNull = $isNotNull;
        return $this;
    }

    /**
     * 设置字段的主键
     * @param $isPrimaryKey bool 是否为主键
     * @return $this
     */
    public function primaryKey(bool $isPrimaryKey): CrudField
    {
        $this->_primaryKey = boolval($isPrimaryKey);
        return $this;
    }

    /**
     * 设置字段的自增长
     * @param $isAutoIncrement bool 是否为自增长
     * @return $this
     */
    public function autoIncrement(bool $isAutoIncrement): CrudField
    {
        $this->_autoIncrement = boolval($isAutoIncrement);
        return $this;
    }

    /**
     * 设置字段的二进制
     * @param $isBinary bool 是否为二进制
     * @return $this
     */
    public function binary(bool $isBinary): CrudField
    {
        $this->_binary = boolval($isBinary);
        return $this;
    }

    /**
     * 设置字段的无符号
     * @param $isUnsigned mixed 是否为无符号|错误消息
     * @return $this
     */
    public function unsigned(bool $isUnsigned = true): CrudField
    {
        $this->_unsigned = $isUnsigned;
        return $this;
    }

    /**
     * 设置字段的是否有默认值
     * @param $hasDefault bool 是否有默认值
     * @return $this
     */
    public function hasDefault(bool $hasDefault = true): CrudField
    {
        $this->_hasDefault = boolval($hasDefault);
        return $this;
    }

    /**
     * 设置字段的注释/标题
     * @param $description string 标题/注释
     * @return $this
     */
    public function description(string $description): CrudField
    {
        $this->_description = $description;
        return $this;
    }

    /**
     * 设置字段的默认值
     * @param $defaultValue mixed 默认值
     * @return $this
     */
    public function defaultValue($defaultValue): CrudField
    {
        $this->_defaultValue = $defaultValue;
        return $this;
    }

    /**
     * 设置字段的枚举值
     * @param $enums array 枚举值
     * @return $this
     */
    public function enums(array $enums): CrudField
    {
        $this->_enums = $enums;
        return $this;
    }

    /**
     * 设置字段的集合值
     * @param $sets array 集合值
     * @return $this
     */
    public function sets(array $sets): CrudField
    {
        $this->_sets = $sets;
        return $this;
    }

    //是否在列表/搜索/详情/添加/修改/打印/导出中显示,默认为显示
    public $_searchType = true;

    /**
     * 设置字段是否参与搜索
     * 模糊匹配|精确匹配|开头匹配|结尾匹配|范围匹配|true,默认不参与搜索
     * @param $type string|bool  是否参与搜索
     * @return $this
     */
    public function searchType($type = true): CrudField
    {
        //可能的设置值
        if (!in_array($type, ['精确匹配', '模糊匹配', '开头匹配', '结尾匹配', '范围匹配', true, false])) {
            trigger_error('CRUD' . $this->_name . ' 的搜索配置参数错误',E_USER_ERROR);
        }

        //如果设置了默认值
        if ($type === true) {
            if ($this instanceof CrudString or $this instanceof CrudText) {
                //字符串,文本,列表,使用模糊匹配
                $type = '模糊匹配';
            } elseif ($this instanceof CrudBit or $this instanceof CrudInt or $this instanceof CrudFloat or $this instanceof CrudList) {
                //位,整数,浮点 使用精确匹配
                $type = '精确匹配';
            } elseif ($this instanceof CrudDate) {
                //日期,使用范围匹配
                $type = '范围匹配';
            } else {
                //二进制,不能与搜索
                $type = false;
            }
        }
        $this->_searchType = $type;
        return $this;
    }

    //字段显示顺序
    public $_order = PHP_INT_MAX;

    /**
     * 每个字段设置显示顺序
     * @param $order int 顺序,小的先显示
     * @return $this
     */
    public function order(int $order): CrudField
    {
        $this->_order = intval($order);
        return $this;
    }

    //点击列表中的字段时,要跳转的Action名称
    public $_click;

    /**
     * 设置用户点击列表中的此字段时,要跳转的Action
     * @param $action string 动作名称
     * @return $this
     */
    public function click(string $action): CrudField
    {
        $this->_click = $action;
        return $this;
    }

    //本字段的搜索时的选项(针对枚举和集合,或字符串)
    public $_searchList;

    /**
     * 设置字段在搜索时的下拉内容
     * @param array $list 列表内容
     * @return $this
     */
    public function searchList(array $list): CrudField
    {
        $this->_searchList = $list;
        return $this;
    }

    //本字段的输入时的选项(针对枚举和集合,或字符串)
    public $_inputList;

    /**
     * 设置字段在输入(添加/编辑)时的下拉/列表内容
     * @param array $list 列表内容
     * @return $this
     */
    public function inputList(array $list): CrudField
    {
        $this->_inputList = $list;
        return $this;
    }

    //本字段不参与 CRUD
    public $_ignore;

    /**
     * 设置本字段是否不参与CRUD
     * @param bool $isIgnore
     * @return $this
     */
    public function ignore(bool $isIgnore = true): CrudField
    {
        $this->_ignore = $isIgnore;
        return $this;
    }

    /**
     * 获取本字段精确匹配的表现方式
     * @return array [conditionTpl=>控制器中获取搜索参数的代码模板名称,列表页中搜索条视图模板名称,参数数组]
     */
    abstract public function _searchEqual(): array;

    /**
     * 获取本字段模糊匹配的表现方式
     * @return array [conditionTpl=>控制器中获取搜索参数值的代码模板名称,tpl=>列表视图中搜索栏部分代码模板名称,params=>参数数组]
     */
    public function _searchLike(): array
    {
        if ($this instanceof CrudString or $this instanceof CrudText) {
            return self::searchText('conditionStringLike', 'searchTextLike');
        }
        trigger_error('CRUD中本类型字段不允许模糊匹配搜索',E_USER_ERROR);
        exit;
    }

    /**
     * 获取本字段开头匹配的表现方式
     * @return array [conditionTpl=>控制器中获取搜索参数值的代码模板名称,tpl=>列表视图中搜索栏部分代码模板名称,params=>参数数组]
     */
    public function _searchBegin(): array
    {
        if ($this instanceof CrudString or $this instanceof CrudText) {
            return self::searchText('conditionString');
        }
        trigger_error('CRUD中本类型字段不允许开头搜索方式',E_USER_ERROR);
        exit;
    }

    /**
     * 获取本字段结尾匹配的表现方式
     * @return array [conditionTpl=>控制器中获取搜索参数值的代码模板名称,tpl=>列表视图中搜索栏部分代码模板名称,params=>参数数组]
     */
    public function _searchEnd(): array
    {
        if ($this instanceof CrudString or $this instanceof CrudText) {
            return self::searchText('conditionString');
        }
        trigger_error('CRUD中本类型字段不允许结尾搜索方式',E_USER_ERROR);
        exit;
    }

    /**
     * 获取本字段范围匹配的表现方式
     * @return array [conditionTpl=>控制器中获取搜索参数值的代码模板名称,tpl=>列表视图中搜索栏部分代码模板名称,params=>参数数组]
     */
    public function _searchRange(): array
    {
        if ($this instanceof CrudInt) {
            return self::searchTextRange('conditionIntRange');
        }
        if ($this instanceof CrudFloat) {
            return self::searchTextRange('conditionFloatRange');
        }
        if ($this instanceof CrudString) {
            return self::searchTextRange('conditionStringRange');
        }
        trigger_error('CRUD中本类型不允许范围匹配',E_USER_ERROR);
        exit;
    }

    /**
     * 获取 一个搜索条中的文本框 的模板信息
     * @param $conditionTpl string 搜索时的模板名称
     * @param $tpl string 模板名称
     * @return array [conditionTpl=>控制器中获取搜索参数值的代码模板名称,tpl=>列表视图中搜索栏部分代码模板名称,params=>参数数组]
     */
    protected function searchText(string $conditionTpl, string $tpl = 'searchText'): array
    {
        return [
            'conditionTpl' => $conditionTpl,
            'tpl' => $tpl,
            'params' => [
                'title' => $this->_description,
                'name' => $this->_name
            ]
        ];
    }

    /**
     * 获取 一个搜索条中的范围文本框 的模板信息
     * @param $conditionTpl string 控制器中获取搜索参数值的代码模板名称
     * @return array [系统模板名称,参数数组]
     */
    protected function searchTextRange(string $conditionTpl): array
    {
        return [
            'conditionTpl' => $conditionTpl,
            'tpl' => 'searchTextRange',
            'params' => [
                'title' => $this->_description,
                'name' => $this->_name
            ]
        ];
    }

    /**
     * 生成本类型字段的添加视图模板
     * @return string 模板代码
     */
    public function _add(): string
    {
        //二进制不参与添加
        if ($this instanceof CrudBinary) {
            return '';
        }

        //常用模板变量
        $params = [
            'title' => $this->_description,
            'name' => $this->_name,
            'default' => $this->_hasDefault ? $this->_defaultValue : '',
            'notNull' => $this->_notNull ? 'star' : '',
        ];

        //浮点
        if ($this instanceof CrudFloat) {
            return self::tpl('addFloat', $params);
        }

        //整数
        if ($this instanceof CrudInt) {
            return self::tpl('addInt', $params);
        }

        //字符串
        if ($this instanceof CrudString) {
            if ($this->_isUploadImage) {
                return self::tpl('addStringImage', $params);
            }
            return self::tpl('addString', $params);
        }

        //大文本
        if ($this instanceof CrudText) {
            return self::tpl('addText', $params);
        }

        //列表和布尔要用到
        $params['list'] = static::phpize();
        if ($this instanceof CrudBit) {
            return self::tpl('addBit', $params);
        }
        if ($this instanceof CrudList) {
            return self::tpl('addList', $params);
        }

        //日期的本方法被覆盖,不会进入本方法
        trigger_error('CRUD中不可能的字段类型:' . get_class($this));
        exit;
    }

    /**
     * 生成本类型字段的编辑视图模板
     * @return string
     */
    public function _edit(): string
    {
        //二进制类型字段不参与编辑
        if ($this instanceof CrudBinary) {
            return '';
        }

        //常用模板替换内容
        $params = ['title' => $this->_description, 'name' => $this->_name, 'notNull' => $this->_notNull ? 'star' : ''];

        //整数的编辑代码
        if ($this instanceof CrudInt) {
            return self::tpl('editInt', $params);
        }

        //浮点
        if ($this instanceof CrudFloat) {
            return self::tpl('editFloat', $params);
        }

        //字符串
        if ($this instanceof CrudString) {
            if ($this->_isUploadImage) {
                return self::tpl('editStringImage', $params);
            }
            return self::tpl('editString', $params);
        }

        //大文本
        if ($this instanceof CrudText) {
            return self::tpl('editText', $params);
        }

        //以下是布尔和枚举
        $params['list'] = static::phpize();
        if ($this instanceof CrudBit) {
            return self::tpl('editBit', $params);
        }
        if ($this instanceof CrudList) {
            return self::tpl('editList', $params);
        }

        //日期的本方法被覆盖,不会进入本方法
        trigger_error('CRUD中不可能的字段类型:' . get_class($this));
        exit;
    }

    /**
     * 生成本类型字段的详情页面模板
     * @return string 生成的模板内容
     */
    public function _detail(): string
    {
        //二进制字段,不在详情页显示
        if ($this instanceof CrudBinary) {
            return '';
        }

        //如果本字段是上传图片
        if ($this->_isUploadImage) {
            return self::tpl('detailFieldImage', ['title' => $this->_description, 'name' => $this->_name]);
        }

        //如果本字段是外键字段
        if ($this->_foreign) {
            return self::tpl('detailField', ['title' => $this->_description, 'name' => $this->_name . '_' . $this->_foreign['value']]);
        }

        //如果本字段是字典字段
        if ($this->_dict) {
            return self::tpl('detailFieldDict', array_merge($this->_dict, ['title' => $this->_description, 'name' => $this->_name]));
        }

        //本字段是普通内容
        return self::tpl('detailField', ['title' => $this->_description, 'name' => $this->_name]);
    }

    /**
     * 将枚举数组转化为PHP表达方式
     * @param array $enums 默认使用本字段 的枚举值列表
     * @return string
     */
    protected function phpize(array $enums = null): string
    {
        //如果没指定列表内容,使用本字段的可选项
        if (!$enums) {
            $enums = $this->_enums;
        }

        //拼接PHP代码
        $list = '[';
        foreach ($enums as $key => $enum) {
            $list .= "'" . $enum . "'=>'" . $enum . "',";
        }
        $list = trim($list, ',');
        $list .= ']';

        //返回 ['k1'=>'v1',...]
        return $list;
    }

    /**
     * 生成本字段在控制器中的获取参数值的代码
     * @return string 生成的模板代码
     */
    abstract public function _input(): string;

    //如果当前字段是上传图片,此处保存配置信息
    //false:不是上传图片
    //true:上传图片,但不需要缩略图
    //[width=>宽度,height=>高度,prefix=>前缀,type=>缩略类型]
    public $_isUploadImage = false;

    /**
     * 设置本字段是否是上传图片
     * @param bool $config true/false/配置数组
     * @return $this
     */
    public function isUploadImage($config = true): CrudField
    {
        trigger_error('当前数据类型无法存储上传图片:' . get_class($this) . ',config:' . ($config ? 'true' : 'false'), E_USER_ERROR);
        exit;
    }

    /**
     * CRUD专用获取模板并替换变量
     * 调用 Crud类实现
     * @param $name string 模板名称
     * @param array $params 参数替换表
     * @return string 替换后的模板内容
     */
    protected static function tpl(string $name, array $params = []): string
    {
        return Crud::_tpl($name, $params);
    }
}