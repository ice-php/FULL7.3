
                    {#搜索条件:{$title}({$name})#}
                    {let($v=isset($page->where["{$name}"])?$page->where["{$name}"]:'')}
                    <div class="left inblock">
                        <label class="username control-label">{$title}：</label>
                        <select title="{$title}" class="span0" name="{$name}" id="{$name}">
                            <option value="">请选择</option>
                            {foreach($dict['{$name}'] as $show)}
                                <option value="{$show}"
                                        {if($v==$show)}selected="selected"{/if}
                                >{$show}</option>
                            {/foreach}
                        </select>
                    </div>