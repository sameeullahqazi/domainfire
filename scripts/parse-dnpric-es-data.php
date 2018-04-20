<?php
	require_once dirname(__DIR__). '/includes/app_config.php';
	//autoload all required classes from the /classes directory
	function __autoload($class_name) {
		if(file_exists(dirname(__DIR__). '/classes/'.$class_name.'.class.php'))
			require_once dirname(__DIR__). '/classes/'.$class_name.'.class.php';
	
		// error_log("class_name in autoload: $class_name");
	}

	$db = new Database();
	try{
		$db->connect();
	} catch(Exception $e)
	{
		die ("The following error occurred: ".$e->getMessage());
	}

	//start the session
	session_start();


	error_reporting(E_ALL);

	/*
	-	ITERATE DIRECTORY FOR ALL FILES
	-	OPEN FILE CONTENT, PARSE AND FETCH RELEVANT DATA
	*/
	$dirname = dirname(dirname(dirname(__DIR__))) . '/dnpric.es';
	$files = scandir($dirname);
	
	$arr_domains_data = array();
	
	$files_already_scanned = array();
	$sql = "select distinct(file_name) as file_name from domains_data order by created";
	$rows = BasicDataModel::getDataTable($sql);
	foreach($rows as $row)
	{
		$files_already_scanned[] = $row['file_name'];
	}
	sort($files);
	sort($files_already_scanned);
	$files_to_scan = array_values(array_diff($files, $files_already_scanned));
	
	// error_log("files: " . var_export($files, true));
	// error_log("files_already_scanned: " . var_export($files_already_scanned, true));
	// error_log("files_to_scan: " . var_export($files_to_scan, true));
	
	
	
	
	
	
	// exit();
	
	foreach($files_to_scan as $i => $filename)
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
							$date_month_year = $obj_dates->item(0)->textContent . ' ' . $obj_dates->item(1)->textContent;
							$price = $obj_price->textContent;
							list($price_symbol, $price_value, $price_currency) = explode(' ', $price);
							$broker = $obj_broker->textContent;
							
							$price_value = floatval(str_replace(',', '', $price_value)); // Convert to decimal
							
							
							// error_log("domain_url: $domain_url, domain_name: $domain_name, date_month_year: $date_month_year, price: $price, price_symbol: $price_symbol, price_value: $price_value, price_currency: $price_currency, broker: $broker, broker_url: $broker_url");
							if(!isset($arr_domains_data[$domain_name]))
							{
								$arr_domains_data[$domain_name] = array(
									'domain_name' => $domain_name,
									'time_created' => $time_created,
									// 'domain_url' => $domain_url,
									'date_month_year' => $date_month_year, 
									// 'price' => $price,
									'price_symbol' => $price_symbol,
									'price_value' => $price_value,
									'price_currency' => $price_currency,
									'broker' => $broker,
									// 'broker_url' => $broker_url,
									'file_name' => $filename,
								);
								try
								{
									BasicDataModel::InsertTableData('domains_data', $arr_domains_data[$domain_name]);
								}
								catch(Exception $e)
								{
									$eMessage = $e->getMessage();
									$eCode = Database::mysqli_errno(); // $e->getCode();
									// error_log("Exception (" . $eCode . ") : $eMessage");
									switch($eCode)
									{
										case 1062:
											break;
										
										default:
											throw $e;
									}
								}
							}
						}
					}
				}
				
			}
		
			// Move file
			// $res = rename($dirname . "/" . $filename, $dirname . "/parsed/" . $filename);
			// error_log("file renamed: " . var_export($res, true));
		}
	}
	// error_log("arr_domain_data: " . var_export($arr_domains_data, true));

	exit();
	
?>