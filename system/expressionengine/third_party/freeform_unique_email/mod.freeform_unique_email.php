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
 * Freefrom Unique Email
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		anti
 * @link
 */

class Freeform_unique_email {

  public $return_data;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->EE =& get_instance();
    $this->EE->load->add_package_path(PATH_THIRD.'freeform_unique_email/');
    $this->EE->load->library('freeform_unique_email_lib');
  }

  // ----------------------------------------------------------------

  public function validate()
  {
    $response = $this->EE->freeform_unique_email_lib->validate_email_json();
    return json_encode($response);
  }

  /**
   * Start on your custom code here...
   */

}
/* End of file mod.freeform_unique_email.php */
/* Location: /system/expressionengine/third_party/freeform_unique_email/mod.freeform_unique_email.php */