                <div class="form-group">
                    <label class="col-xs-2 control-label {$notNull}">{$title}：</label>
                    <div class="col-xs-3">
                        <textarea name="{$name}" class="form-control crudField" style="margin-left:12px" id="{$name}" placeholder="{$title}">{$row->{$name}}</textarea>
                        <span class="crudFieldMsg alert-danger"></span>
                    </div>
                </div>
