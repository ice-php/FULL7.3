<?php

declare(strict_types = 1);

namespace Program\Model;

use icePHP\Frame\MVC\Model;
use icePHP\Frame\Table\Table;

/**
 * 模型基类,扩展一些通用功能
 */
class BaseModel extends Model
{
	
	/**
	 * 判断 指定表中某查询条件是否重复
	 * @author vYao
	 * @param  $table Table 表对象
	 * @param  $where array 条件
	 * @param  $id    int   可以排除在外的记录ID
	 * @return bool
	 */
	public static function existInTable(Table $table, array $where, int $id = 0)
	{
		//计算查询条件
		if($id){
			$where['id !='] = $id;
		}
		
		//判断 是否存在
		return $table->exist($where);
	}
}