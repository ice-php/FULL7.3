<?php

namespace icePHP\Lib;

/**
 * 常用的图片验证码
 * @author Ice
 *
 */
class QrCode
{

    /**
     * 禁止实例化,静态类
     */
    private function __construct ()
    {}

    /**
     * @param $url string 地址
     * @param $userId int 用户编号
     * @param $branchId int 分院编号
     * @param $level string 等级
     * @param $size int 大小
     * @return string

     */
    static public function create($url, $userId, $branchId, $level = 'L', $size = 5)
    {
        include_once "phpqrcode/phpqrcode.php";
        $dir = DIR_ROOT."public/Upload/qrcode/".$branchId;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $fileName = $dir."/".$userId.".png";
        \QRcode::png($url, $fileName, $level, $size);
    }

}