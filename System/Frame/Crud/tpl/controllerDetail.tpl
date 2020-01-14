    /**
    * 显示[{$title}]页面
    */
    public function detail()
    {
        //获取一行数据
        $row={$upper}Table::instance()->row('*',['{$primaryKey}'=>$this->getId()])->toRow(){$foreignMapCode};

        {$foreignMapArray}
        display(null,[
            'row'=>$row,
            'foreign'=>$foreignMap,
            'controller'=>$this,
            'urlIndex'=>$this->getIndex()
        ]);
    }
