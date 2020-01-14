
        //从请求参数中获取搜索条件:[{$title}]的值,{$table}表的外键
        $search['{$name}']=$this->getForeign('{$name}','{$table}','{$key}','{$where}');
