<?php
declare(strict_types=1);

namespace icePHP\Frame\FileLog;

/**
 * 写文本日志
 * 这是SLog的一个快捷入口
 *
 * @param string $file 日志文件名
 * @param string|array $msg 要记录的内容
 * @param $raw bool 是否以原始格式进行记录,否则会加些附加信息
 */
function writeLog(string $file, $msg, bool $raw = false): void
{
    FileLog::instance()->writeLog($file, $msg, $raw);
}

/**
 * 记录 调试 日志
 * @param $message mixed 日志内容
 * @param array $context 可替换内容
 */
function logDebug($message,array $context):void{
    FileLog::instance()->debug($message,$context);
}

/**
 * 记录 信息 日志
 * @param $message mixed 日志内容
 * @param array $context 可替换内容
 */
function logInfo($message,array $context):void{
    FileLog::instance()->info($message,$context);
}

/**
 * 记录 提醒 日志
 * @param $message mixed 日志内容
 * @param array $context 可替换内容
 */
function logNotice($message,array $context):void{
    FileLog::instance()->notice($message,$context);
}

/**
 * 记录 警告 日志
 * @param $message mixed 日志内容
 * @param array $context 可替换内容
 */
function logWarning($message,array $context):void{
    FileLog::instance()->warning($message,$context);
}

/**
 * 记录 错误 日志
 * @param $message mixed 日志内容
 * @param array $context 可替换内容
 */
function logError($message,array $context):void{
    FileLog::instance()->error($message,$context);
}

/**
 * 记录 崩溃 日志
 * @param $message mixed 日志内容
 * @param array $context 可替换内容
 */
function logCritical($message,array $context):void{
    FileLog::instance()->critical($message,$context);
}

/**
 * 记录 提示 日志
 * @param $message mixed 日志内容
 * @param array $context 可替换内容
 */
function logAlert($message,array $context):void{
    FileLog::instance()->alert($message,$context);
}

/**
 * 记录 紧急 日志
 * @param $message mixed 日志内容
 * @param array $context 可替换内容
 */
function logEmergency($message,array $context):void{
    FileLog::instance()->emergency($message,$context);
}