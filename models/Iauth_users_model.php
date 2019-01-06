<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/Base_module_model.php');

class Iauth_users_model extends Base_module_model {

	public $record_class = 'Iauth_user';
	public $required = array(
		//'username' => 'Please fill out the username',
		'email' => 'email cannot be empty',
		'group_id' => 'Please select a group.',
		'first_name' => 'Please fill out first name.',
		'last_name' => 'Please fill out last name.',
	);
	public $hidden_fields = [
		'username',
		'ip_address',
		#'activation_selector',
		#'activation_code',
		'forgotten_password_selector',
		'forgotten_password_code',
		'forgotten_password_time',
		'remember_code',
		'remember_selector',
		'created_on',
		'last_login'
	];

	public $foreign_keys = [
		'group_id' => 'iauth_groups_model' // see `_common_query()` for joining in `group_id`
	];

	public $unique_fields = [];

	#public $has_many = array('groups' => array('model' => array(IAUTH_FOLDER => 'auth_groups_model')));

	function __construct()
	{
		parent::__construct('iauth_users', IAUTH_FOLDER);
		$this->config->load('ion_auth', TRUE);
	}


	function list_items($limit = NULL, $offset = NULL, $col = 'username', $order = 'asc', $just_count = FALSE)
	{

		$this->db->select("*,FROM_UNIXTIME(created_on) AS created_on, FROM_UNIXTIME(last_login) AS last_login", FALSE);

		$data = parent::list_items($limit, $offset, $col, $order, $just_count);

		if(!$just_count){
			foreach($data as $key => $value) {

				// Add Groups to Listing
				$groups = $this->fuel->iauth->get_users_groups($value['id'])->result_array();
				$group_array = [];
				foreach($groups as $group){
					$group_array[] = $group['description'];
				}

				$data[$key]['group_list'] = implode('<br>',$group_array);

				// Format Date
				$data[$key]['created_on'] = date_formatter($value['created_on']);
				$data[$key]['last_login'] = date_formatter($value['last_login']);
			}
		}

		return $data;
	}



	function form_fields($values = array(), $related = array())
	{
		$fields = parent::form_fields($values, $related);
		$fields['active']['type'] = 'select';
		$fields['active']['options'] = array('1' => 'Yes', '0' => 'No');
		$fields['email']['order'] = 4;

		// save reference so we can reorder
		$pwd_field = $fields['password'];
		unset($fields['password']);

		$user_id = NULL;
		if (!empty($values['id']))
		{
			$user_id = $values['id'];
		}

		if (!empty($user_id))
		{
			$fields['new_password'] = array('label' => lang('form_label_new_password'), 'type' => 'password', 'size' => 20, 'order' => 5);
		}
		else
		{
			$pwd_field['type'] = 'password';
			$pwd_field['size'] = 20;
			$pwd_field['order'] = 5;
			$fields['password']= $pwd_field;
		}
		$fields['confirm_password'] = array('label' => lang('form_label_confirm_password'), 'type' => 'password', 'size' => 20, 'order' => 6);
		$fields['group_id']['order'] = 7;

		$exclude = [
			'remember_selector',
			'activation_selector',
			'activation_code',
			'forgotten_password_code',
			'forgotten_password_selector'
		];

		#ddd($fields,array_flip($exclude),array_diff_key(array_flip(array_keys($fields)), array_flip($exclude)));
		return array_diff_key($fields, array_flip($exclude));
	}


	/**
	 * Model hook executed right before the data is cleaned
	 *
	 * @access  public
	 * @param   array The values to be saved right the clean method is run
	 * @return  array Returns the values to be cleaned
	 */
	public function on_before_clean($values)
	{
		#ddd($values);

		if (!empty($values['password']) OR !empty($values['new_password']))
		{
			$pwd = (!empty($values['new_password'])) ? $values['new_password'] : $values['password'];
			$values['password'] = $pwd;
		}
		return $values;
	}
	/**
	 * Model hook executed right before validation is run
	 *
	 * @access  public
	 * @param   array The values to be saved right before validation
	 * @return  array Returns the values to be validated right before saving
	 */
	public function on_before_validate($values)
	{
		#ddd($values);


		$this->add_validation('email', 'valid_email', lang('error_invalid_email'));

		// for new
		if (empty($values['id']))
		{
			$this->required[] = 'password';
			$this->add_validation('email', array(&$this, 'is_new_email'), lang('error_val_empty_or_already_exists', lang('form_label_email')));
			if (isset($this->normalized_save_data['confirm_password']))
			{
				$this->get_validation()->add_rule('password', 'is_equal_to', lang('error_invalid_password_match'), array($this->normalized_save_data['password'], $this->normalized_save_data['confirm_password']));
			}
		}

		// for editing
		else
		{
			$this->add_validation('email', array(&$this, 'is_editable_email'), lang('error_val_empty_or_already_exists', lang('form_label_email')), $values['id']);
			if (isset($this->normalized_save_data['new_password']) AND isset($this->normalized_save_data['confirm_password']))
			{
				$this->get_validation()->add_rule('password', 'is_equal_to', lang('error_invalid_password_match'), array($this->normalized_save_data['new_password'], $this->normalized_save_data['confirm_password']));
			}
		}

		return $values;
	}

