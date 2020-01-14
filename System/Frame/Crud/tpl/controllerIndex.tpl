    /**
    * 列表及搜索
    */
    public function index()
    {
        //如果是搜索,转换成GET方式,以便以后后退
        $this->forceGet();

        //获取全部搜索参数
        $search=[];
        {$searchFields}

        //获取全部提交参数  submit/export/print
        $submit = $this->getEnum('submit',['search','export','print']);

        //如果在列表页点击导出按钮
        if($submit == 'export'){
            self::export($search);
            return;
        }

        //如果在列表页点击打印按钮
        if($submit=='print'){
            self::printPage($search);
            return;
        }

        //设置回退点(错误返回点,列表返回点)
        $this->setBack();
        $this->setIndex();

        //分页获取数据
        $data={$upper}Table::instance()->search($search);

        //获取所有需要外键的字段的键值信息
        $foreign=[];

{$foreign}
        //获取所有需要字典的字段的键值信息
        $dict=[];
{$dict}
        //显示视图
        display('', [
            'data' => $data,
            'foreign'=>$foreign,
            'dict'=>$dict,
            'controller' => $this
        ]);
    }