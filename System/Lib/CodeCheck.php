<?php

namespace icePHP\Lib;

use icePHP\Frame\Core;

/**
 * 用来代码检查的类
 * @author Ice
 *
 */
class CodeCheck
{

    /**
     * 获取文档中的说明文字
     *
     * @param string $content
     * @return array [代码说明,注释说明,空行说明]
     */
    private function memo($content)
    {
        // 获取代码说明
        $matched = preg_match('/\@codes\:(.*)/', $content, $matches);
        $codesMemo = $matched ? $matches[1] : '';

        // 获取注释说明
        $matched = preg_match('/\@notes\:(.*)/', $content, $matches);
        $notesMemo = $matched ? $matches[1] : '';

        // 获取空行说明
        $matched = preg_match('/\@blanks\:(.*)/', $content, $matches);
        $blanksMemo = $matched ? $matches[1] : '';

        // 返回纯代码说明,注释的说明,空行的说明
        return [$codesMemo, $notesMemo, $blanksMemo];
    }

    /**
     * 计算文本的行数
     *
     * @param string $content
     * @return number
     */
    private function lines($content)
    {
        // 即回车的个数
        return substr_count($content, "\n") + 1;
    }

    /**
     * 计算百分比
     *
     * @param int $line *            分子
     * @param int $all *            分母
     * @return string
     */
    private function per($line, $all)
    {
        return $line . '/' . $all . '=' . round($line * 100 / $all) . '%';
    }

    /**
     * 输出一条统计结果
     *
     * @param string $file *            文件名/目录名
     * @param int $code *            代码数
     * @param int $note *            注释数
     * @param int $blank *            空白数
     * @param string $codesMemo 代码说明
     * @param string $notesMemo 注释的说明
     * @param string $blanksMemo 空白的说明
     * @param string $other 其它说明
     */
    private function tr($file, $code, $note, $blank, $codesMemo = '', $notesMemo = '', $blanksMemo = '', $other = '')
    {
        // 总数
        $all = $code + $note + $blank;

        // 文件名/目录名去除前缀
        $name = substr($file, strlen(DIR_ROOT));

        // 计算三项的达标
        $codeRed = ($code > $all * 55 / 100) ? "style='color:red'" : '';
        $noteRed = ($note < $all * 25 / 100) ? "style='color:red'" : '';
        $blankRed = ($blank < $all * 15 / 100) ? "style='color:red'" : '';
        $otherRed = $other ? "style='color:red'" : '';

        // 生成显示表格行
        echo "<tr>" . "<td>$name</td>" . "<td $codeRed>" .
            $this->per($code, $all) .
            ($codesMemo ? '<br/>' . $codesMemo : '') . "</td>" .
            "<td $noteRed>" . $this->per($note, $all) .
            ($notesMemo ? '<br/>' . $notesMemo : '') . "</td><td $blankRed>" .
            $this->per($blank, $all) .
            ($blanksMemo ? '<br/>' . $blanksMemo : '') . '</td>' .
            "<td $otherRed>" . $other . "</td>" .
            '</tr>';
    }

    /**
     * 检查代码中的问题
     *
     * @param string $content
     * @return string
     */
    private function check($content)
    {
        $return = '';

        // 不允许出现 PHP结束标识
        if (strpos($content, '?' . '>') !== false) {
            $return .= '<br/>出现PHP结束标识';
        }

        // 不允许出现多于一次的PHP开始标识
        $count = substr_count($content, '<' . '?');
        if ($count > 1) {
            $return .= '<br/>出现' . $count . '次PHP开始标识';
        }

        // 不允许出现短标识
        if ($count == 1 and stripos($content, '<' . '?php') === false) {
            $return .= '<br/>出现短的PHP开始标识';
        }

        // 去除字符串常量
        $content = str_replace(['\\"', "\\'"], '', $content);
        $content = preg_replace('/("[^"]*")/', '', $content);
        $content = preg_replace("/('[^']*')/", '', $content);

        // 查找所有的类名
        $matched = preg_match_all('/\sclass\s*(\w*)/i', $content, $matches);
        if ($matched) {
            foreach ($matches[1] as $className) {
                // 类名首字母要大写
                $c = substr($className, 0, 1);
                if ($c < 'A' or $c > 'Z') {
                    $return .= '<br/>类名首字母不合适:' . $className;
                }
            }
        }

        // 记录本文件中是否有类
        $hasClass = $matched;

        // 查找所有方法/函数名
        $matched = preg_match_all('/(.*)function\s+(\w*)/i', $content, $matches);
        if ($matched) {
            foreach ($matches[2] as $name) {
                // 方法名首字母检查,方法作用域检查
                $c = substr($name, 0, 1);
                if ($c >= 'A' and $c <= 'Z') {
                    $return .= '<br/>方法名首字母不允许:' . $name;
                }

                // 排除特殊的魔术方法外,其它方法不建议使用下划线开头
                if ($c == '_' and !in_array($name, ['__construct', '__set', '__get', '__unset', '__call'])) {
                    $return .= '<br/>方法名首字母不推荐使用下划线:' . $name;
                }
            }

            // 如果本文件中有类,要检查方法的作用域
            if ($hasClass) {
                // 检查方法的作用域
                foreach ($matches[1] as $k => $prefix) {
                    if (strpos($prefix, 'public') === false and
                        strpos($prefix, 'protected') === false and
                        strpos($prefix, 'private') === false) {
                        $return .= '<br/>方法未指定作用域:' . $matches[2][$k];
                    }
                }
            }
        }

        // 检查常量名
        $matched = preg_match_all('/const\s*(\w+)\s*=/i', $content, $matches);
        if ($matched) {
            foreach ($matches[1] as $n) {
                if (strtoupper($n) !== $n) {
                    $return .= '<br/>常量名必须大写:' . $n;
                }
            }
        }

        // 检查类变量作用域
        $pure = $content;

        // 去除小括号
        while (true) {
            $pure2 = preg_replace('/(\([^\)\(]*\))/', '', $pure);
            if ($pure2 == $pure) {
                break;
            }
            $pure = $pure2;
        }

        // 去除花括号
        while (true) {
            // 如果只剩类的花括号,不再去除了
            if (preg_match('/class[\w\s]*\{[^\{\}]*\}$/', $pure)) {
                break;
            }

            // 去无可去
            $pure2 = preg_replace('/(\{[^\}\{]*\})/', '', $pure);
            if ($pure2 == $pure) {
                break;
            }
            $pure = $pure2;
        }

        // 去除方法定义
        $pure = preg_replace('/([\w\s]*function[\w\s]*\{[^\}]*\})/', '', $pure);

        // 取类变量
        $matched = preg_match_all('/(.*)(\$\w*)/', $pure, $matches);
        if ($matched) {
            foreach ($matches[1] as $k => $prefix) {
                // 类变量应该指定作用域
                if (strpos($prefix, 'public') === false and
                    strpos($prefix, 'protected') === false and
                    strpos($prefix, 'private') === false) {
                    $return .= '<br/>类变量未指定作用域:' . $matches[2][$k];
                }
            }
        }

        // 查找所有变量名
        $matched = preg_match_all('/(\$\w+)/', $content, $matches);
        if ($matched) {
            foreach ($matches[1] as $v) {
                // 首字母要大写
                $c = substr($v, 1, 1);
                if ($c >= 'A' and $c <= 'Z') {
                    $return .= '<br/>变量名首字母大写:' . $v;
                }
            }
        }

        return $return;
    }

