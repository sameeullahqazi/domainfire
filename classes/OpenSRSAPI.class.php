<?php
// namespace App;
class OpenSRSApi
{
    protected $api_host_port;
    protected $reseller_username;
    protected $api_key;
    protected $ip;
    
    public function __construct($ip = null) {
		
		global $opensrs_live;
		// error_log("NamecheapApi class: nc_api_live: " . var_export($nc_api_live, true) . ", nc_api_user: $nc_api_user, nc_api_key: $nc_api_key, nc_api_ip: $nc_api_ip");
		
		if($opensrs_live)
		{
			// LIVE MODE
			$this->api_key				= '88192e93dc8917e2b20532e84181036ab7379c03890782e12650c5f9c15bea34e3036fd3d9ef278560087103e8b0b214ef0ce4e98fffe369';
			$this->reseller_username	= 'davistv';
			$this->ip					= '';
			$this->api_host_port		= 'https://rr-n1-tor.opensrs.net:55443';
		}
		else
		{
			// TEST MODE
			$this->api_key				= '6284b4179e2746b48f243c72ddf3b4bc929db428c6031026956a2f9ddae4f4385bd4c01adfbc6f07674caf8194b4dfefc94e6c9e23e60b48';
			$this->reseller_username	= 'lamproslabs';
			$this->api_host_port		= 'https://horizon.opensrs.net:55443';
		}
    }

	public function generateXMLTree($params, $level = 1)
	{
		$str_tab_indentation = str_repeat("\t", $level);
		$xml = "\n" . $str_tab_indentation . "<dt_assoc>";
		foreach($params as $key => $val)
		{
			$xml .= "\n\t" . $str_tab_indentation . '<item key="' . $key . '">';
			if(is_array($val))
				$xml .= "\t" . $str_tab_indentation . $this->generateXMLTree($val, $level + 1) . "\n\t" . $str_tab_indentation . "</item>";
			else
				$xml .= $val . "</item>";
		}
		$xml .= "\n" . $str_tab_indentation . "</dt_assoc>";
		return $xml;
	}

	public function createXML($params)
	{
		$xml = '
			<?xml version=\'1.0\' encoding=\'UTF-8\' standalone=\'no\' ?>
			<!DOCTYPE OPS_envelope SYSTEM \'ops.dtd\'>
			<OPS_envelope>
			<header>
				<version>0.9</version>
			</header>
			<body>
			<data_block>';
		$xml .= "\t\t\t" . $this->generateXMLTree($params);
		
		$xml .= '</data_block>
			</body>
			</OPS_envelope> 
			';
		
		return $xml;
	}
	
	public function apiCall($params)
	{
		global $nc_api_categories;
		// error_log("this in apiCall: " . var_export($this, true));
		try
		{
			if(!isset($params['action']))
				throw new error ("'action' parameter not provided!");
		
			if(!isset($params['protocol']))
				$params['protocol'] = 'XCP';
			
			if(!isset($params['object']))
				$params['object'] = 'domain';
			
			if(!isset($params['attributes']))
				$params['attributes'] = $params;
			
			// error_log("params in apiCall: " . var_export($params, true));
			$xml = $this->createXML($params);
			// error_log("xml in apiCall: " . $xml);
			$len_xml = strlen($xml);
			$data = [
				'Content-Type:text/xml',
				'X-Username:' . $this->reseller_username,
				'X-Signature:' . md5(md5($xml . $this->api_key) .  $this->api_key),
				'Content-Length: ' . $len_xml,
			];

			$ch = curl_init($this->api_host_port);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $data);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

			$xml_response = curl_exec($ch);
			$curl_error = curl_error($ch);
			if($curl_error)
			{
				error_log("Curl Error: " . curl_error($ch));
				throw new exception ($curl_error);
			}
			// error_log("xml_response in apiCall: " . $xml_response);
			$json_response = $this->makeTheRequest($xml_response);
			$response = json_decode($json_response, true);
			// error_log("response in apiCall: " . var_export($response, true));
			$response_code = $response['response_code'];
			if($response_code + 0 >= 300)
			{
				throw new Exception($response['response_text'] . " ($response_code)");
				// throw new Exception($response['error'] . " ($response_code)");
			}	
			return $response;			
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}
	
    public function lookUp($domain) {
		try
		{
			$params = array(
				'protocol' => 'XCP',
				'action' => 'LOOKUP',
				'object' => 'domain',
				'attributes' => array(
					'domain' => $domain
				),
			);
			$response = $this->apiCall($params);
			return $response;
		}
		catch(Exception $e)
		{
			throw $e;
		}			
    }
	
