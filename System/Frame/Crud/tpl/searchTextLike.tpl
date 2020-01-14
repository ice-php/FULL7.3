

                    {#搜索条件:{$title}({$name})#}
                    {let($v=isset($page->where["{$name}_like"])?$page->where["{$name}_like"]:'')}
                    <div class="left inblock">
                        <!-- <label class="username control-label">{$title}：</label> -->
                        <input type="text" class="span0" value="{$v}" name="{$name}_like" placeholder="{$title}">
                    </div>