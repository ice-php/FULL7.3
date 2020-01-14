<?php
declare(strict_types=1);

namespace Program\Table\Base;

use icePHP\Frame\Table\Table;
use Program\Table\UserTable;
use Program\Record\UserRecord;

use function icePHP\Frame\Page\page;

/**
 * 用户表(user) 表对象基类
 * @auth 蓝冰大侠
 */
class UserTableBase extends Table
{
    /**
     * 单例句柄
     * @var UserTable
     */
    protected static $handle;
    
    /**
     * 构造方法
     */
    protected function __construct ()
    {
        parent::__construct('user');
    }
    
    /**
     * 单例化
     *
     * @return UserTable
     */
    public static function instance ()
    {
        return parent::instance();
    }

    //主键字段
    public static $primaryKey="id";

    //列表默认行数
    public static $_pageSize=10;

        
	//本表常用字段列表
	public static $fields=['id','name','companyId','companyName','account','password','type','offlinePay','offlinePayQuota','imToken','data','created','updated'] ;
	
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

	
	//字段[账号类型]的枚举值
    const E_type_系统添加='系统添加';
    const E_type_登录接口='登录接口';
    const ENUMS_type = ['系统添加', '登录接口'];

	/**
	 * 根据搜索条件构造查询条件
	 * @param array $info 搜索条件
	 * @return array 查询条件
	 */
	private function searchWhere(array $info): array
	{
		//初始化搜索条件
        $where=[];
				
		//拼接 <id>条件
		if(isset($info['id']) and $info['id']){
			$where['id']=$info['id'];
		}		
		//拼接 <name>条件
		if(isset($info['name_like']) and $info['name_like']){
			$where['name like']='%'.$info['name_like'].'%';
		}
		if(isset($info['name']) and $info['name']){
			$where['name']=$info['name'];
		}
				
		//拼接 <companyId>条件
		if(isset($info['companyId_begin']) and $info['companyId_begin']){
			$where['companyId >']=$info['companyId_begin'];
		}		
		if(isset($info['companyId_end']) and $info['companyId_end']){
			$where['companyId <']=$info['companyId_end'];
		}		
				
		//拼接 <companyName>条件
		if(isset($info['companyName_like']) and $info['companyName_like']){
			$where['companyName like']='%'.$info['companyName_like'].'%';
		}
		if(isset($info['companyName']) and $info['companyName']){
			$where['companyName']=$info['companyName'];
		}
				
		//拼接 <account>条件
		if(isset($info['account_like']) and $info['account_like']){
			$where['account like']='%'.$info['account_like'].'%';
		}
		if(isset($info['account']) and $info['account']){
			$where['account']=$info['account'];
		}
				
		//拼接 <password>条件
		if(isset($info['password_like']) and $info['password_like']){
			$where['password like']='%'.$info['password_like'].'%';
		}
		if(isset($info['password']) and $info['password']){
			$where['password']=$info['password'];
		}
				
		//拼接 <type>条件
		if(isset($info['type']) and $info['type']){
			$where['type']=$info['type'];
		}		
		//拼接 <offlinePay>条件
		if(isset($info['offlinePay']) and $info['offlinePay']){
			$where['offlinePay']=$info['offlinePay'];
		}		
		//拼接 <offlinePayQuota>条件
		if(isset($info['offlinePayQuota']) and $info['offlinePayQuota']){
			$where['offlinePayQuota']=$info['offlinePayQuota'];
		}		
		//拼接 <imToken>条件
		if(isset($info['imToken_like']) and $info['imToken_like']){
			$where['imToken like']='%'.$info['imToken_like'].'%';
		}
		if(isset($info['imToken']) and $info['imToken']){
			$where['imToken']=$info['imToken'];
		}
				
		//拼接 <data>条件
		if(isset($info['data']) and $info['data']){
			$where['data']=$info['data'];
		}		
		//拼接 <created>条件
		if(isset($info['created_begin']) and $info['created_begin']){
			$where['created >']=$info['created_begin'];
		}		
		if(isset($info['created_end']) and $info['created_end']){
			$where['created <']=$info['created_end'];
		}		
				
		//拼接 <updated>条件
		if(isset($info['updated_begin']) and $info['updated_begin']){
			$where['updated >']=$info['updated_begin'];
		}		
		if(isset($info['updated_end']) and $info['updated_end']){
			$where['updated <']=$info['updated_end'];
		}		
		
		//返回查询条件
		return $where;
	}

	/**
	 * 分页搜索及列表功能,此功能需要开发人员进行完善
	 * @param array $info 搜索条件 
	 * @return array[UserRecord]
	 */
    public function search(array $info)  : array
    {
    	//获取查询条件
		$where=$this->searchWhere($info);
		
		//计数满足条件的用户
		$count = $this->count ( $where );
		
		//让分页控件记录数量及条件
		page(self::$_pageSize,self::$primaryKey,'desc')->count ( $count );
		page()->where ( $info );

		//没有查询到数据
		if(!$count){
			return [];
		}

		//查询
		$list = $this->select ( self::$fields, $where,page()->orderby(), page()->limit () )	;

		//返回查询到的数据 , Result对象
		return $list->toRecords('\Program\Record\UserRecord');
    }

	/**
	 * 根据条件,查询需要导出的数据
	 * @param array $info 搜索条件
	 * @param array $fields 要查询的字段列表
	 * @return \Iterator
	 */
	public function export(array $info,array $fields): \Iterator
	{
		//获取查询条件
		$where=$this->searchWhere($info);

		//生成查询句柄
		$handle=$this->selectHandle($fields,$where,page()->orderby());

		while($row=$handle->fetch(\PDO::FETCH_ASSOC)){
			yield $row;
		}
	}
	
	/**
	 * 根据ID获取一条记录
	 * @param int $id 编号ID
	 * @return UserRecord
	 */
	public function info($id)  : UserRecord
	{
		return $this->row(self::$fields,$id);
	}

	/**
	* 取一条记录,将父类查询结果实例为Row的对象
	* @param $fields mixed 要查询的字段
	* @param $where mixed 查询条件
	* @param $orderBy mixed 排序
	* @return UserRecord
	*/
	public function row ($fields = null, $where = null, $orderBy = null)  : UserRecord
	{
		$instance = parent::row($fields,$where,$orderBy)->toRecord('\Program\Record\UserRecord');
		/**
		 * @var $instance UserRecord
		 */
		return $instance;
	}
}
