                <div class="form-group">
                    <label class="col-xs-2 control-label {$notNull}">{$title}：</label>
                    <div class="col-xs-3">
                        <select title="{$title}" class="form-control crudField" name="{$name}" id="{$name}">
                            {foreach({$list} as $p)}
                                <option value="{$p}" {if("{$p}"=="{$default}")}selected{/if}>{$p}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
