<?php
declare(strict_types=1);

namespace icePHP\Lib;

use function icePHP\Frame\Functions\left;

/**
 * 图像绘制类,调整坐标系统,以左下角为原点(0,0) *
 * User: 蓝冰
 * Date: 2017/9/27
 * Time: 9:28
 */
class Draw
{
    public function __construct()
    {
    }

    //图像宽高,像素
    private $width, $height;

    /**
     * @var resource 图像资源句柄
     */
    private $handle;

    /**
     * @var string 原始图像文件格式
     */
    private $sourceType;

    /**
     * 创建一个新图像
     * @param $width int
     * @param $height int
     * @param $back string 背景颜色
     * @param $trueColor bool 是否是真彩色图像
     * @return $this
     */
    public function create($width, $height, $back = 'white', $trueColor = false):self
    {
        $this->width = intval($width);
        $this->height = intval($height);

        //是否创建真彩色
        if ($trueColor) {
            $this->handle = imagecreatetruecolor($width, $height);
        } else {
            $this->handle = imagecreate($width, $height);
        }

        //如果指定了背景色,则用矩形
        imagerectangle($this->handle, 0, 0, $this->width - 1, $this->height - 1, $this->color($back));
        return $this;
    }

    /**
     * 从字符串内容中创建图像(可以是文件内容,也可以是DATAURL)
     * @param $string string
     * @return $this
     */
    public function fromString($string)
    {
        $this->handle = imagecreatefromstring($string);
        $this->sourceType = 'string';
        return $this;
    }

    /**
     * 从文件读取一个图像
     * @param $file string 文件名
     * @return $this
     * @throws
     */
    public function fromFile($file)
    {
        //取文件前八个字节
        $header = file_get_contents($file, 0, null, 0, 8);

        // GIF 图像
        //由于系统漏洞,暂不支持此图片格式
//        if (left($header, 3) == 'GIF') {
//            $this->handle = imagecreatefromgif($file);
//            $this->sourceType = 'gif';
//            return $this;
//        }

        // JPEG 图像
        if (bin2hex(left($header, 2)) == 'ffd8') {
            $this->handle = imagecreatefromjpeg($file);
            $this->sourceType = 'jpeg';
            return $this;
        }

        //判断是PNG,  *png
        if (bin2hex($header) == '89504e470d0a1a0a') {
            $this->handle = imagecreatefrompng($file);
            $this->sourceType = 'png';
            return $this;
        }

        // WBMP 图像
        if (bin2hex(left($header, 4)) == '00001414') {
            $this->handle = imagecreatefromwbmp($file);
            $this->sourceType = 'wbmp';
            return $this;
        }

        // WEBP 图像
        if (left($header, 4) == 'RIFF') {
            $this->handle = imagecreatefromwebp($file);
            $this->sourceType = 'webp';
            return $this;
        }

        // GD 图像
        if (bin2hex($header) == 'ffff001400140000') {
            $this->handle = imagecreatefromgd($file);
            $this->sourceType = 'gd';
            return $this;
        }

        // GD2 图像
        if (bin2hex(left($header, 3)) == '676432') {
            $this->handle = imagecreatefromgd2($file);
            $this->sourceType = 'gd2';
            return $this;
        }

        // XBM 图像
        if ($header == '#define ') {
            $this->handle = imagecreatefromxbm($file);
            $this->sourceType = 'xbm';
            return $this;
        }

        //不认识
        throw new Exception('unknown image file:' . $file . ' HEADER:' . bin2hex($header));
    }

    /**
     * 创建DataUrl格式
     * @return string
     */
    public function dataUrl()
    {
        //保存原输出缓冲区内容
        $ob = ob_get_clean();

        //生成本图像的内容到缓冲区(以PNG格式)
        imagepng($this->handle);

        //从缓冲区取回本图像内容
        $image = ob_get_clean();

        //恢复缓冲区
        echo $ob;

        //创建DataURL
        return 'data:' . image_type_to_mime_type(IMAGETYPE_PNG) . ';base64,' . base64_encode($image);
    }

    /**
     * 输出图像内容,以PNG格式
     * @return $this
     */
    public function output()
    {
        header('Content-Type: ' . image_type_to_mime_type(IMAGETYPE_PNG));
        imagepng($this->handle);
        return $this;
    }

    /**
     * 析构时释放图像句柄
     */
    public function __destruct()
    {
        //如果已经创建了图像句柄
        if (is_resource($this->handle)) {
            imagedestroy($this->handle);
            $this->handle = null;
        }
    }

