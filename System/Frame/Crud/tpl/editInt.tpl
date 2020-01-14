                <div class="form-group">
                    <label class="col-xs-2 control-label {$notNull}">{$title}：</label>
                    <div class="col-xs-3">
                        <input name="{$name}" type="text" class="form-control crudField" id="{$name}" placeholder="{$title}" value="{$row->{$name}}">
                        <span class="crudFieldMsg alert-danger"></span>
                    </div>
                </div>
