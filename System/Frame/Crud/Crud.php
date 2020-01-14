<?php
declare(strict_types=1);

namespace icePHP\Frame\Crud;

use function icePHP\Frame\Functions\formatter;
use icePHP\Frame\Mysql\Mysql;
use icePHP\Frame\MVC\Template;

use function icePHP\Frame\Write\makeDir;
use function icePHP\Frame\Table\table;
use function icePHP\Frame\Router\url;
use function icePHP\Frame\Write\write;

/**
 * CRUD脚手架 类
 */
class Crud
{
    //基础表名
    private $name;

    //标准化后的表名(小写开头)
    private $formatName;

    /**
     * 表的元信息
     * @var array
     */
    private $meta;

    //表的主键字段名
    private $primaryKey;

    //系统根目录
    private $root;

    /**
     * 构造方法
     * @param $root string 系统根目录
     * @param $name string 基础表名
     */
    public function __construct(string $root, string $name)
    {
        $this->root = $root;
        $this->name = $name;
        $this->formatName = formatter($name, false);
        $this->meta = table($name)->meta();
        $this->primaryKey = table($name)->getPrimaryKey();
    }

    /**
     * @var $title array 页面标题
     * 如果是数组,要有 list/add/edit/print/export/detail 六项
     */
    private $title;

    /**
     * 设置页面标题
     * @param $title string|array 页面标题,如果是数组,要有 list/add/edit/print/export 五项
     * @return Crud
     */
    public function title($title): Crud
    {
        if (is_string($title)) {
            //从字符串拆分出六个页面的标题
            $this->title = [
                'list' => $title . '-列表',
                'add' => $title . '-添加',
                'edit' => $title . '-编辑',
                'print' => $title . '-打印',
                'export' => $title . '-导出',
                'detail' => $title . '-详情',
            ];
        } elseif (is_array($title)) {
            //已经是数组了
            $this->title = $title;
        } else {
            trigger_error('CRUD 标题设置必须是字符串或数组');
        }

        return $this;
    }

    /**
     * @var $submit array
     * 页面提交按钮的标题
     */
    private $submit;

    /**
     * 设置页面提交按钮的标题
     * 例:['添加'=>'保存','编辑'=>'提交']
     * @param array $submit
     * @return Crud
     */
    public function submit(array $submit): Crud
    {
        $this->submit = $submit;
        return $this;
    }

    //是否显示行号,默认显示
    private $rowNo = false;

    /**
     * 设置是否显示行号,默认不显示
     * @param bool $showNo
     * @return $this
     */
    public function rowNo(bool $showNo = true): Crud
    {
        $this->rowNo = $showNo;
        return $this;
    }

    //列表页全部功能
    private $operations = [
        '列表' => '列表',
        '搜索' => '搜索',
        '详情' => '详情',
        '添加' => '添加',
        '编辑' => '编辑',
        '删除' => '删除',
//        '打印' => '打印',
//        '导出' => '导出'
    ];

    /**
     * 设置列表页上的全部功能,默认全部
     * @param mixed $ops 功能列表
     * @return $this
     */
    public function operations($ops = true): Crud
    {
        if ($ops === false) {
            //没有操作
            $this->operations = [];
        } elseif ($ops !== true) {
            //指定操作
            $this->operations = $ops;
        } else {
            $this->operations = ['列表' => '列表', '搜索' => '搜索', '详情' => '详情', '添加' => '添加', '编辑' => '编辑', '删除' => '删除', '打印' => '打印', '导出' => '导出'];
        }
        return $this;
    }

    //是否有多选操作
    private $multi = true;

    /**
     * 设置列表是否有多选操作
     * @param bool $hasMulti 是/否
     * @return $this
     */
    public function multi(bool $hasMulti = true): Crud
    {
        $this->multi = $hasMulti;
        return $this;
    }

    //是否有多选删除
    private $multiDelete = '多选删除';

    /**
     * 设置列表是否有多选删除操作
     * @param string $hasMultiDelete 按钮标题
     * @return $this
     */
    public function multiDelete($hasMultiDelete = '多选删除'): Crud
    {
        $this->multiDelete = $hasMultiDelete;
        return $this;
    }

    //额外的多选操作
    private $multiOperations = [];

    /**
     * 指定额外的多选操作
     * @param array $operations 操作名值对
     * @return $this
     */
    public function multiOperations(array $operations): Crud
    {
        $this->multiOperations = $operations;
        return $this;
    }

    //额外的行操作
    private $rowOperations = [];

    /**
     * 指定额外的行操作
     * @param array $operations 操作名值对
     * @return $this
     */
    public function rowOperations(array $operations): Crud
    {
        $this->rowOperations = $operations;
        return $this;
    }

    //额外的表操作
    private $tableOperations = [];

    /**
     * 指定额外的表操作
     * @param array $operations 操作名值对
     * @return $this
     */
    public function tableOperations(array $operations): Crud
    {
        $this->tableOperations = $operations;
        return $this;
    }

    /**
     * @var array 所有字段的配置
     */
    private $fields = [];

    /**
     * 生成一个字段的配置对象
     * @param $name string 字段名称
     * @return CrudField
     * @throws \Exception
     */
    public function field(string $name): CrudField
    {
        $field = CrudField::_instance($name, $this->meta[$name], $this);
        $this->fields[$name] = $field;
        return $field;
    }

