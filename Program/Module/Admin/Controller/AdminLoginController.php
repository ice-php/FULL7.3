<?php

declare(strict_types = 1);
namespace Program\Module\Admin\Controller;
/**
 *  后台首页
 *  User: vYao
 *  Date: 2019/7/29
 *  Time: 12:06
 */

use function icePHP\Frame\Functions\dump;
use function icePHP\Frame\MVC\display;
use icePHP\Frame\MVC\Request;
use icePHP\Frame\MVC\Controller;
use Program\Model\VcodeModel;
use Program\Model\CurrentUserModel;
use Program\Module\Admin\Model\AdminUserModel;

class AdminLoginController extends Controller
{
    /**
     * 此处要检查权限
     * @param Request $req
     * @return bool
     */
    public function construct(Request $req): bool
    {
        //当前对象必须与当前类处于相同的命名空间,不允许跨模块继承
        if (strpos(get_class($this),__NAMESPACE__)!==0) {
            return false;
        }

        //调用父类的构造方法
        return parent::construct($this->request);
    }


	public function index()
	{
		display('', [
			'controller' => $this
		]);
	}
	
	/**
	 * 执行登录
	 * @version 2018/11/13
	 */
	public function indexSubmit()
	{
		//检查验证码
//		if (VcodeModel::checkVCode($this->getVCode())) {
//            VcodeModel::clearVCode();
//			$this->error("验证码错误");
//			return;
//		}
		
		//取用户名和密码
		$username = $this->getName();
		$password = $this->getPassword();
		
		//判断可否登录
		$info = AdminUserModel::loginAdmin($username, $password);
		
		if (is_string($info)) {
            //VcodeModel::clearVCode();
			$this->error($info);
			return;
		}

        CurrentUserModel::setAdmin($info);
		
		//跳转到欢迎页
		$this->redirect($this->url('user', 'index'));
	}
	
	/**
	 * 显示验证码
	 * @version 2018/11/13
	 */
	public function vCode()
	{
        VcodeModel::vCode();
	}
	
	/**
	 * 退出登录
	 * @version 2018/11/13
	 */
	public function logout()
	{
		//清除登录信息
		CurrentUserModel::logoutAdmin();
		
		//跳回登录页面
		$this->ok('', $this->url('', 'index'));
	}
}