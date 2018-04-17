<?php
/*
ToDos
1.	Try a Stripe Auth-Capture approach instead of Charge-Refund.
2.	Store some kind of customer reference number in order to pre fill in all their info the next time they come to make a purchase.
3.	Show the customer a list of paginated transactions that they've performed.
	i.	Create a database table 'transactions' and corresponding 'Transaction' class.
	ii.	
4.	Apple Pay: a payment gateway that takes place thru the phone. (check into it). Provide this as an alternate payment method.
5.	Grab HTML content from the given pages on Slack.
*/
	header('Content-Type: application/json; charset=utf-8');
	
	try
	{
		$arr_form_data = array();
		parse_str($_POST['form_data'], $arr_form_data);
		// error_log("arr_form_data: " . var_export($arr_form_data, true));
		$op = $_POST['op'];
		
		switch($op)
		{
			case 'purchase_domain':
			{
				$stripe = new StripeAPI();
				// $list_of_charges = $stripe->listAllCharges();
				// error_log("list_of_charges: " . var_export($list_of_charges, true));
				// exit();
				$amount = 3.57;
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
				$registrantOrgName = $arr_form_data['registrantOrgName'];
				$years	=	1;
				
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
						$charge = $stripe->createCharge($amount);
						// error_log("charge when created initially: " . var_export($charge, true));
						$charge_id = $charge['id'];
						$output[] = "Card successfully charged via Stripe";
						
						//	4.	Create specified Domain
						$contact_info = array(
							'first_name' => $registrantFirstName,
							'last_name' => $registrantLastName,
							'phone' => $registrantPhone,
							'email' => $registrantEmail,
							'address1' => $registrantAddress1,
							'address2' => 'N Nazimbad',
							'city' => $registrantCity,
							'state' => $registrantStateProvince,
							'country' => 'US',
							'postal_code' => $registrantPostalCode,
							'org_name' => $registrantOrgName,
						);
						
						$reg_type = 'new';
						$reg_username = 'sameeullahqazi';
						$reg_password = 'abcdef123456';
						
						$attributes = array(
							'domain' => $domain,
							'period' => $years,
							'custom_tech_contact' => 0, //0 means use the reseller's contact info, 1 means tech contact info must be separately provided
							'reg_type' => $reg_type,
							'reg_username' => $reg_username,
							'reg_password' => $reg_password,
							'contact_set' => array(
								'admin' => $contact_info,
								'owner' => $contact_info,
								'billing' => $contact_info,
							),
						
						$response = $openSRS->registerDomain($attributes);
						
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
							
							$registrant_id = Registrant::AddNewRegistrant($contact_info);
							
							$transaction_info['registrant_id'] = $registrant_id;
							$transaction_info['domain_id'] = $response['domain_id'];
							$transaction_info['order_id'] = $response['id'];
							
							Transaction::AddNewTransaction($transaction_info);
							
							// error_log("charge when captured: " . var_export($charge, true));
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