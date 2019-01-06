<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 */

// ------------------------------------------------------------------------

/**
 * Fuel_iauth object 
 *
 * We load the original ion_auth library and model here
 * and use <b>__call()</b> as alias to call ion_auth methods like this:
 * `$this->fuel->iauth->is_admin()`
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 */

// ------------------------------------------------------------------------

class Fuel_iauth extends Fuel_advanced_module {

	public $name = "iauth"; // the folder name of the module
	
	/**
	 * Constructor - Sets preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	public function __construct($params = array())
	{
		parent::__construct();

		// load Ion_auth Library and Model
		$this->CI->load->module_library(IAUTH_FOLDER,'ion_auth');
		$this->CI->load->module_model(IAUTH_FOLDER,'ion_auth_model');

		/**
		 * Select the correct Language and language File
		 */

		if ($this->has_lang()) {
			$lang = (defined('FUEL_ADMIN')) 
				? $this->fuel->auth->user_lang() 
				: $this->language(detect_lang()); // $this->language('en') @return 'english'
			// Load language iauth_lang.php
			$this->CI->lang->load(IAUTH_FOLDER . '/iauth',    $lang);
			$this->CI->lang->load(IAUTH_FOLDER . '/ion_auth', $lang);
		}

		$this->initialize($params);
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize the backup object
	 *
	 * Accepts an associative array as input, containing preferences.
	 * Also will set the values in the config as properties of this object
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	public function initialize($params = array())
	{
		parent::initialize($params);
		$this->set_params($this->_config);
	}

	/**
	 * Returns the language abbreviation currently used in CodeIgniter
	 * 'en' => 'english'
	 * depends on <b>config/language_codes.php</b>
	 *
	 * @access	public
	 * @param	string	Language Code
	 * @return	string
	 */
	public function language($code = FALSE)
	{

		if ($this->fuel->language->has_multiple())
		{
			$language = $this->fuel->language->detect();
		}
		else
		{
			$language = $this->CI->config->item('language');	
		}
		
		if ($code)
		{
			$this->CI->config->module_load(IAUTH_FOLDER, 'language_codes');
			$codes = $this->CI->config->item('lang_codes');

			if (isset($codes[$language]))
			{
				return $codes[$language];
			}
			return FALSE;
		}
		else
		{
			return $language;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Add your custom methods for this advanced module below.
	 * You will be able to access it via $this->fuel->iauth->my_method()
	 */


	/**
	 * __call
	 *
	 * Acts as a simple way to call models/Ion_auth_model and libraries/Ion_auth methods without loads of stupid alias'
	 * 
	 *
	 * @param string $method
	 * @param array  $arguments
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function __call($method, $arguments)
	{
		#d($this->fuel,$this->CI->ion_auth_model, $method,$arguments,$this->CI->ion_auth,method_exists($this->CI->ion_auth, $method));
		
		if (method_exists( $this->CI->ion_auth, $method) ){
			return call_user_func_array( [$this->CI->ion_auth, $method], $arguments);
		}
		elseif(method_exists( $this->CI->ion_auth_model, $method) ){
			return call_user_func_array( [$this->CI->ion_auth_model, $method], $arguments);
		}else{
			throw new Exception('Undefined method Ion_auth::' . $method . '() or Ion_auth_model::' . $method . '() called');
		}

	}

}
