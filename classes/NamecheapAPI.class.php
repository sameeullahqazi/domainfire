<?php
// namespace App;
class NamecheapApi
{
    protected $client;
    protected $base_uri;
    protected $user;
    protected $apiKey;
    protected $ip;
    public function __construct($ip = null) {
		
		global $nc_api_live, $nc_api_user, $nc_api_key, $nc_api_ip, $nc_pwd, $nc_sandbox_api_key;
		// error_log("NamecheapApi class: nc_api_live: " . var_export($nc_api_live, true) . ", nc_api_user: $nc_api_user, nc_api_key: $nc_api_key, nc_api_ip: $nc_api_ip");
		
        $live = $nc_api_live; // env('NAMECHEAP_LIVE');
        $this->apiKey = $live ? $nc_api_key : $nc_sandbox_api_key; // env('NAMECHEAP_API_KEY') : env('NAMECHEAP_SANDBOX_API_KEY');
        $this->user = $nc_api_user; // env('NAMECHEAP_USER');
        $this->ip = $nc_api_ip; // env('SERVER_IP', $ip);
        $this->base_uri = $live ? "https://api.namecheap.com/xml.response?" : "https://api.sandbox.namecheap.com/xml.response?";
    }

	public function apiCall($method, $params, $category = "domains")
	{
		global $nc_api_categories;
		
		try
		{
			if(!isset($nc_api_categories[$category]))
			{
				throw new Exception("Invalid category passed: " . $category);
			}
			$command = "namecheap." . $category . "." . $method;
			
			$api_url = $this->base_uri.
				"ApiUser=".$this->user.
				"&ApiKey=".$this->apiKey.
				"&UserName=".$this->user.
				"&Command=".$command.
				"&ClientIp=".$this->ip;
			
			if(!empty($params))
				$api_url .= "&" . http_build_query($params);
				
			// error_log("api_url in apiCall: " . $api_url);
			$xml_response = file_get_contents($api_url);
			$json_response = $this->makeTheRequest($xml_response);
			$response = json_decode($json_response, true);
			
		
			return $response;			
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}
	
    public function search($domain) {
		try
		{
			$params = array(
				'DomainList' => $domain,
			);
			$response = $this->apiCall("check", $params);
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
			$response = $this->search($domain);
			
			if(!empty($response['Errors']))
			{
				throw new Exception("Error when checking domain availability: " . implode(". ", $response['Errors']));
			}
			
			// error_log("response in isDomainAvailable: " . var_export($response, true));
			$str_available = $response['CommandResponse']['DomainCheckResult']['@attributes']['Available'];
			$str_domain = $response['CommandResponse']['DomainCheckResult']['@attributes']['Domain'];
			
			$bAvailable = $str_available === "true" && $str_domain === $domain;
			// error_log("str_available; " . var_export($str_available, true) . ", str_domain: " . var_export($str_domain , true) . ", bAvailable: " . var_export($bAvailable, true));
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
	
	/*
	public function isDomainPurchaseSuccessful($domain, $address)
	{	
		try
		{
			$bPurchaseSuccessful = false;
			$response = $this->buy($domain, $address, $years);
			if(!empty($response['Errors']))
			{
				throw new Exception("Error when trying to purchase a domain: " . implode(". ", $response['Errors']));
			}
			return $bPurchaseSuccessful;
		}
		catch(Exception $e)
		{
			throw $e;
		}
		
	}
	*/
	
    public function setDefault($sld, $tld) {
        $response = file_get_contents($this->base_uri.
            "ApiUser=".$this->user.
            "&ApiKey=".$this->apiKey.
            "&UserName=".$this->user.
            "&Command="."namecheap.domains.dns.setDefault".
            "&ClientIp=".$this->ip.
            "&SLD=".$sld.
            "&TLD=".$tld
        );
        return $this->makeTheRequest($response);
    }
    public function setHosts($sld, $tld, $records) {
        $recordString = "";
        foreach ($records as $record) {
            $recordString .=
                "&HostName1=".$record->name.
                "&RecordType1=".$record->type.
                "&Address1=".($record->ip) ? $record->ip : $this->ip.
                "&TTL1=1200";
        }
        $response = file_get_contents($this->base_uri.
            "ApiUser=".$this->user.
            "&ApiKey=".$this->apiKey.
            "&UserName=".$this->user.
            "&Command="."namecheap.domains.dns.setHosts".
            "&ClientIp=".$this->ip.
            "&SLD=".$sld.
            "&TLD=".$tld.
            $recordString
        );
        return $this->makeTheRequest($response);
    }
    private function makeTheRequest($request) {
        str_replace("@", "", $request);
        $xml = simplexml_load_string($request);
		// error_log("xml in makeTheRequest(): " . var_export($xml, true));
        return json_encode($xml);
    }
}