<?php
namespace Program\Module\Admin\Controller;

use function icePHP\Frame\Functions\dump;
use icePHP\Frame\MVC\Request;
use Program\Model\CurrentUserModel;
use Program\Model\ImModel;
use Program\Module\Admin\Model\AdminUserModel;
use function icePHP\Frame\MVC\display;
use Program\Table\CompanyTable;
use Program\Table\UserTable;

/**
 * 模块Admin的控制器基类
 */
class AdminUserController extends AdminBaseController
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

    /**
     * 用户管理
     * @funcName 用户管理-列表
     */
   public function index()
   {
       $name = $this->getName(false);
       $list = AdminUserModel::listUser($name);
       display('', [
           'name'       => $name,
           'list'       => $list,
           'controller' => $this
       ]);
   }

    /**
     * 用户管理
     * @funcName 用户管理-修改
     */
   public function edit ()
   {
       $id = $this->getId();
       //获取用户信息
       $user = UserTable::instance()->row(['*'], ['id' => $id])->toArray();
       //获取公司名称
       $company = CompanyTable::instance()->select(['id', 'name'], [], 'id asc', [0, 100])->toArray();

       display('', [
           'user'       => $user,
           'company'    => $company,
           'controller' => $this
       ]);

   }

    public function editSubmit ()
    {
        $id = $this->getId();
        $account = $this->getString('account');
        $companyId = $this->getInt('companyId');
        $offlinePay = $this->getInt('offlinePay');
        $type = $this->getString('type');
        $offlinePayQuota = $this->getFloat('offlinePayQuota');

        $updateData = [
            'offlinePay' => $offlinePay,
        ];
        if ($type == "系统添加") {
            //获取公司名称
            $companyName = '';
            if ($companyId > 0) {
                $companyInfo = CompanyTable::instance()->row(['*'], ['id' => $companyId])->toArray();
                $companyName = $companyInfo['name'];
            }
            $updateData['account'] = $account;
            $updateData['name'] = $account;
            $updateData['companyId'] = $companyId;
            $updateData['companyName'] = $companyName;
        }
        if ($offlinePay == 1) {
            $updateData['offlinePayQuota'] = $offlinePayQuota;
        } else {
            $updateData['offlinePayQuota'] = 0.00;
        }
        UserTable::instance()->update($updateData, ['id' => $id]);
        $this->redirect($this->url('', 'index'));
    }

    /**
     * 用户管理
     * @funcName 用户管理-添加
     */
   public function add ()
   {
       //获取公司名称
       $company = CompanyTable::instance()->select(['id', 'name'], [], 'id asc', [0, 100])->toArray();
       display('', [
           'company'    => $company,
           'controller' => $this
       ]);
   }

   public function addSubmit ()
   {
        $account = $this->getString('account');
        $companyId = $this->getInt('companyId');
        $offlinePay = $this->getInt('offlinePay');
        $offlinePayQuota = $this->getFloat('offlinePayQuota');
        $password = $this->getPassword();
       //获取公司名称
       $companyName = '';
       if ($companyId > 0) {
           $companyInfo = CompanyTable::instance()->row(['*'], ['id' => $companyId])->toArray();
           $companyName = $companyInfo['name'];
       }
        $insertData = [
            'account' => $account,
            'name'    => $account,
            'companyId' => $companyId,
            'offlinePay' => $offlinePay,
            'companyName' => $companyName,
            'password' => password_hash($password, PASSWORD_DEFAULT, ['cost' => 10])
        ];
        if ($offlinePay == 1) {
            $insertData['offlinePayQuota'] = $offlinePayQuota;
        } else {
            $insertData['offlinePayQuota'] = 0.00;
        }
        $insertId = UserTable::instance()->insert($insertData);
        ImModel::createImUser('user', $insertId, $account);
        $this->redirect($this->url('', 'index'));
   }

    /**
     * 用户管理
     * @funcName 用户管理-重置密码
     */
   public function resetPassword ()
   {
       $id = $this->getId();
       $password = password_hash('666666', PASSWORD_DEFAULT, ['cost' => 10]);
       UserTable::instance()->update(['password' => $password], ['id' => $id]);
       $this->redirect($this->url('', 'index'));
   }


}