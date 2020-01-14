<?php

namespace icePHP\Enhance;

/**
 * 常用的图片验证码
 * @author Ice
 */
class Vcode
{
	
	/**
	 * 禁止实例化,静态类
	 */
	private function __construct()
	{
	}
	
	/**
	 * 输出验证码图片,返回验证码
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
	
	/**
	 * 生成验证码图片
	 * @return array(图片,验证码)
	 */
	private static function createImage()
	{
		
		// 图片宽
		$width = 77;
		
		// 图片高
		$height = 25;
		
		// 创建图片句柄
		$im = imagecreate($width, $height) or die("Cannot Initialize new GD image stream");
		
		// 背景色
		$colorBG = explode(",", "255,255,255");
		
		// 边框色
		$colorBorder = explode(",", "100,100,100");
		
		// 文字色
		$colorTxt = explode(",", "0,0,0");
		
		// 背景填充
		$imBG = imagecolorallocate($im, $colorBG[0], $colorBG[1], $colorBG[2]);
		
		// 画一个边框
		$border = imagecolorallocate($im, $colorBorder[0], $colorBorder[1], $colorBorder[2]);
		$imBorder = imagerectangle($im, 0, 0, $width - 1, $height - 1, $border);
		
		// 文字色保存到图片中
		$imTxt = imagecolorallocate($im, $colorTxt[0], $colorTxt[1], $colorTxt[2]);
		
		// 画800个随机色点
		for($i = 0; $i < 800; $i++){
			$imPoints = imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height * 5), $border);
		}
		
		// 生成验证码
		$str = self::random(4, 1);
		
		// 每个验证码字符写入
		for($i = 0; $i < strlen($str); $i++){
			// get $x's location
			$x = 15 * $i + 5;
			
			$putCode = substr($str, $i, 1);
			
			// 写入文字
			$setCode = imagestring($im, 5, $x, 3, $putCode, $imTxt);
		}
		
		// 返回图片句柄和验证码串
		return [
			$im,
			$str
		];
	}
	
	/**
	 * 随机生成验证码
	 * @param int $length  长度
	 * @param int $numeric 是否纯数字
	 * @return string
	 */
	private static function random($length, $numeric = 0)
	{
		// 随机数种子
		mt_srand((double)microtime() * 1000000);
		
		if($numeric){
			// 纯数字验证码
			$hash = sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
		}else{
			// 带文字
			$hash = '';
			$chars = 'ABCDEFGHJKLMNPQRSTUVWXY3456789abcdefghijkmnpqrstuvwxyz';
			$max = strlen($chars) - 1;
			for($i = 0; $i < $length; $i++){
				$hash .= $chars[mt_rand(0, $max)];
			}
		}
		
		return $hash;
	}
}