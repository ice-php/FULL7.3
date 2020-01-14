<?php
declare(strict_types=1);

namespace icePHP\Frame\Functions;

use function foo\func;

/**
 * icePHP框架的底层函数库
 * User: 蓝冰大侠
 * Date: 2018/8/31
 * Time: 10:12
 */

/**
 * 判断当前是否处于命令行模式
 * @return bool
 */
function isCliMode(): bool
{
    return (php_sapi_name() == 'cli');
}

/**
 * 判断当前操作系统是否Windows
 * @return bool
 */
function isWindows(): bool
{
    return strtolower(PHP_OS) != 'linux';
}

/**
 * 包含文件,将系统中所有文件引入集中到这里
 * @param $path string 路径
 * @return mixed 文件内容 (文件不存在时抛出异常,包含文件中有Return时,按返回值,否则 返回1)
 */
function requireOnce(string $path)
{
    return require_once($path);
}

/**
 * 常用的XML中的CDATA段
 *
 * @param $key string
 * @param $val string
 * @return string
 */
function cdata(string $key, string $val): string
{
    if ($key) {
        return "<{$key}><![CDATA[{$val}]]></{$key}>";
    }
    return "<![CDATA[{$val}]]>";
}

/**
 * 常用XML中的CDATA 数组 字段
 * @param array $arr
 * @return string
 */
function cdatas(array $arr): string
{
    $ret = '';
    foreach ($arr as $k => $v) {
        $ret .= cdata($k, $v);
    }
    return $ret;
}

/**
 * 生成 Y-m-d H:i:s的时间字符串
 * 此方法过于常用
 *
 * @param $time int 时间戳
 * @return string 返回 年-月-日 时-分-秒
 */
function datetime(int $time = null): string
{
    return date('Y-m-d H:i:s', $time ?: time());
}

/**
 * 以标准时区显示时间(0时区)
 * @param int $time 时间戳
 * @return string 返回 年-月-日 时-分-秒
 */
function gmdatetime(int $time = null): string
{
    return gmdate('Y-m-d H:i:s', $time ?: time());
}

/**
 * 以Y-m-d格式显示当前日期
 * @return string 年-月-日
 */
function today(): string
{
    return date('Y-m-d');
}

/**
 * 取指定定界符中间的内容
 *
 * @param string $content 要截取的字符串
 * @param string|null $beginString 开始定界符
 * @param string|null $endString 结束定界符
 * @return null|string
 */
function mid(string $content, string $beginString = null, string $endString = null): ?string
{
    // 如果提供了开始定界符
    if (!is_null($beginString)) {
        // 计算开始定界符的出现位置
        $beginPos = mb_stripos($content, $beginString);

        // 如果没找到开始定界符,失败
        if ($beginPos === false) {
            return null;
        }

        // 去除开始定界符及以前的内容.
        $content = mb_substr($content, $beginPos + strlen($beginString));
    }

    // 如果未提供结束定界符,直接 返回了.
    if (is_null($endString)) {
        return $content;
    }

    // 计算结束定界符的出现位置
    $endPos = mb_stripos($content, $endString);

    // 如果没找到,失败
    if ($endPos === false) {
        return null;
    }

    // 如果位置为0,返回空字符串
    if ($endPos === 0) {
        return '';
    }

    // 返回 字符串直到定界符开始的地方
    return mb_substr($content, 0, $endPos);
}

/**
 * 简化 字符串 左取
 * @param $str string 母串
 * @param $len int  长度
 * @return string 左取的结果
 */
function left(string $str, int $len = 10): string
{
    return substr($str, 0, intval($len));
}

/**
 * 以可读格式显示变量内容
 *
 * @param mixed $vars 变量/数组/...
 * @param string $label 变量名称(可省略)
 * @param boolean $return 返回而不是显示(默认是显示)
 * @return string
 */
function dump($vars, $label = '', bool $return = false): string
{
    // 是否输出了UTF8头
    static $outHeader = 0;

    // 第一次要输出UTF8头,以后就不输出了
    if (!$outHeader and !$return and !isCliMode()) {
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' .
            '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        $outHeader = 1;
    }

    $debug = debug_backtrace();
    $from = $debug[0];
    $fromMsg = $label . ' LINE:' . $from['line'] . ' FILE:' . $from['file'];

    $line = isCliMode() ? PHP_EOL : '<br/>';
    $content = $fromMsg . $line . _dump($vars) . $line;

    // 不需要返回情况下,打印
    if (!$return) {
        echo $content;
    }

    // 无论如何也要返回
    return $content;
}

/**
 * 打印复杂变量
 * @param $var
 * @param int $level 嵌套层级
 * @return string 输出结果
 */
