<?php
/*
|--------------------------------------------------------------------------
| FUEL NAVIGATION: An array of navigation items for the left menu
|--------------------------------------------------------------------------
*/
$config['nav']['iauth'] = array(
    'iauth/users'  => lang('index_heading'),
    'iauth/groups' => lang('index_groups_th'),
);


/*
|--------------------------------------------------------------------------
| Configurable in settings if auth_use_db_table_settings is set
|--------------------------------------------------------------------------
 */

// deterines whether to use this configuration below or the database for controlling the users behavior
$config['iauth_use_db_table_settings'] = true;

$config['iauth'] = array();

$config['iauth']['settings']['uri']               = array('value' => 'user');
$config['iauth']['settings']['use_cache']         = array('type' => 'checkbox', 'value' => '1');
$config['iauth']['settings']['asset_upload_path'] = array('default' => 'images/auth/');
$config['iauth']['settings']['per_page']          = array('value' => 1, 'size' => 3);

// the cache folder to hold user cache files
$config['iauth_cache_group'] = 'iauth';

/*
|--------------------------------------------------------------------------
| Programmer specific config (not exposed in settings)
|--------------------------------------------------------------------------
| See ion_auth.php for database table settings and all 
| ion_auth related settings since model and library access this config file directly
 */

require('ion_auth.php');

