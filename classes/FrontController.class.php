<?php

/**
* FrontController Class
*/
class FrontController
{
	public static function render()
	{
		// Define access level of user roles
		global $privileges;
		$privileges = array(
			'dashboard' => array(
				'privileges' => array('admin', 'classteacher', 'teacher', 'assistant', 'operator'),
				'title' => 'Home',
			),
			'school-term' => array(
				'privileges' => array('admin', 'assistant'),
				'title' => 'School Terms',	
			),
			'class' => array(
				'privileges' => array('admin', 'assistant'),
				'title' => 'Classes',
			),
			'subject' => array(
				'privileges' => array('admin'),
				'title' => 'Subjects',
				),
			'teacher' => array(
				'privileges' => array('admin', 'operator'),
				'title' => 'Employees',
			),
			'student' => array(
				'privileges' => array('admin', 'assistant', 'operator', 'classteacher' ),
				'title' => 'Students',
			),
			'new-admissions' => array(
				'privileges' => array('admin', 'assistant', 'operator'),
				'title' => 'New Admissions',
			),
			'recording-slip' => array(
				'privileges' => array('admin', 'classteacher', 'teacher'),
				'title' => 'Recording Slips',
			),
			'result-statement' => array(
				'privileges' => array('admin', 'classteacher'),
				'title' => 'Result Statement',
			),
			'report-card' => array(
				'privileges' => array('admin', 'classteacher', 'assistant'),
				'title' => 'Report Cards',	
			),
			'report-card-preview' => array(
				'privileges' => array('admin', 'classteacher', 'operator', 'assistant'),
				'title' => 'Report Cards',
				'hidden' => true,
				'layout' => 'empty',
			),	
			'report-card-details' => array(
				'privileges' => array('admin', 'classteacher'),
				'title' => 'Report Cards',
				'hidden' => true	
			),
			'download-data' => array(
				'privileges' => array('admin', 'operator'),
				'title' => 'Export Data',			
			),
			'search-report-card' => array(		
				'privileges' => array('admin', 'assistant', 'operator'),
				'title' => 'Search Report Card',
			),
			'rectify-student-data' => array(
				'privileges' => array('admin'),
				'title' => 'Rectify Students Data',
			),		
			'promote-students' => array(
				'privileges' => array('admin', 'assistant', 'classteacher'),
				'title' => 'Promote Students',
			),	
			'warnings' => array(
				'privileges' => array('admin', 'classteacher', 'assistant'),
				'title' => 'Data Warnings',
			),
			'user-activity-log' => array(
				'privileges' => array('admin'),
				'title' => 'User Activity Log',
			),
			'fee-types' => array(
				'privileges' => array('admin', 'assistant'),
				'title' => 'Manage Fee Types',
			),
			'add-edit-fee-type' => array(
				'privileges' => array('admin', 'assistant'),
				'title' => 'Add Fee Type',
				'hidden' => true,
			),
			/*'student-fee-discounts' => array(
				'privileges' => array('admin', 'assistant'),
				'title' => 'Manage Students Fee Discounts',
			),
			'add-edit-student-fee-discount' => array(
				'privileges' => array('admin', 'assistant'),
				'title' => 'Add Student Fee Discount',
				'hidden' => true,
			),*/
			'challan' => array(
				'privileges' => array('admin', 'assistant', 'operator'),
				'title' => 'View Challans',
			),
			'add-edit-challan' => array(
				'privileges' => array('admin', 'assistant'),
				'title' => 'Add Single Challan',
				'hidden' => true,
			),
			'add-monthly-challans2' => array(
				'privileges' => array('admin', 'assistant'),
				'title' => 'Generate Annual / Monthly Challans',
			),
			'update-challans' => array(
				'privileges' => array('admin', 'assistant'),
				'title' => 'Update Challan',
				'hidden' => true,
			),
			'update-students' => array(
				'privileges' => array('admin', 'assistant'),
				'title' => 'Update Students',
				'hidden' => true,
			),
			'reports' => array(
				'privileges' => array('admin', 'assistant', 'operator'),
				'title' => 'Reports',
			),
			'outstanding-fee' => array(
				'privileges' => array('admin', 'assistant', 'operator'),
				'title' => 'Outstanding Dues',
				'hidden' => true,
				'layout' => 'empty',
			),
			'received-fee' => array(
				'privileges' => array('admin', 'assistant', 'operator'),
				'title' => 'Received Fee',
				'hidden' => true,
				'layout' => 'empty',
			),	
			'expected-received-fee' => array(
				'privileges' => array('admin', 'assistant', 'operator'),
				'title' => 'Expected / Received Fee',
				'hidden' => true,
				'layout' => 'empty',
			),	
			'summary-expected-received-fee' => array(
				'privileges' => array('admin', 'assistant', 'operator'),
				'title' => 'Expected / Received Fee - Summary',
				'hidden' => true,
				'layout' => 'empty',
			),
			'students-report' => array(
				'privileges' => array('admin', 'assistant', 'operator'),
				'title' => 'Students Report',
				'hidden' => true,
				'layout' => 'empty',
			),
			'user-guide.html' => array(
				'privileges' => array('admin', 'classteacher', 'teacher', 'assistant', 'operator'),
				'title' => 'User Manual',
			),
			'user-feedback' => array(
				'privileges' => array('admin', 'classteacher', 'teacher', 'assistant'),
				'title' => 'Your Feedback',
			),
			'print-preview-test' => array(
				'privileges' => array('admin', 'assistant', 'operator'),
				'title' => 'Print Preview',
				'hidden' => true,
				'layout' => 'empty',
			),
			'view-challans-for-printing' => array(
				'privileges' => array('admin', 'assistant', 'operator'),
				'title' => 'Print Preview',
				'hidden' => true,
				'layout' => 'empty',
			),
			'view-challans-for-printing-kkamhs' => array(
				'privileges' => array('admin', 'assistant', 'operator'),
				'title' => 'Print Preview',
				'hidden' => true,
				'layout' => 'empty',
			),
		);
		
		$flash_msg = array(
			'text'=>'', 		// Used to display messages on the screen
			'class' =>'' // Used to style the message on the screen
		);
		

		if(!empty($_SESSION['flash_msg']))
		{
			$flash_msg = $_SESSION['flash_msg'];
			unset($_SESSION['flash_msg']);
		}
		
		global $request;
		//error_log("GET in front controller: ".var_export($_GET, true));
		$layout = "default";
		
		
		$default_page_request = "login";
	
		


		//clean it
		$request_path = "";
		$request_string = Database::mysqli_real_escape_string($_GET['request']);
		//error_log("request_string:".$request_string);		
		
		// Suppose, if the request was http://dev.mysamplephpapp.local/admin/users/list, then $request_string would be 'admin/users/list'
		
		if(empty($request_string))
		{
			$request = $default_page_request;
		}
		else
		{
			//now we need to get controller/view from the full path
			$request_params = explode('/', $request_string);
			$num_request_params = count($request_params);
			//error_log("request_params: ".var_export($request_params, true));

			$request = end($request_params);
			// Get request path
			
			// $request would be 'list' 
			if(!empty($request))
			{
				$pos = strpos($request_string, $request);

				$request_path = substr($request_string, 0, $pos); // and $request_path would be 'admin/users'
			}
			else
			{
				$request_path = $request_string;
				$request = "index";
			}
			
			
			//error_log("request_path:".$request_path);
			
			// If requested file doesn't exist, show an error
			if(!file_exists('views/' .$request_path. $request . '.view.php')) 
			{
				Errors::show404();
			}
			
			// The user must be logged in to access 
			
			if(isset($privileges[$request]))
			{
				if(empty($_SESSION['user'])) 
				{
					Errors::show600();
				}
				else 
				{
					$user = $_SESSION['user'];
					$user_role = $user['role'];
					if($user_role != 'admin')
					{
						if(!in_array($user_role, $privileges[$request]['privileges']))
						{
							// Then show an authorization error
							Errors::show700();
						}
					}
				}
			}

			/*
			// Getting the name of the first folder and its layout
			if($num_request_params > 1)
			{
				$sub_folder_name = $request_params[0];
				if(file_exists('layouts/' . $sub_folder_name . '.layout.php'))
					$layout = $sub_folder_name;
				
			}
			*/
		}
		
		
		//error_log("request in front controller: ".$request);
		
		if(!empty($privileges[$request]['layout']))
			$layout = $privileges[$request]['layout'];

		//load the global controller
		if(file_exists('controllers/global.controller.php'))
		{
			require_once('controllers/global.controller.php');
		}

		//check to see if the default controller for this request exists, and load it
		if(file_exists('controllers/' .$request_path. $request . '.controller.php'))
		{
			require_once('controllers/' .$request_path. $request . '.controller.php');
		}

		//check to see if the default view for this request exists
		if(file_exists('views/' .$request_path. $request . '.view.php'))
		{
			ob_start();
			require_once('views/' .$request_path. $request . '.view.php');	
			$content_for_layout = ob_get_clean();

			require_once('layouts/'.$layout.'.layout.php');
		}
		else 
		{
			Errors::show404();
		}

		

	}
}


?>