function _dump($var, $level = 0): string
{
    // 超过20层直接返回不记录 防止死循环
    if ($level >= 20) {
        return '...[' . gettype($var) . ']';
    }

    $line = isCliMode() ? PHP_EOL : '<br/>';
    $tab = isCliMode() ? "\t" : str_repeat('&nbsp;', 4);

    //数值直接输出
    if (is_int($var) or is_float($var)) {
        return $var . '';
    }

    //空,输出null
    if (is_null($var)) {
        return 'null';
    }

    //字符串,用双引号,加转义
    if (is_string($var)) {
        //如果无法JSON编码,表示无法正常显示
        if (json_encode($var)) {
            return '"' . addslashes($var) . '"';
        }

        return trim(json($var));
    }

    //布尔型,输出TRUE/FALSE
    if (is_bool($var)) {
        if ($var) {
            return 'TRUE';
        }
        return 'FALSE';
    }

    //数组
    if (is_array($var)) {
        $ret = $line . str_repeat($tab, ++$level) . '[' . $line;
        $level++;
        foreach ($var as $k => $v) {
            $ret .= str_repeat($tab, $level) . _dump($k) . ' => ' . _dump($v, $level) . ',' . $line;
        }
        $level--;
        $ret .= str_repeat($tab, $level) . ']';
        return $ret;
    }

    //对象
    if (is_object($var)) {
        //如果对象有魔术方法,调用魔术方法
        if (method_exists($var, '__dump')) {
            $lines = explode(PHP_EOL, $var->__dump());
            $ret = '';
            foreach ($lines as $l) {
                $ret .= str_repeat($tab, $level) . $l;
            }
            return $ret;
        }

        //输出对象类名
        $level++;
        $ret = 'object(' . (get_class($var)) . ') {' . $line;
        $level++;
        foreach ($var as $k => $v) {
            $ret .= str_repeat($tab, $level) . _dump($k) . ' => ' . _dump($v, $level) . ',' . $line;
        }
        $level--;
        $ret .= str_repeat($tab, --$level) . '}';
        return $ret;
    }

    if (is_resource($var)) {
        return '[' . $var . ' ' . get_resource_type($var) . ']';
    }

    trigger_error('无法打印的变量类型' . gettype($var));
    return '';
}

/**
 * 不区分大小写的查找文件并包含(路径区分大小写,文件名不区分)
 * @param $filename string 文件全路径
 * @return bool|mixed
 * @throws RequireFileException
 */
function requireFile(string $filename)
{
    //小写文件名
    $baseLower = strtolower(basename($filename));

    //目录
    $dirName = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, dirname($filename));

    //目录不存在
    if (!is_dir($dirName)) {
        throw new RequireFileException('目录不存在:' . $dirName, RequireFileException::DIR_NOT_FOUND);
    }

    //此目录下的所有 文件 及文件 夹
    $dir = dir($dirName);

    //遍历 查看
    while ($file = $dir->read()) {
        //文件夹略过
        if ($file == '.' or $file == '..' or is_dir($dirName . '/' . $file)) {
            continue;
        }

        //不区分大小写并匹配
        if (strtolower($file) == $baseLower) {
            return require($dirName . '/' . $file);
        }
    }

    //未找到
    throw new RequireFileException('文件不存在:' . $filename, RequireFileException::FILE_NOT_FOUND);
}

/**
 * 判断当前请求是否是Ajax请求
 * @param $forceAjax bool 强制设置为Ajax状态
 * @return bool 当前是否是Ajax状态
 */
function isAjax(?bool $forceAjax = null): bool
{
    //记录是否强制指定了Ajax模式
    static $force;

    //如果要求强制,则记录下来
    if ($forceAjax) {
        return $force = true;

    }

    if ($forceAjax === false) {
        return $force = false;
    }

    //如果已经强制了,返回是
    if ($force) {
        return true;
    }

    //否则 判断是否是Ajax
    return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
}

/**
 * 美化存储容量数字的格式,K,M,G,T
 *
 * @param int $bytes 要转换的数值
 * @param int $precision 精度
 * @return string 转换成KMGT之后的字符串
 */
