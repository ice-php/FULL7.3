<?php
declare(strict_types=1);

namespace Program\Module\System\Controller;

use icePHP\Frame\MVC\Controller;
use icePHP\Frame\MVC\Request;
use icePHP\Frame\MVC\Template;
use icePHP\Frame\TableLog\TableLog;
use icePHP\Frame\Meta\Meta;

use function icePHP\Frame\Functions\dump;
use function icePHP\Frame\MVC\display;
use function icePHP\Frame\Router\url;
use function icePHP\Frame\Cache\clearCache;
use function icePHP\Frame\Debug\isDebug;
use icePHP\Lib\CodeCheck;

/**
 * 系统附加功能
 * @author 蓝冰大侠 && vYao
 *
 */
class SystemIndexController extends Controller
{  //菜单配置
    private $config = [
        ['name' => '服务器环境', 'action' => 'phpinfo', 'isConfirm' => '0'],
        ['name' => '框架文档', 'action' => 'document', 'isConfirm' => '0'],
        ['name' => '数据字典', 'action' => 'dict', 'isConfirm' => '0'],
        ['name' => '创建/更新表对象', 'action' => 'tableView', 'isConfirm' => '0'],
        ['name' => '代码检查', 'action' => 'check', 'isConfirm' => '0'],
        //['name' => '服务器状态', 'action' => 'monitor', 'isConfirm' => '0'],
        ['name' => '数据库对比', 'action' => 'compare', 'isConfirm' => '0'],
        ['name' => 'CRUD', 'Controller' => 'crud', 'action' => 'create', 'isConfirm' => '0'],
        ['name' => '数据修复', 'action' => 'repair', 'isConfirm' => '1'],
        ['name' => '清除缓存', 'action' => 'clearCache', 'isConfirm' => '1'],
        ['name' => '重新编译视图', 'action' => 'recompile', 'isConfirm' => '1'],
      /*  ['name' => '更新菜单/权限', 'action' => 'updateMenu', 'isConfirm' => '1'],
        ['name' => 'opcache', 'action' => 'opcache', 'isConfirm' => '0'],*/
        
        //['name' => '清除表数据', 'action' => 'deleteTableData', 'isConfirm' => '1']
    ];

    /**
     * 本类所有方法只在Debug模式下有效
     *
     * @param $req Request
     * @return boolean
     */
    public function construct(Request $req): bool
    {
        set_time_limit(0);

        if (!isDebug()) {
            return false;
        }

        Meta::setRoot(DIR_ROOT);

        return isDebug() and parent::construct($req);
    }


    /**
     * 页面入口
     */
    public function view()
    {
        display('', ['config' => $this->config, 'controller' => $this]);
    }

    /**
     * 显示服务器环境
     */
    public function phpinfo()
    {
        phpinfo();
    }

    /**
     * 框架文档
     */
    public function document()
    {
        $view = $this->getController() . '/' . $this->getAction();
        //当前模块
        $m = $this->getModule();
        include(DIR_ROOT . 'program/Module/' . $m . '/view/' . $view . '.tpl');
    }

    /**
     * 显示数据字典
     */
    public function dict()
    {
        set_time_limit(0);

        header('content-type:text/html;charset=utf-8');

        $databases = Meta::dictDatabases();
        $tables = Meta::dictTables();

        display(null, array(
            'tables' => $tables,
            'dbs' => $databases
        ));
    }

    /**
     * 自动创建表对象,记录对象 页面
     */
    public function tableView()
    {
        // 取所有表
        $tables = Meta::dictTables();

        display('table', array(
            'tables' => $tables,
            'controller' => $this
        ));
    }

    /**
     * 自动创建一条表对象
     */
    public function table()
    {
        $table = $this->getMust('table');

        Meta::tableOne($table);
    }

    /**
     * 自动一条记录对象
     */
    public function record()
    {
        $table = $this->getMust('table');

        Meta::recordOne($table);
    }


    /**
     * 代码检查
     */
    public function check()
    {
        header('content-type:text/html;charset=utf-8');
        new CodeCheck();
    }

