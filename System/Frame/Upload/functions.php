<?php
declare(strict_types=1);

namespace icePHP\Frame\Upload;

/**
 * 上传一个APK(安卓安装包)文件
 * @param string $name $_FILE的名字
 * @return array(错误否,错误信息/路径)
 */
function uploadApk(string $name): array
{
    $upload = new Upload();
    return $upload->apk($name);
}

/**
 * 上传一个图片或文件
 * @param string $name $_FILE的名字
 * @return array(错误否,错误信息/路径)
 */
function uploadFileImage(string $name): array
{
    $upload = new Upload();
    return $upload->fileImageUpload($name);
}

/**
 * 上传文件
 * @param $name string $_FILE的名字
 * @param $mime array MIME信息
 * @param $maxSize int 最大长度(字节)
 * @return array(错误否,错误信息/路径)
 */
function uploadFile(string $name, array $mime = null, int $maxSize = null): array
{
    $upload = new Upload();
    return $upload->fileUpload($name, $mime, $maxSize);
}

/**
 * 上传一个图片
 * @param string $name
 * @param int|null $maxSize
 * @return array
 */
function uploadImage(string $name, int $maxSize = null): array
{
    $upload = new Upload();
    return $upload->image($name, $maxSize);
}

/**
 * 上传多个图片
 * @param string $name
 * @return array
 */
function uploadImageMulti(string $name): array
{
    $upload = new Upload();
    return $upload->imageMulti($name);
}

/**
 * 上传一个视频
 * @param string $name
 * @return array
 */
function uploadVideo(string $name): array
{
    $upload = new Upload();
    return $upload->video($name);
}