    /**
     * 设置忽略字段
     * @param $fieldName string|array 字段名
     * @return $this
     */
    public function ignore($fieldName): Crud
    {
        //如果是数组,逐个处理
        if (is_array($fieldName)) {
            foreach ($fieldName as $name) {
                $this->fields[$name] = CrudField::_instance($name, $this->meta[$name], $this)->ignore();
            }
            return $this;
        }

        //字符串,只是一个字段
        if (is_string(($fieldName))) {
            $this->fields[$fieldName] = CrudField::_instance($fieldName, $this->meta[$fieldName], $this)->ignore();
            return $this;
        }

        trigger_error('CURD 配置中的 IGNORE参数错误', E_USER_ERROR);
        exit;
    }

    /**
     * 全部配置完成
     */
    private function over(): void
    {
        //如果未指定标题,生成默认标题
        if (!$this->title) {
            $description = $this->meta[$this->primaryKey]['description'];
            if ($description) {
                $this->title($description);
            } else {
                $this->title($this->name);
            }
        }

        //如果指定了多选删除,则必须有多选操作
        if ($this->multiDelete) {
            $this->multi = true;
        }

        //如果数据表中的字段,未指定配置,使用默认配置构造数据字段
        foreach ($this->meta as $fieldKey => $field) {
            if (!isset($this->fields[$fieldKey])) {
                $this->fields[$fieldKey] = CrudField::_instance($fieldKey, $field, $this);
            }

            /**
             * 逐个字段完善
             * @var $crudField CrudField
             */
            $crudField = $this->fields[$fieldKey];
            $crudField->_over();
        }
    }

    //列表时,字段的显示顺序
    public $orderList;

    /**
     * 按显示顺序获取列表页面的字段列表
     * @return CrudField[]
     */
    private function getOrderIndex(): array
    {
        //根据设置,获取字段列表
        if ($this->orderList) {
            return $this->orderFormat($this->orderList);
        }

        //如果未指定顺序,使用默认顺序
        return array_filter($this->fields, function (CrudField $field) {
            //二进制,文本,自动日期字段,默认不在列表中显示
            $notIn = ($field instanceof CrudBinary or $field instanceof CrudText or $field->_isAutoField());
            return !$notIn;
        });
    }

    //导出时,字段的显示顺序
    public $orderExport;

    /**
     * 按显示顺序获取导出页面的字段列表
     * @return CrudField[]
     */
    private function getOrderExport(): array
    {
        //根据设置,获取字段列表
        if ($this->orderExport) {
            return $this->orderFormat($this->orderExport);
        }

        //如果未指定顺序,使用默认顺序
        return array_filter($this->fields, function (CrudField $field) {
            //二进制,文本,默认不在导出中显示
            $notIn = ($field instanceof CrudBinary or $field instanceof CrudText);
            return !$notIn;
        });
    }

    //详情时,字段的显示顺序
    public $orderDetail;

    /**
     * 按显示顺序获取详情页面的字段列表
     * @return CrudField[]
     */
    private function getOrderDetail(): array
    {
        //根据设置,获取字段列表
        if ($this->orderDetail) {
            return $this->orderFormat($this->orderDetail);
        }

        //如果未指定顺序,使用默认顺序
        return array_filter($this->fields, function (CrudField $field) {
            //自动时间字段,默认不在详情页面显示
            $notIn = $field->_isAutoField();
            return !$notIn;
        });
    }

    //添加时,字段的显示顺序
    public $orderAdd;

    /**
     * 按显示顺序获取添加页面的字段列表
     * @return CrudField[]
     */
    private function getOrderAdd(): array
    {
        //根据设置,获取字段列表
        if ($this->orderAdd) {
            return $this->orderFormat($this->orderAdd);
        }

        //如果未指定顺序,使用默认顺序
        $ret = array_filter($this->fields, function (CrudField $field) {
            //二进制,自动时间,自增长 不在添加页面显示
            $notIn = ($field instanceof CrudBinary or $field->_isAutoField() or $field->_autoIncrement);
            return !$notIn;
        });

        return $ret;
    }

    //修改时,字段的显示顺序
    public $orderEdit;

    /**
     * 按显示顺序获取编辑页面的字段列表
     * @return CrudField[]
     */
    private function getOrderEdit(): array
    {
        //根据设置,获取字段列表
        if ($this->orderEdit) {
            return $this->orderFormat($this->orderEdit);
        }

        //如果未指定顺序,使用默认顺序
        return array_filter($this->fields, function (CrudField $field) {
            //二进制,自动时间,自增长 不在编辑页面显示
            $notIn = ($field instanceof CrudBinary or $field->_isAutoField() or $field->_autoIncrement);
            return !$notIn;
        });
    }

    //打印时,字段的显示顺序
    public $orderPrint;

    /**
     * 按显示顺序获取打印页面的字段列表
     * @return CrudField[]
     */
    private function getOrderPrint(): array
    {
        //根据设置,获取字段列表
        if ($this->orderPrint) {
            return $this->orderFormat($this->orderPrint);
        }

        //如果未指定顺序,使用默认顺序
        return array_filter($this->fields, function (CrudField $field) {
            //二进制,自动时间,自增长 不在打印页面显示
            $notIn = ($field instanceof CrudBinary or $field->_isAutoField() or $field->_autoIncrement);
            return !$notIn;
        });
    }

    //搜索条时,字段的显示顺序
    public $orderSearch;

