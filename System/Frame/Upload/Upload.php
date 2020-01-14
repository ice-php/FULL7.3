<?php
declare(strict_types=1);

namespace icePHP\Frame\Upload;

use function icePHP\Frame\Config\config;
use icePHP\Frame\Config\ConfigException;
use function icePHP\Frame\Config\configMust;
use function intval;

/**
 * 说明：图片上传类
 * @codes:有两个MIME硬编码数组,近90行,导致纯代码量较多
 */
class Upload
{
    //自定义目录
    private $customPath = '';

    //自定义名称
    private $customName = '';

    // 图片的MIME与后缀的对应关系
    private static $imageMime = array(
        'image/bmp' => 'bmp',
        'image/x-cals' => 'cal',
        'image/cis-cod' => 'cod',
        'image/x-dcx' => 'dcx',
        'image/x-eri' => 'eri',
        'image/x-freehand' => 'fh4',
        'image/fif' => 'fif',
        'image/x-fpx' => 'fpx',
        'image/gif' => 'gif',
        'image/ief' => 'ief',
        'image/ifs' => 'ifs',
        'image/j2k' => 'j2k',
        'image/jpeg' => 'jpg',
        'image/pipeg' => 'jfif',
        'image/nbmp' => 'nbmp',
        'image/vnd.nok-oplogo-color' => 'nokia-op-logo',
        'image/x-pcx' => 'pcx',
        'image/x-pda' => 'pda',
        'image/x-pict' => 'pict',
        'image/png' => 'png',
        'image/x-quicktime' => 'qti',
        'image/svg+xml' => 'svg',
        'image/tiff' => 'tif',
        'image/x-cmu-raster' => 'ras',
        'image/vnd.rn-realflash' => 'rf',
        'image/vnd.rn-realpix' => 'rp',
        'image/si6' => 'si6',
        'image/vnd.stiwap.sis' => 'si7',
        'image/vnd' => 'svf',
        'image/svg-xml' => 'svg',
        'image/svh' => 'svh',
        'image/toy' => 'toy',
        'image/vnd.wap.wbmp' => 'wbmp',
        'image/wavelet' => 'wi',
        'image/x-up-wpng' => 'wpng',
        'image/x-cmx' => 'cmx',
        'image/x-icon' => 'ico',
        'image/x-portable-anymap' => 'pnm',
        'image/x-portable-bitmap' => 'pbm',
        'image/x-portable-graymap' => 'pgm',
        'image/x-portable-pixmap' => 'ppm',
        'image/x-rgb' => 'rgb',
        'image/x-xbitmap' => 'xbm',
        'image/x-xpixmap' => 'xpm',
        'image/x-xwindowdump' => 'xwd'
    );

    private static $wordOrExcelMime = array(
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.ms-excel' => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/vnd.ms-powerpoint' => 'ppt',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
        'application/pdf' => 'pdf',
        'text/plain' => 'txt'
    );

    /**
     * 图片上传
     * @param string $name $_FILE的名字
     * @param int|string $maxSize 限制大小
     * @return array (错误否,错误信息/路径)
     */
    public function image(string $name, ?int $maxSize = null): array
    {
        return $this->imageOrVideo($name,
            array(
                'image/bmp' => 'bmp',
                'image/gif' => 'gif',
                'image/jpeg' => 'jpg',
                'image/jpg' => 'jpg',
                'image/pjpeg' => 'jpg',
                'image/png' => 'png',
                'image/x-png' => 'png',
            ), $maxSize);
    }

    /**
     * 批量上传,上传错误不返回
     * @param $name string 上传文件名
     * @return array (错误否,错误信息/路径)
     */
    public function imageMulti(string $name): array
    {
        return $this->uploadImageMulti($name,
            array(
                'image/bmp' => 'bmp',
                'image/gif' => 'gif',
                'image/jpeg' => 'jpg',
                'image/pjpeg' => 'jpg',
                'image/png' => 'png',
                'image/x-png' => 'png',
            ),
            configMust('Upload', 'maxSize'));
    }

