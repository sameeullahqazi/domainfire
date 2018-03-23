<?php
	error_reporting(E_ALL);
	require_once(__DIR__ . "/classes/OpenSRSAPI.class.php");

	/* gets the data from a URL */
	function get_data($url) {
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		if (curl_error($ch))
			error_log("curl error: " . curl_error($ch));

		// Get the status code
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		error_log("curl status: " . var_export($status, true));
		curl_close($ch);
		return $data;
	}
	
	
	/*
	-	ITERATE DIRECTORY FOR ALL FILES
	-	OPEN FILE CONTENT, PARSE AND FETCH RELEVANT DATA
	*/
	$dirname = dirname(dirname(__DIR__)) . '/dnpric.es';
	$files = scandir($dirname);
	error_log("files: " . var_export($files, true));
	$arr_domains_data = array();
	
	foreach($files as $i => $filename)
	{
		$exploded_filename = explode('-', $filename);
		$num_elements_exploded_filename = count($exploded_filename);
		// error_log("exploded_filename: " . var_export($exploded_filename, true));
		// error_log("num_elements_exploded_filename: " . var_export($num_elements_exploded_filename, true));
		
		if($num_elements_exploded_filename == 7)
		{
			list($tmp, $year, $month, $day, $hour, $minute, $second) = $exploded_filename;
			$time_created = $year . '-' . $month . '-' . $day . ' '. $hour . ':' . $minute . ':00';
			// error_log("time_created: " . $time_created);
			$html = file_get_contents($dirname . "/" . $filename);
			// error_log("html: " . $html);
			$dom = new DOMDocument();
			@$dom->loadHTML($html);
			$dom->preserveWhiteSpace = false;
			// $obj_table = $dom->getElementsByTagName('table')->item(0);
			$obj_table = $dom->getElementById('known_domain_name_prices');
			$obj_rows = $obj_table->getElementsByTagName('tr');
			$num_rows = $obj_rows->length;
			// error_log("num_rows: " . $num_rows);
			foreach($obj_rows as $j => $row)
			{
				// Ignore the header row
				if($j > 0)
				{
					$obj_columns = $row->getElementsByTagName('td');
					if(is_object($obj_columns))
					{
						$num_columns = $obj_columns->length;
						// error_log("num_columns: " . $num_columns);
					
						if($num_columns == 4)
						{
							$broker_url = NULL;
							$domain_url = NULL;
							$price_currency = NULL;
							$price_value = NULL;
							$price_symbol = NULL;
							
							$obj_domain_name = $obj_columns->item(0)->getElementsByTagName('a')->item(0);
							$obj_dates = $obj_columns->item(1)->getElementsByTagName('a');
							$obj_price = $obj_columns->item(2);
							$obj_broker = $obj_columns->item(3)->getElementsByTagName('a')->item(0);
							if(!empty($obj_broker))
							{
								$broker_url = $obj_broker->getAttribute('href');
							}
							else
							{
								$obj_broker = $obj_columns->item(3);
							}
							
							$domain_url = $obj_domain_name->getAttribute('href');
							
							$domain_name = $obj_domain_name->textContent;
							$date = $obj_dates->item(0)->textContent . ' ' . $obj_dates->item(1)->textContent;
							$price = $obj_price->textContent;
							list($price_symbol, $price_value, $price_currency) = explode(' ', $price);
							$broker = $obj_broker->textContent;
							
							$price_value = floatval(str_replace(',', '', $price_value)); // Convert to decimal
							
							
							// error_log("domain_url: $domain_url, domain_name: $domain_name, date: $date, price: $price, price_symbol: $price_symbol, price_value: $price_value, price_currency: $price_currency, broker: $broker, broker_url: $broker_url");
							if(!isset($arr_domains_data[$domain_name]))
							{
								$arr_domains_data[$domain_name] = array(
									'created' => $time_created,
									'domain_url' => $domain_url,
									'date' => $date, 
									'price' => $price,
									'price_symbol' => $price_symbol,
									'price_value' => $price_value,
									'price_currency' => $price_currency,
									'broker' => $broker,
									'broker_url' => $broker_url,
									'file_name' => $filename,
								);
							}
						}
					}
				}
				
			}
		
			// Move file
			$res = rename($dirname . "/" . $filename, $dirname . "/parsed/" . $filename);
			error_log("file renamed: " . var_export($res, true));
		}
	}
	error_log("arr_domain_data: " . var_export($arr_domains_data, true));

	exit();
	
	
	
	$url = "http://dnpric.es/recent/";
	$url = "https://namebio.com/search-submit.php";
	
	$params = array(
		// 'venue' => 'Any',
		'placement' => 'Anywhere',
		'extension' => 'Any',
		'maincat' => '0',
		'daterange' => 'Any',
		'draw' => '1',
		'length' => '10',
		'subcat' => 'N%2FA',
		// 'excludehyphens' => 'false',
		// 'excludenumbers' => 'false',
	);
	$data_json = json_encode($params);	
	
	$arr_curl_options = array(
		'Connection: keep-alive',
		'Keep-Alive: timeout=15, max=92',
		'Access-Control-Allow-Origin: *',
		'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
		'Content-Length: ' . strlen($data_json),
		'Cookie: __utmz=225200645.1521465046.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); __utma=225200645.740171997.1521465046.1521518892.1521688812.5; __utmc=225200645; __utmt=1; __utmb=225200645.40.9.1521689006113',
		
	);
	

	$post_fields = $data_json;
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $arr_curl_options);

	//Execute request 
	$response = curl_exec($ch);
	$err = curl_error($ch);
	// if($err)
	error_log("Curl Error: " . var_export($err, true));

	curl_close($ch);
	flush();
	error_log("xml_response: " . var_export($response, true));

	exit();
	

	
	
		$response = get_data($url);
	error_log("response: " . var_export($response, true));
	exit();
	
	
	
	
	
	

	// Note: Requires cURL library
	$TEST_MODE = true;

	$connection_options = [
		'live' => [
			'api_host_port' => 'https://rr-n1-tor.opensrs.net:55443',
			'api_key' => '88192e93dc8917e2b20532e84181036ab7379c03890782e12650c5f9c15bea34e3036fd3d9ef278560087103e8b0b214ef0ce4e98fffe369',
			'reseller_username' => 'davistv'
		],
		'test' => [
			'api_host_port' => 'https://horizon.opensrs.net:55443',
			'api_key' => '6284b4179e2746b48f243c72ddf3b4bc929db428c6031026956a2f9ddae4f4385bd4c01adfbc6f07674caf8194b4dfefc94e6c9e23e60b48',
			'reseller_username' => 'lamproslabs'
		]
	];

	if ($TEST_MODE) {
		$connection_details = $connection_options['test'];
	} else {
		$connection_details = $connection_options['live'];
	}

	$xml = '
	<?xml version=\'1.0\' encoding=\'UTF-8\' standalone=\'no\' ?>
	<!DOCTYPE OPS_envelope SYSTEM \'ops.dtd\'>
	<OPS_envelope>
	<header>
		<version>0.9</version>
	</header>
	<body>
	<data_block>
		<dt_assoc>
			<item key="protocol">XCP</item>
			<item key="action">LOOKUP</item>
			<item key="object">DOMAIN</item>
			<item key="attributes">
			 <dt_assoc>
					<item key="domain">myfirstopensrsapitest.com</item>
			 </dt_assoc>
			</item>
		</dt_assoc>
	</data_block>
	</body>
	</OPS_envelope> 
	';

	$data = [
		'Content-Type:text/xml',
		'X-Username:' . $connection_details['reseller_username'],
		'X-Signature:' . md5(md5($xml . $connection_details['api_key']) .  $connection_details['api_key']),
	];

	$ch = curl_init($connection_details['api_host_port']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $data);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

	$response = curl_exec($ch);
	error_log("Curl Error: " . curl_error($ch));
	error_log('Request as reseller: ' . $connection_details['reseller_username'] . "\n" .  $xml . "\n");
	error_log( "Response after calling:\n" . var_export($response, true));
	
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
	error_log("arr_data: " . var_export($arr_data, true));
	exit();

	$openSRS = new OpenSRSAPI();
	
	
	$arr_data = array(
		'protocol' => 'XCP',
		'action' => 'LOOKUP',
		'object' => 'DOMAIN',
		'attributes' => array(
			'domain' => 'myfirstopensrsapitest.com',
			'domain1' => 'myfirstopensrsapitest.com1',
			'domain2' => 'myfirstopensrsapitest.com2',
			'attributes2' => array(
				'domain5' => 'myfirstopensrsapitest.com23',
				'domain16' => 'myfirstopensrsapitest.com143',
				'domain27' => 'myfirstopensrsapitest.com25',
			),
		),
	);
	$xml = $openSRS->generateXMLTree($arr_data);
	$xml = '<?xml version=\'1.0\' encoding=\'UTF-8\' standalone=\'no\' ?>
	<!DOCTYPE OPS_envelope SYSTEM \'ops.dtd\'>
	<OPS_envelope>
	<header>
		<version>0.9</version>
	</header>
	<body>
	<data_block>
		<dt_assoc>
			<item key="protocol">XCP</item>
			<item key="action">LOOKUP</item>
			<item key="object">DOMAIN</item>
			<item key="attributes">
			 <dt_assoc>
					<item key="domain">myfirstopensrsapitest.com</item>
			 </dt_assoc>
			</item>
		</dt_assoc>
	</data_block>
	</body>
	</OPS_envelope>';
	error_log("xml: " . $xml);
	$data = new SimpleXMLElement($xml);
	error_log("data converted: " . var_export($data, true));
	exit();
?>