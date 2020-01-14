{include('common/head')}
{include('common/left')}

<!---------- main ---------->
<div class="mainright">
    <div class="navcrumbs"><i class="fa fa-home"></i> 用户管理 > 用户管理 > 用户列表
        <div class="fr">
            <button class="btn btn-default" id="goBack">返回</button>
        </div>
    </div>

    <div class="wrap wrapfull">
        <!-- 搜索条件 -->
        <div class="listTitle">
            <form action="" method="post">
                <div class="filters">
                    <div class="filters-block">
                        <label>用户id/名称：</label>
                        <input class="w230" type="text" name="name" id="" placeholder="用户id/名称" value="{$name}"/>
                    </div>
                    <div class="filters-block">
                        <input type="submit" value="搜　索" class="btn btn-main ml20">
                    </div>
                    <div class="fr">
                        <a href="{$controller->url('', 'add')}" class="btn btn-success">新建</a>
                    </div>
                </div>

            </form>
        </div>
        <!-- table -->
        <table class="tables mt30">
            <thead>
            <tr>
                <th>用户ID</th>
                <th>用户账号(手机号)</th>
                <th>用户名称</th>
                <th>公司名称</th>
                <th>创建时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            {foreach($list as $value)}
            <tr>
                <td>{$value['id']}</td>
                <td>{$value['account']}</td>
                <td>{$value['name']}</td>
                <td>{$value['companyName']}</td>
                <td>{$value['created']}</td>
                <td>
                    <a href="{$controller->url('', 'edit', ['id' => $value['id']])}">修改</a>
                    {if($value['type'] == "系统添加")}
                    <a href="{$controller->url('', 'resetPassword', ['id' => $value['id']])}">重置密码</a>
                    {/if}
                </td>
            </tr>
            {/foreach}
            </tbody>
        </table>
        <!-- 分页 -->
        {include('common/page')}
    </div>

</div>
{include('common/foot')}