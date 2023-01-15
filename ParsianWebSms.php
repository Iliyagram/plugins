<?php

/**
 * In the name of Allah
 *
 * Api for send sms of Parsian Web
 *
 * Hoping the appearance Promised Savior
 * If you want get api id and api pass go to [tg: @hamidrezartvet912][parsianwebco.ir]
 *
 * @author Iliya Gholami 2023 - 2024 [tg: @iliya_gholami]
 */

class ParsianWebSms
{
	/**
	 * @const string BASE_URL api for send request
	 */
	const BASE_URL = "https://api.parsianwebco.ir/webservice";
	
	/**
	 * @var int $cid username of webservice
	 */
	private $cid;
	/**
	 * @var string $cpass password of webservice
	 */
	private $cpass;
	
	/**
	 * Initialize
	 *
	 * @param int $username username
	 * @param string $password password
	 */
	public function __construct(int $username, string $password)
	{
		$this->cid = $username;
		$this->cpass = $password;
	}
	
	/**
	 * Create method
	 *
	 * @param string $method method name
	 * @param array $datas datas to send
	 */
	public function __call($name, $datas)
	{
		if( in_array($name, ["send", "status", "account"]) ) {
			return $this->sendRequest($name, $datas[0]);
		}
	}
	
	public function sendRequest(string $method, array $datas)
	{
		$datas["cid"] = $this->cid;
		$datas["cpass"] = $this->cpass;
		
		$method = strtr($method, [
			"send" => "-send-sms/send",
			"status" => "-check-sms/get",
			"account" => "-get-credit/get"
		]);
		
		$url = self::BASE_URL . $method;
		
		$ch = curl_init();
		
		@curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $datas
		]);
		
		$result = curl_exec($ch);
		$error = curl_error($ch);
		
		if( !empty($error) ) {
			error_log($error);
		}
		
		return json_decode($result);
	}
}