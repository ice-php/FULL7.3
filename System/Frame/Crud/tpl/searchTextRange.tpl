

                    {#搜索条件:{$title}({$name})#}
                    {let($begin=.'{$name}_begin')}
                    {let($end='{$name}_end')}
                    {let($b=isset($page->where[$begin])?$page->where[$begin]:'')}
                    {let($e=isset($page->where[$end])?$page->where[$end]:'')}
                    <div class="left inblock">
                        <label class="username control-label">{$title}：</label>
                        <input type="text" class="span0" value="{$b}" name="{$begin}" placeholder="下限">
                        -
                        <input type="text" class="span0" value="{$e}" name="{$end}" placeholder="上限">
                    </div>
