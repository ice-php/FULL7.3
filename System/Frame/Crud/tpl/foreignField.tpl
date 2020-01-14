        //外键字段[{$fieldName}]的取值
        $foreign['{$fieldName}']=array_column(table('{$table}')->select('{$key},{$value}','{$where}','{$order}')->toArray(),'{$value}','{$key}');