    /**
     * 按显示顺序获取搜索条的字段列表
     * @return CrudField[]
     */
    private function getOrderSearch(): array
    {
        //根据设置,获取字段列表
        if ($this->orderSearch) {
            return $this->orderFormat($this->orderSearch, true);
        } else {
            return [];
        }

        /*//如果未指定顺序,使用默认顺序
        return array_filter($this->fields, function (CrudField $field) {
            //二进制,自动时间,大文本 不在搜索条显示
            $notIn = ($field instanceof CrudBinary or $field->_isAutoField() or $field instanceof CrudText);
            return !$notIn;
        });*/
    }

    /**
     * 对顺序设置参数进行标准化
     * @param array $setting 顺序设置
     * @param $search bool 是否是搜索条设置
     * @return CrudField[] 调整顺序后的字段列表
     */
    private function orderFormat(array $setting, bool $search = false): array
    {
        $ret = [];

        //逐个检查
        foreach ($setting as $key => $field) {
            if (is_integer($key)) {
                //如果未指定键,值表示字段名,则使用字段原始配置
                $ret[] = $this->fields[$field];
            } else {
                /**
                 * 如果指定键(字段名)=>值(显示名称),则修改字段的标题配置
                 * @var $realField CrudField
                 */
                $realField = $this->fields[$key];

                if (!$search) {
                    //非搜索条,设置的是显示标题
                    $realField->description($field);
                } else {
                    //搜索条设置的是搜索配置
                    $realField->_searchType = $field;
                }
                $ret[] = $realField;
            }
        }
        return $ret;
    }

    //创建时的模块名称
    private $module;

    /**
     * 创建全部代码
     * @param $module string 模块名称,生成的代码将放在此模块中
     */
    public function create(string $module = ''): void
    {
        $this->module = $module;

        //完善配置信息
        self::over();

        //生成控制器代码
        self::controller($module);

        //补充目录
        $pathV = $this->root . 'Program/' . ($module ? '/Module/' . formatter($module) : '') . '/View/' . $this->name;
        is_dir($pathV) ?: makeDir($pathV);

        //生成页面视图代码
        self::viewIndex($pathV);
        self::viewDetail($pathV);
        self::viewAdd($pathV);
        self::viewEdit($pathV);
        self::viewPrint($pathV);
    }

    /**
     * 生成控制器中的外键字段取值代码
     * @param CrudField[] $fields 列表/添加/编辑/...等页面中的字段列表
     * @return string 外键代码
     */
    private function controllerForeign(array $fields): string
    {
        $content = [];
        foreach ($fields as $field) {
            /**
             * @var $field CrudField
             */
            if (!$field->_foreign) {
                continue;
            }
            $tpl = 'foreignField';
            $content[] = self::_tpl($tpl, array_merge($field->_foreign, ['fieldName' => $field->_name]));
        }

        return implode('', $content);
    }

    /**
     * 生成控制器中的字典字段代码
     * @param CrudField[] $fields
     * @return string
     */
    private function controllerDict(array $fields): string
    {
        $content = [];
        foreach ($fields as $field) {
            /**
             * @var $field CrudField
             */
            if (!$field->_dict) {
                continue;
            }
            $content[] = self::_tpl('dictField', array_merge($field->_dict, ['fieldName' => $field->_name]));
        }

        return implode('', $content);
    }

    /**
     * 控制器详情方法中处理外键的代码
     * @return array[MAP代码,替换字段数组代码]
     */
    private function controllerDetailForeign(): array
    {
        $content = '';
        $maps = '$foreignMap=[';

        //每一个字段
        foreach ($this->getOrderDetail() as $field) {
            /**
             * @var $field CrudField
             */
            if (!$field->_foreign) {
                continue;
            }
            $content .= self::_tpl('controllerDetailForeign', array_merge($field->_foreign, ['fieldName' => $field->_name]));
            $maps .= "'{$field->_name}'=>'{$field->_name}_{$field->_foreign['value']}',";
        }
        return [$content, $maps . '];'];
    }

    /**
     * 检查控制器类或基类是否存在
     * @param string $module
     * @param string $classPrefix
     * @return array [命名空间,文件名]
     */
    private function controllerFile(string $module, string $classPrefix): array
    {
        //控制器类名
        $className = $classPrefix . 'Controller';

        //控制器的命名空间  Program\Controller 或 Program\Module\XXX\Controller
        $namespace = 'Program\\' . ($module ? 'Module\\' . ucfirst($module) . '\\' : '') . 'Controller';

        //控制器所在目录，依据PSR4 标准
        $pathC = $this->root . str_replace('\\', '/', $namespace);

        //控制器文件名
        $file = $pathC . '/' . $className . '.php';

        if (is_file($file)) {
            return [];
        }

        //创建目录
        is_dir($pathC) ?: makeDir($pathC);
        return [$namespace, $file];
    }

    /**
     * 生成指定模块的控制器基类
     * @param string $module 模块名
     */
    private function controllerBase(string $module): void
    {
        $upper = formatter($module) . 'Base';
        $ret = $this->controllerFile($module, $upper);
        if (!$ret) {
            return;
        }
        [$namespace, $file] = $ret;

        write($file, self::_tpl('controllerBase', [
            'module' => $module,
            'upper' => $upper,
            'namespace' => $namespace
        ]));
    }

