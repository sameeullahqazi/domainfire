<?php
	global $database_name;
	global $nc_api_live, $nc_api_user, $nc_api_key, $nc_api_ip, $nc_pwd;
	global $nc_sandbox_api_user, $nc_sandbox_api_key;
	global $nc_api_categories;
	
	
	global $stripe_api_test_public_key, $stripe_api_test_private_key, $stripe_api_live_key, $stripe_api_live_secret, $stripe_test_card_number, $stripe_test_exp_month, $stripe_test_exp_year, $stripe_test_cvc;
	
	$database_name	=	'schoolmanagement';
	$nc_api_live	= false;
	$nc_api_user	= 'davistv';
	$nc_api_key		= '7d1ca42b656a43bab21da2a26a05b459';
	$nc_api_ip		= '39.57.254.167';
	$nc_pwd			= 'xUJ3|?XD:cM2';
	$nc_api_categories	= array(
		'domains' => 'namecheap.domains',
		'domains.dns' => 'namecheap.domains.dns',
		'domains.ns' => 'namecheap.domains.ns',
		'domains.transfer' => 'namecheap.domains.transfer',
		'ssl' => 'namecheap.ssl',
		'users' => 'namecheap.users',
		'users.address' => 'namecheap.users.address',
		'whoisguard' => 'namecheap.whoisguard',
	);
	
	$nc_sandbox_api_user = 'davistv';
	$nc_sandbox_api_key = '90baada8307c406cbd636ab4d9c05106';

	$stripe_api_test_public_key = 'pk_test_vgyD3vHQKm8gYUwHGBi8Cg4T';
	$stripe_api_test_private_key = 'sk_test_nkyoVWuu7djN4tk0Da5f0L1r';
	
	$stripe_test_card_number = '4242424242424242';
	$stripe_test_exp_month = '3';
	$stripe_test_exp_year = '2019';
	$stripe_test_cvc = '314';
	
?>