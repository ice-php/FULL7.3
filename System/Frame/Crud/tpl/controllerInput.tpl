
    /**
    * 为添加和编辑方法获取参数
    * @param $action string 动作名称:add/edit
    * @return array [主键值,请求参数数组]
    */
    private function input($action){
        $data=[];
        {$fields}
        //过滤空值
        $ret=array_filter($data);

        //如果是添加,强制删除主键字段,以防恶意入侵
        if($action=='add' and isset($ret['{$primaryKey}'])){
            unset($ret['{$primaryKey}']);
        }
        return $ret;
    }