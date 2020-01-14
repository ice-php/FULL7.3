<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <!-- 开发环境版本，包含了有帮助的命令行警告 -->
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <!-- 引入样式 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ant-design-vue@1.3.7/dist/antd.min.css">
    <!-- 引入组件库 -->
    <script src="https://cdn.jsdelivr.net/npm/ant-design-vue@1.3.7/dist/antd.min.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://cdn.bootcss.com/qs/6.5.1/qs.min.js"></script>
</head>

<body>
    <script type="text/x-template" id="template-tree">
        <a-row>
            <a-col :span="5">
                <span v-if="list.文件还是目录=='文件'"><a-icon type="file" /></span>
                <span v-if="list.文件还是目录=='目录'" @click="toggleFolder(list.全路径)" class="togglebtn">
                    <span v-if="!list.isopen"><a-icon type="plus-square" /></span>
                    <span v-if="list.isopen"><a-icon type="minus-square" /></span>
                </span>
                {{list.名称}}
            </a-col>
            <a-col :span="4">{{list.最后访问时间}}</a-col>
            <a-col :span="4">{{list.创建时间}}</a-col>
            <a-col :span="4">{{list.最后修改时间}}</a-col>
            <a-col :span="2">{{list.文件大小}}</a-col>
            <a-col :span="2">{{list.文件所属用户}}</a-col>
            <a-col :span="1">{{list.是否是挂载点?'是':'否'}}</a-col>
            <a-col :span="2">{{list.访问权限}}</a-col>
            <a-col :span="24" v-if="list.children && list.isopen" class="children-item">
                <treelist  :list="item"  v-for="item in list.children"></treelist>
            </a-col>
        </a-row>

    </script>
    <div id="app">
        <a-layout id="components-layout-demo-custom-trigger">
            <a-layout-sider :trigger="null" collapsible v-model="collapsed">
                <div class="logo">ICE</div>
                <a-menu theme="dark" mode="inline" :open-keys.sync="sub" v-model="current">
                    <a-sub-menu key="sub1">
                        <span slot="title">

                            <a-icon type="mail"></a-icon><span>文件管理</span>
                        </span>
                        <a-menu-item key="1">资源管理器</a-menu-item>
                    </a-sub-menu>
                    <a-menu-item key="2">
                        <a-icon type="video-camera"></a-icon>
                        <span>nav 2</span>
                    </a-menu-item>
                    <a-menu-item key="3">
                        <a-icon type="upload"></a-icon>
                        <span>nav 3</span>
                    </a-menu-item>
                </a-menu>
            </a-layout-sider>
            <a-layout>
                <a-layout-header style="padding: 0">
                    <a-icon class="trigger topbar-btn" :type="collapsed ? 'menu-unfold' : 'menu-fold'"
                        @click="()=> collapsed = !collapsed"></a-icon>
                    <a-icon class="topbar-btn" type="logout" style="float:right"></a-icon>

                </a-layout-header>
                <a-breadcrumb style="margin: 16px 0 0 16px">
                    <a-breadcrumb-item>
                        <span>
                            <a-icon type="home" /></span> 主页
                    </a-breadcrumb-item>
                    <a-breadcrumb-item>文件管理</a-breadcrumb-item>
                    <a-breadcrumb-item>资源管理器</a-breadcrumb-item>
                </a-breadcrumb>
                <a-layout-content :style="{ margin: '16px', padding: '24px', background: '#fff', flex: 'none' }">
                    <a-row class="foldertree">
                        <a-col :span="5">名称</a-col>
                        <a-col :span="4">最后访问时间</a-col>
                        <a-col :span="4">创建时间</a-col>
                        <a-col :span="4">最后修改时间</a-col>
                        <a-col :span="2">文件大小</a-col>
                        <a-col :span="2">所属用户</a-col>
                        <a-col :span="1">挂载点</a-col>
                        <a-col :span="2">访问权限</a-col>
                        <treelist :list="item"  v-for="item in treeData"></treelist>
                    </a-row>
                </a-layout-content>
                <a-layout-footer :style="{ textAlign: 'center' }">
                    Ant Design ©2018 Created by Ant UED
                </a-layout-footer>
            </a-layout>
        </a-layout>
    </div>
    <script>
        Vue.component('treelist', {
            template: '#template-tree',
            props: ['list'],
            methods: {
                toggleFolder( path) {                    
                    var isopen = !this.list.isopen;
                    Vue.set(this.list, "isopen", isopen);
                    //第一次请求下级菜单
                    var isLoad = this.list.isLoad;

                    if (!isLoad) {
                        instance({
                            method: 'get',
                            url: 'http://resthome.php-liyong.com:9989/?action=listFile&path=' + path,
                        })
                            .then((data) => {
                                var newData = data.data.data;
                                Vue.set(this.list, 'children', newData);
                                Vue.set(this.list, "isLoad", true);

                            })
                            .catch((failed) => {
                                console.log(failed);
                            })
                    }
                    console.log(this.list)
                }
            }

        });
        const instance = axios.create({
            timeout: 10000,
            transformResponse: [function (data) {
                var value = JSON.parse(data);
                if (value.state_code === 60028) {
                    return {
                        reason: value.data,
                        title: value.info
                    };
                }
                return value;
            }]
        });
        new Vue({
            el: '#app',
            data: {
                collapsed: false,
                current: ['1'],
                sub: ['sub1'],
                treeData: [],
            },
            watch: {
                checkedKeys(val) {
                    console.log('onCheck', val)
                }
            },

            methods: {
                toPicTure() {
                    instance({
                        method: 'get',
                        url: 'http://resthome.php-liyong.com:9989/?action=listFile',
                    })
                        .then((data) => {
                            this.treeData = data.data.data
                        })
                        .catch((failed) => {
                            console.log(failed);
                        })
                },

            },
            created: function created() {
                this.toPicTure();
            }
        })

    </script>
    <style>
        body,
        div,
        ul,
        li,
        p {
            padding: 0;
            margin: 0;
        }

        ul {
            list-style: none
        }

        #app,
        #components-layout-demo-custom-trigger {
            height: 100%;
        }



        #components-layout-demo-custom-trigger .logo {
            height: 32px;
            margin: 16px;
            text-align: center;
            color: #fff;
            font-weight: 600;
            font-size: 30px;
            font-family: Avenir, Helvetica Neue, Arial, Helvetica, sans-serif;
        }

        .topbar-btn {
            line-height: 62px;
            color: #fff;
            font-size: 20px;
            transition: color .3s;
            display: inline-block;
            cursor: pointer;
            padding: 0 20px;
        }

        .topbar-btn:hover {
            background-color: #06213e;
            color: #1890ff;
        }

        .children-item>.ant-row div:first-child {
            padding-left: 15px;
        }

        .togglebtn {
            cursor: pointer;
        }
        
        .children-item{
            position:relative;
        }
        .children-item:before {
            content: ' ';
            width: 1px;
            border-left: 1px solid #d9d9d9;
            height: 100%;
            position: absolute;
            left: 0;0
        }
        .foldertree [class^="ant-col"]{
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }
    </style>
</body>

</html>