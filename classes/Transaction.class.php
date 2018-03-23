<?php
	class Transaction extends BasicDataModel
	{
		var $registrant_id;
		var $domain_name;
		var $years;
		var $registered;
		var $amount;
		var $domain_id;
		var $response_code;
		var $response_text;
		var $response_error;
		var $order_id;
		var $transaction_id;
		var $stripe_charge_id;
		var $state;
		var $created;
		var $modified;
		
		public static function AddNewTransaction($data)
		{
			try
			{
				$transaction_id = BasicDataModel::InsertTableData('transactions', $data);
				return $transaction_id;
			}
			catch(Exception $e)
			{
				throw $e;
			}
		}
		
		public static function getTransactions($params, $limit, $offset)
		{
			$search_criteria = "";
			if(!empty($params['search_criteria']))
				$search_criteria = " where " . implode(' and ', $params['search_criteria']);
		
			$order_by_clause = ""; //, st.firstname, st.lastname, ch.issue_date"; // " order by c.branch, c.class_number, c.section, st.roll_no ";
			if(!empty($params['sort_by']))
			{
				$order_by_clause = " order by " . $params['sort_by'];
				if(!empty($params['sort_order']))
					$order_by_clause .= " " . $params['sort_order'];
			
				$order_by_clause .= ", receipt_number";
			}
		
			$rows = array();
		
			$num_rows = 0;
			$sql = "select count(tr.id) as num_rows
			from transactions tr
			$search_criteria";
		
			$rs = Database::mysqli_query($sql);
			$row = Database::mysqli_fetch_assoc($rs);
			if(!empty($row['num_rows']))
				$num_rows = $row['num_rows'];
		
			$sql = "select tr.* 
			from transactions tr
			$search_criteria
			$order_by_clause
			limit $offset, $limit";
		
			// error_log("SQL in Challan::getTransactions(): " . $sql);
		
			$rs = Database::mysqli_query($sql);
			if(!$rs)
			{
				error_log("SQL error when getting transactions: " . Database::mysqli_error() . "\nSQL: " . $sql);
			}
			else
			{
				while($row = Database::mysqli_fetch_assoc($rs))
				{
					$transaction_id = $row['id'];
					$rows[$transaction_id] = $row;
				}
			}
			return array($rows, $num_rows);
		}
	}
?>