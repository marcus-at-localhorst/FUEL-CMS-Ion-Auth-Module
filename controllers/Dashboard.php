<?php
require_once(FUEL_PATH.'libraries/Fuel_base_controller.php');
class Dashboard extends Fuel_base_controller {
	
	function __construct()
	{
		parent::__construct();
		$this->config->module_load('iauth', 'iauth');
		$this->view_location = 'iauth';
	}
	
	function index()
	{
		
		if ($this->fuel->auth->has_permission('iauth')){

			$users = $this->fuel->iauth->users()->order_by('last_login','asc');

			$vars['users'] = $users->result_array();
			$this->load->view('_admin/dashboard', $vars);
		}
	}

}
