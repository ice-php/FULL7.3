    /**
    * 根据搜索条件显示打印页面
    * @param $search array 搜索条件
    */
    private function printPage(array $search){
        //防止执行超时
        set_time_limit(0);

        //全部需要打印的字段
        $fields={$fields};

        //获取要打印的数据
        $data={$upper}Table::instance()->export($search,array_keys($fields));

        //获取所有需要外键的字段的键值信息
        $foreign=[];

{$foreign}
        display(null,[
            'description'=>'{$description}',
            'foreign'=>$foreign,
            'data'=>$data
        ]);
    }