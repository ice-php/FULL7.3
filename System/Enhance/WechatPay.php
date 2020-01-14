<?php
/**
 * Created by PhpStorm.
 * User: vYao
 * Date: 2019/6/19
 * Time: 12:49
 */
namespace icePHP\Enhance;

require_once 'wechatpay/WxPay.Config.php';
require_once 'wechatpay/WxPay.Api.php';
require_once 'wechatpay/WxPay.Notify.php';

use function array_merge;
use icePHP\Frame\MVC\Request;
use function icePHP\Frame\FileLog\writeLog;
use WxPayApi;
use WxPayConfig;
use WxPayException;
use WxPayRefund;
use WxPayResults;
use WxPayUnifiedOrder;

/**
 * 微信支付
 */
class WechatPay
{
	
	/**
	 * H5支付
	 * @param $title   string 商品标题
	 * @param $price   float    商品总价单位 元
	 * @param $tradeNo string   回执单号
	 * @param $from    string   支付设备来源
	 * @param $attach  array  扩展参数
	 * @return array
	 * @throws \WxPayException
	 */
	public static function h5($title, $price, $tradeNo, $from, $attach = [])
	{
		
		if(!in_array($from, ['android', 'ios', 'web'])){
			return [false, "type类型必须为'android', 'ios'"];
		}
		
		//②、统一下单
		$config = new WxPayConfig();
		$input = new WxPayUnifiedOrder();
		$input->SetBody($title);
		$dataJson = json_encode($attach);
		$input->SetAttach($dataJson);
		$input->SetOut_trade_no($tradeNo);
		$input->SetTotal_fee($price * 100);
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 600));
		$input->SetNotify_url($config->GetNotifyUrl());
		$input->SetTrade_type("MWEB");
		
		if($from == 'ios'){
			// 需要管ios要bundle_id
			$scene = '{"h5_info": {"type":"IOS","app_name": "中交一品","bundle_id": ""}}';
		}elseif($from == 'android'){
			// 需要管Android要package_name 包名
			$scene = '{"h5_info": {"type":"Android","app_name": "中交一品","package_name": ""}}';
		}else{
			// web
			$scene = '{"h5_info": {"type":"Wap","wap_url": "' . Request::instance()->domain() . '","wap_name": "中交一品"}}';
		}
		
		$input->SetSceneInfo($scene);

		$order = WxPayApi::unifiedOrder($config, $input);

		if($order['return_code'] !== 'SUCCESS'){
			return [false, $order['return_msg']];
		}
		
		if($order['result_code'] !== 'SUCCESS'){
			return [false, $order['err_code'] . ':' . $order['err_code_des']];
		}
		
		return [true, $order];
	}
	
	/**
	 * 二维码支付
	 * @param $productId string 商品id
	 * @param $title     string 商品标题
	 * @param $price     float    商品总价单位 元
	 * @param $tradeNo   string 单号
	 * @param $attach    array  扩展参数
	 * @return array
	 * @throws \WxPayException
	 */
	public static function qrCode($productId, $title, $price, $tradeNo, $attach)
	{
		//②、统一下单
		$config = new WxPayConfig();
		$input = new WxPayUnifiedOrder();
		$input->SetBody($title);
		$dataJson = json_encode($attach);
		$input->SetAttach($dataJson);
		$input->SetOut_trade_no($tradeNo);
		$input->SetTotal_fee($price * 100);
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 630));
		$input->SetNotify_url($config->GetNotifyUrl());
		
		$input->SetNotify_url($config->GetNotifyUrl());
		$input->SetTrade_type("NATIVE");
		$input->SetProduct_id($productId);
		
		$result = WxPayApi::unifiedOrder($config, $input);
		
		if($result['return_code'] !== 'SUCCESS'){
			return [false, $result['return_msg']];
		}
		
		if($result['result_code'] !== 'SUCCESS'){
			return [false, $result['err_code'] . ':' . $result['err_code_des']];
		}
		
		return [true, $result];
	}
	
	/**
	 * 退款
	 * @param $transactionId
	 * @param $refundNo
	 * @param $totalFee
	 * @param $refundFee
	 * @return array
	 * @throws \WxPayException
	 */
	public static function refund($transactionId, $refundNo, $totalFee, $refundFee)
	{
		$input = new WxPayRefund();
		$input->SetTransaction_id($transactionId);
		$input->SetTotal_fee($totalFee * 100);
		$input->SetRefund_fee($refundFee * 100);
		
		$config = new WxPayConfig();
		$input->SetOut_refund_no($refundNo);
		$input->SetOp_user_id($config->GetMerchantId());
		$input->SetNotify_url($config->GetRefundNotifyUrl());
		$result = WxPayApi::refund($config, $input);
		
		if($result['return_code'] !== 'SUCCESS'){
			return [false, $result['return_msg']];
		}
		
		if($result['result_code'] !== 'SUCCESS'){
			return [false, $result['err_code'] . ':' . $result['err_code_des']];
		}
		
		return [true, $result];
	}
	
	public static function notify()
	{
		
		$config = new WxPayConfig();
		
		//存储微信的回调
		$xml = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
		
		self::notifyLog($xml);
		
		if(empty($xml)){
			# 如果没有数据，直接返回失败
			return false;
		}
		
		//如果返回成功则验证签名
		try{
			$result = WxPayResults::Init($config, $xml);
			self::notifyLog($result);
			
			return $result;
		}catch(WxPayException $e){
			$msg = $e->errorMessage();
			self::notifyLog($msg);
			
			return false;
		}
	}
	
	public static function notifyRefund()
	{
		//存储微信的回调
		$xml = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
		
		self::notifyLog($xml);
		
		//$xml = "<xml><return_code>SUCCESS</return_code><appid><![CDATA[wx1a8893801a77f61d]]></appid><mch_id><![CDATA[1562224291]]></mch_id><nonce_str><![CDATA[91d53bd7857c345f54fa24f4a2275d82]]></nonce_str><req_info><![CDATA[3+7C5RFneOpD7ITrrpmHF4Y6T8ipZ9LdeTMLB0XLPVipepfRX7hx75rZ56iWHS9Bq76WOzPHMIAutDfiBZxnIVYg+H7ojZg/+Pwv9UYc/kPWgW3mUBATmrXx/Fo7n03u9eleFHvRKDXJdoDUFoQpvSghmYONcFQpTnKZeHnncYHVumvIMGWsz8jzhHDbaU6UoTXKVieq2SvTK2e87G1aBXoGfYkZSnDC0TUdR7G++GjXcBeITb+iSsieJ5J7RmW8IjBiARHth0OVjEgb0oiByl8bSEPOblS3ffjZHc72NlwQKSz7J5j/T6E626Jmy9Y0Iv1qSq/IlRHKkSM0A/kCzjPZLVC9U0MOY4y551seeAiWYqcMO2oim80ncUk75J0L7wZy0L6Cuz7QM+X9RNoK3qntZEWqSPyHLc3VvKmvCeR8P5YITHaMmXrML0zfzTXL/dwpo1EUoPR/0buJoX/8OQJTP5KqgL/DWfowxS4Xw2BAmlm1cQWoT+tnEcn+BiRRaVfGeE+7qC9rrrunNu6rZZ4N88xBm7wFBAdgoE3BW/dH/oq0zZPxHxHHkMbKNq5Xl+0rDPRzJNh+Yd6QYV4NH4JJqoVX4doZ01xDkCCcbdfH8m8u+TGKxAwNxiEe1K2bWFBEwfL8YjHgqJkLZq36Oh0OV/sVYJZ5Q/tfiucsUS/Wf29Zgm7i+2bE1BtkO13Dy9ClN0I+A7Dyh+5gYpi74DQ9rvf17W6EC3kd7iLxVKiCb7HQuQRqtOp4hUKfJTfD2ktntYapFVtpXb+cih03s27hzBqPAAmpMwbjhAAKURUtOJbff5fpOjznukwB8H4Cgx+4r9d5Pd75eNawrSHd9Sd8cBUL/riKbNp1MDyRBfyQsRp7hgR5T5Ix6kBipXDjbdyzckEJk1UL27cLnfw5+tcrRqkwq1msMwALDM6vYhA3/Dn8a2PSGuQTYSYM06FyZyrI13SDg1iog+qJ0UNV5rtLPFvU/ZDm/t1AsFmgpP2Uk58zIhyF849PwS46Cv26zCHQNxn/ISd7AZRfC8mlWjIarNeAdxWWzG40UVFIdtYSTkpykojrnPmGMlXKxCHZ]]></req_info></xml>";
		
		if(empty($xml)){
			# 如果没有数据，直接返回失败
			return false;
		}
		
		//如果返回成功则验证签名
		try{
			$result = WxPayResults::InitRefund($xml);
			
			self::notifyLog($result);
			
			if(!isset($result['req_info'])){
				return false;
			}
			
			// req_info 解密
			$data = self::decryptAes($result['req_info']);
			if(!$data){
				return false;
			}
			
			$result = array_merge($result, $data);
			unset($result['req_info']);
			
			return $result;
		}catch(WxPayException $e){
			$msg = $e->errorMessage();
			self::notifyLog($msg);
			
			return false;
		}
	}
	
	public static function notifyReply($res = true)
	{
		
		if($res){
			$data['return_code'] = 'SUCCESS';
		}else{
			$data['return_code'] = 'FAIL';
		}
		
		$data['return_msg'] = 'OK';
		
		$xml = "<xml>";
		foreach($data as $key => $val){
			if(is_numeric($val)){
				$xml .= "<" . $key . ">" . $val . "</" . $key . ">";
			}else{
				$xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
			}
		}
		$xml .= "</xml>";
		
		self::notifyLog($xml);
		
		echo $xml;
		exit;
	}
	
	public static function notifyLog($data)
	{
		writeLog('wechatPayNotify', $data);
	}
	
	public static function decryptAes($data)
	{
		$config = new WxPayConfig();
		
		$key = $config->GetKey();
		$md5 = md5($key);
		
		$resXml = openssl_decrypt(base64_decode($data), 'AES-256-ECB', $md5, 1);
		
		if(!$resXml){
			return false;
		}
		
		libxml_disable_entity_loader(true);
		$res = json_decode(json_encode(simplexml_load_string($resXml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		
		return $res;
	}
}