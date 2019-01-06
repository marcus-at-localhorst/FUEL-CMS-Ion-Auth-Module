<?php 

/**
 * Models im Fuel Backend
 * @var array
 */
$auth_models = array(
	'users'  => 'auth_users',
    'groups' => 'auth_groups',
);

foreach ($auth_models as $path => $model) {
	$route[FUEL_ROUTE . IAUTH_FOLDER . '/' . $path]           = FUEL_FOLDER . '/module';
    $route[FUEL_ROUTE . IAUTH_FOLDER . '/' . $path . '/(.*)'] = FUEL_FOLDER . '/module/$1';
}

/**
 * Frontend Controller
 * @var array
 */
$auth_controllers = array(
	'login'                  => 'iauth/login',
	'logout'                 => 'iauth/logout',
	'change_password'        => 'iauth/change_password',
	'register'               => 'iauth/register',
	'edit_user'              => 'iauth/edit_user',
	'edit_user/(:any)'       => 'iauth/edit_user/$2', // <= '(|[a-z]{2}\/)' . $path  =  $1/path/$2
	'create_user'            => 'iauth/create_user',
	'create_group'           => 'iauth/create_group',
	'activate/(:any)/(:any)' => 'iauth/activate/$1/$2',
);

$route['(|[a-z]{2}/)auth'] = 'iauth';

foreach ($auth_controllers as $path => $model) {
    $route['(|[a-z]{2}\/)' . $path] = $model;
    // temp, to keep all the original routes
    $route['(|[a-z]{2}/)auth/' . $path] = $model;

    #$route[FUEL_ROUTE.AUTH_FOLDER.'/'.$path] = AUTH_FOLDER.'/'.$model;
    #$route[FUEL_ROUTE.AUTH_FOLDER.'/'.$path.'/(.*)'] = AUTH_FOLDER.'/'.$model.'/$1';
}
//$route['auth'] = 'auth/auth';
//$route['courses(:any)'] = 'course/course$1';


#var_dump($route);exit;
