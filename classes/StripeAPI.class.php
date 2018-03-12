<?php
require_once(dirname(__DIR__) . "/includes/stripe-php-6.4.1/init.php");
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
	
	public function createCharge($amount, $capture = false, $currency = 'usd', $description = '')
	{
		try
		{
			$src_token = $this->createToken();
			$source = $src_token['id'];
			$params = array(
			  "amount" => $amount * 100,
			  "currency" => $currency,
			  "source" => $source, // obtained with Stripe.js
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
}
?>