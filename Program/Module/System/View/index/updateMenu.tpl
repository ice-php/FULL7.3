
<div class="col-md-12" role="complementary" style="margin-top: 20px;margin-bottom: 20px;">
	<div class="col-md-6">
		<div class="input-group">
			<input type="text" id="word" class="form-control" placeholder="输入执行的连接(注意区分大小写)，可确认后单独处理" aria-describedby="basic-addon2">
			<span class="input-group-addon btn btn-default" id="search">查找</span>
		</div>
	</div>
	<button type="button" id="doAll" class="btn btn-primary"> 更新全部 </button>
	<button type="button" id="doClearAll" class="btn btn-warning"> 停止 </button>
</div>

<div class="col-md-12" role="complementary">

	<table class="table table-hover" id="table">
		<tr>
			<th class="col-md-4">要执行的连接</th>
			<th class="col-md-6">菜单权限处理结果</th>
			<th class="col-md-2">操作</th>
		</tr>
        {foreach($temp as $k=> $v)}
			<tr id="menu_{$k}" data-table="{$v}" class="tabletr">
				<td>{$v}</td>
				<td><span class="glyphicon glyphicon-ok" style="color:#5adb58;display: none" aria-hidden="true"></span> <span class="doOneMsg"> </span></td>
				<td><button type="button" data-menu="menu_{$k}"  class="doOne btn btn-primary btn-xs"> 更新 </button></td>
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
            var menu = tableNameArr[0];
            //取完删除
            tableNameArr.splice(0, 1);
            doTable(menu, true);
		}

		//否则等待
	}

    //执行一条table
    function doTable(menu, isScroll = false){
        $("#isOk").val('doing');

        var self = $("#"+menu);
		var url = self.data('table');

        self.find("td:eq(1) .glyphicon-ok").hide();
        self.find("td:eq(1) .doOneMsg").html('');

        $.ajax({
            url: url,
            timeout: 10000,
            cache: false,
            type: "post",
            dataType: "html",
            success: function (res){
                self.find("td:eq(1) .glyphicon-ok").fadeIn();
                self.find("td:eq(1) .doOneMsg").html(res);
                $("#isOk").val('nodo');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
                if(textStatus=='timeout'){
                    self.find("td:eq(1) .doOneMsg").text('请求超时，请稍后再试');
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
            var obj = $("#table tr[data-table*='" + word + "']");
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

            confirmMsg("<p>确认执行全部更新么？</p>", funcName);
		})

        $(".doOne").click(function(){
            var menu = $(this).data('menu');
            doTable(menu);
        })

        $("#word").keyup(function(){
            if(event.which == 13){
                $("#search").click();
                return false;
            }
        })
    })
</script>