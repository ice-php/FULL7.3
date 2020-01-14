<!DOCTYPE html>

{#通用的头文件,需要传递的参数为title#}

<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,Chrome=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=9" />
    {css('bootstrap')}
    {css('style')}
    {js('jquery-1.11.1.min')}
    {js('bootstrap')}
    {js('calendar')}
    {js('WdatePicker')}

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="http://apps.bdimg.com/libs/html5shiv/3.7/html5shiv.min.js"></script>
    <script src="http://apps.bdimg.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!--[if lt IE 7]>
    <script src=”http://ie7-js.googlecode.com/svn/version/2.0(beta)/IE7.js” type=”text/javascript”></script>
    <![endif]-->
    <!--[if lt IE 8]>
    <script src=”http://ie7-js.googlecode.com/svn/version/2.0(beta)/IE8.js” type=”text/javascript”></script>
    <![endif]-->
    <title>{$title}</title>

</head>
<body>

<script>
    $(function(){
        //显示成功,失败,以及提示信息
        var html='';
        {foreach(icePHP\Core\MVC\Message::getErrors() as $msg)}
        {let($msg=str_replace('\'',"\\'",$msg))}
        html+='<div class="alert alert-danger"> <a class="close" data-dismiss="alert">×</a> <span class="glyphicon glyphicon-remove" style="font-size: 25px;"></span>{$msg}</div>';
        {/foreach}
        {foreach(icePHP\Core\MVC\Message::getInfos() as $msg)}
        {let($msg=str_replace('\'',"\\'",$msg))}
        html+='<div class="alert alert-warning"><a class="close" data-dismiss="alert" >×</a><span style="font-size: 25px;">！</span>{$msg}</div>';
        {/foreach}
        {foreach(icePHP\Core\MVC\Message::getSuccesses() as $msg)}
        {let($msg=str_replace('\'',"\\'",$msg))}
        html+='<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><span class="glyphicon glyphicon-ok" style="font-size: 25px;"></span>{$msg}</div>';
        {/foreach}
        if(html) {
            $('.msgContainer').prepend(html);
        }

        //定义单次操作按钮
        $(document).on('click','.once',function(){
            var obj=$(this);
            obj.text('执行中');
            obj.attr('disabled',true);
        });

        //定义需要确认的按钮
        $(document).on('click','.confirm',function(){
            return confirm('您确认要进行此操作么?');
        });
    })
</script>