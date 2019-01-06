<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/Base_module_model.php');

class Iauth_groups_model extends Base_module_model {

    public $record_class = 'Iauth_group';
	public $required = array(
		'name' => 'Please fill out the name',
		'description' => 'Please fill out the description',
	);


    function __construct()
    {
        parent::__construct('iauth_groups', IAUTH_FOLDER);
    }

    function list_items($limit = NULL, $offset = NULL, $col = 'name', $order = 'asc', $just_count = FALSE)
    {
    	$data = parent::list_items($limit, $offset, $col, $order, $just_count);
    	return $data;
    }

    public function options_list($key = 'id', $val = 'name', $where = '', $order = TRUE, $group = TRUE){

    	$key = 'id';
    	$val = 'description';

    	$data = parent::options_list($key, $val, $where, $order);
    	return $data;
    }

	function form_fields($values = array(), $related = array())
	{
		$fields = parent::form_fields($values, $related);
		return $fields;
	}
}

class Iauth_group_model extends Base_module_record {

	private $_tables;

	function on_init()
	{
		$this->_tables = $this->_CI->config->item('tables');
	}

}