    /**
     * 生成控制器类
     * @param string $module 模块名称（小写开头）
     */
    private function controller(string $module): void
    {
        //先生成控制器基类
        $this->controllerBase($module);

        //大写开头的表名
        $upper = formatter($this->name);

        //计算命名空间及文件名
        $ret = $this->controllerFile($module, formatter($this->module) . $upper);
        if (!$ret) {
            echo "控制器[{$this->name}]已经存在,跳过.<br/>";
            return;
        }
        [$namespace, $file] = $ret;

        //生成控制器类文件
        write($file, self::_tpl('controller', [
            'upper' => $upper, //控制器类名前缀 AdminUser
            'name' => $this->name, //控制器名称 user
            'module' => $module, //模块名称 Admin
            'namespace' => $namespace, //命名空间 Program\Module\Admin\Controller

            //列表方法
            'index' => self::_tpl('controllerIndex', [
                'upper' => $upper,

                //构造外键方法
                'foreign' => self::controllerForeign($this->getOrderIndex()),

                //构造字典方法
                'dict' => self::controllerDict($this->getOrderIndex()),

                //搜索条件的构造
                'searchFields' => self::controllerSearch(),
            ]),

            //添加和修改时的获取参数值的方法
            'input' => self::_tpl('controllerInput', [
                'upper' => $upper,
                'title' => $this->operations['添加'],
                'fields' => self::controllerInput(),
                'primaryKey' => $this->primaryKey
            ]),

            //构造添加及执行添加方法(add,addSubmit)
            'add' => self::_tpl('controllerAdd', [
                'name' => $this->name,
                'upper' => $upper,
                'foreign' => self::controllerForeign($this->getOrderAdd()),
                'dict' => self::controllerDict($this->getOrderAdd()),
                'title' => $this->operations['添加'],
            ]),

            //构造编辑及执行编辑方法(edit,editSubmit)
            'edit' => self::_tpl('controllerEdit', [
                'name' => $this->name,
                'upper' => $upper,
                'foreign' => self::controllerForeign($this->getOrderEdit()),
                'dict' => self::controllerDict($this->getOrderEdit()),
                'title' => $this->operations['编辑'],
                'primaryKey' => $this->primaryKey
            ]),

            //构造执行删除的方法(remove)
            'delete' => self::_tpl('controllerDelete', [
                'name' => $this->name,
                'upper' => $upper,
                'title' => $this->operations['删除'],
                'primaryKey' => $this->primaryKey
            ]),

            //构造执行多选删除的方法
            'multiDelete' => self::_tpl('controllerMultiDelete', [
                'name' => $this->name,
                'upper' => $upper,
                'title' => $this->multiDelete,
                'primaryKey' => $this->primaryKey
            ]),

            //构造表操作的方法
            'tableOperations' => $this->controllerTableOperations(),

            //构造行操作的方法
            'rowOperations' => $this->controllerRowOperations(),

            //构造多选操作的方法
            'multiOperations' => $this->controllerMultiOperations(),

            //构造详情方法
            'detail' => self::_tpl('controllerDetail', [
                'upper' => $upper,
                'foreignMapCode' => $this->controllerDetailForeign()[0],
                'foreignMapArray' => $this->controllerDetailForeign()[1],
                'title' => $this->operations['详情'],
                'primaryKey' => $this->primaryKey
            ]),

            //构造导出方法
            'export' => self::_tpl('controllerExport', [
                'upper' => $upper,
                'foreign' => self::controllerForeign($this->getOrderExport()),
                'description' => $this->title['export'],
                'fields' => $this->getExport()
            ]),

            //构造打印方法
            'print' => self::_tpl('controllerPrint', [
                'upper' => $upper,
                'foreign' => self::controllerForeign($this->getOrderPrint()),
                'description' => $this->title['print'],
                'fields' => $this->getPrint()
            ])
        ]));

        echo "生成控制器类文件:" . $file . "<br/>";
    }

    /**
     * 生成控制器中行操作的代码
     * @return string
     */
    private function controllerRowOperations(): string
    {
        $content = [];
        foreach ($this->rowOperations as $title => $action) {
            $content [] = self::_tpl('controllerRowOperation', [
                'title' => $title,
                'action' => $action
            ]);
        }
        return implode("\r\n", $content);
    }

    /**
     * 生成控制器中表操作的代码
     * @return string
     */
    private function controllerTableOperations(): string
    {
        $content = [];
        foreach ($this->tableOperations as $title => $action) {
            $content [] = self::_tpl('controllerTableOperation', [
                'title' => $title,
                'action' => $action
            ]);
        }
        return implode("\r\n", $content);
    }

    /**
     * 生成控制器中多选操作的代码
     * @return string
     */
    private function controllerMultiOperations(): string
    {
        $content = [];
        foreach ($this->multiOperations as $title => $action) {
            $content [] = self::_tpl('controllerMultiOperation', [
                'title' => $title,
                'action' => $action
            ]);
        }
        return implode("\r\n", $content);
    }

    /**
     * 取视图文件原始内容,仅限system/dao/meta/*
     *
     * @param  $name string 文件名
     * @return string
     */
    private static function getTpl($name): string
    {
        // 构造所在目录(当前模块,当前控制器,当前动作
        $path = __DIR__ . '/tpl/';
        $file = $path . $name . '.tpl';
        $content = file_get_contents($file);
        if (false === $content) {
            trigger_error('无法读取模板文件:' . $file, E_USER_ERROR);
        }
        return $content;
    }

    /**
     * 为友类所使用,开发人员不要用
     * CRUD专用获取模板并替换变量
     * @param $name string 模板名称
     * @param array $params 参数替换表
     * @return string 替换后的模板内容
     */
    public static function _tpl(string $name, array $params = []): string
    {
        return str_replace("\r\n\r\n\r\n", "\r\n\r\n", Template::replace(self::getTpl($name), $params));
    }

