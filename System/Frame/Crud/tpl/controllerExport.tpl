    /**
    * 根据搜索条件导出Excel数据
    * @param $search array 搜索条件
    */
    private function export(array $search){
        //防止执行超时
        set_time_limit(0);

        //头部设定 文件名 时间等
        //写入头部标题
        csvHeader('{$description}');

        //全部需要导出的字段
        $fields={$fields};

        // 打开PHP文件句柄，php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'w');

        //输出表头
        fputcsv($fp, iconvRecursion(array_values($fields)), ',');

        //获取要导出的数据
        $data={$upper}Table::instance()->export($search,array_keys($fields));

        //获取所有需要外键的字段的键值信息
        $foreign=[];

{$foreign}
        //逐行输出
        foreach($data as $row) {
            //调整外键字段
            foreach($row as $k=>$v){
                if(isset($foreign[$k])){
                    $row[$k]=$foreign[$k][$v];
                }
            }
            fputcsv($fp, iconvRecursion($row), ',');

            //刷新输出缓存区
            ob_flush();
            flush();
        }
        exit();
    }