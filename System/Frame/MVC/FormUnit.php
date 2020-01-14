<?php
declare(strict_types=1);

namespace icePHP\Frame\MVC;

/**
 * 所有表单项的基类
 * @author Ice
 *
 */
class FormUnit
{
    /**
     * @var string 表单项名称(英文)
     */
    private $name;

    /**
     * @var string 表单项标题(中文)
     */
    private $title;

    /**
     * 创建一个表单项对象
     * @param string $name 名称
     * @param string|null $title 标题
     */
    public function __construct(string $name, string $title = null)
    {
        $this->name = $name;
        $this->title = $title ?: $name;
    }

    /**
     * 获取当前表单项的名称(英文)
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @var mixed 当前值
     */
    private $value;

    /**
     * 获取表单项当前值
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * 构造字段名称的显示值: 字段[XX]
     * @return string
     */
    private function title()
    {
        return '字段[' . $this->title . ']';
    }

    /**
     * @var bool 是否是必须填写的
     */
    private $must;

    /**
     * @var string 如果未填写则需要返回的错误信息
     */
    private $mustMsg;

    /**
     * 构造错误信息
     * @param null|string $pattern 开发者提供的错误信息,可使用%s代替字段名称(中文)
     * @param string $suffix 未提供错误信息时的消息后缀
     * @return string
     */
    private function msg(?string $pattern, string $suffix)
    {
        return $pattern ? str_replace('%s', $this->title, $pattern) : ($this->title() . $suffix);
    }

    /**
     * 设置本字段为必填项
     * @param null|string $msg 错误信息
     * @return $this
     */
    public function must(?string $msg = null)
    {
        $this->must = true;
        $this->mustMsg = $this->msg($msg, '必须填写');
        return $this;
    }

    /**
     * @var mixed 最小值
     */
    private $min;

    /**
     * @var string 小于最小值的错误提示
     */
    private $minMsg;

    /**
     * 设置本字段允许的最小值
     * @param $min mixed 最小值
     * @param null|string $minMsg 错误消息模板
     * @return $this
     */
    public function min($min, ?string $minMsg)
    {
        $this->min = $min;
        $this->minMsg = $this->msg($minMsg, '的值不允许小于[' . $min . ']');
        return $this;
    }

    /**
     * @var mixed 最大值
     */
    private $max;

    /**
     * @var string 超出最大值的错误提示
     */
    private $maxMsg;

    /**
     * 设置本字段的最大值
     * @param $max mixed 最大值
     * @param null|string $maxMsg 错误消息模板
     * @return $this
     */
    public function max($max, ?string $maxMsg = null)
    {
        $this->max = $max;
        $this->maxMsg = $this->msg($maxMsg, '的值不允许大于[' . $max . ']');
        return $this;
    }

    /**
     * @var int 最小长度
     */
    private $minLength;

    /**
     * @var string 少于最小长度时的错误提示
     */
    private $minLengthMsg;

    /**
     * 设置最小长度
     * @param int $minLength 最小长度
     * @param null|string $minLengthMsg 错误信息模板
     * @return $this
     */
    public function minLength(int $minLength, ?string $minLengthMsg = null)
    {
        $this->minLength = $minLength;
        $this->minLengthMsg = $this->msg($minLengthMsg, '的长度不允许小于[' . $minLength . ']');
        return $this;
    }

    /**
     * @var int 最大长度
     */
    private $maxLength;

    /**
     * @var string 最大长度错误提示
     */
    private $maxLengthMsg;

    /**
     * 设置本字段的最大长度
     * @param int $maxLength 最大长度
     * @param null|string $maxLengthMsg 错误信息模板
     * @return $this
     */
    public function maxLength(int $maxLength, ?string $maxLengthMsg = null)
    {
        $this->maxLength = $maxLength;
        $this->maxLengthMsg = $this->msg($maxLengthMsg, '的长度不允许大于[' . $maxLength . ']');
        return $this;
    }

    /**
     * @var string 正则表达式
     */
    private $reg;

    /**
     * @var string 不符合正则表达式时的错误消息
     */
    private $regMsg;

    /**
     * 设置正则表达式
     * @param string $reg 正则表达式
     * @param null|string $regMsg 错误信息模板
     * @return $this
     */
    public function reg(string $reg, ?string $regMsg = null)
    {
        $this->reg = $reg;
        $this->regMsg = $this->msg($regMsg, '格式错误');
        return $this;
    }

    /**
     * @var array 允许的枚举值
     */
    private $enums;

    /**
     * @var string 超出枚举值时的错误消息提示
     */
    private $enumsMsg;

    /**
     * 设置允许的枚举值范围
     * @param array $enums 值数组
     * @param $msg string 错误消息
     * @return $this
     */
    public function enums(array $enums, ?string $msg = null)
    {
        $this->enums = $enums;
        $this->enumsMsg = $this->msg($msg, '的值不在允许范围内');
        return $this;
    }

    /**
     * @var bool
     */
    private $unsigned;

    /**
     * @var string 错误信息模板
     */
    private $unsignedMsg;

    /**
     * 设置无符号号
     * @param $msg string 错误消息
     * @return $this
     */
    public function unsigned(?string $msg = null)
    {
        $this->unsigned = true;
        $this->unsignedMsg = $this->msg($msg, '不允许有符号');
        return $this;
    }

    /**
     * @var int 精度
     */
    private $scale;

    /**
     * @var string 精度错误时的错误消息
     */
    private $scaleMsg;

    /**
     * 设置精度 及错误消息
     * @param $scale int 精度位数(小数后位数)
     * @param $msg string 错误消息模板
     * @return $this
     */
    public function scale(int $scale, ?string $msg = null)
    {
        $this->scale = $scale;
        $this->scaleMsg = $this->msg($msg, '精度错误');
        return $this;
    }

    /**
     * @var string 开发人员自定义的错误信息
     */
    private $errorMsg;

    /**
     * 记录开发人员自定义的错误信息
     * @param string $msg
     * @return $this
     */
    public function error(string $msg)
    {
        $this->errorMsg = $msg;
        return $this;
    }

    /**
     * 检查是否出错
     * @return string
     */
    public function check()
    {
        //全部请求参数转换成数组
        $req = Request::instance()->requests();

        // 检查是否允许 为空
        if (!$this->must and !isset($req[$this->name])) {
            return $this->mustMsg;
        }

        //当前值
        $this->value = $v = $req[$this->name];

        //如果开发人员自定义了错误信息
        if($this->errorMsg){
            return $this->errorMsg;
        }

        // 检查最小值
        if (!is_null($this->min) and $v < $this->min) {
            return $this->minMsg;
        }

        // 验证最大值
        if (!is_null($this->max) and $v > $this->max) {
            return $this->maxMsg;
        }

        //验证最大长度
        if ($this->maxLength and strlen($v) > $this->maxLength) {
            return $this->maxLengthMsg;
        }

        //验证最小长度
        if ($this->minLength and strlen($v) < $this->minLength) {
            return $this->minLengthMsg;
        }

        //验证无符号
        if ($this->unsigned) {
            $first = substr($v, 0, 1);
            if ($first == '+' or $first == '-') {
                return $this->unsignedMsg;
            }
        }

        //检查精度
        if ($this->scale) {
            $parts = explode('.', $v);
            if (count($parts) == 2 and strlen($parts[1]) > $this->scale) {
                return $this->scaleMsg;
            }
        }

        // 验证正则表达式
        if (!is_null($this->reg) and !preg_match($this->reg, $v)) {
            return $this->regMsg;
        }

        // 验证通过
        return '';
    }
}