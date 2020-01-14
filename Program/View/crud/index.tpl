{#页头#}
{include('crud/head',['config'=>$config])}

<div class="rightiframe_con">
    {#面包屑#}
    {include('crud/crumbs',['crumbs'=>[$config->title['list']],'config'=>$config,'Controller'=>$controller])}
    <div class="panel panel-default">
        {#消息容器#}
        {include('crud/msgContainer')}

        {if($config->getSearch())}
            <div class="panel-body">
                {#搜索表单#}
                <form class="form-horizontal" action="" method="post">
                    <div class="form-group">
                        {#搜索字段#}
                        {foreach($config->getSearch() as $field)}
                            {include($field['tpl'],$field['params'])}
                        {/foreach}

                        {#搜索按钮#}
                        <button type="submit" id="searchSearch" style="margin-left:10px;" class="left inblock btn btn-primary" name="submit" value="search">搜索</button>

                        {#导出按钮#}
                        {if(in_array('导出',$config->operations))}
                            <button id="searchExport" type="submit" style="margin-left:10px;" class="left inblock btn btn-primary" name="submit" value="export">导出</button>
                        {/if}

                        {#打印按钮#}
                        {if(in_array('打印',$config->operations))}
                            <button id="searchPrint" type="submit" style="margin-left:10px;" class="left inblock btn btn-primary" name="submit" value="print">打印</button>
                        {/if}
                    </div>
                </form>
            </div>
        {/if}

        {#数据列表#}
        <div class="panel-body">
            <table class="table table-hover">

                {#表头部分#}
                <thead>
                <tr>

                    {#表头的全选按钮#}
                    {if($config->multi)}
                        <th style="width: 70px;"><span class="checkall"><input type="checkbox" id="checkall" class="checka">全选</span>                    </th>
                    {/if}

                    {#表头的行号#}
                    {if($config->rowNo)}
                        <th>行号</th>
                    {/if}

                    {#表头的每一个字段#}
                    {let($listConfig=$config->getList())}
                    {foreach($listConfig as $field)}
                        <th>
                            {$field->description}
                        </th>
                    {/foreach}

                    <th >操作</th>
                </tr>
                </thead>

                {#行号#}
                {let($rowNo=1)}

                {#表格的数据#}
                <tbody>
                {foreach($data as $row)}
                    <tr>

                        {#行的选择按钮#}
                        {if($config->multi)}
                            <td>
                                <input type="checkbox" class="checkOne" data-id="{$row->id}">
                            </td>
                        {/if}

                        {#行号#}
                        {if($config->rowNo)}
                            <th>{$rowNo++}</th>
                        {/if}

                        {#行数据#}
                        {foreach($listConfig as $field)}
                            <td>{$row[$field->name]}</td>
                        {/foreach}

                        {#行操作#}
                        <td>
                            {#额外行操作#}
                            {if($config->rowOperations)}
                                {foreach($config->rowOperations as $name=>$action)}
                                    <a style="color:blue" href="{$controller->url('','row',['action'=>$action,'id'=>$row->id])}">{$name}</a>&nbsp;
                                {/foreach}
                            {/if}

                            {#行详情#}
                            {if(in_array('详情',$config->operations))}
                                <a style="color:blue" href="{$controller->url('','detail',['id'=>$row->id])}">详情</a>&nbsp;
                            {/if}

                            {#行编辑#}
                            {if(in_array('编辑',$config->operations))}
                                <a style="color:blue" href="{$controller->url('','edit',['id'=>$row->id])}">编辑</a>&nbsp;
                            {/if}

                            {#行删除#}
                            {if(in_array('删除',$config->operations))}
                                <a style="color:blue" class="confirm" href="{$controller->url('','remove',['id'=>$row->id])}">删除</a>&nbsp;
                            {/if}
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>

            {#分页#}
            {include('crud/page')}

            {#多选操作#}
            {if($config->multi)}
                <div class="col-sm-4">

                    {#多选删除#}
                    {if($config->multiDelete)}
                        <button type="button" class="btn btn-primary" id="multiRemove">批量删除</button>
                    {/if}

                    {#额外的多选操作#}
                    {if($config->multiOperations)}
                        {foreach($config->multiOperations as $name=>$action)}
                            <button type="button" class="multiChoice btn btn-primary" id="{$action}" data-action="{$action}">{$name}</button>
                        {/foreach}
                    {/if}
                </div>
            {/if}

        </div>
    </div>
</div>

<script type="text/javascript">
    $(function(){
        //实现反选功能
        $("#checkall").click(function () {
            $(".checkOne").prop('checked', $(this).prop('checked'))
        });
        $('.multiChoice').click(function(){
            if($('.checkOne:checked').length<1){
                alert('您没有选择要操作的数据');
                return false;
            }

            var ids=[];
            $('.checkOne:checked').each(function(k){
                ids.push($(this).data('id'))
            });
            window.location="{$controller->url('','multi')}&action="+$(this).data('action')+"&ids[]="+ids.join();
        })

        $('#searchPrint').click(function(){
            $(this).parent('form').attr('target','_blank');
        })
        $('#searchExport').click(function(){
            $(this).parent('form').attr('target','_blank');
        })
        $('#searchSearch').click(function(){
            $(this).parent('form').attr('target','_self');
        })

    })


</script>

</body>
</html>
