                <div class="form-group">
                    <label class="col-xs-2 control-label {$notNull}">{$title}：</label>
                    <div class="col-xs-3">
                        {foreach({$list} as $p)}
                            <span class="selectdot">
                                <input type="radio" name="{$name}" value="{$p}" class="selectdot crudField" {if("{$p}"=="{$default}")}checked{/if}>{$p}
                            </span>
                        {/foreach}
                    </div>
                </div>