    /**
     * 清除全部MemCache缓存
     */
    public function clearCache()
    {
        clearCache();

        echo '<div style="margin:20px;font-size:18px">Clear File Type  Ok<br/></div>';
        echo '<div style="margin:20px;font-size:18px">Clear Memcache Type Ok<br/></div>';
        echo '<div style="margin:20px;font-size:18px">Clear Must Type Ok<br/></div>';
    }

    /**
     * 清除表数据
     */
    public function deleteTableData()
    {
        exit;
        Meta::deleteTableData();
    }

    /**
     * 重新编译所有视图
     */
    public function recompile()
    {
        Template::recompile();
    }

    /**
     * 数据库升级后的对比
     */
    public function compare()
    {
        //新库(源)
        $configSrc = [
            'host' => '47.94.149.206',
            'user' => 'root',
            'password' => 'i*c#e%T(e^S!t)_',
            'database' => 'shop',
            'port' => '3306'
        ];

        //旧库(目标库)
        $configTrg = [
            'host' => 'rm-2zeo8ft9c3w8927ykgo.mysql.rds.aliyuncs.com',  //主机
            'user' => 'zhongjiaoyipin', //数据库用户
            'password' => 'Zjyp#&%2#0!1^9&S1', //密码
            'database' => 'shop', //数据库名称
            'port' => '3306' //端口
        ];

        Meta::compare($configSrc, $configTrg);
    }

    /**
     * 数据库修复
     */
    public function repair()
    {
        Meta::repair();
    }

    /**
     * 数据库修复
     */
    public function innodb()
    {
        Meta::innodb();
    }

    /**
     * 创建日志表(Request,Operation,Debug)
     * @throws \Exception
     */
    public function createLogTable()
    {
        TableLog::createTable();
    }
    
    /**
     * 更新菜单权限
     * @throws \Exception
     */
    public function updateMenu()
    {
        //DELETE FROM adminFunc WHERE updated < "2018-11-14"

        //判断是否登录 未登录不让操作
        if(!MLogin::isLoginAdmin()){
            exit('必须登录才可操作');
        }
       
        header('content-type:text/html;charset=utf-8');
        
        //$menu = config('authority', 'mainMenu');
    
        $path = DIR_ROOT . 'program/Module/admin/Controller/';
    
        $file = scandir($path);
    
        $controllerName = [];
        foreach($file as $name){
            if($name == '.' || $name == '..' || $name == '.svn'){
                continue;
            }
            
            $fileName = explode('.', $name)[0];
            
            if(in_array($fileName, ['login', 'job', 'alipay', 'braceletPush', 'adminBase', 'queue', 'mattressCenter'])){
                continue;
            }
        
            $controllerName[] = $fileName;
        }

        $temp = [];
        
        $site = Request::instance()->domain();
        
        foreach($controllerName as $v){
            $url = $site . url('admin', $v, 'createFunc');
            if(!in_array($url, $temp)){
                $temp[] = $url;
            }
        }
        
        //删除不存在的控制器的权限数据
        $temp[] = $site . url('system', 'index', 'deleteNoExistController');
        
	    //更新分院权限菜单
	    $temp[] = $site . url('system', 'index', 'updateBranchFunc');
	    
        display(null, array(
            'temp' => $temp
        ));
    }
    
