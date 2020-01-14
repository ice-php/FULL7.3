                <div class="form-group">
                    <label class="col-xs-2 control-label {$notNull}">{$title}：</label>
                    <div class="col-xs-3">
                        <input name="{$name}" type="file" id="{$name}" placeholder="{$title}" onchange="selectImage('{$name}')">
                    </div>
                    <label class="col-xs-2 control-label"></label>
                    <div class="col-xs-3">
                        <img id ="{$name}_view" src="{$row->{$name}}" style="width:250px; margin-top:10px;margin-left: 28px;"/>
                    </div>
                </div>
