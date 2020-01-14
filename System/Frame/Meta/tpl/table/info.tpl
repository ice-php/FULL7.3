
	/**
	 * 根据ID获取一条记录
	 * @param int $id 编号ID
	 * @return {$recordName}
	 */
	public function info($id)  : {$recordName}
	{
		return $this->row(self::$fields,$id);
	}

	/**
	* 取一条记录,将父类查询结果实例为Row的对象
	* @param $fields mixed 要查询的字段
	* @param $where mixed 查询条件
	* @param $orderBy mixed 排序
	* @return {$recordName}
	*/
	public function row ($fields = null, $where = null, $orderBy = null)  : {$recordName}
	{
		$instance = parent::row($fields,$where,$orderBy)->toRecord('\Program\Record\{$recordName}');
		/**
		 * @var $instance {$recordName}
		 */
		return $instance;
	}