    /**
     * 视频上传
     * @param string $name $_FILE的名字
     * @return array(错误否,错误信息/路径)
     */
    public function video($name): array
    {
        return $this->imageOrVideo($name, self::$videoMime);
    }

    /**
     * 上传文件
     * @param $name string $_FILE的名字
     * @param $mime array MIME信息
     * @param $maxSize int 最大长度(字节)
     * @return array(错误否,错误信息/路径)
     */
    public function fileUpload(string $name, ?array $mime = null, ?int $maxSize = null)
    {
        $mime = $mime ?: self::$wordOrExcelMime;
        return $this->imageOrVideo($name, $mime, $maxSize);
    }

    /**
     * 上传文件和图片
     * @param $name string
     * @return array
     */
    public function fileImageUpload(string $name)
    {
        return $this->imageOrVideo($name, array_merge(self::$wordOrExcelMime, array(
            'image/bmp' => 'bmp',
            'image/gif' => 'gif',
            'image/jpeg' => 'jpg',
            'image/pjpeg' => 'jpg',
            'image/png' => 'png',
            'image/x-png' => 'png',
        )));
    }

    /**
     * 生成一张图片的所有缩略图
     * @param string $srcFile
     * @return boolean
     * @throws ConfigException
     */
    public static function allThumb($srcFile)
    {
        $config = config('Upload', 'thumbs');

        foreach ($config as $c) {
            self::thumb($srcFile, $c);
        }
        return true;
    }

    /**
     * 计算缩略图的文件名
     * @param string $srcFile 源文件名(可以是绝对,也可以是相对)
     * @param string $prefix 要加的前缀,后缀固定为jpg
     * @return string
     */
    public static function thumbName($srcFile, $prefix)
    {
        $base = basename($srcFile);
        $trgBase = $prefix . $base;
        $trgFile = dirname($srcFile) . '/' . $trgBase;
        return $trgFile;
    }

