<?php

/**
 * 示例代码
 * User: Administrator
 * Date: 2017/10/17
 * Time: 16:34
 */

declare(strict_types=1);

namespace Program\Controller;

use function array_column;
use function array_filter;
use function array_unique;
use function base64_decode;
use function base64_encode;
use icePHP\Enhance\Im;
use icePHP\Enhance\WechatPay;
use function icePHP\Frame\Cache\cache;
use function icePHP\Frame\Cache\simple;
use function icePHP\Frame\Config\config;
use function icePHP\Frame\Config\configDefault;
use function icePHP\Frame\FileLog\writeLog;
use function icePHP\Frame\Functions\datetime;
use function icePHP\Frame\Functions\dump;
use function icePHP\Frame\Functions\isAjax;
use function icePHP\Frame\MVC\display;
use icePHP\Frame\MVC\Request;
use function icePHP\Frame\Debug\isDebug;
use function json_decode;
use function password_verify;
use function preg_match;
use function preg_match_all;
use function preg_replace;
use function print_r;
use Program\Model\CateModel;
use Program\Model\ImModel;
use Program\Module\Home\Model\HomeGoodsModel;
use Program\Module\Home\Model\HomeOrderModel;
use Program\Table\Base\OrderTableBase;
use Program\Table\GoodsAttrTable;
use Program\Table\GoodsCateRelationTable;
use Program\Table\GoodsCateTable;
use Program\Table\OrderParentTable;
use Program\Table\OrderTable;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;
use function var_dump;

use  \Psr\Cache\InvalidArgumentException;

class ExampleController extends BaseController
{

    /**
     * 本类所有方法只在Debug模式下有效
     * @param $req Request
     * @return boolean
     */
    public function construct(Request $req): bool
    {
        return parent::construct($req) && isDebug();
    }

    public function test()
    {
        display('hello');
    }
}