    /**
     * 生成每个页面的面包屑代码
     * @param array $crumbs 面包屑配置
     * @param bool $isList 是否是列表页
     * @return string 视图代码
     */
    private function viewCrumbs(array $crumbs, bool $isList = false): string
    {
        //面包屑头
        $content = self::getTpl('crumbsHead');

        //每一级面包屑
        $i = 0;
        foreach ($crumbs as $name => $url) {
            $content .= '<li>';

            //最开始有一个图标
            if (0 == $i) {
                $content .= self::getTpl('crumbsIcon');
            }

            if (!is_integer($name)) {
                //非最后一层,有标题,有链接
                $content .= self::_tpl('crumbsHref', ['url' => $url, 'name' => $name]);
            } else {
                //最后一层,只有标题,没有链接
                $content .= $url;
            }

            $content .= '</li>';

            $i++;
        }

        //如果是列表页
        if ($isList) {
            //额外 表操作按钮
            foreach ($this->tableOperations as $name => $action) {
                $content .= self::_tpl('crumbsButton', ['name' => $name, 'action' => $action]);
            }

            //添加操作的按钮
            if (isset($this->operations['添加'])) {
                $content .= self::_tpl('crumbsButton', ['name' => $this->operations['添加'], 'action' => 'add']);
            }
        }

        //面包屑的脚
        $content .= self::getTpl('crumbsFoot');

        return $content;
    }

    /**
     * 生成列表控制器的搜索条件部分代码
     * @return string 视图代码
     */
    private function controllerSearch(): string
    {
        //取搜索配置
        $config = $this->getSearch();

        //没有搜索字段
        if (!$config) {
            return '';
        }

        $content = '';

        //搜索字段
        foreach ($config as $field) {
            $content .= self::_tpl($field['conditionTpl'], $field['params']);
        }

        return $content;
    }

    /**
     * 生成列表页的搜索条代码
     * @return string 视图代码
     */
    private function viewSearch(): string
    {
        //取搜索配置
        $config = $this->getSearch();

        //没有搜索字段
        if (!$config) {
            return '';
        }

        //搜索头
        $content = self::getTpl('searchHead');

        //搜索字段
        foreach ($config as $field) {
            $tpl = $field['tpl'];
            $params = $field['params'];
            $content .= self::_tpl($tpl, $params);
        }

        //搜索按钮
        $content .= self::_tpl('searchButton', ['title' => $this->operations['搜索'], 'value' => 'search']);

        //导出按钮
        if (isset($this->operations['导出'])) {
            $content .= self::_tpl('searchButton', ['title' => $this->operations['导出'], 'value' => 'export']);
        }

        //打印按钮
        if (isset($this->operations['打印'])) {
            $content .= self::_tpl('searchButton', ['title' => $this->operations['打印'], 'value' => 'print']);
        }

        //搜索的脚
        $content .= self::getTpl('searchFoot');
        return $content;
    }

    /**
     * 生成列表页视图文件
     * @param $path string 文件目录
     */
    private function viewIndex(string $path): void
    {
        $file = $path . '/index.tpl';
        if (is_file($file)) {
            echo '列表视图文件已经存在,不进行覆盖:' . $file . "<br/>";
            return;
        }

        //面包屑
        $crumbs = self::viewCrumbs([$this->title['list']], true);

        //消息容器
        $msgContainer = self::getTpl('msgContainer');

        //搜索条
        $search = self::viewSearch();

        //表头及行中的全选Check
        if ($this->multi) {
            $multi = self::getTpl('indexMulti');
            $multiOne = self::_tpl('indexMultiOne', ['primaryKey' => $this->primaryKey]);

        } else {
            $multi = $multiOne = '';
        }

        //表头及行中的 行号
        if ($this->rowNo) {
            $rowNo = self::getTpl('indexRowNo');
            $rowNoOne = self::getTpl('indexRowNoOne');
        } else {
            $rowNo = $rowNoOne = '';
        }

        //表头及行中的字段
        $header = '';
        $row = '';
        foreach ($this->getOrderIndex() as $field) {

            //字段名要传递给模块
            $nameArray = ['name' => $field->_name];

            /**
             * @var $field CrudField
             */
            $header .= self::_tpl('indexListHeader', ['title' => $field->_description]);
            if ($field->_isUploadImage) {
                //如果是图片
                $row .= self::_tpl('indexListCellImage', $nameArray);
            } elseif ($field->_dict) {
                //如果是字典字段
                $row .= self::_tpl('indexListCellDict', array_merge($field->_dict, $nameArray));
            } elseif ($field->_foreign) {
                //如果是外键字段
                $row .= self::_tpl('indexListCellForeign', array_merge($field->_foreign, $nameArray));
            } else {
                //普通字符串
                $row .= self::_tpl('indexListCell', $nameArray);
            }
        }

        //行操作按钮
        $rowButton = '';

        //如果有额外行操作
        if ($this->rowOperations) {
            foreach ($this->rowOperations as $name => $action) {
                $rowButton .= self::_tpl('indexRowButton', ['title' => $name, 'action' => $action, 'class' => 'btn-success', 'primaryKey' => $this->primaryKey]);
            }
        }

        //如果有行详情操作
        if (isset($this->operations['详情'])) {
            $rowButton .= self::_tpl('indexRowButton', ['title' => $this->operations['详情'], 'action' => 'detail', 'class' => 'btn-info', 'primaryKey' => $this->primaryKey]);
        }

        //如果有行编辑操作
        if (isset($this->operations['编辑'])) {
            $rowButton .= self::_tpl('indexRowButton', ['title' => $this->operations['编辑'], 'action' => 'edit', 'class' => 'btn-primary', 'primaryKey' => $this->primaryKey]);
        }

        //如果有行删除操作
        if (isset($this->operations['删除'])) {
            $rowButton .= self::_tpl('indexRowButton', ['title' => $this->operations['删除'], 'action' => 'remove', 'class' => 'confirm btn-warning', 'primaryKey' => $this->primaryKey]);
        }

        //多选删除按钮
        if ($this->multiDelete) {
            $multiRemove = self::_tpl('multiRemove', ['title' => $this->multiDelete]);
        } else {
            $multiRemove = '';
        }

        //额外的多选操作按钮
        $multiButton = '';
        foreach ($this->multiOperations as $name => $action) {
            $multiButton .= self::_tpl('multiButton', ['name' => $name, 'action' => $action, 'url' => url($this->module, $this->name, $action)]);
        }

        //全部多选操作按钮
        $multiOperations = self::_tpl('multiOperations', ['multiRemove' => $multiRemove, 'multiButton' => $multiButton]);
        write($file, self::_tpl('index', [
            'title' => $this->title['list'],
            'crumbs' => $crumbs,
            'msgContainer' => $msgContainer,
            'search' => $search,
            'multi' => $multi,
            'rowNo' => $rowNo,
            'header' => $header,
            'multiOne' => $multiOne,
            'rowNoOne' => $rowNoOne,
            'row' => $row,
            'rowButton' => $rowButton,
            'multiOperations' => $multiOperations
        ]));
        echo $this->name . "生成列表视图文件:" . $file . "<br/>";
    }

