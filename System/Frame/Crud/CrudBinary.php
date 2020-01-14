<?php
declare(strict_types=1);

namespace icePHP\Frame\Crud;

/**
 * Class SCrudBinary
 * 包括binary,varbinary,blob,tinyblob,mediumblob,longblob
 */
class CrudBinary extends CrudField
{
    /**
     * 二进制类型不参与添加与编辑
     * @return string 生成的模板代码
     */
    public function _input():string
    {
        return '';
    }

    /**
     * 二进制类型不能与参与搜索
     */
    public function _searchEqual():array
    {
        trigger_error('CRUD搜索条件中,二进制相关类型不可参与搜索',E_USER_ERROR);
        exit;
    }
}