    /**
     * 生成缩略图,等比缩放
     *
     * @param string $srcFile
     * @param array $config
     * @return string 缩略图文件名(只有文件名,存放在同一路径下)
     */
    public static function thumb($srcFile, $config)
    {
        // 获取图片信息
        $info = getimagesize($srcFile);

        // 取图片的MIME
        $type = $info['mime'];

        // 如果不识别,返回错误
        $types = self::$imageMime;
        if (!isset($types[$type])) {
            return '无法识别的文件MIME:' . $type;
        }

        // 获取对应的后缀
        $ext = $types[$info['mime']];

        // 取出源图,PHP只支持以下图片格式
        switch ($ext) {
            //由于有系统漏洞, 暂不支持此格式
//            case 'gif':
//                $src = imagecreatefromgif($srcFile);
//                break;
            case "jpg":
                $src = imagecreatefromjpeg($srcFile);
                break;
            case 'png':
                $src = imagecreatefrompng($srcFile);
                break;
            case 'wbmp':
                $src = imagecreatefromwbmp($srcFile);
                break;
            case 'xbm':
                $src = imagecreatefromxbm($srcFile);
                break;
            default:
                return '无法处理此类型的图片';
        }


        // 目标图片高,宽
        $trgFileHeight = $config['height'];
        $trgFileWidth = $config['width'];

        // 源图高,宽
        $srcFileWidth = $info[0];
        $srcFileHeight = $info[1];

        // 缩略类型:拉伸|等比裁剪|等比缩放填充|等比缩放留空
        $type = $config['type'];

        // 默认源区域与源文件相同
        $srcWidth = $srcFileWidth;
        $srcHeight = $srcFileHeight;

        // 默认目标区域与目标文件相同
        $trgWidth = $trgFileWidth;
        $trgHeight = $trgFileHeight;

        // 默认源和目标的坐标源点均为0
        $trgX = $trgY = $srcX = $srcY = 0;

        // 拉伸或正好等比
        if ($type == '拉伸' or ($srcFileHeight * $trgFileWidth ==
                $trgFileHeight * $srcFileWidth)) {
            // 什么也不用作,默认值即可
        } elseif ($srcFileHeight * $trgFileWidth > $trgFileHeight * $srcFileWidth) {
            // 源图比目标图瘦

            if ($type == '等比缩放留空') {
                // 目标区域高相同,宽度重新计算,要瘦一些,文件宽度随之变化
                $trgFileWidth = $trgWidth = $srcWidth * $trgHeight / $srcHeight;
            } elseif ($type == '等比缩放填充') {
                // 目标区域高相同,宽度重新计算,要瘦一些
                $trgWidth = $srcWidth * $trgHeight / $srcHeight;

                // 目标的坐标源点向右
                $trgX = ($trgFileWidth - $trgWidth) / 2;
            } elseif ($type == '等比裁剪') {
                // 源区域满宽度,但高度要矮一些
                $srcHeight = $trgHeight * $srcWidth / $trgWidth;

                // 源的坐标源点向下
                $srcY = ($srcFileHeight - $srcHeight) / 2;
            }
        } else {
            // 源图比目标图胖

            if ($type == '等比缩放留空') {
                // 目标区域宽相同,高度重新计算,要矮一些
                $trgFileHeight = $trgHeight = $srcHeight * $trgWidth / $srcWidth;
            } elseif ($type == '等比缩放填充') {
                // 目标区域宽相同,高度重新计算,要矮一些
                $trgHeight = $srcHeight * $trgWidth / $srcWidth;

                // 目标的坐标源点向下
                $trgY = ($trgFileHeight - $trgHeight) / 2;
            } elseif ($type == '等比裁剪') {
                // 源区域满高度,但宽度要瘦一些
                $srcWidth = $trgWidth * $srcHeight / $trgHeight;

                // 源的坐标源点向右
                $srcX = ($srcFileWidth - $srcWidth) / 2;
            }
        }

        // 创建目标图片句柄
        $trg = imagecreatetruecolor($trgFileWidth, $trgFileHeight);

        // 填充背景
        $bg = imagecolorallocate($trg, 0x00, 0x00, 0x00);
        imagefill($trg, 0, 0, $bg);

        // 从源图缩略复制
        $sampled = imagecopyresampled($trg, $src, intval($trgX), intval($trgY), intval($srcX), intval($srcY),
	        intval($trgWidth), intval($trgHeight), intval($srcWidth), intval($srcHeight));

        // 复制失败
        if (!$sampled) {
            return ' 缩略图生成失败';
        }

        // 计算缩略图文件名
        $trgFile = self::thumbName($srcFile, $config['prefix']);

        // 保存图片文件
        $result = imagejpeg($trg, $trgFile);

        // 保存文件失败
        if (!$result) {
            return '缩略图创建失败';
        }

        // 返回成功
        return true;
    }

    // 视频MIME类型与后缀列表
    private static $videoMime = array(
        'video/mpeg' => 'mpeg',
        'video/quicktime' => 'qt',
        'video/x-la-asf' => 'lsf',
        'video/x-ms-asf' => 'asf',
        'video/x-ms-asf-plugin' => 'asx',
        'video/x-msvideo' => 'avi',
        'application/x-troff-msvideo' => 'avi',
        'video/avi' => 'avi',
        'video/msvideo' => 'avi',
        'video/avs-video' => 'avs',
        'video/x-dv' => 'dif',
        'video/dl' => 'dl',
        'video/x-dl' => 'dl',
        'video/fli' => 'fli',
        'video/x-fli' => 'fli',
        'video/x-sgi-movie' => 'movie',
        'video/3gpp' => '3gp',
        'video/isivideo' => 'fvi',
        'video/x-mng' => 'mng',
        'video/mp4' => 'mp4',
        'video/x-pv-pvx' => 'pvx',
        'video/vnd.rn-realvideo' => 'rv',
        'video/vdo' => 'vdo',
        'video/vivo' => 'viv',
        'video/x-ms-wm' => 'wm',
        'video/x-ms-wmx' => 'wmx',
        'video/wavelet' => 'wv',
        'video/x-ms-wvx' => 'wvx',
        'video/rm' => 'rm',
        'video/x-ms-wmv' => 'wmv',
        'video/animaflex' => 'afl',
        'video/x-atomic3d-feature' => 'fmf',
        'video/gl' => 'gl',
        'video/x-gl' => 'gl',
        'video/x-isvideo' => 'isu',
        'video/x-motion-jpeg' => 'mjpg',
        'video/x-mpeg' => 'mp2',
        'video/x-mpeq2a' => 'mp2',
        'video/x-qtc' => 'qtc',
        'video/x-scm' => 'scm',
        'video/vnd.vivo' => 'vivo',
        'video/x-amt-demorun' => 'xdr',
        'video/vosaic' => 'vos',
        'video/x-amt-showrun' => 'xsr'
    );

