<?php  
/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 *
 * @package		FUEL CMS
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2018, Daylight Studio LLC.
 * @license		http://docs.getfuelcms.com/general/license
 * @link		http://www.getfuelcms.com
 */

// ------------------------------------------------------------------------

/**
 * iauth Helper
 *
 * Contains functions for the {module_name} module
 *
 * @package		User Guide
 * @subpackage	Helpers
 * @category	Helpers
 */

// --------------------------------------------------------------------


if ( ! function_exists('array_flatten'))
{
    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @link   https://github.com/rappasoft/laravel-helpers/blob/master/src/helpers.php#L205
     * @param  array  $array
     * @return array
     */
    function array_flatten($array)
    {
    	$return = array();
    	array_walk_recursive($array, function($x) use (&$return) { $return[] = $x; });
    	return $return;
    }
}
