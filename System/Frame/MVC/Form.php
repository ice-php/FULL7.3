<?php
declare(strict_types=1);

namespace icePHP\Frame\MVC;

use function icePHP\Frame\Functions\json;

/**
 * 表单类的基类
 * @package icePHP
 */
class Form
{
    /**
     * @var string 表单的名称(中文),我们不关注英文名称
     */
    private $name;

    /**
     * 创建表单对象,记录中文名称
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @var array[FormUnit] 所包含的表单项对象
     */
    private $units = [];

    /**
     * 向表单中添加一个或多个表单项对象
     * @param $units array[FormUnit] 表单项对象
     * @return $this
     */
    public function add(FormUnit ...$units)
    {
        $this->units = array_merge($this->units, $units);
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
     * 整个表单验证
     * @return string 错误信息 如果有多条,使用\n分隔
     */
    public function check()
    {
        $msgs = [];

        //逐项检查
        foreach ($this->units as $item) {
            //检查元素类型
            if (!$item instanceof FormUnit) {
                trigger_error('表单验证的元素必须是FormUnit对象:' . json($item));
            }

            /**
             * @var $item FormUnit
             */
            $name = $item->name();
            $msg = $item->check();
            if ($msg) {
                $msgs[$name] = $msg;
            }
        }

        //表单数据存放到Message对象中,可能不使用
        Message::setFormDataOld($this->value());

        //如果开发人员自定义错误信息,则插入到最前面
        if ($this->errorMsg) {
            array_unshift($msgs,$this->errorMsg);
        }

        //返回错误信息
        return implode("\n", $msgs);
    }

    /**
     * 取整个表单的值
     * @return array
     */
    public function value()
    {
        $values = [];
        foreach ($this->units as $item) {
            /**
             * @var $item FormUnit
             */
            $values[$item->name()] = $item->value();
        }

        return $values;
    }

    /**
     * 将整个表单的验证配置进行JSON编码,以便前端使用
     */
    public function json()
    {
        return json($this->value());
    }
}