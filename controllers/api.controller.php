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
	header('Access-Control-Allow-Origin: http://domainfire');
	$params = $_GET;
	// error_log("params in api controller: ".var_export($params, true));
	
	
	$required_params = array(
		'LOOKUP' => array(
			array('name' => 'domain'),
		),
		'register' => array(
			array('name' => 'domain'),
			array('name' => 'period'),
			array('name' => 'reg_type', 'values' => array('new')),
			array('name' => 'reg_username'),
			array('name' => 'reg_password'),
			array('name' => 'phone'),
			array('name' => 'email'),
			array('name' => 'address1'),
			array('name' => 'address2'),
			array('name' => 'city'),
			array('name' => 'state'),
			array('name' => 'country'),
			array('name' => 'postal_code'),
		),
		
	);
	
	try
	{
		$openSRS = new OpenSRSAPI();
		$action = $params['action'];
		$errors = array();
		foreach($required_params[$action] as $param)
		{
			if(!isset($params['attributes'][$param['name']]))
				$errors[] = "'" . $param['name'] . "'";
		}
		if(!empty($errors))
			throw new Exception("Please provide the following attributes: " . implode(", ", $errors) . ".");
		// $errors = $csapi->AuthenticateUser($params);
		$response = $openSRS->apiCall($params);
		// error_log("response in api controller: ".var_export($response, true));
	
		echo Common::json_format(json_encode($response));
		// echo json_encode($response);
		exit();
	}
	catch(Exception $e)
	{
		$err_msg = $e->getMessage();
		echo Common::json_format(json_encode(array('errors' => $err_msg)));
		exit();
	}
?>