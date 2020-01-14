<?php
declare(strict_types=1);

namespace icePHP\Frame\Mongo;

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;

/**
 * 生成一个Mongo表对象
 * 这是一个SMongo的快捷入口
 * @param string $name Mongo表名
 * @return Mongo
 */
//function mongo(string $name): Mongo
//{
//    return new Mongo($name);
//}

/**
 * 获取Mongo客户端对象
 * @param string $name 数据库别名
 * @return Client
 */
function mongoClient(string $name): Client
{
    return Mongo::client($name);
}

/**
 * 获取Mongo数据库对象
 * @param string $name 数据库别名
 * @return Database
 */
function mongoDatabase(string $name): Database
{
    return Mongo::database($name);
}

/**
 * 获取Mongo集合对象
 * @param string $name 集合对象别名
 * @return Collection
 */
function mongoCollection(string $name): Collection
{
    return Mongo::collection($name);
}

/**
 * 将Mongo查询结果转换成数组
 * @param \Traversable $result
 * @return array
 */
function mongoResult(\Traversable $result):array {
    return Mongo::result($result);
}