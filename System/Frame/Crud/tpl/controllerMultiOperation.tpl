
    /**
    * 执行多选 [{$title}] 操作
    */
    public function {$action}(){
        //从请求参数中获取要操作的多行数据的ID数组
        $ids=$this->getArrayMust('ids');

        //@TODO 此处添加操作内容
        dump($ids);exit;

        //操作完成,显示成功并跳转到之前setBack处
        $this->ok('{$title}成功');
    }