    //本图像使用到的颜色集
    private $colors = [];

    //颜色名称与颜色值对照表
    private $colorNames = [
        'black' => '#000000',
        'navy' => '#000080',
        'darkblue' => '#00008b',
        'mediumblue' => '#0000cd',
        'blue' => '#0000ff',
        'darkgreen' => '#006400',
        'green' => '#008000',
        'teal' => '#008080',
        'darkcyan' => '#008b8b',
        'deepskyblue' => '#00bfff',
        'darkturquoise' => '#00ced1',
        'mediumspringgreen' => '#00fa9a',
        'lime' => '#00ff00',
        'springgreen' => '#00ff7f',
        'aqua' => '#00ffff',
        'cyan' => '#00ffff',
        'midnightblue' => '#191970',
        'dodgerblue' => '#1e90ff',
        'lightseagreen' => '#20b2aa',
        'forestgreen' => '#228b22',
        'seagreen' => '#2e8b57',
        'darkslategray' => '#2f4f4f',
        'limegreen' => '#32cd32',
        'mediumseagreen' => '#3cb371',
        'turquoise' => '#40e0d0',
        'royalblue' => '#4169e1',
        'steelblue' => '#4682b4',
        'darkslateblue' => '#483d8b',
        'mediumturquoise' => '#48d1cc',
        'indigo' => '#4b0082',
        'darkolivegreen' => '#556b2f',
        'cadetblue' => '#5f9ea0',
        'cornflowerblue' => '#6495ed',
        'mediumaquamarine' => '#66cdaa',
        'dimgray' => '#696969',
        'dimgrey' => '#696969',
        'slateblue' => '#6a5acd',
        'olivedrab' => '#6b8e23',
        'slategray' => '#708090',
        'lightslategray' => '#778899',
        'mediumslateblue' => '#7b68ee',
        'lawngreen' => '#7cfc00',
        'chartreuse' => '#7fff00',
        'aquamarine' => '#7fffd4',
        'maroon' => '#800000',
        'purple' => '#800080',
        'olive' => '#808000',
        'gray' => '#808080',
        'skyblue' => '#87ceeb',
        'lightskyblue' => '#87cefa',
        'blueviolet' => '#8a2be2',
        'darkred' => '#8b0000',
        'darkmagenta' => '#8b008b',
        'saddlebrown' => '#8b4513',
        'darkseagreen' => '#8fbc8f',
        'lightgreen' => '#90ee90',
        'mediumpurple' => '#9370db',
        'darkviolet' => '#9400d3',
        'palegreen' => '#98fb98',
        'darkorchid' => '#9932cc',
        'yellowgreen' => '#9acd32',
        'sienna' => '#a0522d',
        'brown' => '#a52a2a',
        'darkgray' => '#a9a9a9',
        'lightblue' => '#add8e6',
        'greenyellow' => '#adff2f',
        'paleturquoise' => '#afeeee',
        'lightsteelblue' => '#b0c4de',
        'powderblue' => '#b0e0e6',
        'firebrick' => '#b22222',
        'darkgoldenrod' => '#b8860b',
        'mediumorchid' => '#ba55d3',
        'rosybrown' => '#bc8f8f',
        'darkkhaki' => '#bdb76b',
        'silver' => '#c0c0c0',
        'mediumvioletred' => '#c71585',
        'indianred' => '#cd5c5c',
        'peru' => '#cd853f',
        'chocolate' => '#d2691e',
        'tan' => '#d2b48c',
        'lightgray' => '#d3d3d3',
        'thistle' => '#d8bfd8',
        'orchid' => '#da70d6',
        'goldenrod' => '#daa520',
        'palevioletred' => '#db7093',
        'crimson' => '#dc143c',
        'gainsboro' => '#dcdcdc',
        'plum' => '#dda0dd',
        'burlywood' => '#deb887',
        'lightcyan' => '#e0ffff',
        'lavender' => '#e6e6fa',
        'darksalmon' => '#e9967a',
        'violet' => '#ee82ee',
        'palegoldenrod' => '#eee8aa',
        'lightcoral' => '#f08080',
        'khaki' => '#f0e68c',
        'aliceblue' => '#f0f8ff',
        'honeydew' => '#f0fff0',
        'azure' => '#f0ffff',
        'sandybrown' => '#f4a460',
        'wheat' => '#f5deb3',
        'beige' => '#f5f5dc',
        'whitesmoke' => '#f5f5f5',
        'mintcream' => '#f5fffa',
        'ghostwhite' => '#f8f8ff',
        'salmon' => '#fa8072',
        'antiquewhite' => '#faebd7',
        'linen' => '#faf0e6',
        'lightgoldenrodyellow' => '#fafad2',
        'oldlace' => '#fdf5e6',
        'red' => '#ff0000',
        'fuchsia' => '#ff00ff',
        'magenta' => '#ff00ff',
        'deeppink' => '#ff1493',
        'orangered' => '#ff4500',
        'tomato' => '#ff6347',
        'hotpink' => '#ff69b4',
        'coral' => '#ff7f50',
        'darkorange' => '#ff8c00',
        'lightsalmon' => '#ffa07a',
        'orange' => '#ffa500',
        'lightpink' => '#ffb6c1',
        'pink' => '#ffc0cb',
        'gold' => '#ffd700',
        'peachpuff' => '#ffdab9',
        'navajowhite' => '#ffdead',
        'moccasin' => '#ffe4b5',
        'bisque' => '#ffe4c4',
        'mistyrose' => '#ffe4e1',
        'blanchedalmond' => '#ffebcd',
        'papayawhip' => '#ffefd5',
        'lavenderblush' => '#fff0f5',
        'seashell' => '#fff5ee',
        'cornsilk' => '#fff8dc',
        'lemonchiffon' => '#fffacd',
        'floralwhite' => '#fffaf0',
        'snow' => '#fffafa',
        'yellow' => '#ffff00',
        'lightyellow' => '#ffffe0',
        'ivory' => '#fffff0',
        'white' => '#ffffff',
    ];

