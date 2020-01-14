<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>中交一品集采平台1.0-登录</title>
    <link rel="stylesheet" href="{:pathRoot()}/Static/css/font-awesome.min.css">
    <link rel="stylesheet" href="{:pathRoot()}/Static/css/style.css" >
    <script type="text/javascript" src="{:pathRoot()}/Static/js/jquery-1.11.1.min.js"></script>
</head>

<body onload="msgShow()">

    <div class="login">
        <form action="" method="post" onsubmit="return CheckFormLogin()">
            <div></div>
            <div class="login_tit"><img src="{:pathRoot()}/Static/images/logo.png" alt="" style="height:60px;margin-bottom:5px;">
                <div>中交一品管理平台</div>
            </div>
            <div class="mb30">
                <p><input type="text" name="name" id="name" placeholder="账户名" /></p>
                <p><input type="password" name="password" id="password" placeholder="账户密码" /></p>
                <!--<p class="pr">
                    <input type="text" name="vCode" id="vCode" placeholder="验证码" />
                    <i class="error" id="verify"></i>
                    <img onclick="changeImg()" id="codeimg" onmouseover="this.style.cursor='hand'" class="vcodeimg" src="{$controller->url('','vCode')}" alt="验证码" style="cursor: pointer;">
                </p>-->
            </div>
            <!--提示信息-->
            <div class="msgContainer"></div>
            <p><input type="submit" value="登录" class="btn btn-main"></p>
        </form>
    </div>
    
    <script>
        //显示成功,失败,以及提示信息
        var html='';
        {foreach(\icePHP\Frame\MVC\Message::getErrors() as $msg)}
            {let($msg=str_replace('\'',"\\'",$msg))}
            html+='<div class="alert alert-danger"><span class="glyphicon glyphicon-remove"></span> {$msg} </div>';
        {/foreach}
        {foreach(\icePHP\Frame\MVC\Message::getInfos() as $msg)}
            {let($msg=str_replace('\'',"\\'",$msg))}
            html+='<div class="alert alert-warning"><span>!</span> {$msg} </div>';
        {/foreach}
        {foreach(\icePHP\Frame\MVC\Message::getSuccesses() as $msg)}
            {let($msg=str_replace('\'',"\\'",$msg))}
            html+='<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span>{$msg}</div>';
        {/foreach}
        if(html) {
            $('.msgContainer').prepend(html);
            //1秒后自动消除成功信息
            setTimeout(function(){
                $('.alert').fadeOut();
            }, 5000);
        }

        // 点击图片更换验证码
        function changeImg()
        {
            document.getElementById("codeimg").src = "{$controller->url('','vCode')}&t="+new Date();
        }
        // 显示错误信息
        function msgShow()
        {
            var verify = $('#verify').text();
            if (verify != "" && verify != null){
                $('#verify').show();
            }
        }

        // 信息验证
        function CheckFormLogin()
        {
            var name = $.trim($("#name").val());
            var pwd = $.trim($("#password").val());
            //var vCode = $.trim($("#vCode").val());

            if(name== "" || name== null){
                $('#verify').text('请输入用户名！').show();
                return false;
            }
            if (pwd == "" || pwd == null){
                $('#verify').text('请输入密码！').show();
                return false;
            }
            // if (vCode=="" || vCode==null){
            //     $('#verify').text('请输入验证码！').show();
            //     return false;
            // }
            //var reg = /^\d{4}$/;
            // if(!reg.test(vCode)){
            //     $('#verify').text('验证码应该是4位数字').show();
            //     return false;
            // }
            return true;
        }

    </script>
</body>

</html>