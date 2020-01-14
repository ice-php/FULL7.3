{include('common/head')}
<link rel="stylesheet" href="{:pathRoot()}/Static/css/select2/select2.min.css" type="text/css" >
{include('common/left')}

<!---------- main ---------->
<div class="mainright">
    <div class="navcrumbs"><i class="fa fa-home"></i>用户管理 > 用户管理 > 新增</div>

    <div class="wrap wrapfull">
        <form action="{$controller->url('', 'addSubmit')}" id="submitForm" method="post">
            <dl class="account">
                <dd>
                    <span>用户名称：</span>
                    <input class="w400" type="text" placeholder="此处填写用户名称" name = "account" value="" required data-msg-required="用户名称">
                </dd>
                <dd>
                    <span>用户密码：</span>
                    <input class="w400" type="password" placeholder="此处填写用户密码" name = "password" value="" required data-msg-required="用户密码">
                </dd>
                <dd>
                    <span>公司名称：</span>
                    <select class="w400 select2" required data-msg-required="请选择公司名称" name = 'companyId' >
                        <option value="">请选择公司</option>
                        {foreach($company as $value)}
                        <option value="{$value['id']}">{$value['name']}</option>
                        {/foreach}
                    </select>
                </dd>
                <dd>
                    <span>是否可挂单：</span>
                    <select class="w400" required data-msg-required="状态" name="offlinePay" id="offlinePay">
                        <option value="0">否</option>
                        <option value="1">是</option>
                    </select>
                </dd>
                <dd style="display: none" id = "offlinePayQuota">
                    <span>挂单额度：</span>
                    <input class="w400" type="text" placeholder="0.00" name="offlinePayQuota" value="" required data-msg-required="">
                </dd>
                <dd class="mt30"><span> </span>
                    <input class="btn btn-main lgh-btn" type="submit" value="完成">
                    <a class="btn btn-default lgh-btn" href="javascript:history.go(-1)">返回</a>
                </dd>
            </dl>
        </form>
    </div>
</div>
<script type="text/javascript" src="{:pathRoot()}/Static/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="{:pathRoot()}/Static/js/select2/select2.full.js"></script>
<script>
    $(function () {
        $(".select2").select2();
        $("#offlinePay").change(function() {
            var offlinePay = $(this).val();
            if (offlinePay == 1) {
                $("#offlinePayQuota").show()
            } else {
                $("#offlinePayQuota").hide()
            }
        });
        //提交表单验证
        $("#submitForm").validate();
    })
</script>
{include('common/foot')}