
	<div class="col-md-12" role="complementary" id="publicParam" style="z-index: 1000; border-radius: 10px;background-color: #D6E9C6;border-color: #d6e9c6;margin-bottom: 10px;">
		<div  style="margin:10px 0;width: 250px;float:left">
			<select  class="form-control" name="module" id="module">
				{foreach($modules as $vo)}
					<option value="{$vo}" >模块选择 {$vo} </option>
				{/foreach}
			</select>
		</div>

		<div  style="margin-top:17px;width: 250px;float:left;margin-left: 20px;">
			<label class="radio-inline">
				<input type="radio" name="multi" id="inlineRadio1" value="1"> 多选
			</label>
			<label class="radio-inline">
				<input type="radio" name="multi" id="inlineRadio2" value="2"> 多选删除
			</label>
			<label class="radio-inline">
				<input type="radio" name="multi" id="inlineRadio3" value="3" checked="checked"> 都不要
			</label>
		</div>

		<div  style="margin:10px 0;width: 300px;float:left">
			<input type="text" class="form-control" value="" id="rowOperations" placeholder="额外操作例如: 冻结|lock, 解冻|unlock">
		</div>

		<button type="button" id="doCreate" class="btn btn-primary" style="margin-top: 10px;margin-left: 20px;"> 创建CRUD </button>

		<button type="button" id="doHide" class="btn btn-default" style="display:none;margin-top: 10px;margin-left: 20px;">隐藏信息框</button>

		<div class="alert alert-success" role="alert" id="result" style="display:none;border-radius: 20px;margin: 10px 0px;background-color: #ecf7e3">

		</div>
	</div>

	<table class="table table-hover">
		<tr>
			<th class="col-md-1">字段名</th>
			<th class="col-md-2">名称</th>
			<th class="col-md-1">搜索项</th>
			<th class="col-md-1"><label style="cursor: pointer"><input type="checkbox" class="listsAll" checked="checked"> 列表项</label></th>
			<th class="col-md-1"><label style="cursor: pointer"><input type="checkbox" class="addAll" checked="checked"> 增/改项</label></th>
			<th class="col-md-6">外键关联配置</th>
		</tr>
		{foreach($info['meta'] as $col)}
			<tr class="fieldTr" data-field="{$col['name']}">
				<td>{$col['name']}</td>
				<td> <input type="text" class="form-control description" data-last="{$col['description']}" value="{$col['description']}"> </td>
				<td>
					{if($col['name'] == 'created' || $col['name'] == 'updated')}
						<div class="checkbox has-error disabled">
							<label>
								<input type="checkbox" value="0" disabled>
								不可选
							</label>
						</div>
					{else}
						<div class="checkbox has-success">
							<label>
								<input type="checkbox" class="search" value="1">
								可选择
							</label>
						</div>
					{/if}
				</td>
				<td>
					<div class="checkbox has-success">
						<label>
							<input type="checkbox"  class="lists" value="1" checked="checked">
							可选择
						</label>
					</div>
				</td>
				<td>
					<div class="checkbox has-success">
						<label>
							<input type="checkbox"  class="add" value="1" checked="checked">
							可选择
						</label>
					</div>
				</td>
				<td>
					{if(strrpos($col['name'], "Id") !== false)}
					<input type="text" class="form-control foreign1" value="" placeholder="外键表" style="width: 150px;float: left;margin-right: 5px">
					<input type="text" class="form-control foreign2" value="" placeholder="关联字段" style="width: 150px;float: left;margin-right: 5px">
					<input type="text" class="form-control foreign3" value="" placeholder="显示字段" style="width: 150px;float: left">
					{/if}
				</td>
			</tr>
		{/foreach}
	</table>


	<script>
        var urlCreate = "{$controller->url('crud', 'doCreate')}";

        $(function() {
            var offsetTop = $("#page").offset().top;

            $(window).scroll(function(){
                //获取元素到顶部的高度
                var scroll = $(window).scrollTop();
                if(scroll > offsetTop){
                    var width = $("#tableInfo").width();
                    var tempWidth = width - 30;
                    $("#publicParam").css({top:0,position:"fixed",width:tempWidth});
                }else{
                    $("#publicParam").css({top:0,position:"initial",width:"100%"});
                }
            });
/*

            //窗口变化监听
		   	window.onresize = function(){
                width = $("#tableInfo").width();
        	}
*/

        	$(".foreign1").blur(function(){
                var v = $(this).val();
                if(v != ''){
                    var obj = $("#tableName option[value='" + v + "']");
                    if(obj.length == 0){
                        alertMsg('表不存在');
                        $(this).focus();
                        return false;
                    }
                }
            })

            $(".foreign2").blur(function(){
                var v = $(this).val();
                var reg = /^[a-zA-Z_]+$/;
                if(v != '' && !reg.test(v)){
                    alertMsg('关联字段格式错误');
                    $(this).focus();
                    return false;
                }
            })

            $(".foreign3").blur(function(){
                var v = $(this).val();
                var reg = /^[a-zA-Z_]+$/;
                if(v != '' && !reg.test(v)){
                    alertMsg('显示字段格式错误');
                    $(this).focus();
                    return false;
                }
            })

        	$("#doCreate").click(function(){
				//获取表名
				var tableParam = $("#tableParam").val();
				if(tableParam == ''){
					alertMsg('请确认选择表名');return false;
				}

				//获取模块
				var module = $("#module").val();

				//获取 多选 删除
				var multi = $("input[name='multi']:checked").val();

				//获取额外操作
				var rowOperations = $("#rowOperations").val();

                var reg = /^([\u4e00-\u9fa5]+\|[a-zA-Z_]+,?)+$/;

                //额外操作判断
				if(rowOperations != '' && !reg.test(rowOperations)){
                    alertMsg('请输入正确的额外操作');
                    $("#rowOperations").focus();
                    return false;
				}

                var data = { "description": [], "search" : [] , "lists" : [] , 'add' : [], 'foreign' : []};
                var i = 0;

				//获取别名项  搜索项 列表项 增改项 外键关联配置
                $(".fieldTr").each(function(){
                    var self = $(this);
                    var fiildName = self.data('field');

                    //别名项
                    var desObj = self.find('.description');
                    //发生改变 写入
                    var currentVal = desObj.val();
                    if(desObj.data('last') != currentVal){
                        data.description.push({"name":fiildName, "value":currentVal});
                    }

                    //搜索项
                    var searchObj = self.find('.search');
                    if(searchObj.is(":checked") && searchObj.val() == 1){
                        data.search.push(fiildName);
                    }

                    //列表项
                    var listsObj = self.find('.lists');
                    if(listsObj.is(":checked")){
                        data.lists.push(fiildName);
                    }

                    //增改项
                    var addObj = self.find('.add');
                    if(addObj.is(":checked")){
                        data.add.push(fiildName);
                    }

                    //外键关联配置
                    if(self.find('.foreign1').length == 1){
                        var foreign1 = self.find('.foreign1').val();
                        var foreign2 = self.find('.foreign2').val();
                        var foreign3 = self.find('.foreign3').val();

                        //只有三个值都填写了 才存入
                        if(foreign1 != '' && foreign2 != '' && foreign3 != '' ){
                            var value = foreign1 + ',' + foreign2 + ',' + foreign3;
                            data.foreign.push({"name":fiildName, "value":value});
                        }
                    }
                })

                if(data.lists.length == 0){
                    alertMsg('请至少选择一个列表项');return false;
                }

                if(data.add.length == 0){
                    alertMsg('请至少选择一个增改项');return false;
                }

                var dataStr = JSON.stringify(data);

                var load = new Loading();
                load.init({
                    target: "#page"
                });
                load.start();

                $("#result").hide();

                $.ajax({
                    url: urlCreate,
                    data:{table:tableParam, module:module, multi:multi, rowOperations:rowOperations, data:dataStr},
                    timeout: 10000,
                    cache: false,
                    type: "post",
                    dataType: "html",
                    success: function (res){
                        $("#result").html(res).fadeIn();
                        $("#doHide").text('隐藏信息框').fadeIn();
                        load.stop();
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown){
                        if(textStatus=='timeout'){
                            alertMsg('请求超时，请稍后再试');
                            load.stop();
                            return false;
                        }
                    }
                });

                console.log(data);

			})


            $("#doHide").click(function(){
                if($(this).text() == "隐藏信息框"){
                    $(this).text('显示信息框');
                    $("#result").fadeOut();
                }else{
                    $(this).text('隐藏信息框');
                    $("#result").fadeIn();
                }
            })

            {#实现反选功能#}
            $(".listsAll").click(function () {
                $(".lists").prop('checked', $(this).prop('checked'))
            });

            {#实现反选功能#}
            $(".addAll").click(function () {
                $(".add").prop('checked', $(this).prop('checked'))
            });


            $(".add").click(function(){
                var nowChecked = $(".add:checked");
                $(".addAll").prop('checked', nowChecked.length > 0 ? true : false);
            })

            $(".lists").click(function(){
                var nowChecked = $(".lists:checked");
                $(".listsAll").prop('checked', nowChecked.length > 0 ? true : false);
            })

        })
	</script>

