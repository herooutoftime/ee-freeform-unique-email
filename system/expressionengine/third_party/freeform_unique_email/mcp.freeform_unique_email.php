<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * test Module Control Panel File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		test
 * @link
 */

class Freeform_unique_email_mcp {

  public $return_data;

  private $_base_url;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->EE =& get_instance();

    $this->_base_url = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=freeform_unique_email';

    $this->EE->cp->set_right_nav(array(
      'module_home'	=> $this->_base_url,
      // Add more right nav items here.
    ));
  }

  // ----------------------------------------------------------------

  /**
   * Index Function
   *
   * @return 	void
   */
  public function index()
  {
    $this->EE->cp->set_variable('cp_page_title',
      lang('freeform_unique_email_module_name'));

    /**
     * This is the addons home page, add more code here!
     */
  }

  /**
   * Start on your custom code here...
   */

}
/* End of file mcp.test.php */
/* Location: /system/expressionengine/third_party/test/mcp.test.php */