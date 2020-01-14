<?php
namespace {$namespace};

use icePHP\Frame\MVC\Controller;
use icePHP\Frame\MVC\Request;

/**
 * 模块{$module}的控制器基类
 */
abstract class {$upper}Controller extends Controller
{
   /**
    * 此处要检查权限
    * @param Request $req
    * @return bool
    */
   public function construct(Request $req): bool
   {
        //当前对象必须与当前类处于相同的命名空间,不允许跨模块继承
        if(strpos(get_class($this),__NAMESPACE__)!==0){
            return false;
        }

        //调用父类的构造方法
        return parent::construct($this->request);
    }
}