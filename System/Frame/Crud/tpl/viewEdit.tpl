{#{$title} 模板文件#}

{#页头#}
{include('common/head',['title'=>'{$title}'])}

<div class="rightiframe_con">
    {#面包屑#}
    {$crumbs}

    <div class="panel panel-default">
        {#消息容器#}
        {include('crud/msgContainer')}

        <div class="panel-body">
            {#表单#}
            <form action="" method="post" enctype="multipart/form-data"  data-need-verify-part="form" class="form-horizontal form-item">
{$fields}
                {#提交按钮#}
                <div class="form-group">
                    <label class="col-xs-2 control-label "></label>
                    <input id="crudSubmit" type="submit" name="edit" class="once btn btn-primary" value="保存"/>
                    <input id="goBack" type="button" name="" class="once btn btn-default" value="返回"/>
                    </div>
                    <script>
                        $(function(){
                            {$validate}
                            //提交时进行输入数据检查
                            $('#crudSubmit').click(function(){
                                var error=false;

                                //检查每一个需要检查的表单元素
{$validateSubmit}
                                //如果有错误
                                if(error){
                                    alert('输入数据有错误,请修改后再次提交');
                                    //跳转到第一个出错的表单元素
                                    error.focus();
                                    return false;
                                }

                                //没有错误,可以提交
                                return true;
                            });

                            //后退按钮
                            $('#goBack').click(function(){
                                //window.history.go(-1);
                                window.location.href='{$urlIndex}';
                            })
                        })
                    </script>
                </div>
            </form>
        </div>
    </div>
</div>
