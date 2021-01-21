<?php

namespace Wechat;

use think\facade\Cache;
use think\facade\Config;

class Jssdk {

	private $appId;
	private $appSecret;

	public function __construct() {
		$this->appId = Config::get('wechat.app_id');
		$this->appSecret = Config::get('wechat.app_secret');
	}

	public function getSignPackage($url)
	{
		$jsapiTicket = $this->getJsApiTicket();
		// // 注意 URL 一定要动态获取，不能 hardcode.

		$timestamp = time();
		$nonceStr = $this->createNonceStr();

		// 这里参数的顺序要按照 key 值 ASCII 码升序排序
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
		$signature = sha1($string);

		$signPackage = array(
			"appId"     => $this->appId,
			"nonceStr"  => $nonceStr,
			"timestamp" => $timestamp,
			"url"       => $url,
			"signature" => $signature,
			"rawString" => $string
		);
		return $signPackage; 
	}

	private function createNonceStr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}

	private function getJsApiTicket()
	{
		// jsapi_ticket 应该全局存储与更新
		$jsapi_ticket = Cache::store('redis')->get('thinkphp:jsapi_ticket');
		if (!$jsapi_ticket) {
			$accessToken = $this->getAccessToken();
			// 如果是企业号用以下 URL 获取 ticket
			// $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
			$res = json_decode($this->httpGet($url));
			$ticket = $res->ticket;
			if ($ticket) {
				$expire_time = $res->expires_in - 100;
				$jsapi_ticket = $ticket;
				Cache::store('redis')->set('thinkphp:jsapi_ticket', $jsapi_ticket, $expire_time);
			}
		}

		return $jsapi_ticket;
	}

	private function getAccessToken() {
		// access_token 应该全局存储与更新
		$access_token = Cache::store('redis')->get('thinkphp:access_token');
		if (!$access_token) {
			// 如果是企业号用以下URL获取access_token
			// $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
			$res = json_decode($this->httpGet($url));
			$access_token = $res->access_token;
			if ($access_token) {
				$expire_time = $res->expires_in - 100;
				Cache::store('redis')->set('thinkphp:access_token', $access_token, $expire_time);
			}
		}

		return $access_token;
	}

	private function httpGet($url)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 500);
		// 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
		// 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_URL, $url);

		$res = curl_exec($curl);
		curl_close($curl);

		return $res;
	}
}
