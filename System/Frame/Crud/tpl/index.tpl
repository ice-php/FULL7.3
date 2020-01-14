{#{$title} 模板文件#}

{#页头#}
{include('common/head',['title'=>'{$title}'])}

<div class="rightiframe_con">
    {#面包屑#}
    {$crumbs}

    <div class="panel panel-default">
        {#消息容器#}
        {$msgContainer}

        {#搜索条#}
        {$search}

        {#数据列表#}
        <div class="panel-body">
            <table class="table table-hover">

                {#表头部分#}
                <thead>
                <tr>

                    {#表头的全选按钮#}
                    {$multi}

                    {#表头的行号#}
                    {$rowNo}

                    {#表头的每一个字段#}
                    {$header}

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
                        {$multiOne}

                        {#行号#}
                        {$rowNoOne}

                        {#行数据#}{$row}

                        {#行操作#}
                        <td>{$rowButton}
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>

            {#分页#}
            {include('common/page')}

            {#多选操作#}
            {$multiOperations}

        </div>
    </div>
</div>

<script type="text/javascript">
    $(function(){
        {#实现反选功能#}
        $("#checkall").click(function () {
            $(".checkOne").prop('checked', $(this).prop('checked'))
        });

        {#多选操作#}
        $('.multiChoice').click(function(){
            if($('.checkOne:checked').length<1){
                alert('您没有选择要操作的数据');
                return false;
            }

            var ids=[];
            $('.checkOne:checked').each(function(k){
                ids.push($(this).data('id'))
            });
            window.location=$(this).data('url')+"&ids="+ids.join();
        });

        {#多选删除#}
        $('#multiRemove').click(function(){
            if($('.checkOne:checked').length<1){
                alert('您没有选择要删除的数据');
                return false;
            }
            if(!confirm('您确认要删除这些数据么?')){
                return false;
            }
            var ids=[];
            $('.checkOne:checked').each(function(k){
                ids.push($(this).data('id'))
            });
            window.location="{$controller->url('','removeMulti')}&ids="+ids.join();
        });

        {#二次确认#}
        $('.confirm').click(function(){
            return confirm('您确认要进行此操作么?');
        })
    })
</script>

</body>
</html>
