<?php
declare(strict_types=1);

namespace icePHP\Frame;

//使用到的类
use icePHP\Frame\Config\Config;
use icePHP\Frame\Debug\Debug;
use icePHP\Frame\FileLog\FileLog;
use function icePHP\Frame\Functions\formatter;
use icePHP\Frame\MVC\Controller;
use icePHP\Frame\MVC\Decorator;
use icePHP\Frame\MVC\Request;
use icePHP\Frame\MVC\Template;
use icePHP\Frame\Router\Router;
use icePHP\Frame\Page\Page;
use icePHP\Frame\TableLog\TableLog;

//使用到的函数
use function icePHP\Frame\Functions\json;
use function icePHP\Frame\Config\configDefault;
use function icePHP\Frame\Config\configMust;
use function icePHP\Frame\Cache\cache;
use function icePHP\Frame\Debug\isDebug;
use function icePHP\Frame\FileLog\writeLog;
use function icePHP\Frame\Functions\isCliMode;
use function icePHP\Frame\Functions\requireOnce;


/**
 * 框架核心类,实现派发及显示模板
 *
 * @author Ice
 */
final class Core
{
    // 模块/控制器/动作参数的名称
    public static $mcaName = array('m', 'c', 'a');

    /**
     * 静态类,禁止实例化
     */
    private function __construct()
    {
    }

    // 当前控制器及动作名称
    private static $module;
    private static $controller;
    private static $action;

    // 控制器类名
    private static $controllerClassName;

    /**
     * 当前控制器类实例
     * @var Controller
     */
    private static $controllerInstance;

    /**
     * 获取当前的控制器类实例
     * @return Controller
     */
    public static function getControllerInstance()
    {
        return self::$controllerInstance;
    }

    /**
     * 请求参数对象
     * @var Request
     */
    private static $request;

    // 是否命令行方式进入
    public static $isProgram = false;

    /**
     * 框架初始化工作,四种模式通用(框架模式,插入模式,命令行模式,片段模式)
     */
    public static function construct(): void
    {
        // 定义系统时区
        date_default_timezone_set('PRC');

        //设置系统配置目录
        Config::append(DIR_ROOT . 'Program/Config/');
        $mode = Config::mode();
        Config::append(DIR_ROOT . 'Program/Config/' . $mode . '/');

        // 调试状态,显示所有错误
        if (isDebug()) {
            error_reporting(E_ALL | E_STRICT);
            ini_set("display_errors", '1');
        } else {
            //非调试状态,隐藏所有状态
            error_reporting(0);
            ini_set("display_errors", '0');
        }

        // 非CLI模式, 开会话, 开输出缓存
        if (!isCliMode()) {
            $config = configMust('Session');
            if ($config($_REQUEST['m'] ?? '', $_REQUEST['c'] ?? '', $_REQUEST['a'] ?? '')) {
                self::sessionStart();
            }
            ob_start();
        }

        // 记录开始执行时间
        Debug::start();

        //捕获错误并显示
        if (!isset($_REQUEST['debug']) or $_REQUEST['debug'] != 'error') {
            set_error_handler(function (int $no, string $msg, string $file, int $line) {
                Core::pageError($no, $msg, $file, $line);
                writeLog('error', [
                    'no' => $no,
                    'msg' => $msg,
                    'file' => $file,
                    'line' => $line,
                    'trace' => self::pureTrace()
                ]);
                exit;
            });
        }
    }

    /**
     * 从Web请求的URL中获取请求参数
     * @return string 类似'/admin/auth/user/?'
     */
    private static function getPathFromUrl(): string
    {
        // 取相对URL部分,即 http://www.xxx.com:port,以/开头
        $path = $_SERVER['REQUEST_URI'];

        // 取查询参数部分 , ? 后面的字符串
        $query_string = $_SERVER['QUERY_STRING'];

        // path去除查询参数,如:/home/index
        if ($query_string) {
            $path = substr($path, 0, -strlen($query_string));
        }

        return $path;
    }

