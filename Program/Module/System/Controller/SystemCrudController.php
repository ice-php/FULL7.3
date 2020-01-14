<?php
declare(strict_types=1);

namespace Program\Module\System\Controller;

use function icePHP\Frame\Functions\formatter;
use icePHP\Frame\MVC\Controller;
use icePHP\Frame\Crud\Crud;
use icePHP\Frame\Core;
use icePHP\Frame\Meta\Meta;
use icePHP\Frame\MVC\Request;

use function icePHP\Frame\MVC\display;
use function icePHP\Frame\Debug\isDebug;
use function icePHP\Frame\Table\table;
use function icePHP\Frame\Router\url;

/**
 * Crud 系统功能
 * @author vYao
 */
class SystemCrudController extends Controller
{
    public function construct(Request $req): bool
    {
        set_time_limit(0);

        if (!isDebug()) {
            return false;
        }
        return parent::construct($req);
    }

    /**
     * 创建CRUD文件 页面
     * @throws \Exception
     */
    public function create()
    {
        set_time_limit(0);

        // 取所有表
        $tables = Meta::dictTables();

        display('', array(
            'tables' => $tables,
            'controller' => $this
        ));
    }

    /**
     * 设置CRUD页面 Ajax
     */
    public function setInfo()
    {
        $tableName = $this->getMust('tableName',"表名称必须填写");

        // 取所有模块
        $modules = Core::getModules();

        //取单个表结构
        $primaryKey = table($tableName)->getPrimaryKey();

        if (!$primaryKey) {
            $this->error("本表无主键，不可创建Crud, 请设置主键id");
        }

        $meta = table($tableName)->meta();


        $info = [
            'name' => $tableName,
            'desc' => $meta[$primaryKey]['description'],
            'meta' => $meta
        ];

        display(null, array(
            'info' => $info,
            'modules' => $modules,
            'controller' => $this
        ));
    }

    /**
     * 创建CRUD文件 Ajax
     */
    public function doCreate()
    {
        $tableName = $this->getMust('table');
        $module = $this->getMust('module');
        $multi = $this->getMust('multi');
        $rowOperations = $this->get('rowOperations','');
        $data = json_decode($_POST['data'], true);

        //清除 原有 Controller及View
        self::clear($tableName, $module);

        $table = new Crud(DIR_ROOT,$tableName);

        //多选和多选删除选择
        $isMulti = false;
        $isMultiDelete = false;
        if ($multi == 1) {
            $isMulti = true;
        } elseif ($multi == 2) {
            $isMulti = true;
            $isMultiDelete = '多选删除';
        }

        //额外操作
        $rowOperationsParam = [];
        if (!empty($rowOperations)) {
            $rowOperationsArr = explode(',', $rowOperations);
            foreach ($rowOperationsArr as $k => $v) {
                $temp = explode('|', $v);
                if (count($temp) != 2 || empty($temp[0]) || empty($temp[1])) {
                    continue;
                }
                $rowOperationsParam[$temp[0]] = $temp[1];
            }
        }

        //指定外键关联
        if (!empty($data['foreign'])) {
            foreach ($data['foreign'] as $k => $v) {
                $temp = explode(',', $v['value']);
                $table->field($v['name'])->foreign($temp[0], $temp[1], $temp[2]);
            }
        }

        //指定搜索项
        $table->orderSearch = $data['search'];

        //自设置名称
        if (!empty($data['description'])) {
            foreach ($data['description'] as $k => $v) {
                if (in_array($v['name'], $data['lists'])) {
                    //先删除
                    foreach ($data['lists'] as $key => $value) {
                        if ($v['name'] == $value) {
                            unset($data['lists'][$key]);
                        }
                    }
                    //在赋值
                    $data['lists'][$v['name']] = $v['value'];
                }
                if (in_array($v['name'], $data['add'])) {
                    //先删除
                    foreach ($data['add'] as $key => $value) {
                        if ($v['name'] == $value) {
                            unset($data['add'][$key]);
                        }
                    }
                    //在赋值
                    $data['add'][$v['name']] = $v['value'];
                }
            }
        }


        //指定列表显示项
        $table->orderList = $data['lists'];

        //指定新增和修改时的显示项
        $table->orderAdd = $table->orderEdit = $data['add'];

        $table->rowOperations($rowOperationsParam)
            ->multi($isMulti)
            ->multiDelete($isMultiDelete)
            ->create($module);

        $url = url($module, $tableName, 'index');

        echo("<p style='padding: 10px;'>处理成功！ 访问地址 <a href='{$url}' target='_blank'> 点击访问 </a> </p>");
    }

    /**
     * 删除原有CRUD文件
     * @param $tableName string 表名
     * @param $module string 模块
     */
    private function clear(string $tableName, $module = 'admin')
    {
        //模块名标准化
        $module=formatter($module);

        $modulePath=DIR_ROOT.'Program/Module/'.$module.'/';

        //删除视图文件
        $path = $modulePath.'View/' . $tableName . '/';
        foreach (['index', 'add', 'edit', 'detail'] as $name) {
            if (is_file($f = $path . $name . '.tpl')) unlink($f);
        }

        //删除控制器文件
        $path = $modulePath.'Controller/';
        if (is_file($f = $path . $module.formatter($tableName) . 'Controller.php')) unlink($f);
    }
    
    public function testCrud()
    {
        $tableName = 'testCrud';

        self::clear($tableName);

        $table = new Crud(DIR_ROOT,$tableName);
        $table->field('id');
        $table->field('userId')->foreign('user', 'id', 'name');
        $table->field('branchId')->foreign('atBranch', 'id', 'name');
        $table->field('contact1');
        $table->field('telephone1');
        $table->field('relation1');
        $table->field('contact2');
        $table->field('telephone2')->defaultValue('telephone2运营中');
        $table->field('relation2')->defaultValue('relation2运营中');
        $table->field('contactNo');
        $table->field('contactNo');
        $table->field('contactType');
        $table->field('contactBegin');
        $table->field('contactEnd');
        $table->field('contactFile')->isUploadImage();
        $table->field('content');
        $table->field('liveType');
        $table->field('abilityId')->foreign('typeAbility', 'id', 'name');
        $table->field('nurseId')->foreign('typeNurse', 'id', 'name');
        $table->field('bedId');
        $table->field('outStatus');
        $table->field('outDate');
        $table->field('outType');
        $table->field('outReason');
        $table->field('outAudit');
        $table->field('outAuditReason');
        $table->field('moneyBase');
        $table->field('moneyConsume');
        $table->field('moneyMedical');
        $table->field('moneyDeposit');
        $table->field('feeBed');
        $table->field('feeNurse');
        $table->field('feeEat');
        $table->field('feeManage');

        //指定搜索项
        $table->orderSearch = ['branchId', 'contact1', 'telephone1', 'relation1', 'id'];

        //指定列表显示项
        $table->orderList = ['branchId', 'contact1' => 'ddddd', 'telephone1', 'relation1', 'contact2', 'telephone2', 'relation2', 'created'];

        //指定新增和修改时的显示项
        $table->orderAdd = $table->orderEdit = ['contact1', 'telephone1', 'relation1', 'contact2', 'telephone2', 'relation2'];

        $table->rowOperations(['冻结' => 'lock', '解冻' => 'unlock', '删除' => 'delete'])
            ->multi(true)
            ->multiDelete(false)
            ->create('admin');
    }
}