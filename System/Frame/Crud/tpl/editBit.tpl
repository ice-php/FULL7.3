                <div class="form-group">
                    <label class="col-xs-2 control-label {$notNull}">{$title}：</label>
                    <div class="col-xs-3">
                        {foreach({$list} as $p)}
                            <label class="radio-inline"><input class="crudField" type="radio" name="{$name}" value="{$p}" {if($row->{$name}==$p)} class="selectdot" {endif}>{$p}</label>
                        {/foreach}
                    </div>
                </div>