function kmgt($bytes, $precision = 1): string
{
    $units = ['B', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'];
    $factor = 1;

    foreach ($units as $unit) {
        if ($bytes < $factor * 1024) {
            return number_format($bytes / $factor, $factor > 1 ? $precision : 0) . ' ' . $unit;
        }
        $factor *= 1024;
    }

    $factor /= 1024;
    return number_format($bytes / $factor, $precision) . ' Y';
}

/**
 * 常用的JSON编码,中文不转码
 *
 * @param mixed $something
 * @return string
 */
function json($something): string
{
    if (!$something) return '';
    $ret = json_encode($something, JSON_UNESCAPED_UNICODE);
    if ($ret) {
        return $ret;
    }
    return json_encode(_jsonable($something), JSON_UNESCAPED_UNICODE);
}

/**
 * 私有,专为json使用,将需要JSON化的数据转化成可转化的格式,主要处理二进制数据
 * @param $something mixed
 * @return mixed
 */
function _jsonable($something)
{
    //如果可以转化,直接返回
    if (json_encode($something, JSON_UNESCAPED_UNICODE)) {
        return $something;
    }

    //字符串,则返回十六进制表示
    if (is_string($something)) {
        return _jsonHex($something);
    }

    //如果是数组,逐个查看值,(键就不看了)
    if (is_array($something)) {
        foreach ($something as $k => $v) {
            $something[$k] = _jsonable($v);
        }
        return $something;
    }

    //如果是对象,也查看值
    if (is_object($something)) {
        foreach ($something as $k => $v) {
            $something->{$k} = _jsonable($v);
        }
        return $something;
    }

    //反正不认识
    return _jsonHex('' . $something);
}

/**
 * 私有,二进制数据转为十六进制表示
 * @param string $str
 * @return string
 */
function _jsonHex(string $str): string
{
    return '[ice:Hex]' . bin2hex($str);
}

/**
 * 构造并打印JsonP结果
 * @param $data mixed
 * @throws JsonPException
 */
function jsonP($data): void
{
    header("Content-Type: text/html; charset=utf-8");
    $data = json($data);

    // 如果是JsonP
    if (isset($_REQUEST['callback'])) {
        //检查变量名是否合法
        if (!preg_match('/\w+/i', $_REQUEST['callback'])) {
            throw new JsonPException('当前请求不是JSONP.');
        }
        $callback = $_REQUEST['callback'];
        echo $callback . '(' . $data . ')';
        exit();
    }

    // 普通Ajax
    echo $data;
    exit();
}

/**
 * 时间记录及计算耗时
 *
 * @param $begin int 开始时间
 * @return int 开始时间(如果未指明开始时间)/时间间隔(如果指明时间间隔)
 */
function timeLog($begin = null)
{
    // 不带参数则返回当前时间
    if (!$begin) {
        return microtime(true);
    }

    // 带参数(开始时间),则返回当前时间与开始时间的差
    return round(microtime(true) - $begin, 6);
}

/**
 * 将下划线分隔的名字,转换为驼峰模式
 *
 * @param string $name 下划线分隔的名字
 * @param bool $firstUpper 转换后的首字母是否大写
 * @return string
 */
function formatter(string $name, bool $firstUpper = true): string
{
    // 将表名中的下划线转换为大写字母
    $words = explode('_', $name);
    foreach ($words as $k => $w) {
        $words [$k] = ucfirst($w);
    }

    // 合并
    $name = implode('', $words);

    // 如果明确要求首字母小写
    if (!$firstUpper) {
        $name = lcfirst($name);
    }

    // 返回名字
    return $name;
}

/**
 * 判断是否包含中文
 * @param $str string 要判断的字符串
 * @return bool
 */
function hasCN(string $str): bool
{
    return preg_match('/[\x{4e00}-\x{9fa5}]/ui', $str) ? true : false;
}

/**
 * 计算下一天(或几天或前几天)的日期
 *
 * @param string $day
 * @param int $n
 * @return string
 */
function nextDay(string $day = '', int $n = 1): string
{
    return date('Y-m-d', strtotime($n . ' day', $day ? strtotime($day) : time()));
}

/**
 * 将字符串分割为数组    
 * @param string $str 字符串
 * @return array 分割得到的数组
 */
function mb_str_split(string $str): array
{
    return preg_split('/(?<!^)(?!$)/u', $str);
}

/**
 * 输出CSV表格头
 * @param $name string CSV表格名称
 */
function csvHeader(string $name)
{
    //头部设定 文件名 时间等
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . iconvRecursion($name) . '.csv"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . date('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: Cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0
}


/**
 * 转换编码格式 utf-8  gb2312,递归,可转换数组
 * @param $data  array|string 数据
 * @return array|string
 */
function iconvRecursion($data)
{
    //原来的编码
    $in_charset = 'utf-8';

    //转变后的编码
    $out_charset = 'GB2312';

    if (substr($out_charset, -8) == '//IGNORE') {
        $out_charset = substr($out_charset, 0, -8);
    }
    if (is_string($data)) {
        return \iconv($in_charset, $out_charset . '//IGNORE', $data);
    }

    if (!is_array($data)) {
        return $data;
    }

    $rtn = [];
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $key = \iconv($in_charset, $out_charset . '//IGNORE', $key);
            $rtn[$key] = iconvRecursion($value);
        } elseif (is_string($key) || is_string($value)) {
            if (is_string($key)) {
                $key = \iconv($in_charset, $out_charset . '//IGNORE', $key);
            }
            if (is_string($value)) {
                $value = \iconv($in_charset, $out_charset . '//IGNORE', $value);
            }
            $rtn[$key] = $value;
        } else {
            $rtn[$key] = $value;
        }
    }

    return $rtn;
}

/**
 * 返回调用者的调用者类名
 * @param $except string 被调用者的类名,排除掉
 * @return string 调用者的类名,带命名空间
 */
function getCaller(string $except=null):string
{
    //获取调用堆栈
    $trace = debug_backtrace();

    //如果未指定排除,返回直接调用者的类名
    if(!$except) {
        return $trace[2]['class'];
    }

    //追溯,直到不是被排除的类名
    foreach ($trace as $item) {
        if(!isset($item['class']))continue;
        if($item['class']==$except)continue;
        return $item['class'];
    }

    //找不到调用类
    return '';
}