    /**
     * 处理请求路径,分解为模块/控制器/动作,以及后继的参数
     * @param string $path
     */
    private static function parsePath(string $path): void
    {
        $modules = self::getModules();

        //初始化路由类
        Router::init(self::$mcaName, $modules);

        //模板解析类也需要全部模块列表信息
        Template::setModules($modules);

        // 去除末尾独立的问号
        $path = trim($path, '?');

        // 系统配置的路径根,通常是 /
        $path_root = configDefault('/', 'System', 'host');

        // 从Path中去除路径根, home/index
        if ($path_root == substr($path, 0, strlen($path_root))) {
            $path = substr($path, strlen($path_root));
        }

        // 去除首尾的斜线
        $path = trim($path, '/');

        // 如果此地址被配置为忽略解析,则直接包含此文件.
        if (Router::ignore($path)) {
            requireOnce(DIR_ROOT . 'Public/' . $path);
            return;
        }

        // 路由解析,解析结果会存储到$_REQUEST,$_GET中.
        Router::decode($path);

        // 构造Request对象
        self::$request = Request::instance();

        // 取MCA三项名称
        [$m, $c, $a] = self::$mcaName;

        //检查MCA参数名是否异常
        $reg = '/^\w*$/i';
        if (!preg_match($reg, $_REQUEST[$m]) or !preg_match($reg, $_REQUEST[$c]) or !preg_match($reg, $_REQUEST[$a])) {
            //记录异常日志
            FileLog::instance()->mca($_REQUEST);
            exit;
        }

        //记录当前MCA
        self::setMCA($_REQUEST[$m], $_REQUEST[$c], $_REQUEST[$a]);
    }

    /**
     * 设置当前动作
     * @param string $action
     */
    private static function setAction(string $action): void
    {
        self::$action = $action;
        Template::setAction($action);
    }

    /**
     * 设置当前模块/控制器/动作
     * @param string $module
     * @param string $controller
     * @param string $action
     */
    private static function setMCA(string $module, string $controller, string $action): void
    {
        //记录当前模块名
        self::$module = $module;

        //设置当前模块配置目录
        Config::insert(DIR_ROOT . 'Program/Module/' . ucfirst($module) . '/Config/');

        //初始化视图模块
        Template::setRoot(DIR_ROOT);
        Template::setModule($module);

        //初始化分页模块
        Page::setMCA($module, $controller, $action);

        //初始化日志系统,这个必须在配置类的初始化之后进行
        FileLog::instance()->init(DIR_ROOT . 'Run/Log/');

        //记录当前控制器名称
        self::$controller = $controller;
        Template::setController($controller);

        //记录当前动作名称
        self::setAction($action);
    }

    /**
     * 开始核心框架的运行,Web方式,参考根目录下的index.php
     */
    public static function run(): void
    {
        // 从URL中获取请求路径 /path/file.html? 不包括请求参数
        $path = self::getPathFromUrl();

        //如果路径以下面开头,这表明是404
        foreach (['/Static/', '/Upload/'] as $prefix) {
            if (strpos($path, $prefix) === 0) {
                //显示404 页面,然后退出(exit)
                self::page404($path);
            }
        }

        // 处理请求路径
        self::parsePath($path);

        // 派发后,退出(exit)
        self::dispatch();
    }

    /**
     * 以插入模式提供框架服务
     *
     * @param string $m 模块名
     * @param string $c 控制器名
     * @param string $a 动作名
     * @param array $params 其它参数
     */
    public static function plugin($m = null, $c = null, $a = null, array $params = []): void
    {
        // 合并请求参数
        $_REQUEST = array_merge($_REQUEST, $params);

        // 记录请求参数
        self::$request = Request::instance();

        // 记录MCA
        self::setMCA($m, $c, $a);

        // 派发,退出(exit)
        self::dispatch();
    }

    /**
     * 执行指定控制器实例对象的初始化工作
     * @param Controller $controller 控制器实例
     * @return bool 是否应该继续执行
     */
    private static function initControllerInstance(Controller $controller): bool
    {
        return $controller->init(
            self::$module,
            self::$controller,
            self::$action,
            self::$request,
            [__CLASS__, 'destruct']
        );
    }