	public function isDomainAvailable($domain)
	{
		try
		{
			$response = $this->lookUp($domain);
			$bAvailable = isset($response['response_code']) && $response['response_code'] === '210';
			return $bAvailable;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}
	
	public function registerDomain($domain, $period, $reg_type, $reg_username, $reg_password, $contact_info)
	{
		try
		{
			$params = array(
				'protocol' => 'XCP',
				'action' => 'SW_REGISTER',
				'object' => 'domain',
				'attributes' => array(
					'domain' => $domain,
					'period' => $period,
					'custom_tech_contact' => 0, //0 means use the reseller's contact info, 1 means tech contact info must be separately provided
					'reg_type' => $reg_type,
					'reg_username' => $reg_username,
					'reg_password' => $reg_password,
					'contact_set' => array(
						'admin' => $contact_info,
						'owner' => $contact_info,
						'billing' => $contact_info,
					),
				),
			);
			$response = $this->apiCall($params);
			return $response;
		}
		catch(Exception $e)
		{
			throw $e;
		}			
    }
	
	public function domainRegistrationSuccessful($domain)
	{
		try
		{
			$response = $this->registerDomain($domain);
			$bAvailable = isset($response['response_code']) && $response['response_code'] === '200';
			return $bAvailable;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}
	
    public function buy($domain, $address, $years = 1) {
		try
		{
			$params = array(
				"DomainName" => $domain,
				"Years" => $years,
				"AuxBillingFirstName" => $address['fName'],
				"AuxBillingLastName" => $address['lName'],
				"AuxBillingAddress1" => urlencode($address['address']),
				"AuxBillingStateProvince" => $address['state'],
				"AuxBillingPostalCode" => $address['zip'],
				"AuxBillingCountry" => "US",
				"AuxBillingPhone" => $address['phone'],
				"AuxBillingEmailAddress" => $address['email'],
				"AuxBillingCity" => $address['city'],
				"TechFirstName" => $address['fName'],
				"TechLastName" => "Zen",
				"TechAddress1" => urlencode($address['address']),
				"TechStateProvince" => $address['state'],
				"TechPostalCode" => $address['zip'],
				"TechCountry" => "US",
				"TechPhone" => $address['phone'],
				"TechEmailAddress" => $address['email'],
				"TechCity" => $address['city'],
				"AdminFirstName" => $address['fName'],
				"AdminLastName" => $address['lName'],
				"AdminAddress1" => urlencode($address['address']),
				"AdminStateProvince" => $address['state'],
				"AdminPostalCode" => $address['zip'],
				"AdminCountry" => "US",
				"AdminPhone" => $address['phone'],
				"AdminEmailAddress" => $address['email'],
				"AdminCity" => $address['city'],
				"RegistrantFirstName" => $address['fName'],
				"RegistrantLastName" => $address['lName'],
				"RegistrantAddress1" => urlencode($address['address']),
				"RegistrantStateProvince" => $address['state'],
				"RegistrantPostalCode" => $address['zip'],
				"RegistrantCountry" => "US",
				"RegistrantPhone" => $address['phone'],
				"RegistrantEmailAddress" => $address['email'],
				"RegistrantCity" => $address['city']
			);
			$response = $this->apiCall("create", $params);
			return $response;
		}
		catch(Exception $e)
		{
			throw $e;
		}
    }
	
    private function makeTheRequest($response) {
        $dom = new DOMDocument();

		# loadHTML might throw because of invalid HTML in the page.
		@$dom->loadHTML($response);
		$dom->preserveWhiteSpace = false;
		$obj_item_tags = $dom->getElementsByTagName('item');
		$arr_data = array();
	
		foreach($obj_item_tags as $i => $item_tag)
		{
			$key = $item_tag->getAttribute('key');
			switch($key)
			{
				case 'attributes':
					$obj_attributes = $item_tag->getElementsByTagName('dt_assoc')->item(0)->getElementsByTagName('item');
					foreach($obj_attributes as $i => $attr)
					{
						$key2 = $attr->getAttribute('key');
						$val2 = $attr->textContent;
						$arr_data['attributes'][$key2] = $val2;
					}
					break;
			
				default:
					$val = $item_tag->textContent;
					$arr_data[$key] = $val;
					break;		
			}
		}
        return json_encode($arr_data);
    }
}