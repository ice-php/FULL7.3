<?php
namespace Program\Module\Admin\Model;

use function icePHP\Frame\Functions\dump;
use function icePHP\Frame\Functions\json;
use function icePHP\Frame\Page\page;
use function icePHP\Frame\Table\table;
use Program\Table\AdminUserTable;
use Program\Table\UserTable;

/**
 * 模块Admin的控制器基类
 */
class AdminUserModel extends AdminBaseModel
{
    //列表默认行数
    public static $_pageSize = 10;

    static public function listUser ($name)
    {
        $where = [];
        if ($name) {
            $where[] = [
                'id like'      => "%" . $name . "%",
                'account like' => "%" . $name . "%",
            ];
        }
        $count = UserTable::instance()->count($where);
        page(self::$_pageSize, 'id', 'desc')->count($count);
        page()->where(['name' => $name]);

        return UserTable::instance()->select(
            ['*'],
            $where,
            'id desc',
            page()->limit()
        );
    }

    static public function listAdminUser($account):array
    {
        $where = [];
        if ($account) {
            $where['account like'] = "%" . $account . "%";
        }
        $count = AdminUserTable::instance()->count($where);
        page(self::$_pageSize, 'id', 'asc')->count($count);
        page()->where(['account' => $account]);
        return AdminUserTable::instance()
            ->select(['*'], $where, 'id asc', page()->limit())
            ->map('admin_user_group', ['id' => 'adminId'], ['groupId'])
            ->map('admin_group', ['groupId' => 'id'], ['name' => 'groupName'])
            ->toArray();

    }

    /**
     * 后台用户是否能登录
     * @param $userName
     * @param $password
     * @return  array|string
     */
    static public function loginAdmin($userName, $password)
    {
        $info = AdminUserTable::instance()->row(['id', 'account', 'password'], ['account' => $userName]);
        if (!$info or $info->isEmpty()) {
            return '用户名错误';
        }
        $pass = password_verify($password, $info->password);
        if (!$pass) {
            return '密码错误';
        }
        return $info->toArray();
    }

    /**
     * 查看指定用户的全部功能点权限
     * @param $userId int 用户编号
     * @return array  功能点编号数组
     */
    private static function myAuth(int $userId): array
    {
        //获取用户所有的全部角色组编号
        $groupIds = self::colGroupByUser($userId);

        //用户不属于任何功能组,则没有权限
        if (!$groupIds) {
            return [];
        }

        //用户的全部功能点编号
        $funcIds = self::colFuncByGroup($groupIds);

        return $funcIds;
    }

    /**
     * 判断用户是否有指定功能点(数组)的权限
     * @param $userId int 用户编号
     * @param $functions array 功能点名称数组
     * @return array
     */
    public static function mustAuth($userId, array $functions): array
    {
        //计算用户拥有的功能点权限与要求的功能点权限之间的交集
        return array_intersect(self::myAuth($userId), table('admin_func')->col('id', ['name' => $functions]));
    }

    /**
     * 检查指定用户是否有指定的MCA的权限
     * @param $userId int 用户编号
     * @param $m string 模块名称
     * @param $c string 控制器名称
     * @param $a string 动作名称
     * @return bool
     */
    public static function hasAuth($userId, $m, $c, $a):bool
    {
        //用户编号
        $userId = intval($userId);

        //MCA对应的功能点
        $funcId = self::searchFunc($m, $c, $a);

        //如果未将MCA指定功能点,则表示此MCA所有人可用
        if (!$funcId) {
            return true;
        }

        //查看我拥有的全部权限
        $funcIds = self::myAuth($userId);

        if (!$funcIds) {
            return false;
        }

        //看MCA指定的功能点是否被用户所拥有
        return in_array($funcId, $funcIds);
    }

    /**
     * 根据MCA,获取功能点的编号,内存缓存
     * @param $m string
     * @param $c string
     * @param $a string
     * @return int
     */
    private static function searchFunc($m, $c, $a): int
    {
        //会话内存缓存
        static $cache = [];

        //读取全部数据
        if (!$cache) {
            $cache[] = '';
            $result = table('admin_func')->selectAll('id,m,c,a');
            foreach ($result as $row) {
                $cache[$row['m'] . '/' . $row['c'] . '/' . $row['a']] = $row['id'];
            }
        }

        //缓存 的键
        $key = $m . '/' . $c . '/' . $a;
        if (isset($cache[$key])) {
            return $cache[$key];
        }
        return 0;
    }

