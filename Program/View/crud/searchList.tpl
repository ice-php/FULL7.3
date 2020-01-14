{let($page=icePHP\page())}
{let($v=isset($page->where[$name])?$page->where[$name]:'')}

<div class="left inblock">
    <label class="username control-label">{$title}：</label>
    <select title="{$title}" class="span0" name="site" id="country">
        <option value="">请选择</option>
        {foreach($list as $row)}
            <option value="{$row}"
                    {if($v==$row)}selected="selected"{/if}
            >{$row}</option>
        {/foreach}
    </select>
</div>