    /**
     * 将数组转换成代码形式
     * @param array $array
     * @return string
     */
    private function phpizeArray(array $array): string
    {
        //拼接PHP代码
        $list = '[';
        foreach ($array as $enum) {
            $list .= "'" . $enum . "',";
        }
        $list = trim($list, ',');
        $list .= ']';

        //返回 ['v1',...]
        return $list;
    }

    /**
     * 生成添加页视图文件
     * @param $path string 视图文件目录
     */
    private function viewAdd(string $path): void
    {
        $file = $path . '/add.tpl';
        if (is_file($file)) {
            echo '添加视图文件已经存在,不进行覆盖:' . $file . "<br/>";
            return;
        }

        //面包屑
        $crumbs = self::viewCrumbs([$this->title['list'] => url($this->module, $this->formatName, 'index'), $this->title['add']], false);

        //消息容器
        $msgContainer = self::getTpl('msgContainer');

        //遍历每一个字段
        $fields = $validate = $validateSubmit = '';
        foreach ($this->getOrderAdd() as $field) {
            /**
             * @var $field CrudField
             */

            //生成添加时的代码
            if ($field->_dict) {
                $ret = self::_tpl('addDict', [
                    'title' => $field->_description,
                    'name' => $field->_name,
                    'notNull' => $field->_notNull ? 'star' : '',
                    'default' => self::phpizeArray($field->_hasDefault ? (is_array($field->_defaultValue) ? $field->_defaultValue : [$field->_defaultValue]) : [])
                ]);
            } else if ($field->_foreign) {
                //优先处理外键字段
                $ret = self::_tpl('addForeign', [
                    'title' => $field->_description,
                    'name' => $field->_name,
                    'notNull' => $field->_notNull ? 'star' : '',
                    'default' => $field->_hasDefault ? $field->_defaultValue : ''
                ]);
            } else {
                $ret = $field->_add();
            }
            if (!$ret) {
                continue;
            }

            //读取字段添加的视图
            $fields .= $ret;

            //构造每个表单元素的检查函数
            $function = self::viewValidate($field, 'add');
            $validate .= $function;

            //如果字段需要检查
            if ($function) {
                //构造在提交方法中调用检查函数的代码
                $validateSubmit .= self::_tpl('validateSubmit', [
                    'name' => $field->_name,
                ]);
            }
        }

        //保存视图文件
        write($file, self::_tpl('viewAdd', [
            'title' => $this->title['add'],
            'crumbs' => $crumbs,
            'msgContainer' => $msgContainer,
            'fields' => $fields,
            'submit' => ($this->submit and isset($this->submit['添加'])) ? $this->submit['add'] : $this->operations['添加'],
            'validate' => $validate,
            'validateSubmit' => $validateSubmit
        ]));

        echo $this->name . "生成添加视图文件:" . $file . "<br/>";
    }