	/**
	 * Update user or register new one
	 * Pass `$values['id']` through so the fuel save routine can be finished
	 *
	 * @param  [array] $values Fields and Values
	 * @return [array]        Just pass `id` on so the rest of fuel can run through
	 */
	function on_before_save($values)
	{

		// `group_id` doesn not exist here anymore, because fields got cleaned (compared to db)
		// so we put it back into the array
		$values['group_id'] = array_get($this->normalized_save_data,'group_id',[]);

		if(!is_array($values['group_id'])){
			$values['group_id'] = [$values['group_id']];
		}

		/**
		 * Update Entry
		 */
		if($values['id'] > 0)
		{
			// Update Record utilize Ion Auth update method
			$status = $this->fuel->iauth->update($values['id'], $values);

			if($status){

				// check if group records exist to reduce calls
				$groups = $this->db->select('group_id')->get_where($this->_tables['users_groups'], ['user_id' => $values['id']])->result_array();

				$groups = array_flatten($groups);

				// if existing record is different from new record
				if(array_diff($groups,$values['group_id'])){
					// remove from group first
					$status = $this->fuel->iauth->remove_from_group(NULL,$values['id']);
					// write group records
					$status = $this->fuel->iauth->add_to_group($values['group_id'],$values['id']);
				}

				if($status){
					// TODO: add success msg
				}else{
					// TODO: Konnte Gruppe nicht speichern
					$errors = $this->fuel->iauth_model->errors();
				}
			}else{
				// TODO: konnte User nicht aktualisieren
				$errors = $this->fuel->iauth->errors();
			}

			// record is written, remove all keys and pass only `id`
			$values = array_intersect_key($values, array_flip(['id']));

		}
		/**
		 * Register New User
		 */
		else
		{
			$email    = strtolower($values['email']);
			$username = ( $this->config->item('identity', 'ion_auth') == 'email') ? $email :  strtolower($values['username']);
			$password = $values['password'];

			$additional_data = array(
				'first_name' => $values['first_name'],
				'last_name'  => $values['last_name'],
				'company'    => $values['company'],
				'phone'      => $values['phone'],
				//'group_id'    => $values['group_id'],
			);

			$status = $this->fuel->iauth->register($username, $password, $email, $additional_data, $values['group_id']);
			// pass only id field on.
			$values = ['id' => $status];
		}

		#if(!empty($errors)) {
		#	ddd($errors);
		#}

		// since only id is present in $values the fuel `save()` function runs through and
		// finishes correctly without writing any data itself (I hope)
		return $values;

	}

	/**
	 * Validation callback to check if a new user's email already exists
	 *
	 * @access  public
	 * @param   string The email address
	 * @return  boolean
	 */
	public function is_new_email($email)
	{
		return $this->is_new($email, 'email');
	}

	// --------------------------------------------------------------------

	/**
	 * Validation callback to check if an existing user's email address doen't already exist in the system
	 *
	 * @access  public
	 * @param   string The email address
	 * @param   string The email address
	 * @return  boolean
	 */
	public function is_editable_email($email, $id)
	{
		return $this->is_editable($email, 'email', $id);
	}

	function _common_query($display_unpublished_if_logged_in = NULL){
		parent::_common_query($display_unpublished_if_logged_in);

		// always get the `auth_users_groups.group_id`
		$this->db->select(
			sprintf('%s.*, %s.group_id',$this->_tables['users'],$this->_tables['users_groups']),
			FALSE);

		$this->db->join(
			$this->_tables['users_groups'],
			sprintf('%s.id = %s.user_id', $this->_tables['users'], $this->_tables['users_groups']),
			'left');
	}

	public function _common_joins()
	{

	}
}

class Iauth_user_model extends Base_module_record {
	private $_tables;


	function on_init()
	{
		$this->_tables = $this->_CI->config->item('tables');
	}

}