    /**
     * 处理上传的图片及视频
     * @param string $name
     * @param array $types
     * @param int $maxSize
     * @return array(错误否,路径 错误信息)
     */
    private function imageOrVideo(string $name, array $types, ?int $maxSize = null): array
    {
        $maxSize = $maxSize ?: configMust('Upload', 'maxSize');

        // 检查是否有此菜单项
        if (!isset($_FILES) or !isset($_FILES[$name]) or !isset($_FILES[$name]['tmp_name']) or empty($_FILES[$name]['tmp_name'])) {
            return array(1, '没有上传文件');
        }

        // 检查上传的文件是否是数组
        $isMulti = is_array($_FILES[$name]['type']);
        $uploadFile = $_FILES[$name];

        // 如果是多文件上传
        if ($isMulti) {
            // 数组中每一个文件的保存路径
            $paths = array();

            // 逐个文件处理
            foreach ($uploadFile['type'] as $k => $type) {

                // 单个 文件检查
                list ($error, $file) = $this->check($uploadFile['error'][$k],
                    $uploadFile['size'][$k], $uploadFile['type'][$k],
                    $uploadFile['tmp_name'][$k], $types, $maxSize);

                // 如果有错误
                if ($error) {
                    return array($error, $file);
                }

                // 保存到数组中
                $paths[] = $file;
            }
        } else {

            // 只上传了一个文件,检查
            list ($error, $file) = $this->check($uploadFile['error'],
                $uploadFile['size'], $uploadFile['type'],
                $uploadFile['tmp_name'], $types, $maxSize);

            // 如果有错误
            if ($error) {
                return array($error, $file);
            }

            // 只保存一个文件名
            $paths = $file;
        }

        // 成功,返回相对路径
        return array(0, $paths);
    }

    /**
     * 处理上传的图片及视频
     * @param string $name
     * @param array $types
     * @param float $maxSize
     * @return array(错误否,路径 错误信息)
     */
    private function uploadImageMulti($name, array $types, $maxSize)
    {
        // 检查是否有此菜单项
        if (!isset($_FILES) or !isset($_FILES[$name]) or !isset($_FILES[$name]['tmp_name']) or empty($_FILES[$name]['tmp_name'])) {
            return array(1, '没有上传文件');
        }

        // 检查上传的文件是否是数组
        $uploadFile = $_FILES[$name];
        $paths = array();

        // 逐个文件处理
        foreach ($uploadFile['type'] as $k => $type) {

            // 单个 文件检查
            list ($error, $file) = $this->check($uploadFile['error'][$k],
                $uploadFile['size'][$k], $uploadFile['type'][$k],
                $uploadFile['tmp_name'][$k], $types, $maxSize);

            // 如果有错误
            if ($error) {
                $paths[] = '';
            } else {
                // 保存到数组中
                $paths[] = $file;
            }
        }

        // 成功,返回相对路径
        return array(0, $paths);
    }

