<?php
$config['modules']['iauth_users'] = array(
	'module_name' => 'Partner Users',
	'module_uri' => 'iauth/users',
	#'model_name' => 'auth_users_model',
	'model_location' => 'iauth',
	'table_headers' => array(
		'id', 
		'username',
		'email', 
		'first_name', 
		'last_name', 
		'company', 
		'phone', 
		'created_on', 
		'last_login', 
		'group_list', 
		'active'
	),
	'display_field' => 'username',
	'permission' => 'iauth/users',
	'instructions' => lang('module_instructions_default', 'Site Users Records'),
	'archivable' => TRUE,
	#'configuration' => array('iauth' => 'iauth', 'iauth' => 'ion_auth'),
	'nav_selected' => 'iauth/users',
	'default_col' => 'username',
	'default_order' => 'asc',
);
$config['modules']['iauth_groups'] = array(
	'module_name' => 'Partner User Groups',
	'module_uri' => 'iauth/groups',
	#'model_name' => 'auth_groups_model',
	'model_location' => 'iauth',
	'display_field' => 'description',
	'permission' => 'iauth/groups',
	#'configuration' => array('iauth' => 'iauth', 'iauth' => 'ion_auth'),
	'nav_selected' => 'iauth/groups'
);
