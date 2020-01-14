
    /**
    * 显示添加页面
    */
    public function add()
    {
        //获取所有需要外键的字段的键值信息
        $foreign=[];

{$foreign}
        //获取所有需要字典的字段的数据信息
        $dict=[];

{$dict}

        //设置出错跳回的页面
        $this->setBack();

        //显示添加页面
        display(null,['foreign'=>$foreign,'dict'=>$dict,'urlIndex'=>$this->getIndex()]);
    }

    /**
    * 执行添加操作
    */
    public function addSubmit(){
        //获取请求参数
        $data=$this->input('add');

        //插入数据
        TableLog::title('添加-{$name}');
        $id={$upper}Table::instance()->insert($data);

        //插入失败
        if(!$id){
            $this->error('网络异常，请稍后再试');
        }

        //插入成功,并跳转到之前setIndex处
        $this->ok('{$title}成功',$this->getIndex());
    }