<?php

/**
 * In the name of Allah
 *
 * Hoping the appearance Promised Savior
 *
 * @author Iliya Gholami 2022 - 2023 <tg: @Iliya_Gholami>
 * @copyright Iliya Gholami 2022 - 2023 <tg: @Iliya_Gholami>
 */

namespace IliyaGholami;

class Idpay
{
	/**
	 * @const string BASE_URL url for request
	 */
	const BASE_URL = "https://api.idpay.ir/v1.1/";
	
	/**
	 * @var string $merchant merchant
	 */
	private $merchant;
	/**
	 * @var boolean $sandbox sandbax
	 */
	private $sandbox;
	
	/**
	 * Initialize
	 *
	 * @param string $merchant merchant
	 * @param boolean $sandbox sandbox
	 */
	public function __construct(object $bot, string $merchant, bool $sandbox = false) 
	{
		$this->merchant = $merchant;
		$this->sandbox = $sandbox;
	}
	
	/**
	 * Send request
	 *
	 * @param string $method idpay method
	 * @param array $datas datas
	 */
	public function sendRequest(string $method, array $datas)
	{
		$ch = curl_init();
		
		@curl_setopt_array($ch, [
			CURLOPT_URL => self::BASE_URL . $method,
			CURLOPT_POSTFIELDS => json_encode($datas),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
				"X-API-KEY: " . $this->merchant,
				"X-SANDBOX: " . $this->sandbox
			]
		]);
		
		$result = json_decode( curl_exec($ch) );
		curl_close($ch);
		
		if( isset( $result->error_message ) ) {
			throw new \Exception($result->error_message);
		}
		return (array) $result;
	}
	
	/**
	 * Payment method
	 *
	 * @param string $order_id order id
	 * @param int $amount amount ( Toman )
	 * @param string $callback callback url
	 * @param string $name name of customer
	 * @param string $phone phone number of customer
	 * @param string $mail email of customer
	 * @param string $desc description
	 */
	public function payment
	(
		string $order_id,
		int $amount,
		string $callback,
		string $name = "",
		string $phone = "",
		string $mail = "",
		string $desc = ""
	)
	{
		return $this->sendRequest("payment", [
			"order_id" => $order_id,
			"amount" => $amount . 0,
			"callback" => $callback,
			"name" => $name,
			"phone" => $phone,
			"mail" => $mail,
			"desc" => $desc
		]);
	}
	
	/**
	 * Verify
	 *
	 * @param string $id payment id
	 * @param string $order_id order id
	 */
	public function verify(string $id, string $order_id)
	{
		return $this->sendRequest("payment/verify", [
			"id" => $id,
			"order_id" => $order_id
		]);
	}
	
	/**
	 * Inquiry
	 *
	 * @param string $id payment id
	 * @param string $order_id order id
	 */
	public function inquiry(string $id, string $order_id)
	{
		return $this->sendRequest("payment/inquiry", [
			"id" => $id,
			"order_id" => $order_id
		]);
	}
	
	/**
	 * Convert code to message
	 *
	 * @param int $code status code
	 */
	public function status(int $code)
	{
		return strtr($code, [
			1 => "پرداخت انجام نشده است",
			2 => "پرداخت ناموفق بوده است",
			3 => "خطا رخ داده است",
			4 => "بلوکه شده",
			5 => "برگشت به پرداخت کننده",
			6 => "برگشت خورده سیستمی",
			7 => "انصراف از پرداخت",
			8 => "به درگاه پرداخت منتقل شد",
			10 => "در انتظار تایید پرداخت",
			100 => "پرداخت تایید شده است",
			101 => "پرداخت قبلا تایید شده است",
			200 => "به دریافت کننده واریز شد"
		]);
	}
	
	public function close()
	{
		$this->merchant = null;
		$this->sandbox = null;
	}
	
	public function __destruct()
	{
		$this->close();
	}
	
}
