<?php

namespace icePHP\Frame\Process;

use function icePHP\Frame\Functions\isWindows;

/**
 * 进程切换控制
 * User: Administrator
 * Date: 2018/12/14
 * Time: 14:20
 */
trait ProcessSwitch
{

    //执行本代码的进程编号(由posix_getpid得到),用于判断是否切换了进程
    private static $processId = 0;

    /**
     * 清除全部连接,下次重新连接
     */
    static public function clearConnections()
    {
        self::$processId = 0;
    }

    /**
     * 查看是否已经 切换进程,如切换进程则回调
     * @param callable $func 回调方法,没有参数
     */
    private static function changeProcess(callable $func)
    {
        if (!isWindows() and function_exists('posix_getpid') and self::$processId != posix_getpid()) {
            $func();
            self::$processId = posix_getpid();
        }
    }
}