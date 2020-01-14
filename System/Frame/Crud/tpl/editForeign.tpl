                <div class="form-group">
                    <label class="col-xs-2 control-label {$notNull}">{$title}：</label>
                    <div class="col-xs-3">
                        <select title="{$title}" class="form-control crudField" name="{$name}" id="{$name}">
                            {foreach($foreign['{$name}'] as $k=>$p)}
                                <option value="{$k}" {if($row->{$name}==$k)} selected {/if} >{$p}</option>
                            {/foreach}
                        </select>
                        <span class="crudFieldMsg alert-danger"></span>
                    </div>
                </div>
