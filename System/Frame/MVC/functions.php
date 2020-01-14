<?php
declare(strict_types=1);

namespace icePHP\Frame\MVC;

use function icePHP\Frame\Config\configDefault;

use icePHP\Frame\Core;

/**
 * 调用视图的方法
 * 这是STemplate的一个快捷入口
 *
 * @param string $file 指定模板文件,如果使用null,则使用当前控制器名称和动作名称作为模板文件名
 * @param array $params 模板参数数组
 * @param boolean $return 要求模板解析结果 返回而不是输出
 * @return string 模板解析结果或空
 */
function display(string $file = null, array $params = [], bool $return = false): string
{
    //获取当前控制器
    $controller=Core::getControllerInstance();

    //如果控制器要求 布局
    if($controller->getLayout()){
        //开启输出缓冲区
        ob_start();

        //生成当前块的视图内容
        Template::display($file,$params);

        //获取缓冲区内容并清除缓冲区
        $content=ob_get_clean();

        //获取并清除布局
        $layout=$controller->getLayout();
        $controller->setLayout('');
        $controller->$layout($content);

        //如果要求返回,则从缓冲区中获取
        if($return){
            return ob_get_clean();
        }
        return '';
    }

    //如果要返回模板结果,则开启Output Buffer
    if ($return) {
        ob_start();
    }

    //模板解析
    $ret = Template::display($file, $params);

    if ($return) {
        return ob_get_clean();
    } else {
        return $ret;
    }
}

/**
 * 获取多语言翻译
 *
 * @param string $name 需要翻译项的名字(Key)
 * @param array $params 翻译过程中的参数
 * @return string 当前语言下的翻译结果|当前语言名
 */
function translation(string $name = '', array $params = []): string
{
    //静态
    static $config;

    // 取整个配置文件
    if (!$config) {
        $config = configDefault(null, 'Language');
        if (!$config) {
            return $name;
        }
    }

    // 请求参数中指定语言的参数名
    $key = $config['_requestKey'];

    // 所有可用的语言列表
    $valid = $config['_valid'];

    if (!isset($_REQUEST[$key])) {
        // 如果请求参数中没有此参数,使用默认语言
        $lan = $config['_default'];
    } else {
        // 获取请求参数中的语言
        $lan = $_REQUEST[$key];

        // 如果不在可用语言列表中,使用默认语言
        if (!in_array($lan, $valid)) {
            $lan = $config['_default'];
        }
    }

    // 如果没有传递要翻译的项目,返回当前语言名
    if (!$name) {
        return $lan;
    }

    // 如果配置文件中没有此项,那就不用研究当前语言了
    if (!isset($config[$name])) {
        return $name;
    }

    // 计算当前指定语言在所有可用语言中的偏移量
    $offset = array_search($lan, $valid);

    // 指定的翻译项的所有语言翻译内容
    $set = $config[$name];

    // 如果没有指定翻译项
    if (!isset($set[$offset])) {
        return $name;
    }

    // 返回配置好的翻译结果
    $ret = $set[$offset];

    //对翻译中的参数进行替换
    foreach ($params as $k => $v) {
        $ret = str_replace("{" . $k . "}", $v, $ret);
    }
    return $ret;
}

/**
 * 记录一个错误信息,在下一个页面显示
 * @param $msg string
 */
function error(string $msg): void
{
    Message::setError($msg);
}

/**
 * 记录一个成功信息,在下一个页面显示
 * @param $msg string
 */
function success(string $msg): void
{
    Message::setSuccess($msg);
}

/**
 * 记录一个提示信息,在下一个页面显示
 * @param $msg string
 */
function info(string $msg): void
{
    Message::setInfo($msg);
}

/**
 * 创建一个表单对象
 * @param string $name
 * @return Form
 */
function form(string $name){
    return new Form($name);
}

/**
 * 创建一个新的表单项对象
 * @param string $name
 * @param string $title
 * @return FormUnit
 */
function formUnit(string $name,string $title=null){
    return new FormUnit($name,$title);
}