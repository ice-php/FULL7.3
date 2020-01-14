<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8" />
	<title>ICE 系统功能</title>
	<link rel="stylesheet" href="{:pathRoot()}/Static/js/load/loading.css">
	<!-- 最新版本的 Bootstrap 核心 CSS 文件 -->
	<link rel="stylesheet" href="{:pathRoot()}/Static/css/bootstrap.css">
	<script src="{:pathRoot()}/Static/js/jquery-1.11.1.min.js" language="javascript" type="text/javascript"></script>
	<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
	<script src="{:pathRoot()}/Static/js/bootstrap.js" language="javascript" type="text/javascript"></script>

	<script src="{:pathRoot()}/Static/js/load/loading.js" language="javascript" type="text/javascript"></script>

	<style>
		.returntop{
			width:40px;
			height:60px;
			cursor:pointer;
			position:fixed;
			bottom:60px;
			right:40px;
			display:none
		}

	</style>
</head>
<script>
</script>
<body style="background-color: #e6e6e6" id="body">

<div class="container-fluid" style="width:90%;min-width: 1200px;">
	<div class="page-header" style="margin-top:20px;margin-bottom:10px;padding-right: 15px;padding-left: 15px;">
		<h1>Ice 框架 <small>系统功能页面</small></h1>
	</div>

	<ul class="nav nav-pills" style="padding-right: 15px;padding-left: 15px;">
		{foreach($config as $k => $v)}
		<li role="presentation" class="menuButton {if($k == 0)}active{/if}"
			data-confirm="{$v['isConfirm']}"
			data-controller="{?$v['Controller']}"
			data-action="{$v['action']}"
		>
			<a href="javascript:void(0)">{$v['name']}</a>
		</li>
		{/foreach}
	</ul>

	<div id="page" class="container-fluid"  style="background-color: #ffffff;margin-top:20px;min-height: 750px;border-radius: 8px;margin-bottom: 150px;">
		<div id="content" style="display:none;margin-top:10px;">

		</div>
	</div>
</div>

<script>

    var tempUrl = "{$controller->url('TEMPCONTROLLER', 'TEMPACTION')}";

    var isDo = false;

    //获取页面信息
    function getPage(action, controller){
        $("#content").html('').hide();

        var load = new Loading();
        load.init({
            target: "#page"
        });
        isDo = true;
        load.start();

        if(controller != ''){
            var url = tempUrl.replace("TEMPCONTROLLER", controller);
		}else{
            var url = tempUrl.replace("TEMPCONTROLLER", 'index');
		}
        url = url.replace("TEMPACTION", action);

        ajaxObj = $.ajax({
            url: url,
			data:{id:'no'},
            timeout: 600000,
            cache: false,
            type: "post",
            dataType: "html",
            success: function (res){
                load.stop();
				$("#content").append(res).fadeIn(1000);
                isDo = false;
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
                if(textStatus=='timeout'){
                    alert('请求超时，请稍后再试');
                    load.stop();
                    isDo = false;
                }
            }
        });
	}

    //提示信息
    function alertMsg(msg){
        $("#alert-content").html(msg);
        $("#alert").fadeIn();
        setTimeout(function(){
            $("#alert").fadeOut();
		}, 1500)
    }

    //确认信息
    function confirmMsg(msg, funcName){
        $("#confirm-content").html(msg);

        if(funcName != ''){
            $("#confirmOk").on("click", funcName);
		}

        $("#confirm").fadeIn();
    }

    //确认信息 取消
	function closeConfirm(){
        $("#confirm").fadeOut();
        $("#confirmOk").unbind("click");
	}

    $(function(){
        $("#closeAlert").click(function(){
			$("#alert").fadeOut();
		})

        $(".closeConfirm").click(function(){
            closeConfirm();
        })

		$(".menuButton").click(function(){
            $('body,html').animate({scrollTop:0}, 50);

            var self = $(this);

            if(self.data('confirm') == 1){
                var funcName = function(){
                    closeConfirm();

                    if(isDo === true){
                        alertMsg("请等待当前任务执行完成");return false;
                    }

                    $(".active").removeClass('active');
                    self.addClass('active');

                    var action = self.data('action');
                    var controller = self.data('controller');
                    getPage(action, controller);
                };

                //确认信息
                confirmMsg("<p>请谨慎操作，确认执行么？</p>", funcName);

			}else{
                if(isDo === true){
                    alertMsg("请等待当前任务执行完成");return false;
                }

                $(".active").removeClass('active');
                self.addClass('active');

                var action = self.data('action');
                var controller = self.data('controller');
                getPage(action, controller);
			}



		})

		//第一次初始化加载
		var defaultAction = $(".active").data('action');
        var defaultController = $(".active").data('controller');
		getPage(defaultAction, defaultController);

        //判断大于滚动条具体顶部100的时候淡出回到顶部的div
        $(window).scroll(function(){
            if ($(window).scrollTop()>100){
                $(".returntop").fadeIn(500);
            }else{
                $(".returntop").fadeOut(500);
            }
        });

		//绑定点击事件 0.5秒回到顶部
        $(".returntop").click(function(){
            $('body,html').animate({scrollTop:0},500);
            return false;
        });
	})
</script>

<div id="alert" class="modal">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button id="closeAlert" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title" id="mySmallModalLabel">提示</h4>
			</div>
			<div class="modal-body"  id="alert-content">

			</div>
		</div>
	</div>
</div>

<!-- 信息删除确认 -->
<div class="modal" id="confirm" role="dialog" style="margin-top: 15%;">
	<div class="modal-dialog">
		<div class="modal-content message_align">
			<div class="modal-header">
				<button class="closeConfirm close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title">确认操作</h4>
			</div>

			<div class="modal-body" id="confirm-content">

			</div>

			<div class="modal-footer">
				<input type="hidden" id="url"/>
				<button  class="closeConfirm btn btn-default" type="button" data-dismiss="modal">取消</button>
				<a id="confirmOk" class="btn btn-success" data-dismiss="modal">确定</a>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="returntop">
	<button type="button" class="btn btn-default" style="height: 60px;"><span class="glyphicon glyphicon-arrow-up" style="font-size:16px;" aria-hidden="true"></span></button>
</div>

</body>
</html>