    /**
     * 根据给定的颜色: 名称/#rgb/#rrggbb 返回颜色标识符
     * @param $color string 名称/#rgb/#rrggbb
     * @return mixed 颜色标识符
     * @throws Exception 不认识的颜色
     */
    private function color($color)
    {
        //如果不指定颜色,使用最后一次的颜色标识符
        if (!$color) {
            return $this->lastColor;
        }

        //全部小写
        $color = strtolower($color);

        if (isset($this->colorNames[$color])) {
            //如果给定的是颜色名称
            $rgb = $this->colorNames[$color];
        } else {
            //给定的是颜色值
            //加上左#号
            if (left($color, 1) != '#') {
                $rgb = '#' . $color;
            } else {
                $rgb = $color;
            }

            //必须以16进制指定颜色值
            if (!preg_match('/^#[a-f0-9]*$/', $rgb)) {
                throw new Exception('color unknown:' . $color);
            }

            //可以是3位 rgb
            if (strlen($rgb) == 4) {
                $rgb = '#' . $rgb{1} . $rgb{1} . $rgb{2} . $rgb{2} . $rgb{3} . $rgb{3};
            } elseif (strlen($rgb) != 7) {
                throw new Exception('color error:' . $color);
            }
        }

        //如果尚未分配,则分配标识符
        if (!isset($this->colors[$rgb])) {
            $r = hexdec(substr($rgb, 1, 2));
            $g = hexdec(substr($rgb, 3, 2));
            $b = hexdec(substr($rgb, 5, 2));
            $this->colors[$rgb] = imagecolorallocate($this->handle, $r, $g, $b);
        }

        //返回标识符
        $this->lastColor = $this->colors[$rgb];

        return $this->lastColor;
    }

    /**
     * 纵坐标翻转, 以实现 左下角为原点
     * @param $y int 左下角为原点的纵坐标
     * @return int 左上角为原点的纵坐标
     */
    private function y($y)
    {
        $y = intval($y);
        $y = min($y, $this->height - 1);
        return $this->height - 1 - $y;
    }

    //最后一次使用的颜色
    private $lastColor = 'black';

    /**
     * 画线
     * @param $color string 颜色名称或颜色值
     * @param $x1 int
     * @param $y1 int
     * @param $x2 int
     * @param $y2 int
     * @return $this
     */
    public function line($x1, $y1, $x2, $y2, $color = null)
    {
        imageline($this->handle, $x1, $this->y($y1), $x2, $this->y($y2), $this->color($color));
        return $this;
    }

    public function wbmp($file, $foreground = null)
    {
        imagewbmp($this->handle, $file, $foreground);
        return $this;
    }

    public function png($file, $quality = null, $filters = null)
    {
        imagepng($this->handle, $file, $quality, $filters);
    }
}