<?php
	$correct_code = "wf0fcvj5";
	$rows = array();
	// error_log("POST in mysql-ui controller: " . var_export($_POST, true));
	if(!empty($_POST))
	{
		$sql = $_POST['sql'];
		$code = $_POST['code'];
		
		$rows = array();
		$num_rows = 0;
		
		
		
		try
		{
			// Database::mysqli_query("set autocommit=0");
			// BasicDataModel::start_transaction();
			if(strpos(strtolower($sql), "select ") === 0 || strpos(strtolower($sql), "desc ") === 0 || strpos(strtolower($sql), "show ") === 0)
			{
				$rows = BasicDataModel::getDataTable($sql);
				$num_rows = count($rows);
			}
			else
			{
				if($code != $correct_code)
					throw new Exception("Can only use 'select', 'show' or 'desc' ");
				
				$arr_sql = explode(";\r\n", $sql);
				// error_log("arr_sql: " . var_export($arr_sql, true));
				foreach($arr_sql as $i => $new_sql)
				{
					$new_sql = trim($new_sql);
					if(!empty($new_sql))
					{
						$rs = Database::mysqli_query($new_sql);
						
						if(!$rs && is_bool($rs))
							throw new Exception("SQL error in query number " . ($i + 1) . ": " . Database::mysqli_error() . "<br />SQL: " . $new_sql);
					}
				}
				// BasicDataModel::commit();
			}
			// BasicDataModel::stop_transaction();
			$msg =  "<h6 style='color:green;font-weight:bold;'>" . $num_rows. " rows found.</h6>";
			
		}
		catch(Exception $e)
		{
			error_log("Exception in mysql-ui: " . $e->getMessage());
			// BasicDataModel::rollback();
			// BasicDataModel::stop_transaction();
			$msg = "<h4 style='color:red;font-weight:bold;'>" . $e->getMessage(). "</h4>";
		}
	}
?>