<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Iauth_module extends Fuel_base_controller {
	
	public $nav_selected = 'iauth|iauth/:any';

	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$vars['page_title'] = $this->fuel->admin->page_title(array(lang('module_iauth')), FALSE);
		$crumbs = array('tools' => lang('section_tools'), lang('module_iauth'));

		$this->fuel->admin->set_titlebar($crumbs, 'ico_iauth');
		$this->fuel->admin->render('_admin/iauth', $vars, '', IAUTH_FOLDER);
	}
}