<div class="bread-crumb">
    <ul class="breadcrumb">
        {let($i=0)}
        {foreach($crumbs as $name=>$url)}
            <li>
                {if($i==0)}
                    <span class="glyphicon glyphicon-home"></span>
                {/if}
                {if($i<count($crumbs)-1)}
                    <a href="{$url}">
                        {$name}
                    </a>
                {else}
                    {$url}
                {/if}
            </li>
            {let($i++)}
        {/foreach}

        {#额外表操作#}
        {if($config->tableOperations)}
            {foreach(array_reverse($config->tableOperations) as $name=>$action)}
                <a href="{$controller->url('','table',['action'=>$action])}" class="btn btn-info right">{$name}</a>
            {/foreach}
        {/if}
    </ul>

</div>