    /**
     * 显示页面片段,可以是缓存
     *
     * @param string $m 模块名
     * @param string $c 控制器名
     * @param string $a 动作名
     * @param array $params 参数数组
     * @param int $cached 缓存时间 0表示不缓存
     */
    public static function fragment($m = null, $c = null, $a = null, array $params = [], $cached = 7200): void
    {
        // 补充默认参数值为当前模块
        $m = $m ?: self::getModule();

        // 补充默认参数值为当前控制器
        $c = $c ?: self::getController();

        // 补充默认参数值为当前动作
        $a = $a ?: self::getAction();

        //实例化控制器类
        $controllerInstance = self::instanceControllerClass($m, $c);

        // 初始化控制器,返回是否希望执行动作
        if (!self::initControllerInstance($controllerInstance)) {
            //本片段不处理任何内容
            return;
        };

        // 如果不要求缓存
        if (!$cached) {
            // 调用 控制器显示页面片段
            echo call_user_func_array(array($controllerInstance, $a), $params);
            return;
        }

        // 缓存
        $cache = cache('Page');
        $key = $m . '-' . $c . '-' . $a . '-' . md5(serialize($params));

        // 从缓存中取
        try {
            $item = $cache->getItem($key);
            $contents = $item->get();
        } catch (\Psr\Cache\InvalidArgumentException $e) {
            echo call_user_func_array(array($controllerInstance, $a), $params);
            return;
        }

        if (!$contents) {
            // 调用 控制器生成
            call_user_func_array(array($controllerInstance, $a), $params);
            $contents = ob_get_clean();

            // 保存到缓存中
            $item->set($contents);
            $item->expiresAfter($cached);
        }

        // 显示生成的片段
        echo $contents;
    }

    /**
     * 命令行模式入口处理,参考根目录下的program.php
     * 本框架可以作为命令行模式使用
     * 通常使用命令为 \...\...\php program.php "Module/Controller/action?p1=..."
     */
    public static function program(): void
    {
        // 检查是否命令行进入
        if (!isCliMode()) {
            echo 'program run in CLI(Command-line Interface) mode only.';
            exit;
        }

        // 记录当前请求是来自于命令行模式
        self::$isProgram = true;

        // 取命令行参数
        $argv = $_SERVER['argv'];

        // 取参数
        $argv1 = $argv[1] ?? '';

        // 分解成为路径和参数
        if (!strpos($argv1, '?')) {
            $argv1 .= '?';
        }
        [$path, $params] = explode('?', $argv1);

        // 参数送入GET,传递到REQUEST
        parse_str($params, $_GET);
        $_REQUEST = array_merge($_REQUEST, $_GET);

        // 虚拟请求参数
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = $path;

        // 当然请求路径 来解析
        self::parsePath($path);

        // 派发,退出(exit)
        self::dispatch();
    }

    /**
     * 根据保存的控制器名称及动作名称进行派发,此方法可能重复调用,以处理重定向
     */
    private static function dispatch(): void
    {
        //需要记录请求体的配置
        $requestBody = configDefault([], 'Log', 'requestBody');

        //获取管理员ID与Name的方法的配置
        $getAdminId = configDefault(null, 'Log', 'getAdminId');
        $getAdminName = configDefault(null, 'Log', 'getAdminName');

        //初始化表日志系统
        TableLog::init(
            self::$module, //当前模块
            self::$controller, //当前控制器
            self::$action, //当前动作
            self::$request->ip(), //当前客户端IP
            in_array([self::$module, self::$controller, self::$action], $requestBody),  //是否需要记录请求体
            $getAdminId ? $getAdminId() : 0,  //当前后台管理员编号
            $getAdminName ? $getAdminName() : '' //当前后台管理员名称
        );

        //查看配置,是否需要记录请求日志
        $requestLog = configDefault(null, 'Log', 'requestLog');
        if ($requestLog) {
            $requestLog();
        }

        // 有些是文件找不到,而不是控制器
        if (in_array(self::$controller, array('Upload', 'Static'))) {
            //显示404页面,退出(exit)
            self::page404(self::$controller);
        }

        // 控制器类名是Controller结尾
        self::$controllerClassName = ucfirst(self::$controller) . 'Controller';

        // 根据是否调试状态,进行不同的处理,退出(exit)
        try {
            self::dispatch2();
        } catch (\Exception $e) {
            self::pageException($e);
            //正式环境中,捕获异常并记录日志
            if (!isDebug()) {
                writeLog('dispatch', $e);
            }
        }
    }

    /**
     * 根据控制器名称(请求参数中的c),构造控制器类名(首字母大写,加Controller后缀)
     * @param string $controller 控制器名称
     * @return string 控制器类名
     */
    private static function buildControllerClass(string $controller): string
    {
        return ucfirst($controller) . 'Controller';
    }