    /**
     * 删除不存在的控制器的权限数据
     * @throws \Exception
     */
    public function deleteNoExistController()
    {
        //判断是否登录 未登录不让操作
        if(!MLogin::isLoginAdmin()){
            exit('必须登录才可操作');
        }
        
        header('content-type:text/html;charset=utf-8');
        
        //$menu = config('authority', 'mainMenu');
        
        $path = DIR_ROOT . 'program/Module/admin/Controller/';
        
        $file = scandir($path);
    
        $controllerName = [];
        foreach($file as $name){
            if($name == '.' || $name == '..' || $name == '.svn'){
                continue;
            }
            
            $fileName = explode('.', $name)[0];
            
            if(in_array($fileName, ['login', 'job', 'alipay', 'braceletPush', 'adminBase', 'queue', 'mattressCenter'])){
                continue;
            }
            
            $controllerName[] = $fileName;
        }
      
        $noExistController = TAdminFunc::instance()->col('id', ['c not in' => $controllerName]);
 
        if(!$noExistController){
            echo '不需处理'; exit;
        }
    
        //删除对应权限
        TAdminFunc::instance()->delete(['id' => $noExistController]);
        TAdminGroupFunc::instance()->delete(['funcId' => $noExistController]);
    
        echo '成功';
        
    }

    
    /**
     * opcache管理
     * @throws \Exception
     */
    public function opcache()
    {
        //判断模块是否加载
        if (!extension_loaded('Zend OPcache')) {
            exit('OPcache 未开启~');
        }
    
        if (isset($_GET['invalidate'])) {
            opcache_invalidate($_GET['invalidate'], true);
            header('Location: ' .  $this->url('index', 'opcache', ['debug' => '']) .'#scripts');
        }
    
        if (isset($_GET['reset'])) {
            opcache_reset();
            header('Cache-Control: no-store, no-Cache, must-revalidate, max-age=0');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-Cache');
            header('Location: ' . $this->url('index', 'opcache', ['debug' => '']) .'#scripts');
        }

        display('', ['Controller' => $this]);
    }

    /**
     * 删除已经删除的表对应的table和record
     * @throws \Exception
     */
    public function deleteNoExistTable()
    {
        //判断是否登录 未登录不让操作
        if(!MLogin::isLoginAdmin()){
            exit('必须登录才可操作');
        }
        
        header('content-type:text/html;charset=utf-8');
        
        //$menu = config('authority', 'mainMenu');
        
        $recordPath = DIR_ROOT . 'program/record/';
        $tablePath = DIR_ROOT . 'program/table/';
        $recordBasePath = DIR_ROOT . 'program/record/base/';
        $tableBasePath = DIR_ROOT . 'program/table/base/';
        
        $recordFile = scandir($recordPath);
        $tableFile = scandir($tablePath);
        $recordBaseFile = scandir($recordBasePath);
        $tableBaseFile = scandir($tableBasePath);
    
        // 取所有表
        $childDir = 'base';
        $tables = Meta::getTables();
        $tables[] = $childDir;
        
        $noExist = [];
        foreach($recordFile as $name){
            if($name == '.' || $name == '..' || $name == '.svn'){
                continue;
            }
            
            $fileName = explode('.', $name)[0];
            
            if(in_array($fileName, $tables)){
                continue;
            }
    
            $noExist[] = $recordPath . $fileName . ".record.php";
        }
    
        foreach($tableFile as $name){
            if($name == '.' || $name == '..' || $name == '.svn'){
                continue;
            }
        
            $fileName = explode('.', $name)[0];
        
            if(in_array($fileName, $tables)){
                continue;
            }
    
            $noExist[] = $tablePath . $fileName. ".table.php";
        }
    
        foreach($recordBaseFile as $name){
            if($name == '.' || $name == '..' || $name == '.svn'){
                continue;
            }
	      
            $fileName = substr(explode('.', $name)[0], 0, -4);
	        
            if(in_array($fileName, $tables)){
                continue;
            }
	       
            $noExist[] = $recordBasePath . $fileName. "Base.record.php";
        }
    
        foreach($tableBaseFile as $name){
            if($name == '.' || $name == '..' || $name == '.svn'){
                continue;
            }
	
	        $fileName = substr(explode('.', $name)[0], 0, -4);
            
            if(in_array($fileName, $tables)){
                continue;
            }
        
            $noExist[] = $tableBasePath . $fileName. "Base.table.php";
        }
        
        dump($noExist);
        
        /*foreach($noExist as $v){
            unlink($v);
        }*/
        
    }

    /**
     * 服务器服务简单明细
     * @throws \Exception
     */
    public function monitor()
    {
        echo file_get_contents(Request::instance()->domain() . ":9988/monitor");
    }

    public function webAdmin()
    {
        display(null, []);
    }
}