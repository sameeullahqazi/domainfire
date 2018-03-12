<?php
/*
ToDos
1.	Try a Stripe Auth-Capture approach instead of Charge-Refund.
2.	Store some kind of customer reference number in order to pre fill in all their info the next time they come to make a purchase.
3.	Show the customer a list of paginated transactions that they've performed.
4.	Apple Pay: a payment gateway that takes place thru the phone. (check into it). Provide this as an alternate payment method.
*/
	header('Content-Type: application/json; charset=utf-8');
	
	try
	{
		$arr_form_data = array();
		parse_str($_POST['form_data'], $arr_form_data);
		error_log("arr_form_data: " . var_export($arr_form_data, true));
		$op = $_POST['op'];
		
		switch($op)
		{
			case 'purchase_domain':
			{
				$stripe = new StripeAPI();
				// $list_of_charges = $stripe->listAllCharges();
				// error_log("list_of_charges: " . var_export($list_of_charges, true));
				// exit();
				$amount = 1;
				$output = array();
			
				//	1.	Get domain name to search for
				$domain = $arr_form_data['domain'];
				
				$registrantFirstName = $arr_form_data['registrantFirstName'];
				$registrantLastName = $arr_form_data['registrantLastName'];
				$registrantAddress1 = $arr_form_data['registrantAddress1'];
				$registrantCity = $arr_form_data['registrantCity'];
				$registrantPostalCode = $arr_form_data['registrantPostalCode'];
				$registrantEmail = $arr_form_data['registrantEmail'];
				$registrantStateProvince = $arr_form_data['registrantStateProvince'];
				$registrantPhone = $arr_form_data['registrantPhone'];
				
				if(!empty($domain))
				{
					$namecheap = new NamecheapAPI();
					$is_domain_available = $namecheap->isDomainAvailable($domain);
					//error_log("is_domain_available: " . var_export($is_domain_available, true));
					
					//	2. Check if domain is available
					if($is_domain_available)
					{

						
						//	3. Charge Card via Stripe
						$charge = $stripe->createCharge($amount);
						error_log("charge when created initially: " . var_export($charge, true));
						$charge_id = $charge['id'];
						$output[] = "Card successfully charged via Stripe";
						
						//	4.	Create specified Domain
						$address = array(
							'fName' => $registrantFirstName,
							'lName' => $registrantLastName,
							'address' => $registrantAddress1,
							'city' => $registrantCity,
							'email' => $registrantEmail,
							'zip' => $registrantPostalCode,
							'state' => $registrantStateProvince,
							'phone' => $registrantPhone,
						);
						$response = $namecheap->buy($domain, $address, 1);
						error_log("response after purchasing domain: " . var_export($response, true));
						if(!empty($response['Errors']))
						{
							// $refund = $stripe->refundCharge($charge_id);
							// error_log("refund: " . var_export($refund, true));
							throw new Exception("Error when trying to purchase the domain $domain: " . implode(". ", $response['Errors']));
						}
						else
						{
							$charge->capture();
							error_log("charge when captured: " . var_export($charge, true));
							$output[] = "Domain $domain purchased and created successfully";
						}
						
						echo json_encode(array('success' => $output));
						exit();
					}
					else
					{
						throw new Exception ("Domain $domain not available");
					}
				}
				else
				{
					throw new Exception("Please type in a domain");
				}
				break;
			}
		}
		exit();
	}
	catch(Exception $e)
	{
		$err_msg = $e->getMessage();
		echo json_encode(array('errors' => $err_msg));
		exit();
	}
	
?>