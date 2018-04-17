<?php
 //error_reporting(0); // to supress errors on php 5 dev environment
	// date_default_timezone_set("UTC");
	$errors = array();
	global $app_id, $app_secret, $app_url;
	global $db;
			
	$response = array(
	);
	// header('Content-type: text/plain');
	header('Content-Type: application/json; charset=utf-8');
	// header('Access-Control-Allow-Origin: http://domainfire');
	$params = $_GET;
	// error_log("params in api controller: ".var_export($params, true));
	// error_log("_POST in api controller: ".var_export($_POST, true));
	
	
	
	try
	{
		$openSRS = new OpenSRSAPI();
		$action = Common::fetchAndRemoveValue($params, 'action');
		if(empty($action))
		{
			throw new Exception("'action' param must be provided");
		}
		$errors = array();
		
		switch($action)
		{
			case "registerDomain":
				$stripe = new StripeAPI();
				$amount = 3.57;
				$output = array();
			
				//	1.	Get domain name to search for
				$domain = $params['domain'];
				$years	= $params['period'];
				$token_id = Common::fetchAndRemoveValue($params, 'token_id');
				
				if(!empty($domain))
				{
					// $namecheap = new NamecheapAPI();
					$openSRS = new OpenSRSAPI();
					$is_domain_available = $openSRS->isDomainAvailable($domain);
					// error_log("is_domain_available: " . var_export($is_domain_available, true));
					
					//	2. Check if domain is available
					// $is_domain_available = false;
					if($is_domain_available)
					{

						//	3. Charge Card via Stripe
						$charge = $stripe->createCharge($amount, $token_id);
						// error_log("charge when created initially: " . var_export($charge, true));
						$charge_id = $charge['id'];
						$output[] = "Card successfully charged via Stripe";
						
						//	4.	Create specified Domain
						$response = $openSRS->registerDomain($params);
						// error_log("response upon calling registerDomain: " . var_export($response, true));
						$transaction_info = array(
							'domain_name' => $domain,
							'years' 	=> $years,
							'state' => $response['state'],
							'stripe_charge_id' => $charge_id,
							'response_code' => $response['response_code'],
							'response_text' => $response['response_text'],
							'registered' => $response['is_success'],
							'amount' => $amount,
						);
						
						$domain_registration_successful = isset($response['response_code']) && $response['response_code'] === '200';
						
						if(!$domain_registration_successful)
						{
							// $refund = $stripe->refundCharge($charge_id);
							$transaction_info['response_error'] = $response['error'];
							Transaction::AddNewTransaction($transaction_info);
							// error_log("refund: " . var_export($refund, true));
							throw new Exception("Error when trying to purchase the domain $domain: " . $response['error']);
						}
						else
						{
							$charge->capture();
							
							// At this point in time, store the transaction and the registrant info
							
							$registrant_id = Registrant::AddNewRegistrant($params['contact_set']['owner']);
							
							$transaction_info['registrant_id'] = $registrant_id;
							$transaction_info['domain_id'] = $response['domain_id'];
							$transaction_info['order_id'] = $response['id'];
							
							Transaction::AddNewTransaction($transaction_info);
							
							// error_log("charge when captured: " . var_export($charge, true));
							$output[] = "Domain $domain purchased and created successfully";
						}
						
						echo json_encode(array('status' => $output, 'info' => $transaction_info));
						exit();
						
					}
					else
					{
						throw new Exception ("Domain $domain not available.");
					}
				}
				else
				{
					throw new Exception("Please specify a domain.");
				}
				break;
			
			default:
				throw new Exception("Invalid value '$action' provided for the 'action' param");
				break;
		}
		exit();
	}
	catch(Exception $e)
	{
		$err_msg = $e->getMessage();
		echo Common::json_format(json_encode(array('errors' => $err_msg)));
		exit();
	}
?>