    /**
     * 检查一个上传文件是否有错误
     * @param string $error
     * @param float $size
     * @param string $type
     * @param string $tmp_name
     * @param array $types 允许的类型列表
     * @param float $maxSize 允许的最大文件大小
     * @return array [code,msg]
     */
    private function check($error, $size, $type, $tmp_name, $types, $maxSize)
    {
        // 检查错误码
        switch ($error) {
            case UPLOAD_ERR_INI_SIZE:
                return array(3, '文件大小超过服务器限制');
            case UPLOAD_ERR_FORM_SIZE:
                return array(4, '文件太大');
            case UPLOAD_ERR_PARTIAL:
                return array(5, '文件只有部分被上传');
            case UPLOAD_ERR_NO_FILE:
                return array(6, '没有文件被上传');
            case UPLOAD_ERR_NO_TMP_DIR:
                return array(7, '找不到临时文件夹');
            case UPLOAD_ERR_CANT_WRITE:
                return array(8, '文件写入失败');
        }

        // 未知的错误码
        if ($error > 0) {
            return array(9, '其它原因导致上传失败:' . $error);
        }

        // 检查上传文件的MIME信息
        if (!in_array($type, array_keys($types))) {
            $str = "上传文件支持格式为:";
            $tyeps = implode(',', $types);
            return array(10, $str . $tyeps);
        }

        // 对应文件后缀名
        $ext = $types[$type];

        // 构造 文件名
        $customName = $this->customName;

        //有自定义文件名用自定义
        if ($customName) {
            $file = $customName . '.' . $ext;
        } else {
            $file = uniqid() . '.' . $ext;
        }

        // 检查图片大小
        if ($size > $maxSize) {
            $maxMessage = intval($maxSize / 1024);
            $maxMessage = $maxMessage > 1024 ? intval($maxMessage / 1024) . 'Mb' : $maxMessage . 'Kb';
            return array(11, '上传文件超过程序限制, 最大：' . $maxMessage);
        }

        // 取上传文件的临时名
        $tmpName = $tmp_name;

        // 检查临时文件是否是上传来的
        if (!is_uploaded_file($tmpName)) {
            return array(12, '获取上传临时文件失败');
        }

        // 保存上传文件的文件目录
        $customPath = $this->customPath;

        //有自定义目录用自定义目录
        if ($customPath) {
            $path = $customPath . $file;
            $dir = DIR_ROOT . 'Public' . $customPath;
        } else {
            $time = time();
            $dir = configMust('Upload', 'dir') . date('Y/m/d/', $time);
            $path = configMust('Upload', 'path') . date('Y/m/d/', $time) . $file;
        }

        // 如果目录不存在则创建
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        // 把文件复制到应该在的目录中
        $result = move_uploaded_file($tmpName, $dir . $file);

        // 如果复制文件失败
        if (!$result) {
            return array(13, '上传文件复制失败');
        }

        // 返回成功(0),及文件名(不包含路径)
        return array(0, $path);
    }

    // APK文件的MIME与后缀的对应关系
    private static $apkMime = array(
        'application/octet-stream' => 'apk'
    );

    // fmap文件的MIME与后缀的对应关系
    private static $fmapMime = array(
        'application/octet-stream' => 'fmap'
    );

    /**
     * APK文件上传
     * @param string $name $_FILE的名字
     * @return array(错误否,错误信息/路径)
     */
    public function apk(string $name)
    {
        return $this->imageOrVideo($name, self::$apkMime);
    }

    /**
     * fmap文件上传
     * @param string $name $_FILE的名字
     * @return array(错误否,错误信息/路径)
     */
    public function fmap($name)
    {
        return $this->imageOrVideo($name, self::$fmapMime, 1 * 1024 * 1024);
    }

    /**
     * 指定上传文件保存文件名及保存路径
     * @param string $name 保存文件名
     * @param string $path 保存路径
     * @return Upload
     */
    public function setLocation(string $name, string $path): Upload
    {
        $this->customName = $name;
        $this->customPath = $path;
        return $this;
    }
}