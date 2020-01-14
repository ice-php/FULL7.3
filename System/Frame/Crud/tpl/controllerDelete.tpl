    /**
    * 执行删除操作
    */
    public function remove(){
        //从请求参数中获取要删除数据的行的ID
        $id=$this->getId();

        //删除数据
        TableLog::title('删除-{$name}');
        {$upper}Table::instance()->delete(['{$primaryKey}'=>$id]);

        //显示操作成功并跳转到之前setBack处
        $this->ok('{$title}成功');
    }