    /**
     * 根据模块名称(请求参数中的m),构造模块目录名称及命名空间(首字母大写,前缀为Program\Module\)
     * @param string $module 模块名称
     * @return string 模块目录/命名空间名称
     */
    private static function buildModulePath(string $module): string
    {
        return 'Program\\Module\\' . ucfirst($module);
    }

    /**
     * 实例化一个没有模块的控制器类
     * @param $controller string 控制器文件名
     * @return Controller
     */
    private static function instanceControllerWithoutModule(string $controller): Controller
    {
        //在主控制器目录下查找
        $className = 'Program\\Controller\\' . self::buildControllerClass($controller);

        //如果找到
        if (class_exists($className)) {
            return new $className();
        }

        //没找到
        self::pageMissController($controller);
        exit;
    }

    /**
     * 实例化一个有模块的控制器类
     * @param $module string 模块名称
     * @param $controller string 控制器文件名
     * @return Controller
     */
    private static function instanceControllerWithModule(string $module, string $controller): Controller
    {
        //模块目录名称/命名空间,首字母大写
        $namespace = self::buildModulePath($module);

        //类名后缀加Controller
        $controller = formatter($module).self::buildControllerClass($controller);

        //在模块的Controller目录下找
        $className = $namespace . '\\Controller\\' . $controller;
        if (class_exists($className)) {
            return new $className();
        }

        //在模块的根目录下找
        $className = $namespace . '\\' . $controller;
        if (class_exists($className)) {
            return new $className();
        }

        //没找到
        self::pageMissController($controller);
        exit;
    }

    /**
     * 实例化一个控制器对象
     * 控制器文件,必须在指定目录下,根据模块名进行目录 分布,文件名必须全小写字母,后缀为.Controller.php
     *
     * @param string $module 模块名
     * @param string $controller 控制器名
     * @return Controller
     */
    private static function instanceControllerClass($module, $controller): Controller
    {
        if ($module) {
            //指定了模块
            return self::instanceControllerWithModule($module, $controller);
        } else {
            //未指定模块
            return self::instanceControllerWithoutModule($controller);
        }
    }

    /**
     * 调试状态下的派发,当控制器或方法不存在时,将抛出错误
     */
    private static function dispatch2(): void
    {
        // 实例化控制器类
        self::$controllerInstance = self::instanceControllerClass(self::$module, self::$controller);

        //表日志系统也需要知道此实例
        TableLog::setControllerInstance(self::$controllerInstance);

        // 初始化控制器,返回是否希望执行动作
        if (!self::initControllerInstance(self::$controllerInstance)) {
            //无法满足程序运行条件
            self::pageDisallowed(self::$module, self::$controller);

            //收尾,退出(exit)
            self::destruct();
        }

        // 动作名称
        $actionName = self::$action;

        // 如果是POST请求
        if (self::$request->isPost()) {
            // 优先查看 Submit动作
            $actionSubmit = $actionName . 'Submit';

            // 如果动作存在,调用Submit动作
            if (self::methodExists(self::$controllerInstance, $actionSubmit)) {
                self::setAction($actionSubmit);
                self::$controllerInstance->$actionSubmit(self::$request);

                //收尾,退出(exit)
                self::destruct(self::$controllerInstance);
            }
        }

        // 查看动作是否存在
        if (self::methodExists(self::$controllerInstance, $actionName)) {
            //如果动作存在
            $action = $actionName;
        } elseif (self::methodExists(self::$controllerInstance, 'default')) {
            //存在默认动作
            $action = 'default';
        } else {
            // 动作不存在,且不存在默认动作,则抛出错误
            self::pageMissAction(self::$module, self::$controller, self::$action);
            return;
        }

        //执行指定动作
        self::$controllerInstance->$action(self::$request);

        //收尾,退出(exit)
        self::destruct(self::$controllerInstance);
    }

    /**
     * 判断方法是否存在(要考虑装饰器的情况)
     * @param object $class 类
     * @param string $method 方法名
     * @return bool
     */
    private static function methodExists(object $class, string $method): bool
    {
        //如果有指定方法,则返回真
        if (method_exists($class, $method)) {
            return true;
        }

        //尝试反射
        try {
            $reflection = new \ReflectionClass($class);
        } catch (\ReflectionException $e) {
            trigger_error('对类[' . $class . ']进行反射失败', E_USER_ERROR);
            exit;
        }

        //查看Trait
        while (true) {
            $traits = $reflection->getTraits();
            if (isset($traits['icePHP\Frame\MVC\Decorator'])) {
                //如果是装饰器模式,查看内部方法是否存在
                return method_exists($class, Decorator::buildName($method));
            }

            //查找父类
            $reflection = $reflection->getParentClass();
            if (!$reflection) {
                return false;
            }
        }

        //没找到
        return false;
    }

