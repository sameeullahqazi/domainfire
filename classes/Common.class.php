<?php

	class Common
	{
		const sql_separator = ";\r\n\r\n";
		
		public static function getBaseURL() {
			$pageURL = 'http';
			$server_name = $_SERVER["SERVER_NAME"];
			
			// error_log("Remote IP: ".REMOTE_IP.", Local IP: ".LOCAL_IP);
			
			// Mapping REMOTE IP to LOCAL IP
			if($server_name == REMOTE_IP)
				$server_name = LOCAL_IP;

			//if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
			$pageURL .= "://";
			if ($_SERVER["SERVER_PORT"] != "80") {
				$pageURL .= $server_name.":".$_SERVER["SERVER_PORT"];
			} else {
				$pageURL .= $server_name;
			}

			//$pageURL .= "/".APP_FOLDER;
			return $pageURL;
		}

		public static function generate_data_grid2(&$data_array)
		{

			$styles = array("regular", "alternate");
			
			$header = false;


			$HTML .= "<table class='datagrid'>";
			
			foreach($data_array as $i => $row)
			{
				if(!$header)
				{
					$header = true;
					// Display Table Header
					$HTML .= "<tr>";
					foreach(array_keys($row) as $column_name)
						$HTML .= "<th><a href='?sort_by=".$column_name."'>$column_name</a></th>";
					$HTML .= "</tr>";
				}
				// Display rest of the table rows
				$HTML .= "<tr class='".$styles[$i % 2]."'>";
				foreach(array_values($row) as $value)
					$HTML .= "<td>$value</td>";	
				
				$HTML .= "</tr>";
			}
			$HTML .= "</table>";

			return $HTML;
		}

		public static function generate_data_grid(&$data_array, $hdn_sort_by_ID)
		{
			
			$styles = array("regular", "alternate");
			
			$header = false;


			
			try
			{
				$HTML .= "<table class='datagrid'>";
				foreach($data_array as $i => $row)
				{
					if(!$header)
					{
						$header = true;
						// Display Table Header
						$HTML .= "<tr>";
						foreach(array_keys($row) as $column_name)
							$HTML .= "<th><a href='#' onclick=\"document.getElementById('".$hdn_sort_by_ID."').value='".$column_name."';document.forms[0].submit();\">$column_name</a></th>";
						$HTML .= "</tr>";
					}
					// Display rest of the table rows
					$HTML .= "<tr class='".$styles[$i % 2]."'>";
					foreach(array_values($row) as $value)
						$HTML .= "<td>$value</td>";	
					
					$HTML .= "</tr>";
				}
				$HTML .= "</table>";
				return $HTML;
			}
			catch(Exception $e)
			{
				Throw $e;
			}
		}

		public static function get_data_table($sql)
		{	
			// error_log("SQL in get_data_table(): ".$sql);
			$data_table = array();
			try
			{
				$rs = Database::mysqli_query($sql);
				while($row = Database::mysqli_fetch_assoc($rs))
					$data_table[] = $row;
				//if(Database::mysqli_error())
					
				return $data_table;
			}
			catch(Exception $e)
			{
				Throw $e;
			}
		}

		public static function get_scalar_value($sql, $column_name)
		{
			//echo "SQL in get_scalar_value(): ".$sql;
			$result = 0;
			try
			{
				$rs = Database::mysqli_query($sql);
				if($row = Database::mysqli_fetch_assoc($rs))
					$result = $row[$column_name];

				return $result;
			}
			catch(Exception $e)
			{
				Throw $e;
			}
		}
	
		public static function sort_data_table(&$data_array, $orderby, $sortorder)
		{
			if($orderby != null)
			{
				$sortorder = ($sortorder == "DESC") ? SORT_DESC : SORT_ASC;
				$sortArray = array();
				foreach($data_array as $row)
					foreach($row as $key=>$value)
						$sortArray[$key][] = $value;
				 
				array_multisort($sortArray[$orderby], $sortorder, $data_array);
			}

		}

		public static function db_error_log($msg)
		{
                    
			$msg = Database::mysqli_real_escape_string($msg);
			$sql = "insert into error_log values (null, '".$msg."', now());";
			//error_log("DB error log sql: ".$sql);
			Database::mysqli_query($sql);
		}

		public static function get_current_store_id()
		{
			/*
			$current_settings = new CurrentSettings();
			$current_settings->Select("id='0'");
			return $current_settings->current_store_id;*/
			return $_SESSION['store_id'];
		}

		public static function get_current_captcha_text()
		{
			$current_settings = new CurrentSettings();
			$current_settings->Select("id='0'");
			return $current_settings->current_captcha_text;
		}

		public static function set_current_store_id($store_id)
		{
			/*
			$current_settings = new CurrentSettings();
			$current_settings->id = 0;
			$current_settings->current_store_id = $store_id;
			$current_settings->Update();*/
			$_SESSION['store_id'] = $store_id;
		}

		public static function set_current_captcha_text($captcha_text)
		{
			$current_settings = new CurrentSettings();
			$current_settings->id = 0;
			$current_settings->current_captcha_text = $captcha_text;
			$current_settings->Update();
		}

		public static function db_event_log($action_code, $media_id=null, $user_id=null)
		{
			$event_log = new EventLog();
			$event_log->action_code = $action_code;
			$event_log->created = 'now()';
			if(isset($_SESSION))
				$event_log->user_id = $_SESSION['user_id'];
			else
				$event_log->user_id = $user_id;

			$event_log->media_id = $media_id;
			$event_log->store_id = Common::get_current_store_id();
			$event_log->Insert();
		}

		public static function GetMonthString($n)
		{
			$timestamp = mktime(0, 0, 0, $n, 1, 2005);
			return date("M", $timestamp);
		}
		
		public static function getRootPath($bDiscardEndingSlash = false) {
			$root_path = $_SERVER['DOCUMENT_ROOT'];
			if($bDiscardEndingSlash)
				$root_path = rtrim($root_path, "/");

			return $root_path;
		}
		
		public static function getFileExtension($filename)
		{
			$ext = substr(strrchr($filename, '.'), 1);
			return $ext;
		}
		
		public static function resizeAndSaveImage($uploaded_file_path)
		{
			$extension = Common::getFileExtension($uploaded_file_path);
	
			if($extension == "jpg" || $extension == "jpeg" )
			{
				$src = imagecreatefromjpeg($uploaded_file_path);
			}
			else if($extension=="png")
			{
				$src = imagecreatefrompng($uploaded_file_path);
			}
			else 
			{
				$src = imagecreatefromgif($uploaded_file_path);
			}
			 
			list($width,$height) = getimagesize($uploaded_file_path);

			$new_width = 150;
			$new_height = ($height / $width) * $new_width;
			$tmp = imagecreatetruecolor($new_width, $new_height);

			imagecopyresampled($tmp, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
			imagejpeg($tmp, $uploaded_file_path, 100);
			imagedestroy($src);
			imagedestroy($tmp);
		}
		
		public static function getDataChangesSinceDate($entity, $date_since)
		{
			$table_names = array(
				'class' => 'classes',
				'teacher' => 'users',
			);
			$table_name = isset($table_names[$entity]) ? $table_names[$entity] : $entity . 's';
			$sql = "select count(id) as num_rows from $table_name where created >= '$date_since' or modified >= '$date_since'";
			error_log("SQL in Common::getDataChangesSinceDate(): " . $sql);
			$row = BasicDataModel::getDataRow($sql);
			$num_rows = empty($row['num_rows']) ? 0 : $row['num_rows'];
			return $num_rows;
		}
		
		public static function parseReportCardInfoByTokens($html, $token_start, $token_end)
		{
			$len = strlen($token_start);
			$start = strpos($html, $token_start) + $len;
			$end = strpos($html, $token_end, $start);
			$res = substr($html, $start, $end - $start);
			return $res;
		}
		
		public static function generateInsertSQLUsingSelectSQL($sql, $table_name, $str_insert_replace = "replace")
		{
			$insert_sql = "";
			// $rows = BasicDataModel::getDataTable($sql);
			// foreach($rows as $i => $row)
			$rs = Database::mysqli_query($sql);
			while($row = Database::mysqli_fetch_assoc($rs))
			{
				$arr_field_names = array();
				$arr_field_values = array();
		
				foreach($row as $name => $value)
				{
					$value = is_null($value) ? "NULL" : "'" . Database::mysqli_real_escape_string($value) . "'";
					$arr_field_names[] = "`".$name."`";
					$arr_field_values[] = $value;
				}
				if($i > 0)
					$insert_sql  .= ";" . PHP_EOL;

				$insert_sql .= "$str_insert_replace into `$table_name` (".implode(', ', $arr_field_names).") values (".implode(', ', $arr_field_values).")";
			}
			Database::mysqli_free_result($rs);
			return $insert_sql;
		}
		
		/*
			Uses mysql format yyyy-mm-dd
			- if $day_of_month = 20, $date = 2016-02-25, $benchmark_date = 2016-02-20
			- if $day_of_month = 20, $date = 2016-02-19, $benchmark_date = 2016-01-20
			- if $day_of_month = 20, $date = 2016-01-19, $benchmark_date = 2015-12-20
		*/
		public static function getBenchmarkDate($date, $day_to_adjust)
		{
			$arr_date = explode('-', $date);
			$day = intval($arr_date[2]);
			$month = intval($arr_date[1]);
			$year = $arr_date[0];
		
			$benchmark_day = $day_to_adjust;
			$benchmark_month = $month;
			if($day < $day_to_adjust)
				$benchmark_month--;
				
			$benchmark_year = $year;
			if($benchmark_month < 1)
			{
				$benchmark_month = 12;
				$benchmark_year--;
			}
			
			$benchmark_date = $benchmark_year . '-' . str_pad($benchmark_month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($benchmark_day, 2, '0', STR_PAD_LEFT);
			
			return $benchmark_date;
		}
		
		public static function parse_fb_date($date_of_birth, $day_before_month = null)
		{
			$date_of_birth = explode('/', $date_of_birth);
			$date_of_birth[2] = isset($date_of_birth[2]) ? $date_of_birth[2] : '0000';
			
			if(!empty($day_before_month))
				$date_of_birth = $date_of_birth[2] . '-'. $date_of_birth[1] . '-'.  $date_of_birth[0];
			else
				$date_of_birth = $date_of_birth[2] . '-'. $date_of_birth[0] . '-'.  $date_of_birth[1];
				
			return $date_of_birth;
		}
		
		public static function json_format($json)
		{
			 $tab = "  ";
			 $new_json = "";
			 $indent_level = 0;
			 $in_string = false;

			 $json_obj = json_decode($json);

			 if($json_obj === false)
				  return false;

			 $json = json_encode($json_obj);
			 $len = strlen($json);

			 for($c = 0; $c < $len; $c++)
			 {
				  $char = $json[$c];
				  switch($char)
				  {
				      case '{':
				      case '[':
				          if(!$in_string)
				          {
				              $new_json .= $char . "\n" . str_repeat($tab, $indent_level+1);
				              $indent_level++;
				          }
				          else
				          {
				              $new_json .= $char;
				          }
				          break;
				      case '}':
				      case ']':
				          if(!$in_string)
				          {
				              $indent_level--;
				              $new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
				          }
				          else
				          {
				              $new_json .= $char;
				          }
				          break;
				      case ',':
				          if(!$in_string)
				          {
				              $new_json .= ",\n" . str_repeat($tab, $indent_level);
				          }
				          else
				          {
				              $new_json .= $char;
				          }
				          break;
				      case ':':
				          if(!$in_string)
				          {
				              $new_json .= ": ";
				          }
				          else
				          {
				              $new_json .= $char;
				          }
				          break;
				      case '"':
				          if($c > 0 && $json[$c-1] != '\\')
				          {
				              $in_string = !$in_string;
				          }
				      default:
				          $new_json .= $char;
				          break;
				  }
			 }

			 return $new_json;
		}
		
		public static function fetchAndRemoveValue(&$array, $key) {
			$value = isset($array[$key]) ? $array[$key] : null;
			unset($array[$key]);
			return $value;
		}
	}
?>
