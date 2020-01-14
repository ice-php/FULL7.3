                <div class="form-group">
                    <label class="col-xs-2 control-label {$notNull}">{$title}：</label>
                    <div class="col-xs-3">
                        <select title="{$title}" class="form-control crudField" multiple="multiple" name="{$name}" id="{$name}">
                            {foreach($dict['{$name}'] as $p)}
                                <option value="{$p}" {if(in_array($p,{$default}))}selected{/if}>{$p}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