    /**
     * 递归获取指定目录的所有下属系统文件的目录
     * @param $path string
     * @return array
     */
    private static function dirSub(string $path): array
    {
        //对应关系
        $maps = [];

        // 查看子目录
        $d = new \DirectoryIterator($path);
        foreach ($d as $f) {
            // 当前目录和父目录
            if ($f->isDot()) {
                continue;
            }

            //文件
            if ($f->isFile()) {
                $name = $f->getFilename();

                //不是系统文件
                if (substr($name, -4) != '.php') {
                    continue;
                }

                //系统文件 加入对应 关系
                $maps[strtolower(substr($name, 0, -4))] = $f->getPathname();
                continue;
            }

            // 如果子目录下有,包含
            $maps = array_merge($maps, self::dirSub($f->getPathname()));
        }

        return $maps;
    }

    /**
     * 在当前目录及直接子目录下包含文件
     *
     * @param string $dir 目录名
     * @param string $filename 文件名
     * @return mixed|false 包含成功与否
     */
    private static function requireInSub(string $dir, string $filename)
    {
        // 如果文件存在目录下,直接包含并返回
        $file = $dir . '/' . $filename;
        if (is_file($file)) {
            return requireOnce($file);
        }

        // 查看子目录
        $d = new \DirectoryIterator($dir);
        foreach ($d as $f) {
            // 不看文件和父目录,当前目录
            if ($f->isFile() or $f->isDot()) {
                continue;
            }

            // 如果子目录下有,包含
            $ret = self::requireInSub($f->getPathname(), $filename);
            if ($ret) {
                return $ret;
            }
        }

        // 不存在
        return false;
    }

    // 获取模块名称
    public static function getModule(): string
    {
        return self::$module;
    }

    // 获取控制器名称
    public static function getController(): string
    {
        return self::$controller;
    }

    // 获取动作名称
    public static function getAction(): string
    {
        return self::$action;
    }

    /**
     * 启动Session,统一控制过期时间
     */
    private static function sessionStart(): void
    {
        // 如果Session已经启动
        if (isset($_SESSION)) {
            return;
        }

        //设置安全参数
        ini_set("session.cookie_httponly", '1');

        // 获取Cookie域名,及过期时间配置
        $host = self::host();
        $persist = configDefault(30 * 60, 'System', 'cookiePersist');

        // 设置Cookie
        session_set_cookie_params($persist, '/', $host);

        // 启动Session
        session_start();

        //设置Cookie
        setcookie(session_name(), session_id(), time() + $persist, '/', $host);
    }

    /**
     * 用于处理参数中的反斜线问题的递归函数
     * 如果INI中magic_quotes_gpc打开,所有参数中将自动加斜线,需要程序去除
     * @param array $array
     * @return array
     */
    private static function stripSlashesRecursive(array $array): array
    {
        // 逐个处理
        foreach ($array as $k => $v) {
            if (is_string($v)) {
                // 字符串去斜线
                $array[$k] = stripslashes($v);
            } elseif (is_array($v)) {
                // 数组,递归处理
                $array[$k] = self::stripSlashesRecursive($v);
            }
        }
        return $array;
    }

    /**
     * 对本次框架的调用进行收尾,并退出
     * @param Controller|null $controller
     * @param $exportFile bool 当前是否是输出文件类的功能
     */
    public static function destruct(Controller $controller = null, bool $exportFile = false): void
    {
        //如果有控制器,先调用控制器的伪析构方法
        if ($controller) {
            $controller->destruct();
        }

        //查看配置,是否需要记录请求日志
        $requestLog = configDefault(false, 'Log', 'requestLog');
        if ($requestLog) {
            call_user_func($requestLog, true);
        }

        //如果是调试模式,调用调试的结束方法
        //如果当前功能是导出/生成图片之类的输出文件的功能,则不要输出调试内容
        if (!$exportFile) {
            Debug::end();
        }

        // 记录执行日志
        FileLog::instance()->dispatch(Debug::getPersist());

        //结束PHP的执行
        exit;
    }

