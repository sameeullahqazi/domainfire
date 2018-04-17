<?php
require_once(dirname(__DIR__) . "/includes/stripe-php-6.4.1/init.php");
require_once(dirname(__DIR__) . '/includes/app_config.php');
class StripeAPI
{
    protected $user;
    protected $publicKey;
    protected $privateKey;
	protected $cardNumber;
	protected $expMonth;
	protected $expYear;
	protected $cvc;
	
    public function __construct($public_key = null, $private_key = null, $card_number = null, $exp_month = null, $exp_year = null, $cvc = null) {
		
		global $stripe_api_test_public_key, $stripe_api_test_private_key, $stripe_test_card_number, $stripe_test_exp_month, $stripe_test_exp_year, $stripe_test_cvc;
		// error_log("StripeApi class: stripe_api_test_public_key: " . $stripe_api_test_public_key . ", stripe_api_test_private_key: " . $stripe_api_test_private_key);
		try 
		{
			$this->publicKey = $public_key ? $public_key : $stripe_api_test_public_key;
			$this->privateKey = $private_key ? $private_key : $stripe_api_test_private_key;
			$this->cardNumber = $card_number ? $card_number : $stripe_test_card_number;
			$this->expMonth = $exp_month ? $exp_month : $stripe_test_exp_month;
			$this->expYear = $exp_year ? $exp_year : $stripe_test_exp_year;
			$this->cvc = $cvc ? $cvc : $stripe_test_cvc;
			
			\Stripe\Stripe::setApiKey($this->privateKey);			
		}
		catch (Exception $e)
		{
			throw $e;
		}
        
    }
    
    public function getPublicKey()
    {
    	return $this->publicKey;
    }
    
    public function getPrivateKey()
    {
    	return $this->privateKey;
    }
	
	public function listAllCharges($limit = null)
	{
		try
		{
			$params = array();
		
			if(!empty($limit))
				$params['limit'] = $limit;
			
			$response = \Stripe\Charge::all($params);
			// error_log("response in StripeAPI::listAllCharges(): " . var_export($response, true));
			return $response;
		}
		catch(Exception $e)
		{
			throw $e;
		}
		
	}
	
	public function createToken()
	{
		error_log("this->cardNumber: " . $this->cardNumber . ", this->exp_month: " . $this->expMonth . ", this->expYear: ". $this->expYear . ", this->cvc: " . $this->cvc);
		$params = array(
			"number" => $this->cardNumber,
			"exp_month" => $this->expMonth,
			"exp_year" => $this->expYear,
		);
		
		if(!empty($this->cvc))
			$params['cvc'] = $this->cvc;
		
		try
		{
			$response = \Stripe\Token::create(array(
			  "card" => $params
			));
			return $response;
		}
		catch(Exception $e)
		{
			throw $e;
		}
		
	}
	
	public function createCharge($amount, $source = null, $capture = false, $currency = 'usd', $description = '')
	{
		try
		{
			if(empty($source))
			{
				$src_token = $this->createToken();
				$source = $src_token['id'];
			}
			$params = array(
			  "amount" => $amount * 100,
			  "currency" => $currency,
			  "source" => $source,
			  "description" => $description,
			  "capture" => $capture,
			);
			$response = \Stripe\Charge::create($params);
			return $response;
		}
		catch(Exception $e)
		{
			throw $e;
		}
		
		
	}
	
	public function refundCharge($charge_id, $reason = null, $amount = null)
	{
		try
		{
			$params = array(
			  "charge" => $charge_id,
			);
			$response = null;
			
			
			if(!empty($reason))
				$params['reason'] = $reason;
			
			if(!empty($amount))
				$params['amount'] = $amount;
			
			$response = \Stripe\Refund::create($params);
			return $response;
		}
		catch(Exception $e)
		{
			throw $e;
		}
		
	}
	
	public function listChargesUsingAPI()
	{
		$url = "https://api.stripe.com/v1/charges";
	
		$secret_key = $this->getPrivateKey();
	
		$curl = curl_init();			
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPGET, true);
		curl_setopt($curl, CURLINFO_HEADER_OUT, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Authorization: Bearer '.$secret_key,
		));
	
		$response = curl_exec($curl);
		$err = curl_error($curl);
		// if($err)
		error_log("Curl Error: " . var_export($err, true));
		error_log("response in executeJSON(): " . var_export($response, true));
		return $response;
	}
	
	public function createChargeUsingAPI($amount, $source, $capture = false, $currency = 'usd', $description = '')
	{
		$url = "https://api.stripe.com/v1/charges";
	
		$secret_key = $this->getPrivateKey();
		$post_data = array(
			'amount' => $amount * 100,
			'currency' => $currency,
			'source' => $source,
			'capture' => $capture,
		);
		$json_post_data = json_encode($post_data);
		error_log("json_post_data: " . $json_post_data);
	
		$curl = curl_init();			
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
		// curl_setopt($curl, CURLINFO_HEADER_OUT, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/x-www-form-urlencoded; charset=UTF-8', 
			'Content-Length: ' . strlen($json_post_data),
			'Authorization: Bearer '.$secret_key,
		));
	
		$response = curl_exec($curl);
		$err = curl_error($curl);
		// if($err)
		error_log("Curl Error: " . var_export($err, true));
		error_log("response in executeJSON(): " . var_export($response, true));
		return $response;
	} 
}
?>