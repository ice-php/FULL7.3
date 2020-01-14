

                    {#搜索条件:{$title}({$name})#}
                    {let($begin='{$name}_begin')}
                    {let($end='{$name}_end')}
                    {let($b=isset($page->where[$begin])?$page->where[$begin]:'')}
                    {let($e=isset($page->where[$end])?$page->where[$end]:'')}
                    <div class="left inblock">
                        <label class="username control-label">{$title}：</label>
                        <input type="text" value="{$b}" name="{$begin}" class="span0" onclick="WdatePicker({dateFmt:'{$format}'})" placeholder="{$title}下限">
                        -
                        <input type="text" value="{$e}" name="{$end}" class="span0" onclick="WdatePicker({dateFmt:'{$format}'})" placeholder="{$title}上限">
                    </div>