    /**
     * 获取当前所有模块,判断原则:./Module/目录下的所有目录
     * @return array
     */
    public static function getModules(): array
    {
        // 单次检查
        static $modules = [];

        //如果已经有结果,返回
        if ($modules) {
            return $modules;
        }

        // 如果没有检查过  遍历 /Program/Module/
        $path = DIR_ROOT . 'Program/Module';
        $path = str_replace('\\', '/', $path);

        //如果模块目录不存在
        if (!is_dir($path)) {
            return [];
        }

        //遍历模块目录
        $d = new \DirectoryIterator($path);
        foreach ($d as $f) {
            // 不看文件和父目录,当前目录
            if (!$f->isFile() and !$f->isDot()) {
                $fileName = $f->getFilename();
                if ($fileName == '.svn') {
                    continue;
                }

                // 所有子目录名都是模块名称
                $modules[] = $fileName;
            }
        }

        // 所有模块名数组
        return $modules;
    }

    /**
     * 获取当前域名
     * @return string 例如 www.php-liyong.com
     */
    private static function host(): string
    {
        /* 域名或IP地址 */
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            return $_SERVER['HTTP_X_FORWARDED_HOST'];
        }

        if (isset($_SERVER['HTTP_HOST'])) {
            return $_SERVER['HTTP_HOST'];
        }

        if (isset($_SERVER['SERVER_NAME'])) {
            return $_SERVER['SERVER_NAME'];
        }

        if (isset($_SERVER['SERVER_ADDR'])) {
            return $_SERVER['SERVER_ADDR'];
        }

