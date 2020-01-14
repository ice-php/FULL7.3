<?php
declare(strict_types=1);

namespace icePHP\Frame\Mongo;

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;

use function icePHP\Frame\Config\configDefault;
use function icePHP\Frame\Config\configMust;
use function icePHP\Frame\Debug\debug;
use function icePHP\Frame\Functions\timeLog;

/**
 * 对MongoDB的封装
 * 尚不完善, 随着使用进度, 逐步添加功能
 * @author Ice
 *
 */
final class Mongo
{
    /**
     * 创建一个Mongo客户端对象
     * @param string $dbAlias 数据库别名
     * @return Client
     */
    public static function client(string $dbAlias): Client
    {
        static $clients = [];
        if (isset($clients[$dbAlias])) {
            return $clients[$dbAlias];
        }
        $config = configMust('Mongo', $dbAlias);
        //配置信息中的server子数组,合并进来
        if (isset($config['server'])) {
            $config = array_merge($config, $config['server']);
            unset($config['server']);
        }

        //主机地址,默认本机
        if (isset($config['host'])) {
            $host = $config['host'];
            unset($config['host']);
        } else {
            $host = 'localhost';
        }

        //端口,默认27017
        if (isset($config['port'])) {
            $port = $config['port'];
            unset($config['port']);
        } else {
            $port = 27017;
        }

        //数据库真实名称,默认使用别名
        if (isset($config['database'])) {
            unset($config['database']);
        }

        //是否是默认数据库
        if (isset($config['default'])) {
            unset($config['default']);
        }

        //本数据库中包含哪些表
        if (isset($config['tables'])) {
            unset($config['tables']);
        }

        //创建并记录 连接句柄
        return $clients[$dbAlias] = new Client('mongodb://' . $host . ':' . $port, $config);
    }

    /**
     * 创建一个Mongo数据库对象
     * @param string $dbAlias 数据库别名
     * @return Database
     */
    public static function database(string $dbAlias): Database
    {
        static $dbs = [];
        if (isset($dbs[$dbAlias])) {
            return $dbs[$dbAlias];
        }

        $client = self::client($dbAlias);
        $dbName = configDefault($dbAlias, 'Mongo', $dbAlias, 'database');

        return $client->selectDatabase($dbName);
    }

    /**
     * 创建一个集合对象
     * @param string $tableAlias
     * @return Collection
     */
    public static function collection(string $tableAlias): Collection
    {
        //表的缓存
        static $collections = [];

        //如果已经存在,直接返回
        if (isset($collections[$tableAlias])) {
            return $collections[$tableAlias];
        }

        // 记录开始时间
        $start = timeLog();

        //取Mongo配置
        $config = configMust('Mongo');

        $dbConfig = null; //数据库的配置信息
        $dbAlias = null; //表(Collection)的别名
        $tableName = null; //表的真实名称

        //查找指定表的配置信息在哪个库中
        foreach ($config as $aliasItem => $configItem) {
            //默认数据库
            if (isset($configItem['default']) and $configItem['default']) {
                $dbConfig = $configItem;
                $dbAlias = $aliasItem;
                $tableName = $tableAlias;
            } elseif (isset($configItem['tables'])) { //指定了所包含的表
                $tables = $configItem['tables'];
                if (isset($tables[$tableAlias])) { //别名=>真实名称
                    $dbConfig = $configItem;
                    $dbAlias = $aliasItem;
                    $tableName = $tables[$tableAlias];
                    break;
                } elseif (in_array($tableAlias, $tables)) { //真实名称
                    $dbConfig = $configItem;
                    $dbAlias = $aliasItem;
                    $tableName = $tableAlias;
                    break;
                }
            }
        }

        //没有找到相应的配置信息
        if (!$dbConfig) {
            trigger_error('未找到指定Mongo表的配置信息:' . $tableAlias, E_USER_ERROR);
        }

        // 连接指定数据库
        $db = self::database($dbAlias);

        // 记录调试信息
        debug("connect to mongo $tableAlias ,persist:" . timeLog($start) . 'ms');

        // 返回集合对象
        return $db->selectCollection($tableName);
    }

    /**
     * 将Mongo的查询结果转换成数组
     * @param \Traversable $traversable
     * @return array
     */
    public static function result(\Traversable $traversable): array
    {
        $ret = [];
        foreach ($traversable as $k => $v) {
            $ret[$k] = $v;
        }
        return json_decode(json_encode($ret),true,20,JSON_OBJECT_AS_ARRAY);
    }

//    /**
//     * 当前集合对象
//     * @var Collection
//     */
//    private $collection;
//
//    // 当前集合名称
//    private $collectionName;
//
//    /**
//     * 构造方法
//     * @param string $name 集合名称
//     */
//    public function __construct(string $name)
//    {
//        $this->collectionName = $name;
//        $this->collection = self::collection($name);
//    }
//
//    /**
//     * 返回当前集合对象
//     */
//    public function getCollection(): Collection
//    {
//        return $this->collection;
//    }
//
//    /**
//     * 转接请求到集合对象上
//     * @param string $name
//     * @param array $arguments
//     * @return mixed
//     */
//    public function __call(string $name, array $arguments)
//    {
//        //请求转接
//        return $this->collection->$name(...$arguments);
//    }
//
//    /**
//     * 此操作对findAndModify的返回结果进行了封装
//     * @param $args array
//     * @return array
//     */
//    public function update(...$args): ?array
//    {
//        //请求转接
//        return $this->collection->updateMany(...$args);
//    }
//
//    /**
//     * 此操作对find方法进行了封装
//     * @param $args array
//     * @return array
//     */
//    public function select(...$args): array
//    {
//
//        //生成请求游标
//        $cursor = $this->collection->find(...$args);
//
//        //逐条取出结果,构造数组
//        $result = [];
//
//        //无限循环获取游标
//        while (true) {
//            //如果超时,报错
//            try {
//                $hasNext = $cursor->hasNext();
//            } catch (\MongoException $e) {
//                trigger_error('MongoDB 访问超时', E_USER_ERROR);
//                exit;
//            }
//
//            //如果没有下一条,跳出循环
//            if (!$hasNext) {
//                break;
//            }
//
//            //如果超时,报错
//            try {
//                $result[] = $cursor->getNext();
//            } catch (\MongoException $e) {
//                trigger_error('MongoDB 访问超时', E_USER_ERROR);
//            }
//        }
//
//        return $result;
//    }
//
//    /**
//     * 此操作对findOne方法进行了封装
//     */
//    public function row(): ?array
//    {
//        //转接请求
//        return call_user_func_array([
//            $this->collection,
//            'findOne'
//        ], func_get_args());
//    }
}
