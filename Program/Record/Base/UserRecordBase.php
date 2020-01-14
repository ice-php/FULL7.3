<?php
declare(strict_types=1);

namespace Program\Record\Base;

use icePHP\Frame\Table\Record;

/**
 * 表用户表(user)的行记录基类
 */
class UserRecordBase extends Record
{
    //主键
    protected $_primaryKey='id';

    //表名(别名)
    protected $_tableName='user';

    //表的类名(格式化后)
    protected $_baseName='UserRecordBase';

    //表用户表的所有字段名
    protected static $_fields=['id','name','companyId','companyName','account','password','type','offlinePay','offlinePayQuota','imToken','data','created','updated'] ;

    //本表所有字段名的常量
    
    //用户表
    const N_Id='id';

    //名称
    const N_Name='name';

    //
    const N_CompanyId='companyId';

    //公司名称
    const N_CompanyName='companyName';

    //账号(用户名称)
    const N_Account='account';

    //密码
    const N_Password='password';

    //账号类型
    const N_Type='type';

    //1可以挂单支付 0不可挂单支付
    const N_OfflinePay='offlinePay';

    //挂单支付配额
    const N_OfflinePayQuota='offlinePayQuota';

    //网易imToken
    const N_ImToken='imToken';

    //接口登录信息
    const N_Data='data';

    //创建时间
    const N_Created='created';

    //修改时间
    const N_Updated='updated';


    //本表所有枚举的常量
    
	//字段[账号类型]的枚举值
    const E_type_系统添加='系统添加';
    const E_type_登录接口='登录接口';


    /**
    * 用户表
    * @var int
    */
    public $id;

    /**
    * 名称
    * @var string
    */
    public $name;

    /**
    * companyId
    * @var int
    */
    public $companyId;

    /**
    * 公司名称
    * @var string
    */
    public $companyName;

    /**
    * 账号(用户名称)
    * @var string
    */
    public $account;

    /**
    * 密码
    * @var string
    */
    public $password;

    /**
    * 账号类型
    * @var string
    */
    public $type;

    /**
    * 1可以挂单支付 0不可挂单支付
    * @var int
    */
    public $offlinePay;

    /**
    * 挂单支付配额
    * @var float
    */
    public $offlinePayQuota;

    /**
    * 网易imToken
    * @var string
    */
    public $imToken;

    /**
    * 接口登录信息
    * @var string
    */
    public $data;

    /**
    * 创建时间
    * @var string
    */
    public $created;

    /**
    * 修改时间
    * @var string
    */
    public $updated;

}
