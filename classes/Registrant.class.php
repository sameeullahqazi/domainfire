<?php
	class Registrant extends BasicDataModel
	{
		var $first_name;
		var $last_name;
		var $address1;
		var $address2;
		var $lastname;
		var $city;
		var $state;
		var $postal_code;
		var $country;
		var $phone;
		var $email;
		var $org_name;
		var $job_title;
		var $created;
		var $modified;
		
		public static function AddNewRegistrant($data)
		{
			try
			{
				$registrant_id = BasicDataModel::InsertTableData('registrants', $data);
				return $registrant_id;
			}
			catch(Exception $e)
			{
				throw $e;
			}
		}
	}
?>