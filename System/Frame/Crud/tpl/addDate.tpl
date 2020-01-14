                <div class="form-group">
                    <label class="col-xs-2 control-label {$notNull}">{$title}：</label>
                    <div class="col-xs-3">
                        <input type="text"  name="{$name}" class="form-control crudField" onclick="WdatePicker({dateFmt:'{$format}'})" placeholder="{$title}" value="{$default}">
                        <span class="crudFieldMsg"></span>
                    </div>
                </div>