    /**
     * 生成前端检查代码
     * @param CrudField $field 字段
     * @param $operation string 操作:add/edit
     * @return string
     */
    private function viewValidate(CrudField $field, string $operation): string
    {

        //添加取值和取消息对象的代码
        $content = '';

        //添加非空检查代码
        if ($field->_notNull and (!$field->_isUploadImage or 'add' == $operation)) {
            $content .= self::_tpl('jsNotNull', ['msg' => $field->_notNull]);
        }

        //正则检查
        if ($field->_preg) {
            $content .= self::_tpl('jsPreg', [
                'preg' => $field->_preg,
                'msg' => $field->_pregMsg
            ]);
        }

        //无符号检查
        if ($field->_unsigned) {
            $content .= self::_tpl('jsUnsigned', [
                'name' => $field->_name,
                'msg' => $field->_unsigned
            ]);
        }

        //最大长度检查
        if ($field->_maxLength) {
            $content .= self::_tpl('jsMaxLength', [
                'len' => $field->_maxLength,
                'msg' => $field->_maxLengthMsg
            ]);
        }

        //最小长度检查
        if ($field->_minLength) {
            $content .= self::_tpl('jsMinLength', [
                'len' => $field->_minLength,
                'msg' => $field->_minLengthMsg
            ]);
        }

        $isNumeric = ($field instanceof CrudInt or $field instanceof CrudFloat);

        //最大值检查
        if ($field->_max) {
            $content .= self::_tpl('jsMax', [
                'val' => $isNumeric ? $field->_max : ("'" . $field->_max . "'"),
                'msg' => $field->_maxMsg
            ]);
        }

        //最小值检查
        if ($field->_min) {
            $content .= self::_tpl('jsMin', [
                'val' => $isNumeric ? $field->_min : ("'" . $field->_min . "'"),
                'msg' => $field->_minMsg
            ]);
        }

        //精度检查
        if ($field->_scale) {
            $content .= self::_tpl('jsScale', [
                'scale' => $field->_scale,
                'msg' => $field->_scaleMsg
            ]);
        }

        //如果有需要检查的,加上头代码
        if ($content) {
            return self::_tpl('jsField', [
                'items' => $content,
                'name' => $field->_name,
                'title' => $field->_description
            ]);
        }

        return '';
    }

    /**
     * 生成 修改 视图
     * @param $path string 视图目录
     */
    private function viewEdit(string $path): void
    {
        $file = $path . '/edit.tpl';
        if (is_file($file)) {
            echo '编辑视图文件已经存在,不进行覆盖:' . $file . "<br/>";
            return;
        }

        //面包屑
        $crumbs = self::viewCrumbs([$this->title['list'] => url($this->module, $this->formatName, 'index'), $this->title['edit']], false);

        //消息容器
        $msgContainer = self::getTpl('msgContainer');

        //遍历每一个字段
        $fields = $validate = $validateSubmit = '';
        foreach ($this->getOrderEdit() as $field) {
            /**
             * @var $field CrudField
             */
            //生成修改时的代码
            if ($field->_dict) {
                $ret = self::_tpl('editDict', [
                    'title' => $field->_description,
                    'name' => $field->_name,
                    'notNull' => $field->_notNull ? 'star' : '',
                    'separator' => $field->_dict['separator']
                ]);
            } else if ($field->_foreign) {
                //优先处理外键字段
                $ret = self::_tpl('editForeign', [
                    'title' => $field->_description,
                    'name' => $field->_name,
                    'notNull' => $field->_notNull ? 'star' : '',
                ]);
            } else {
                //获取编辑配置
                $ret = $field->_edit();
            }

            //如果返回为空,无法编辑
            if (!$ret) {
                continue;
            }

            //读取字段添加的视图
            $fields .= $ret;

            //构造每个表单元素的检查函数
            $function = self::viewValidate($field, 'edit');
            $validate .= $function;

            //如果字段需要检查
            if ($function) {
                //构造在提交方法中调用检查函数的代码
                $validateSubmit .= self::_tpl('validateSubmit', [
                    'name' => $field->_name,
                ]);
            }
        }

        //保存视图文件
        write($file, self::_tpl('viewEdit', [
            'title' => $this->title['edit'],
            'crumbs' => $crumbs,
            'msgContainer' => $msgContainer,
            'fields' => $fields,
            'submit' => ($this->submit and isset($this->submit['编辑'])) ? $this->submit['add'] : $this->operations['编辑'],
            'validate' => $validate,
            'validateSubmit' => $validateSubmit
        ]));

        echo $this->name . "生成编辑视图文件:" . $file . "<br/>";
    }

    /**
     * 生成 详情 视图
     * @param $path string 视图目录
     */
    private function viewDetail(string $path): void
    {
        $file = $path . '/detail.tpl';
        if (is_file($file)) {
            echo '详情视图文件已经存在,不进行覆盖:' . $file . "<br/>";
            return;
        }

        //面包屑
        $crumbs = self::viewCrumbs([$this->title['list'] => url($this->module, $this->formatName, 'index'), $this->title['detail']], false);

        //消息容器
        $msgContainer = self::getTpl('msgContainer');

        $fields = '';
        foreach ($this->getOrderDetail() as $detail) {
            /**
             * @var $detail CrudField
             */
            $fields .= $detail->_detail();
        };

        //保存视图文件
        write($file, self::_tpl('viewDetail', [
            'title' => $this->title['detail'],
            'crumbs' => $crumbs,
            'msgContainer' => $msgContainer,
            'fields' => $fields,
            'submit' => $this->operations['列表']
        ]));

        echo $this->name . "生成详情视图文件:" . $file . "<br/>";
    }

    /**
     * 生成打印页面视图,尚未开发完成
     * @param $path
     */
    private function viewPrint(string $path): void
    {
        $file = $path . '/print.tpl';
        if (is_file($file)) {
            echo '打印视图文件已经存在,不进行覆盖:' . $file . "<br/>";
            return;
        }

        //@TODO 打印功能尚未开发完成

        $this->getOrderPrint();
    }

