<?php
declare(strict_types=1);

namespace icePHP\Frame\Email;

use function icePHP\Frame\Config\configMust;

/**
 * 使用SwiftMail的邮件发送
 * @package icePHP
 */
final class Email
{
    /**
     * 发送一封邮件到一个或多个目标邮箱
     * @param string $title 标题
     * @param array $to 目标邮箱[地址=>用户名]
     * @param string $body 邮件内容
     * @return int 错误代码
     */
    public static function email(string $title, array $to, string $body):int
    {
        $config = configMust('Mail');
        // Create the Transport
        $transport = (new \Swift_SmtpTransport($config['smtpServer'], 25))
            ->setUsername($config['username'])
            ->setPassword($config['password']);

        // Create the Mailer using your created Transport
        $mailer = new \Swift_Mailer($transport);

        // Create a message
        $message = (new \Swift_Message($title))
            ->setFrom([$config['username'] => $config['name']])
            ->setTo($to)
            ->setBody($body);

        // Send the message
        $result = $mailer->send($message);

        return $result;
    }
}