
<div class="col-md-12" role="complementary" style="margin-top: 20px;margin-bottom: 20px;">
	<div class="col-md-6">
		<div class="input-group">
			<input type="text" id="word" class="form-control" placeholder="输入表明模糊查询(注意区分大小写)，可确认后单独处理" aria-describedby="basic-addon2">
			<span class="input-group-addon btn btn-default" id="search">查找</span>
		</div>
	</div>
	<button type="button" id="doAll" class="btn btn-primary"> 创建 / 更新全部 </button>
	<button type="button" id="doClearAll" class="btn btn-warning"> 停止 </button>
</div>

<div class="col-md-12" role="complementary">

	<table class="table table-hover" id="table">
		<tr>
			<th class="col-md-2">表名</th>
			<th class="col-md-4">table处理结果</th>
			<th class="col-md-4">record处理结果</th>
			<th class="col-md-2">操作</th>
		</tr>
        {foreach($tables as $k=> $table)}
            {let($name = array_values($table)[0])}
			<tr id="{$name}" class="tabletr">
				<td>[{$table['database']}] {$name}</td>
				<td><span class="glyphicon glyphicon-ok" style="color:#5adb58;display: none" aria-hidden="true"></span> <span class="doOneMsg"> </span></td>
				<td><span class="glyphicon glyphicon-ok" style="color:#5adb58;display: none" aria-hidden="true"></span> <span class="doOneMsg"> </span></td>
				<td><button type="button" data-table="{$name}" class="doOne btn btn-primary btn-xs"> 创建 / 更新 </button></td>
			</tr>
        {/foreach}
	</table>

</div>

<input type="hidden" id="isOk" value="nodo" />

<script>

	var tableNameArr = [];
    var sh;
    var i = 0;

	//轮询job
	function doJob(){
        console.log($("#isOk").val());
	    if(tableNameArr.length == 0){
	        //停止
            clearInterval(sh);
            i = 0;
            return false;
		}

		if( $("#isOk").val() == 'nodo'){
            //先取一个值
            var tableName = tableNameArr[0];
            //取完删除
            tableNameArr.splice(0, 1);
            doTable(tableName, true);
		}

		//否则等待
	}

    //执行一条table
    function doTable(action, isScroll = false){
        $("#isOk").val('doing');
        var urlTable = "{$controller->url('', 'table')}";
        var urlRecord = "{$controller->url('', 'record')}";
        var self = $("#"+action);

        self.find("td:eq(1) .glyphicon-ok").hide();
        self.find("td:eq(1) .doOneMsg").html('');
        self.find("td:eq(2) .glyphicon-ok").hide();
        self.find("td:eq(2) .doOneMsg").html('');

        $.ajax({
            url: urlTable,
            data:{table:action},
            timeout: 10000,
            cache: false,
            type: "post",
            dataType: "html",
            success: function (res){
                self.find("td:eq(1) .glyphicon-ok").fadeIn();
                self.find("td:eq(1) .doOneMsg").html(res);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
                if(textStatus=='timeout'){
                    self.find("td:eq(1) .doOneMsg").text('请求超时，请稍后再试');
                }
            }
        });

        $.ajax({
            url: urlRecord,
            data:{table:action},
            timeout: 10000,
            cache: false,
            type: "post",
            dataType: "html",
            success: function (res){
                self.find("td:eq(2) .glyphicon-ok").fadeIn();
                self.find("td:eq(2) .doOneMsg").html(res);
                $("#isOk").val('nodo');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
                if(textStatus=='timeout'){
                    self.find("td:eq(2) .doOneMsg").text('请求超时，请稍后再试');
                    $("#isOk").val('nodo');
                }
            }
        });

        if(isScroll === true){
            i++;

            if(i % 10 == 0){
                //获取元素到顶部的高度
                var offsetTop = self.offset().top - 100;

                //scroll 应该到达位置
                $('body,html').animate({scrollTop:offsetTop}, 300);
			}

		}
    }


    $(function(){
        $("#doClearAll").click(function(){
            clearInterval(sh);
		})


        $("#search").click(function(){
			var word = $("#word").val();
			if(word == ''){
                $(".tabletr").fadeIn();
                return false;
			}
            var obj = $("#table tr[id*='" + word + "']");
			if(obj.length == 0){
                alertMsg('未查询到结果');
                $("#word").focus();
                return false;
			}

			$(".tabletr").hide();
            obj.fadeIn();
		});

        $("#doAll").click(function(){
            var funcName = function(){
                $(".tabletr").fadeIn();

                closeConfirm();

                if(tableNameArr.length == 0){
                    //获取所有表名称
                    $(".tabletr").each(function(){
                        tableNameArr.push($(this).attr('id'));
                    })
				}

				//执行轮询
                sh = setInterval(doJob, 1000);
            };

            confirmMsg("<p>确认执行全部创建和更新么？</p>", funcName);
		})

        $(".doOne").click(function(){
            var tableName = $(this).data('table');
            doTable(tableName);
        })

        $("#word").keyup(function(){
            if(event.which == 13){
                $("#search").click();
                return false;
            }
        })
    })
</script>