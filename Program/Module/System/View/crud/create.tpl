<link rel="stylesheet" href="{:pathRoot()}/Static/css/select2/select2.min.css" type="text/css" >
<script type="text/javascript" src="{:pathRoot()}/Static/js/select2/select2.full.js"></script>

<div class="col-md-12" role="complementary" style="margin-top: 20px;margin-bottom: 20px;">
	<div class="col-md-3">
		<select  class="crudField" name="tableName" id="tableName" style="width: 100%">
			<option value="">请选择 数据表</option>
            {foreach($tables as $k=> $table)}
                {let($name = array_values($table)[0])}
				<option value="{$name}"> {$table['database']} - {$name} </option>
            {/foreach}
		</select>
		<script>
			$(function() {
				$("#tableName").select2();
			});
		</script>
	</div>

	<button type="button" id="doSet" class="btn btn-primary"> 确认选择 </button>

</div>

<input type="hidden" id="tableParam" value="">

<div class="col-md-12" role="complementary" id="tableInfo">

</div>

<script>
	var urlSetInfo = "{$controller->url('crud', 'setInfo')}";

    $(function() {
        $("#doSet").click(function(){
            var tableName = $("#tableName").val();

            if(tableName == ''){
				alertMsg('请选择数据表再进行操作');return false;
			}

            $("#tableInfo").html('').hide();

            var load = new Loading();
            load.init({
                target: "#page"
            });
            load.start();

            $.ajax({
                url: urlSetInfo,
                data:{tableName:tableName},
                timeout: 10000,
                cache: false,
                type: "post",
                dataType: "html",
                success: function (res){
                    load.stop();
                    $("#tableParam").val(tableName);
                    $("#tableInfo").append(res).fadeIn(1000);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    if(textStatus=='timeout'){
                        alertMsg('请求超时，请稍后再试');
                        $("#tableParam").val('');
                        $("#tableName option:eq(0)").prop("selected", true);
                        load.stop();
                        return false;
                    }
                }
            });

           /* var funcName = function(){

                closeConfirm();

                $.ajax({
                    url: urlTableInfo,
                    data:{tableName:tableName},
                    timeout: 10000,
                    Cache: false,
                    type: "post",
                    dataType: "html",
                    success: function (res){
                        alertMsg('todo');return false;
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown){
                        if(textStatus=='timeout'){
                            alertMsg('请求超时，请稍后再试');return false;
                        }
                    }
                });
            };

            //确认信息
            confirmMsg("<p>确认生成表：" + tableName + " 对应的Crud文件么？</p>", funcName);*/
		});
    });

</script>