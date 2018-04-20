<?php
	require_once(dirname(dirname(__FILE__)) . '/includes/email_parsing.php');
	
	class BasicDataModel
	{
		var $id;
		var $obj_ready_only = false;

		public static $plurals = array(
			'schoolterm' 		=> 'school_terms',
			'classroom' 		=> 'classes',
			'recordingslip'	=> 'recording_slips',
			'recordingslipdetail'	=> 'recording_slip_details',
			'teachersubject'	=> 'teachers_subjects',
			'useractivitylog' => 'user_activity_log',
			'reportcard' 		=> 'report_cards',
			'appconfig' 		=> 'app_config',
			'expensedetail' 	=> 'expense_details',
		);

		public static $salt = '9201340522657012';

		function __construct($id = null, $read_only_mode = false){
			if(!empty($id))
			{
				$id = "id='" . $id . "'";
				$this->Select($id, $read_only_mode);
			}
		}
		
		private static function plural($singular) {
			if (isset(BasicDataModel::$plurals[strtolower($singular)])) {
				return BasicDataModel::$plurals[strtolower($singular)];
			} else {
				return $singular.'s';
			}
		}

		
		
		// Fills the caller object with the data of the first row matching the criteria passed
		public function Select($criteria, $read_only_mode = false)
		{
			// Set read only mode
			$this->obj_ready_only = $read_only_mode;
			try
			{
				$class_name = get_class($this);
				$object_attributes = array_diff_key(get_object_vars($this), get_class_vars(__CLASS__));
				$table_name = $this->plural(strtolower($class_name));
				$sql = "select * from $table_name where $criteria";
				$rs = Database::mysqli_query($sql);

				if(!$rs)
					Throw new Exception("Mysql error " . Database::mysqli_errno() . " executing select statement: " . $sql . "   ---   " . Database::mysqli_error());

				$count = Database::mysqli_num_rows($rs);
				if($count > 0)
				{
					$row = Database::mysqli_fetch_assoc($rs);
					foreach($object_attributes as $name => $value)
						eval("\$this->\$name = \$row[\$name];");

					$this->id = $row['id'];
				}
				return $count;
			}
			catch(Exception $e)
			{
				Throw $e;
			}
		}
		
		// Returns data from the table
		public function getTableData($criteria = '')
		{
			try
			{
				$class_name = get_class($this);
				$table_name = $this->plural(strtolower($class_name));
				
				if(!empty($criteria))
					$criteria = ' where ' . $criteria;
					
				$sql = "select * from $table_name $criteria";
				return self::getDataTable($sql);
			}
			catch(Exception $e)
			{
				Throw $e;
			}
		}

		// Inserts the data that the caller object is filled with in the database
/*
		public function Insert()
		{

			try
			{
				if($this->obj_ready_only)
				{
					die("Invalid operation! Trying to insert a read only object!");
				}

				$class_name = get_class($this);
				$object_vars = get_object_vars($this);
				// $class_vars = get_class_vars(__CLASS__);
				$class_vars = get_class_vars($class_name);
				
				$object_attributes = array_diff($object_vars, $class_vars);
				$table_name = $this->plural(strtolower($class_name));

				$field_names = "";
				$field_values = "";
				// error_log("_CLASS_: " . __CLASS__ . ", class_name: " . $class_name);
				// error_log("object_vars in BasicDataModel::Insert(): " . var_export($object_vars, true));
				// error_log("class_vars in BasicDataModel::Insert(): " . var_export($class_vars, true));
				// error_log("object_attributes in BasicDataModel::Insert(): " . var_export($object_attributes, true));

				foreach($object_attributes as $name => $value)
				{
					if(!is_null($value) && $name != 'id')//
					{
						if($field_names != '')
						{
							$field_names .= ', ';
							$field_values .= ', ';
						}
						$field_names .= "`$name`";
						if(!in_array(strtolower($value), array('now()', 'null')) )
						{
							$value = Database::mysqli_real_escape_string($value);
							$value = "'".$value."'";
						}
						$field_values .= $value;
					}
				}
				$sql = "insert into $table_name ($field_names) values ($field_values)";
				// error_log("BasicDataModel Insert SQL: ".$sql);
				if(!Database::mysqli_query($sql))
				{
					Throw new Exception("Error executing Insert statement: ". (__LINE__) . " " . Database::mysqli_error() . "\nSQL: ".$sql);
				}
				$num_affected_rows = Database::mysqli_affected_rows();
				if($num_affected_rows > 0)
					$this->id = Database::mysqli_insert_id();

				return $num_affected_rows;
			}
			catch(Exception $e)
			{
				Throw $e;
			}

		}
		
		

		public function Update()
		{
			try
			{
				if($this->obj_ready_only)
				{
					error_log("Invalid operation! Trying to update a read only object!");
					die("Invalid operation! Trying to update a read only object!");
				}

				$class_name = get_class($this);
				$object_vars = get_object_vars($this);
				// $class_vars = get_class_vars(__CLASS__);
				$class_vars = get_class_vars($class_name);
				
				
				$object_attributes = array_diff($object_vars, $class_vars);

				$table_name = $this->plural(strtolower($class_name));

				$fields = "";
				foreach($object_attributes as $name => $value)
				{

					if(!is_null($value) && $name != 'id')
					{
						if($fields != '')
						{
							$fields .= ', ';
						}
						if(!in_array(strtolower($value), array('now()', 'null')) )
						{
							$value = Database::mysqli_real_escape_string($value);
							$value = "'".$value."'";
						}
						$fields .= "`".$name."` = ".$value;
					}
				}
				$sql = "update $table_name set $fields where id = '".$this->id."'";
				// error_log("Update SQL: ".$sql);
				$rs = Database::mysqli_query($sql);
				if(Database::mysqli_error()) {
					error_log("Error executing Update statement: " . Database::mysqli_error() . "\n    query: " . $sql);
					Throw new Exception("Error executing Update statement: ".Database::mysqli_error() . "\nSQL: " . $sql);
				}
				$num_affected_rows = Database::mysqli_affected_rows();
				return $num_affected_rows;
			}
			catch(Exception $e)
			{
				// error_log('caught exception in BasicDataModel: ' . $e);
				Throw $e;
			}
		}
		*/
		
		public function Insert()
		{
			try
			{
				$field_names = "";
				$field_values = "";
				$num_affected_rows = 0;
				
				$class_name = get_class($this);
				$table_name = $this->plural(strtolower($class_name));
				
				if($this->obj_ready_only)
				{
					die("Invalid operation! Trying to insert a read only object!");
				}

				$obj = clone($this);
				unset($obj->{'id'});
				unset($obj->{'obj_ready_only'});

				foreach($obj as $name => $value)
				{
					if(!is_null($value))//
					{
						if($field_names != '')
						{
							$field_names .= ', ';
							$field_values .= ', ';
						}
						$field_names .= "`$name`";
						if(!in_array(strtolower($value), array('now()', 'null')) )
						{
							$value = Database::mysqli_real_escape_string($value);
							$value = "'".$value."'";
						}
						$field_values .= $value;
					}
				}
				if(!empty($field_names))
				{
					$sql = "insert into $table_name ($field_names) values ($field_values)";
					// error_log("BasicDataModel Insert SQL in Insert2: ".$sql);
				
					if(!Database::mysqli_query($sql))
					{
						Throw new Exception("Error executing Insert statement: ". (__LINE__) . " " . Database::mysqli_error() . "\nSQL: ".$sql);
					}
					$num_affected_rows = Database::mysqli_affected_rows();
					if($num_affected_rows > 0)
						$this->id = Database::mysqli_insert_id();
						
					
				}

				return $num_affected_rows;
			}
			catch(Exception $e)
			{
				Throw $e;
			}
		}
		
		public function Update()
		{
			try
			{
				if($this->obj_ready_only)
				{
					error_log("Invalid operation! Trying to update a read only object!");
					die("Invalid operation! Trying to update a read only object!");
				}
				
				$obj = clone($this);
				unset($obj->{'id'});
				unset($obj->{'obj_ready_only'});
				
				$fields = "";
				$num_affected_rows = 0;
				$class_name = get_class($this);
				$table_name = $this->plural(strtolower($class_name));

				foreach($obj as $name => $value)
				{
					if(!is_null($value))//
					{
						if($fields != '')
						{
							$fields .= ', ';
						}
						if(!in_array(strtolower($value), array('now()', 'null')) )
						{
							$value = Database::mysqli_real_escape_string($value);
							$value = "'".$value."'";
						}
						$fields .= "`".$name."` = ".$value;
					}
				}
				
				if(!empty($fields))
				{

					$sql = "update $table_name set $fields where id = '".$this->id."'";
					// error_log("Update SQL in Update2(): ".$sql);
					$rs = Database::mysqli_query($sql);
					if(Database::mysqli_error()) {
						error_log("Error executing Update statement: " . Database::mysqli_error() . "\n    query: " . $sql);
						Throw new Exception("Error executing Update statement: ".Database::mysqli_error() . "\nSQL: " . $sql);
					}
					$num_affected_rows = Database::mysqli_affected_rows();
				}
				return $num_affected_rows;
			}
			catch(Exception $e)
			{
				// error_log('caught exception in BasicDataModel: ' . $e);
				Throw $e;
			}
		}

		// Deletes the row matching the id of the caller object
		public function Delete()
		{
			try
			{
				if($this->obj_ready_only)
				{
					die("Invalid operation! Trying to delete a read only object!");
				}

				$class_name = get_class($this);
				$table_name = $this->plural(strtolower($class_name));
				$sql = "delete from $table_name where id = '".$this->id."'";
				// error_log("delete SQL: ".$sql);

				if(!Database::mysqli_query($sql))
						Throw new Exception("Error executing Delete statement: ".Database::mysqli_error() . "\nSQL: ".$sql);

				$num_affected_rows = Database::mysqli_affected_rows();
				return $num_affected_rows;
			}
			catch(Exception $e)
			{
				Throw $e;
			}
		}
		
		/* Validates form field data according to the rules passed in $fields */
		public static function validateFormData($data, $fields)
		{
			$errors = array();
			
			foreach($fields as $field => $checks)
			{
				foreach($checks as $type => $value)
				{
					switch($type)
					{
						// A required field; cannot be left empty
						case 'required':
							if($value == 1 && $data[$field] == '')
								$errors[$field] = "Cannot be left empty.";
							
							break;
					
						// The data must conform with the specified type
						case 'type':
							if($value == 'numeric' && !is_numeric($data[$field]))
								$errors[$field] = "Must be a numeric value.";
							
							break;
					
						// Input data cannot exceed the maximum length specified
						case 'length':
							break;
					
					}
				}
			}
			return $errors;
		}
		
		// Returns the resultset resulting from the specified SQL as an array 
		public static function getDataTable($sql)
		{
			$result = array();

			try
			{
				$rs = Database::mysqli_query($sql);
			
				if(!$rs)
				{
					throw new Exception("SQL error in funtion BasicDataModel::getDataTable(): ".Database::mysqli_error() . "\nSQL: " . $sql);
				}
				else if(Database::mysqli_num_rows($rs) > 0)
				{
					while($row = Database::mysqli_fetch_assoc($rs))
					{
						$result[] = $row;
					}
				}

				return $result;
			}
			catch(Exception $e)
			{
				// error_log($e->getMessage()."\nSQL: " . $sql);
				throw $e;
			}
		}
		
		// Returns the first row of the resultset resulting from the specified SQL as an array 
		public static function getDataRow($sql)
		{
			$result = array();
			
			try
			{
				$rs = Database::mysqli_query($sql);
			
				if(!$rs)
				{
					throw new Exception("SQL error in funtion BasicDataModel::getDataRow(): ".Database::mysqli_error() . "\nSQL: " . $sql);
				}
				else if(Database::mysqli_num_rows($rs) > 0)
				{
					if($row = Database::mysqli_fetch_assoc($rs))
					{
						$result = $row;
					}
				}

				return $result;
			}
			catch(Exception $e)
			{
				// error_log($e->getMessage());
				throw $e;
			}
		}
		
		public static function GenerateInsertSQL($table_name, $table_data, $statement = 'insert')
		{
			try
			{
				$arr_field_names = array();
				$arr_field_values = array();
		
				foreach($table_data as $name => $value)
				{
					if(is_null($value))
					{}
					else
					{
						$value = Database::mysqli_real_escape_string($value);
						if(!in_array(strtolower($value), array('now()', 'null')) )
							$value = "'".$value."'";
						
						$arr_field_names[] = "`".$name."`";
						$arr_field_values[] = $value;
					}
				}
		
				$sql = "$statement into `$table_name` (".implode(',', $arr_field_names).") values (".implode(',', $arr_field_values).")";
				return $sql;
			}
			catch(Exception $e)
			{
				// error_log($e->getMessage());
				throw $e;
			}
		}
		
		/* Inserts table data */
		public static function InsertTableData($table_name, $table_data)
		{
			try
			{
				$arr_field_names = array();
				$arr_field_values = array();
		
				foreach($table_data as $name => $value)
				{
					if(is_null($value))
					{}
					else
					{
						$value = Database::mysqli_real_escape_string($value);
						if(!in_array(strtolower($value), array('now()', 'null')) )
							$value = "'".$value."'";
						
						$arr_field_names[] = "`".$name."`";
						$arr_field_values[] = $value;
					}
				}
		
				$sql = "insert into `$table_name` (".implode(',', $arr_field_names).") values (".implode(',', $arr_field_values).")";
				// error_log("Insert SQL in InsertTableData(): ".$sql);
				if(!Database::mysqli_query($sql))
				{
					throw new Exception("SQL error in BasicDataModel::InsertTableData(): ".Database::mysqli_error().", \nSQL: ".$sql);
					// return false;
				}
				return Database::mysqli_insert_id();
			}
			catch(Exception $e)
			{
				// error_log($e->getMessage());
				throw $e;
			}
		}
		
		/* Inserts table data */
		public static function InsertMultipleRows($table_name, $table_data)
		{
			try
			{
				$arr_data = array();
		
				foreach($table_data as $table_row)
				{
					$arr_field_names = array();
					$arr_field_values = array();
				
					foreach($table_row as $name => $value)
					{
						$arr_field_names[] = "`".$name."`";
						$arr_field_values[] = "'".Database::mysqli_real_escape_string($value)."'";
					}
					$arr_data[] = "(" . implode(',', $arr_field_values) . ")";
				}
		
				$sql = "insert into `$table_name` (".implode(',', $arr_field_names).") values ".implode(', ', $arr_data);
				// error_log("Insert SQL in InsertMultipleRows(): ".$sql);
				if(!Database::mysqli_query($sql))
				{
					throw new Exception("SQL error in BasicDataModel::InsertMultipleRows(): ".Database::mysqli_error().", \nSQL: ".$sql);
					// return false;
				}
				return Database::mysqli_insert_id();
			}
			catch(Exception $e)
			{
				// error_log($e->getMessage());
				throw $e;
			}
		}
		
		public static function GenerateUpdateSQL($table_name, $table_data, $row_id, $col_name = 'id')
		{
			try
			{
				$arr_field_names = array();
				$arr_field_values = array();
		
				foreach($table_data as $name => $value)
				{
					$value = is_null($value) ? 'NULL' : "'" . Database::mysqli_real_escape_string($value) . "'";
					// if(!in_array(strtolower($value), array('now()', 'null')) )
						// $value = "'".$value."'";
					$table_data[$name] = $value;
				}
				$str_table_data = urldecode(http_build_query($table_data, '', ', '));
		
				$sql = "update `$table_name` set $str_table_data where $col_name = '".Database::mysqli_real_escape_string($row_id)."'";
				// error_log("Update SQL in UpdateTableData(): ".$sql);
				return $sql;
			}
			catch(Exception $e)
			{
				// error_log($e->getMessage());
				throw $e;
			}
		}
		
		/* Inserts table data */
		public static function UpdateTableData($table_name, $table_data, $row_id, $col_name = 'id')
		{
			try
			{
				$arr_field_names = array();
				$arr_field_values = array();
		
				foreach($table_data as $name => $value)
				{
					$value = is_null($value) ? 'NULL' : "'" . Database::mysqli_real_escape_string($value) . "'";
					// if(!in_array(strtolower($value), array('now()', 'null')) )
						// $value = "'".$value."'";
					$table_data[$name] = $value;
				}
				$str_table_data = urldecode(http_build_query($table_data, '', ', '));
		
				$sql = "update `$table_name` set $str_table_data where $col_name = '".Database::mysqli_real_escape_string($row_id)."'";
				// error_log("Update SQL in UpdateTableData(): ".$sql);
				if(!Database::mysqli_query($sql))
				{
					throw new Exception("SQL error in BasicDataModel::UpdateTableData(): ".Database::mysqli_error().", \nSQL: ".$sql);
				}

				return Database::mysqli_affected_rows();
			}
			catch(Exception $e)
			{
				// error_log($e->getMessage());
				throw $e;
			}
		}
		
		public static function UpdateMultipleRows($table_name, $table_data, $update_column = 'id')
		{
			try
			{
				$sql = "update $table_name set ";
			
				$data_values = array_values($table_data);
				$column_names = array_keys($data_values[0]);
			
				foreach($column_names as $i => $column_name)
				{	
					if($i > 0)
						$sql .= ", ";
					$sql .= "$column_name = (case";
					foreach($table_data as $column_val => $row)
					{
						$data_val = empty($row[$column_name]) ? 'NULL' : "'" . Database::mysqli_real_escape_string($row[$column_name]). "'";
						$sql .= " when $update_column = '$column_val' then " . $data_val . "";
					}
					$sql .= " end)";
				}
			
				$keys = array_keys($table_data);
				if(!empty($keys))
					$sql .= " where id in (" . implode (',', $keys). ")";
			
				// error_log("Update SQL in BasicDataObject::UpdateMultipleRows(): " . $sql);
				if(!Database::mysqli_query($sql))
				{
					throw new Exception("SQL error in BasicDataModel::UpdateMultipleRows(): ".Database::mysqli_error().", \nSQL: ".$sql);
				}

				return Database::mysqli_affected_rows();
			}
			catch(Exception $e)
			{
				// error_log($e->getMessage());
				throw $e;
			}
		}
		
		public static function start_transaction()
		{
			Database::mysqli_query("START TRANSACTION");
		}
		
		public static function commit()
		{
			Database::mysqli_query("COMMIT");
		}
		
		public static function rollback()
		{
			Database::mysqli_query("ROLLBACK");
		}
		
		public static function stop_transaction()
		{
			Database::mysqli_query("STOP TRANSACTION");
		}

	}
?>