        return '';
    }

    /**
     * 输出 404 响应头
     */
    private static function header404(){
        header('HTTP/1.1 404 Not Found');
        header("status: 404 Not Found");
    }

    /**
     * 显示一个友好的404页面
     * @param string $path
     */
    private static function page404(string $path): void
    {
        //输出404响应头
        self::header404();

        //输出错误信息
        self::pageEcho('error', [
            'header' => '您要的文件没有找到!!!   -- icePHP',
            'title' => '以下文件不存在：',
            'message' => $path,
            'info' => '',
            'table' => ''
        ]);
        self::destruct();
    }

    /**
     * 不满足运行条件
     * @param string $module
     * @param string $controller
     */
    private static function pageDisallowed(string $module, string $controller): void
    {
        self::pageEcho('error', [
            'header' => '运行条件不满足!!!   -- icePHP',
            'title' => '运行条件不满足',
            'message' => '当前条件下不能运行指定的控制器:' . $module . '::' . $controller,
            'info' => '',
            'table' => ''
        ]);
        self::destruct();
    }

    /**
     * 控制器不存在时,报错404
     * @param string $controller 控制器名称
     */
    private static function pageMissController(string $controller): void
    {
        //输出404响应头
        self::header404();

        self::pageEcho('error', [
            'header' => '访问地址错误!!!   -- icePHP',
            'title' => '指定的控制器不存在：',
            'message' => $controller,
            'info' => '',
            'table' => ''
        ]);
        self::destruct();
    }

    /**
     * 动作不存在时,报错404
     * @param string $module
     * @param string $controller
     * @param string $action
     */
    private static function pageMissAction(string $module, string $controller, string $action): void
    {
        //输出404响应头
        self::header404();

        self::pageEcho('error', [
            'header' => '访问地址错误!!!   -- icePHP',
            'title' => '指定的动作不存在：',
            'message' => $action,
            'info' => self::buildInfo([
                '模块' => $module,
                '控制器' => $controller,
                '动作' => $action,
            ]),
            'table' => ''
        ]);
        self::destruct();
    }

    /**
     * 捕获到 Error时的错误显示
     * @param int $no
     * @param string $msg
     * @param string $file
     * @param int $line
     */
    private static function pageError(int $no, string $msg, string $file, int $line): void
    {
        //如果是框架出错,那么不报具体错误位置
        if (strpos($file, 'vendor\ice-php')) {
            $info = '';
        } else {
            $info = self::buildInfo(['错误级别' => $no, '文件位置' => $file, '代码行号' => $line]);
        }

        self::pageEcho('error', [
            'header' => '错误提示!!!   -- icePHP',
            'title' => '遇到错误：',
            'message' => $msg,
            'info' => $info,
            'table' => self::buildTable(self::pureTrace())
        ]);
    }

    /**
     * 显示错误信息,根据CLI与否,简洁显示或页面显示
     * @param string $tpl 模板名称
     * @param array $info 模板信息
     */
    private static function pageEcho(string $tpl, array $info): void
    {
        if (isCliMode()) {
            var_dump($info);
        } else {
            echo self::replace(file_get_contents(__DIR__ . '/' . $tpl . '.tpl'), $info);
        }
    }

    /**
     * 捕获到异常时的错误显示
     * @param \Exception $e
     */
    private static function pageException(\Exception $e): void
    {
        $last = self::lastLine($e->getTrace());
        self::pageEcho('error', [
            'header' => '异常提示!!!   -- icePHP',
            'title' => '遇到异常：',
            'message' => $e->getMessage(),
            'info' => self::buildInfo([
                '异常代码' => intval($e->getCode()),
                '文件位置' => $last['file'] ?? $e->getFile(),
                '代码行号' => intval(isset($last['line']) ? $last['line'] : $e->getLine())
            ]),
            'table' => self::buildTable(self::pureTrace($e->getTrace()))
        ]);
    }

    /**
     * 构造错误地点表格
     * @param array $info 错误内容键值对数组
     * @return string
     */
    private static function buildInfo(array $info): string
    {
        //命令行模式,打印就好
        if (isCliMode()) {
            return var_export($info, true);
        }

        $rows = [];
        foreach ($info as $key => $value) {
            $rows[] = '<tr><th>' . $key . '</th><td>' . $value . '</td></tr>';
        }

        if (!$rows) {
            return '';
        }

        return '<table class="table"><tbody>' . implode('', $rows) . '</tbody></table>';
    }

    /**
     * 判断调用堆栈中的一步,是否需要显示跟踪, 核心类/框架核心 不显示
     * @param array $row 调用堆栈中的一步,包含file/class元素
     * @return bool
     */
    private static function notTrace(array $row): bool
    {
        $file = $row['file'] ?? '';
        $class = $row['class'] ?? '';
        return
            stripos($file, 'system\\core')
            or stripos($file, 'Frame.php')
            or $class == __CLASS__;
    }

    /**
     * 取错误调用堆栈中的最后一行开发代码
     * @param array $context
     * @return array
     */
    private static function lastLine(array $context): array
    {
        foreach ($context as $row) {
            //不显示Vendor 及Frame中的跟踪过程
            if (!self::notTrace($row)) return $row;
        }
        return [];
    }

    /**
     * 返回纯净的调用堆栈,去除框架调用部分
     * @param $trace array
     * @return array
     */
    private static function pureTrace(?array $trace = null): array
    {
        //获取调用堆栈
        $trace = $trace ?: debug_backtrace();

        return array_filter($trace, function (array $row) {
            return !self::notTrace($row);
        });
    }

    /**
     * 构造错误跟踪表格
     * @param array $context 调用堆栈
     * @return string
     */
    private static function buildTable(array $context): string
    {
        //只有调试状态才显示跟踪表格
        if (!isDebug()) {
            return '';
        }

        if (isCliMode()) {
            return var_export($context, true);
        }

        $rows = '';
        foreach ($context as $row) {
            if (self::notTrace($row)) continue;

            //构造一行信息
            $file = $row['file'] ?? '';
            $class = $row['class'] ?? '';

            $rows .= '<tr>';
            $rows .= '<td>' . (isset($row['line']) ? $row['line'] : '') . '</td>';
            $rows .= '<td><span class="code">' . $class . ($row['type'] ?? '') . $row['function'] . '</span></td>';
            $rows .= '<td>' . json($row['args'] ?? '') . '</td>';
            $rows .= '<td>' . $file . '</td>';
            $rows .= '</tr>';
        }
        if (!$rows) {
            return '';
        }

        return '<table class="table">
                    <thead>
                        <tr>
                            <td width="60">行号</td>
                            <td width="200">方法</td>
                            <td>参数</td>
                            <td>文件位置</td>
                        </tr>
				    </thead>
				    <tbody>
				' . $rows . '</tbody></table>';
    }

    /**
     * 简单的模板替换
     * @param string $template
     * @param array $params
     * @return string
     */
    private static function replace(string $template, array $params = []): string
    {
        foreach ($params as $k => $v) {
            $template = str_replace('{$' . $k . '}', $v, $template);
        }
        return $template;
    }
} //End of Class Frame
