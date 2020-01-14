

                    {#搜索条件:{$title}({$name})#}
                    {let($v=isset($page->where["{$name}"])?$page->where["{$name}"]:'')}
                    <div class="left inblock">
                        <label class="username control-label">{$title}：</label>
                        <input type="text" value="{$v}" name="{$name}" class="span0" onclick="WdatePicker({dateFmt:'{$format}'})" placeholder="{$title}">
                    </div>