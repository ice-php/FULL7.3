{#{$title} 模板文件#}

{#页头#}
{include('common/head',['title'=>'{$title}'])}

<div class="rightiframe_con">
    {#面包屑#}
    {$crumbs}

    <div class="panel panel-default">
        {#消息容器#}
        {include('crud/msgContainer')}

        <div class="panel-body form-horizontal form-item">
{$fields}
                {#返回按钮#}
                <div class="form-group">
                    <label class="col-xs-2 control-label"></label>
                    <input id="goBack" type="button" name="" class="once btn btn-primary" value="返回"/>
                    <script>
                        $(function(){
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
