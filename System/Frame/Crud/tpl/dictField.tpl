        //字典字段[{$fieldName}]的字典数据
        $dict['{$fieldName}']=explode('{$separator}',table('{$table}')->get('{$contentField}',['{$nameField}'=>'{$name}']));
