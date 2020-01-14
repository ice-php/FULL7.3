
<div class="col-md-12" role="complementary">
	<table class="table table-hover col-md-3"  style="margin-top:10px;">
        {foreach($dbs as $vo)}
			<tr style="font-size:20px;font-weight: bold;height: 50px;line-height: 50px;">
				<th style="text-align:right;" class="col-md-3">数据库</th>
				<th class="col-md-6">{$vo['database']} </th>
				<th class="col-md-3"></th>
			</tr>
			<tr>
				<th style="text-align:right" class="col-md-3">表名</th><th class="col-md-6">说明</th><th class="col-md-3"></th>
			</tr>
			{foreach($tables as $k=>$table)}
			{if($vo['database'] == $table['database'])}
			<tr class="notice" data-table="{$table['name']}" style="cursor: pointer">
				<td style="text-align:right">{$table['name']}</td>
				<td><a>{$table['desc']}</a></td>
				<td></td>
			</tr>
			{/if}
			{endforeach}
        {endforeach}
	</table>
</div>

<div class="col-md-12" role="complementary">
    {foreach($tables as $k=> $table)}
		<div class="alert alert-success" role="alert" style="margin-top: 10px;" id="{$table['name']}div">
			<a name="t{$k}" href="javascript:void(0)"> 数据库：{$table['database']} - 表名：<strong>{$table['name']}</strong> - <strong>说明：{$table['desc']}</strong></a>
		</div>
		<table class="table table-hover">
			<tr>
				<th class="col-md-4">字段名</th>
				<th class="col-md-4">数据类型</th>
				<th class="col-md-4">说明</th>
			</tr>
            {foreach($table['meta'] as $col)}
				<tr>
					<td>{$col['name']}</td>
					<td>{icePHP\Frame\Meta\Meta::mapType($col)}</td>
					<td>{$col['description']}</td>
				</tr>
            {/foreach}
		</table>
    {/foreach}
</div>


<script>
    $(function() {
        //获取浏览器高度
        //var documentHeight = $(window).height() - 100;
        //$("#nav").css({height:documentHeight});

        //$('.notice').popover();

        /* $(window).scroll(function(){
             //获取元素到顶部的高度
             var offsetTop = $("#page").offset().top;
             var scroll = $(window).scrollTop();
             if(scroll > offsetTop){
                 var top = 0;
             }else{
                 var top = offsetTop - scroll;
             }
             $("#nav").css({top:top});
        });
*/
        /* //窗口变化监听
         window.onresize = function(){
             //获取浏览器高度
             var documentHeight = $(window).height() - 100;
             $("#nav").css({height:documentHeight});
        }*/

        //绑定锚点连接
        $(".notice").click(function(){
            var table = $(this).data('table');
            var doObj = $("#"+table+"div");

            //获取元素到顶部高度
            var offsetTop = doObj.offset().top;

            //scroll 应该到达位置
            $('body,html').animate({scrollTop:offsetTop},500);
        })

    })
</script>