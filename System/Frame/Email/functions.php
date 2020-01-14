<?php
declare(strict_types=1);

namespace icePHP\Frame\Email;

/**
 * 发送一封邮件到一个或多个目标邮箱
 * @param string $title 标题
 * @param array $to 目标邮箱[地址=>用户名]
 * @param string $body 邮件内容
 * @return int 错误代码
 */
function email(string $title, array $to, string $body): int
{
    return Email::email($title, $to, $body);
}