    /**
     * 计算一个目录的代码百分比
     *
     * @param string $path *            目录
     * @return array(代码数,注释数,空行数)
     */
    private function notes($path)
    {
        // 代码行数
        $codes = 0;

        // 空行数
        $blanks = 0;

        // 注释行数
        $notes = 0;

        if (!is_dir($path)) {
            return [0, 0, 0];
        }

        // 循环目录中的每个子文件夹和文件
        $d = new \DirectoryIterator($path);
        foreach ($d as $f) {
            // 去除上级目录和本目录和SVN目录
            if ($f->isDot() or $f->getFilename() == '.svn') {
                continue;
            }

            // 如果是文件,并且不是PHP文件
            if ($f->isFile() and $f->getExtension() != 'php') {
                continue;
            }

            if ($f->isDir()) {
                // 这三个目录 不要查了,是第三方的
                if (in_array($f->getFilename(), ['alipay', 'PHPExcel', 'PHPMailer','vendor','Enhance'])) {
                    continue;
                }

                // 递归处理子目录
                $name = $f->getPathname() . '\\';
                list ($code, $note, $blank) = $this->notes($name);
            } else {
                // 文件名
                $name = $f->getPathname();

                // 文件内容
                $content = trim(file_get_contents($name));

                // 总行数
                $all = $this->lines($content);

                // 获取说明文字
                list ($codesMemo, $notesMemo, $blanksMemo) = $this->memo(
                    $content);

                // 去除注释
                $content = preg_replace('/\/\*(.*?)\*\//is', '', $content);
                $content = preg_replace('/[^\'|^"]\/\/(.*?)\\n/is', '', $content);

                // 去除注释后的行数
                $now = $this->lines($content);

                // 注释行数
                $note = $all - $now;

                // 去除空行
                $content = preg_replace('/(\n[\s| ]*\n)/', "\n", $content);

                // 纯代码行数
                $last = $this->lines($content);

                // 空行数
                $blank = $now - $last;

                // 纯代码行数
                $code = $last;

                // 检查代码中的其它问题
                $other = $this->check($content);

                // 显示
                $this->tr($name, $code, $note, $blank, $codesMemo, $notesMemo, $blanksMemo, $other);
            }

            // 本目录累加
            $codes += $code;
            $blanks += $blank;
            $notes += $note;
        }

        // 如果本目录有需要显示的数据,显示
        if ($codes) {
            $this->tr($path, $codes, $notes, $blanks);
        }

        // 返回本目录统计数据
        return [$codes, $notes, $blanks];
    }

    /**
     * 计算整个项目的代码百分比
     */
    public function __construct()
    {
        echo "
        <style>
        .dict {
            font-size: 12px;
            margin: 20px;
            width: 95%;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-collapse: collapse;
            border-spacing: 0;
        }
        
        td,th {
            box-sizing: border-box;
            border: 1px solid #ddd;
        }
        </style>
";
        echo '<table class="dict" ><tr><th>文件/目录</th><th>代码量</th><th>注释量</th><th>空行量</th><th>其它问题</th></tr>';

        $modules = Core::getModules();

        foreach ($modules as $m) {
            // 检查控制器目录
            $this->notes(DIR_ROOT . 'Program/Module/' . $m . '/Controller/');

            // 检查模型类目录
            $this->notes(DIR_ROOT . 'Program/Module/' . $m . '/Model/');
        }

        // 检查系统控制器
        $this->notes(DIR_ROOT . 'Program/Controller/');

        // 检查系统模型
        $this->notes(DIR_ROOT . 'Program/Model/');


        // 检查业务逻辑类目录
        $this->notes(DIR_ROOT . 'Program/Logical/');

        // 检查表类目录
//        $this->notes(DIR_ROOT . 'program/table/');

        // 检查业务单元类目录
        $this->notes(DIR_ROOT . 'Program/Unit/');

        // 检查系统类目录
        $this->notes(DIR_ROOT . 'System/');

        // 结束
        echo '</table>';
    }
}