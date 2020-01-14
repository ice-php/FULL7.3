<!DOCTYPE html>
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

    <!--[if lt IE 7]>
    <script src=”http://ie7-js.googlecode.com/svn/version/2.0(beta)/IE7.js” type=”text/javascript”></script>
    <![endif]-->
    <!--[if lt IE 8]>
    <script src=”http://ie7-js.googlecode.com/svn/version/2.0(beta)/IE8.js” type=”text/javascript”></script>
    <![endif]-->
    <title>{?$config->title['list']}</title>

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
            $('.rightcontent .ordercontact').prepend(html);
        }

        //定义单次操作按钮
        $('a.once').click(function(){
            var obj=$(this);
            obj.text('执行中');
            obj.setAttribute('disabled','disabled');
        })
    })


</script>