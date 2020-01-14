<?php
/**
 * 路由配置
 * 在这里,我们假设,你会使用正则表达式
 */
return [
    // 可以忽略解码的地址列表,只能处理HTML和PHP文件
    // 不能处理JS,CSS,图片,请用重写处理
    '_ignore' => [
        '/^Static\/js\/ueditor\/.*\.html/',
        '/^Static\/js\/ueditor\/.*\.php/',
        '/^ignore\.php$/'
    ],
    /*
    //每一项是一种路由解析规则
    '产品详情页面' => array(
        // 解码部分,必须按路径模式书写
        'decode' => array(
            //正则表达式
            '/^(\d+)(\.html)?$/',
            //替换成以下
            'front/goods/detail/id/$1'
        ),
        //编码部分
        'encode' => array(
            //M,C,A
            'front',
            'goods',
            'detail',
            //编码成以下形式
            '/{id}.html'
        )
    ),
    */

    '商家后台地址'=>[
	    'decode'=>[
		    '/^supplier(\.php|\.html)?$/i','supplier/login/index'
	    ],
	    'encode'=>[
		    'supplier','login','index','/supplier'
	    ]
    ],
    '总后台地址'=>[
        'decode'=>[
            '/^admin(\.php|\.html)?$/i','admin/login/index'
        ],
        'encode'=>[
            'admin','login','index','/admin'
        ]
    ],
    '微信支付回调'=>[
	    'decode'=>[
            '/wechatPayNotify/i', '/home/pay/wechatNotify'
	    ]
    ],
    '微信退款回调'=>[
	    'decode'=>[
		    '/wechatRefundNotify/i', '/home/pay/wechatRefundNotify'
	    ]
    ]
];