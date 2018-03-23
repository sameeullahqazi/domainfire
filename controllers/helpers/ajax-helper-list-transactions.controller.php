<?php
	$params = array();
	$limit = 5;
	$page_number = 1;
	$search_criteria = array();
	$sort_by = "";
	$sort_order = "";
	$num_pages = NULL;
	
	// error_log("POST in ajax-helper-list-transactions: " . var_export($_POST, true));
	if(!empty($_POST))
	{
		$arr_form_data = array();
		parse_str($_POST['form_data'], $arr_form_data);
		
		// error_log("arr_form_data in ajax-helper-list-transactions: " . var_export($arr_form_data, true));
		$num_pages	= $arr_form_data['hdn_num_pages'];
		$limit		= $arr_form_data['txt_limit'];
		$page_number = $arr_form_data['txt_page_number'];
		
		if(!empty($_POST['btn_first_page']))
			$page_number = 1;
		
		if(!empty($_POST['btn_next_page']) && $page_number < $num_pages)
			$page_number++;
		
		if(!empty($_POST['btn_previous_page']) && $page_number > 1)
			$page_number--;
			
		if(!empty($_POST['btn_last_page']))
			$page_number = $num_pages;
	}

	$offset = ($page_number - 1) * $limit;
	// error_log("offset: $offset, limit: $limit, num_pages: $num_pages, page_number: $page_number");
	$params = array();
	// $params['offset'] = $offset;
	// $params['limit'] = $limit;
	$params['search_criteria'] = $search_criteria;
	$params['sort_by'] = $sort_by;
	$params['sort_order'] = $sort_order;
	
	// list($challan_data, $challan_details_data, $num_rows) = Challan::getChallanData($params);
	list($rows, $num_rows) = Transaction::getTransactions($params, $limit, $offset);
	
	$num_pages = ceil($num_rows / $limit);
	if($num_pages == 0) $num_pages = 1;	
?>