    /**
     * 列表所有指定功能组的功能点编号
     * @param $groupId array 功能组编号
     * @return array
     */
    public static function colFuncByGroup(array $groupId): array
    {
        //会话期内存缓存
        static $all = [];

        //缓存 的索引
        $key = json($groupId);

        //如果没有缓存 ,则取出并缓存
        if (!isset($all[$key])) {
            $all[$key] = table('admin_group_func')->col('funcId', ['groupId' => $groupId]);
        }
        return $all[$key];
    }

    /**
     * 初始化更新 当前控制器的 所有功能点(动作)
     * @param string $module 当前模块
     * @param string $controller 当前控制器
     * @param $newFunctions array
     */
    public static function initFunc($module, $controller,array $newFunctions):void
    {
        //功能点表
        $tFunc = table('admin_func');

        //原来有的A的列表
        $oldFuncAs = table('admin_func')->col('a', ['m' => $module, 'c' => $controller]);

        //遍历新的
        foreach ($newFunctions as $name => $note) {
            //如果原来有更新
            if (in_array($name, $oldFuncAs)) {
                $tFunc->update(['name' => $note], ['m' => $module, 'c' => $controller, 'a' => $name]);
            } else {
                $tFunc->insert(['name' => $note, 'm' => $module, 'c' => $controller, 'a' => $name]);
            }
        }

        //遍历旧的,如果新的没有,则删除
        foreach ($oldFuncAs as $oldFunc) {
            if (!isset($newFunctions[$oldFunc])) {
                $tFunc->delete(['m' => $module, 'c' => $controller, 'a' => $oldFunc]);
            }
        }

        //查看 有没有一个 叫做 "全部资源" 的任务,如果没有,建一个
        $tGroup = table('admin_group');
        $allId = $tGroup->getInt('id', ['name' => '超级管理员']);
        if (!$allId) {
            $allId = $tGroup->insert(['name' => '超级管理员']);
        }

        //取全部功能点
        $allFunctions = $tFunc->col('id', '1=1');

        //将全部功能点分配 给"超级管理员"的功能组
        self::updateGroupFunc($allId, $allFunctions);

        //用户1,有且只有 "超级管理员"这一个用户组
        self::updateUserGroup(1, [$allId]);
    }


    /**
     * 更新一个功能组的功能对应
     * @param $groupId int 功能组编号
     * @param $newFuncIds array [int]  功能编号 数组
     */
    public static function updateGroupFunc($groupId, array $newFuncIds): void
    {
        self::updateRelation('admin_group_func', 'groupId', 'funcId', $groupId, $newFuncIds);
    }

    /**
     * 更新用户的用户组分配
     * @param $adminId int 用户编号
     * @param array $newGroupIds 用户组编号数组
     */
    public static function updateUserGroup($adminId, array $newGroupIds): void
    {
        self::updateRelation('admin_user_group', 'adminId', 'groupId', $adminId, $newGroupIds);
    }

    /**
     * 更新对应关系表,并记录日志
     * @param $tableName string 表名
     * @param $leftName string 左部字段名
     * @param $rightName string 右部字段名
     * @param $leftId int  左部字段值
     * @param $newRightIds array[int] 右部字段值数组
     */
    private static function updateRelation($tableName, $leftName, $rightName, $leftId, array $newRightIds): void
    {
        //用户组与功能组对应关系表
        $t = table($tableName);

        //原有权限
        $oldRightIds = $t->col($rightName, [$leftName => $leftId]);

        //遍历新的权限
        foreach ($newRightIds as $newId) {
            //如果原来没有,则新增
            if (!in_array($newId, $oldRightIds)) {
                $t->insert([$leftName => $leftId, $rightName => $newId]);
            }
        }

        //遍历旧的权限
        foreach ($oldRightIds as $oldId) {
            //如果现在没有,则删除
            if (!in_array($oldId, $newRightIds)) {
                $t->delete([$leftName => $leftId, $rightName => $oldId]);
            }
        }
    }

    /**
     * 列表指定用户组的所有功能组编号
     * @param $adminId int|array 用户组编号
     * @return array
     */
    public static function colGroupByUser($adminId): array
    {
        //会话期缓存
        static $all = [];

        //缓存 的索引
        $key = json($adminId);

        //如果未缓存 则取出并缓存
        if (!isset($all[$key])) {
            $all[$key] = table('admin_user_group')->col('groupId', ['adminId' => $adminId]);
        }

        //返回
        return $all[$key];
    }

    public static function getCurrentAdminUserFunc()
    {

    }

}