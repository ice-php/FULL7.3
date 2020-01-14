{#分页#}
<div class="page juzhong">
    <ul class="pagination">
    {let($spage=icePHP\page())}
    {let($page=$spage->page)}
    {let($params=$spage->where())}
    {if($page >1)}
         {let($params['page']=1)}
        <li><a href="{$spage->url('','','',$params)}">首页</a></li>
        {let($params['page']=$page-1)}
         <li><a href="{$spage->url('','','',$params)}">上一页</a></li>
    {else}
        <li> <span class="disabled first_page">首页</span></li>
        <li><span class="disabled first_page">上一页</span></li>
    {endif}

    {for($i=max($page-5,1);$i<$page;$i++)}
        {let($params['page']=$i)}
        <li> <a href="{$spage->url('','','',$params)}">{$i}</a></li>
    {/for}

        <li><a id="current">{$page}</a></li>

    {for($i=$page+1;$i<=min($page+5,$spage->allPage);$i++)}
        {let($params['page']=$i)}
        <li> <a href="{$spage->url('','','',$params)}">{$i}</a></li>
    {/for}

    {if($page<$spage->allPage)}
        {let($params['page']=$page+1)}
        <li><a href="{$spage->url('','','',$params)}">下一页</a></li>
        {let($params['page']=$spage->allPage)}
        <li>  <a href="{$spage->url('','','',$params)}">尾页</a></li>
    {else}
        <li><span class="disabled first_page">下一页</span></li>
        <li> <span class="disabled first_page">尾页</span></li>
    {/if}
    </ul>
</div>