    /**
     * 获取整个表的搜索相关配置
     * @return array
     */
    public function getSearch(): array
    {
        $ret = [];
        foreach ($this->getOrderSearch() as $field) {
            /**
             * @var $field CrudField
             */

            //调整默认搜索配置
            if ($field->_searchType === true) {
                $field->searchType(true);
            }

            //有可能不适合搜索
            if ($field->_searchType === false) {
                continue;
            }

            //优先处理外键列表搜索
            if ($field->_foreign) {
                $ret[] = [
                    'conditionTpl' => 'conditionForeign',
                    'tpl' => 'searchForeign',
                    'params' => array_merge($field->_foreign, [
                        'title' => $field->_description,
                        'name' => $field->_name
                    ])
                ];
                continue;
            }

            //处理字典字段
            if ($field->_dict) {
                $ret[] = [
                    'conditionTpl' => 'conditionDict',
                    'tpl' => 'searchDict',
                    'params' => array_merge($field->_dict, [
                        'title' => $field->_description,
                        'name' => $field->_name
                    ])
                ];
                continue;
            }

            //按搜索类型调用不同的方法
            if ('精确匹配' == $field->_searchType) {
                $ret[] = $field->_searchEqual();
            } elseif ('模糊匹配' == $field->_searchType) {
                $ret[] = $field->_searchLike();
            } elseif ('开头匹配' == $field->_searchType) {
                $ret[] = $field->_searchBegin();
            } elseif ('结尾匹配' == $field->_searchType) {
                $ret[] = $field->_searchEnd();
            } elseif ('范围匹配' == $field->_searchType) {
                $ret[] = $field->_searchRange();
            } else {
                trigger_error('CRUD中,不认识的搜索匹配类型:' . $field->_searchType, E_USER_ERROR);
            }
        }

        return $ret;
    }

    /**
     * 获取所有需要导出的字段列表
     * @return string [字段名称=>标题,...]
     */
    private function getExport(): string
    {
        //如果没有字段
        if (!count($this->fields)) {
            return '[]';
        }

        $result = [];
        foreach ($this->getOrderExport() as $field) {
            /**
             * @var $field CrudField
             */
            $result[] = "'" . $field->_name . "'=>" . Mysql::markValue($field->_description);
        }
        return '[' . implode(',', $result) . ']';
    }

    /**
     * 获取所有需要打印的字段列表
     * @return string [字段名称=>标题,...]
     */
    private function getPrint(): string
    {
        //如果没有字段
        if (!count($this->fields)) {
            return '[]';
        }

        $result = [];
        foreach ($this->getOrderPrint() as $field) {
            /**
             * @var $field CrudField
             */
            $result[] = "'" . $field->_name . "'=>" . Mysql::markValue($field->_description);
        }
        return '[' . implode(',', $result) . ']';
    }

    /**
     * 生成 从请求参数中获取参数值  的 控制器代码,用于addSubmit,editSubmit方法中
     * @return string 控制器代码
     */
    private function controllerInput(): string
    {
        $content = '';

        //逐个字段检查
        foreach ($this->fields as $field) {
            /**
             * @var $field CrudField
             */

            //获取本字段的生成代码
            $template = $field->_input();

            //有些字段会返回 空,表明不参与添加/编辑
            if (!$template) {
                continue;
            }

            //生成获取本字段参数值的代码
            $content .= $template;

            //附加参数检查代码
            $content .= $this->controllerInputValidate($field);
        }

        return $content;
    }

    /**
     * 生成获取输入参数值时的参数检查代码
     * @param CrudField $field 字段
     * @return string 检查 代码
     */
    private function controllerInputValidate(CrudField $field): string
    {
        $content = '';

        //主键字段不检查
        if ($field->_primaryKey) {
            return '';
        }

        //添加非空检查代码
        if ($field->_notNull) {
            if ($field->_isUploadImage) {
                $content .= self::_tpl('validateNotNullImage', ['name' => $field->_name, 'msg' => $field->_notNull]);
            } else {
                $content .= self::_tpl('validateNotNull', ['name' => $field->_name, 'msg' => $field->_notNull]);
            }
        }

        //最大长度检查
        if ($field->_maxLength) {
            $content .= self::_tpl('validateMaxLength', [
                'name' => $field->_name,
                'len' => $field->_maxLength,
                'msg' => $field->_maxLengthMsg
            ]);
        }

        //最小长度检查
        if ($field->_minLength) {
            $content .= self::_tpl('validateMinLength', [
                'name' => $field->_name,
                'len' => $field->_minLength,
                'msg' => $field->_minLengthMsg
            ]);
        }

        //最大值检查
        if ($field->_max) {
            $params = [
                'name' => $field->_name,
                'val' => $field->_max,
                'msg' => $field->_maxMsg
            ];
            if ($field instanceof CrudInt) {
                $content .= self::_tpl('validateMaxInt', $params);
            } else {
                $content .= self::_tpl('validateMax', $params);
            }
        }

        //最小值检查
        if ($field->_min) {
            $params = [
                'name' => $field->_name,
                'val' => $field->_min,
                'msg' => $field->_minMsg
            ];
            if ($field instanceof CrudInt) {
                $content .= self::_tpl('validateMinInt', $params);
            } else {
                $content .= self::_tpl('validateMin', $params);
            }
        }

        //无符号检查
        if ($field->_unsigned) {
            $content .= self::_tpl('validateUnsigned', [
                'name' => $field->_name,
                'msg' => $field->_unsigned
            ]);
        }

        //正则检查
        if ($field->_preg) {
            $content .= self::_tpl('validatePreg', [
                'name' => $field->_name,
                'preg' => trim($field->_preg, '/'),
                'msg' => $field->_pregMsg
            ]);
        }

        //精度检查
        if ($field->_scale) {
            $content .= self::_tpl('validateScale', [
                'name' => $field->_name,
                'scale' => $field->_scale,
                'msg' => $field->_scaleMsg
            ]);
        }

        return $content;
    }
}