<?php

/**
 * 常用的图片验证码,空心字母验证码
 * @author Ice
 *
 */
class SVCode2
{

    /**
     * 禁止实例化,静态类
     */
    private function __construct()
    {
    }

    /**
     * 输出验证码图片,返回验证码
     *
     * @return string
     */
    static public function create()
    {
        ob_clean();
        header("Content-type:image/png");
        list ($image, $code) = self::createImage();
        Imagepng($image);
        Imagedestroy($image);
        return $code;
    }

    //文字颜色从这里随机
    private static $colors = [
        [255, 0, 0],
        [0, 255, 0],
        [0, 0, 255],
        [255, 128, 0],
        [0, 255, 255],
        [255, 0, 255],
        [0, 0, 0]
    ];

    private static $margin=5;
    private static $size=20;

    /**
     * 生成验证码图片
     *
     * @return array(图片,验证码)
     */
    private static function createImage()
    {

        // 图片宽
        $width = self::$size*4+2*self::$margin;

        // 图片高
        $height = self::$size + 2*self::$margin;

        // 创建图片句柄
        $im = imagecreate($width, $height) or
        die("Cannot Initialize new GD image stream");

        // 背景色
        $imBG = imagecolorallocate($im, 255, 255, 255);

        // 边框色
        $border = imagecolorallocate($im, 100, 100, 100);
        // 画一个边框
        $imBorder = imagerectangle($im, 0, 0, $width - 1, $height - 1, $border);

        //$alpha = imagecolorallocatealpha($im, 255, 255, 255, 24);
        //imagefilledellipse($im, $width/2, $height/2, $width*2, $height*2, $alpha);

        // 文字色保存到图片中
        $txtColor=[];
        foreach(self::$colors as $c){
            $txtColor[]=imagecolorallocate($im,$c[0],$c[1],$c[2]);
        }

        // 画800个随机色点
        for ($i = 0; $i < 800; $i++) {
            $c=rand(0,count($txtColor)-1);
            //imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height * 5), $txtColor[$c]);
        }

        // 生成验证码
        $str = self::random(4, 0);

        // 每个验证码字符写入
        for ($i = 0; $i < strlen($str); $i++) {
            // get $x's location
            $x = self::$size * $i + self::$margin;

            $putCode = substr($str, $i, 1);

            //$angle = rand(-30, 30);
            $angle=0;
            $c=rand(0,count($txtColor)-1);
            // 写入文字

            imagettftext($im, self::$size-4, $angle, $x, self::$size + self::$margin, $txtColor[$c], __DIR__ . '/LARSON.TTF', $putCode);
        }

        // 返回图片句柄和验证码串
        return array(
            $im,
            $str
        );
    }

    /**
     * 随机生成验证码
     *
     * @param int $length 长度
     * @param int $numeric 是否纯数字
     * @return string
     */
    private static function random($length, $numeric = 0)
    {
        // 随机数种子
        mt_srand((double)microtime() * 1000000);

        if ($numeric) {
            // 纯数字验证码
            $hash = sprintf('%0' . $length . 'd',
                mt_rand(0, pow(10, $length) - 1));
        } else {
            // 带文字
            $hash = '';
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYabcdefghijkmnpqrstuvwxyz';
            $max = strlen($chars) - 1;
            for ($i = 0; $i < $length; $i++) {
                $hash .= $chars[mt_rand(0, $max)];
            }
        }
        return $hash;
    }

}