
    /**
    * 显示[{$title}]页面
    */
    public function edit()
    {
        //获取要编辑数据的ID
        $id=$this->getId();

        //获取所有需要外键的字段的键值信息
        $foreign=[];

{$foreign}
        //获取所有需要字典的字段的数据信息
        $dict=[];

{$dict}

        //设置出错跳回的页面
        $this->setBack();

        //显示添加页面
        display(null,[
            'foreign'=>$foreign,
            'dict'=>$dict,
            'row'=>{$upper}Table::instance()->row('*',['{$primaryKey}'=>$id]),
            'urlIndex'=>$this->getIndex()
        ]);
    }

    /**
    * 执行[{$title}]操作
    */
    public function editSubmit(){
        //获取请求参数
        $data=$this->input('edit');

        //修改数据
        TableLog::title('修改-{$name}');
        $ret={$upper}Table::instance()->update($data,['{$primaryKey}'=>$data['{$primaryKey}']]);

        //修改失败
        if(!$ret){
            $this->error('网络异常,请稍候再试');
        }

        //显示成功消息,并跳转到之前setIndex处
        $this->ok('{$title}成功',$this->getIndex());
    }
