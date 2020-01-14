<?php
declare(strict_types=1);

namespace Program\Controller;

use icePHP\Frame\MVC\Controller;
use icePHP\Frame\MVC\Request;

use function icePHP\Frame\MVC\display;
use function icePHP\Frame\Upload\uploadImage;

/**
 * 所有控制器的基类,方便添加一些常用方法
 */
class BaseController extends Controller
{
    /**
     * 具体 控制器类可以选择性使用此方法来 预先检查是否可以进入动作
     *
     * @param Request $req
     * @return boolean
     */
    public function construct(Request $req): bool
    {
        return parent::construct($req);
    }

    /**
     * 上传图片
     * @param $name string 页面参数
     * @return array
     */
    protected function uploadImage($name)
    {
        //上传图片
        return uploadImage($name);
    }

    /**
     * 片段,分页
     */
    public function page()
    {
        display('common/page');
    }
}
