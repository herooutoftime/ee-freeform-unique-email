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
 * Freeform Unique Email Extension
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Extension
 * @author		Andreas Bilz
 * @link		http://www.herooutoftime.com
 */

class Freeform_unique_email_ext {

	public $settings 		= array();
	public $description		= 'Validates uniqueness of email address';
	public $docs_url		= '';
	public $name			= 'Freeform Unique Email';
	public $settings_exist	= 'n';
	public $version			= '1.0';

	private $EE;

	/**
	 * Constructor
	 *
	 * @param 	mixed	Settings array or empty string if none exist.
	 */
	public function __construct($settings = '')
	{
		$this->EE =& get_instance();
		$this->settings = $settings;

    $this->EE->load->add_package_path(PATH_THIRD.'freeform_unique_email/');
    $this->EE->load->library('freeform_unique_email_lib');

	}// ----------------------------------------------------------------------

	/**
	 * Activate Extension
	 *
	 * This function enters the extension into the exp_extensions table
	 *
	 * @see http://codeigniter.com/user_guide/database/index.html for
	 * more information on the db class.
	 *
	 * @return void
	 */
	public function activate_extension()
	{
        // Get all languages for later
        $languages = $this->EE->db->select('*')->from('transcribe_languages')->get();
        foreach($languages->result() as $language) {
            $lang_arr[$language->abbreviation] = $language->id;
        }

		// Setup custom settings in this array.
		$this->settings = array();

        $hooks = array(
          'freeform_module_validate_begin'	=> 'validate_email',
        );

        foreach ($hooks as $hook => $method)
        {
          $data = array(
            'class'		=> __CLASS__,
            'method'	=> $method,
            'hook'		=> $hook,
            'settings'	=> serialize($this->settings),
            'version'	=> $this->version,
            'enabled'	=> 'y'
          );

          $this->EE->db->insert('extensions', $data);
        }

        $variables = include "install/freeform_unique_email.inc.php";

        // This creates the variables itself
        // and stores the ID in $variables for further use
        $keys = array_keys($variables);
        foreach($keys as $var) {
            $this->EE->db->insert('transcribe_variables', array('name' => $var));
            $variables[$var]['id'] = $this->EE->db->insert_id();
        }

        // This creates the relevant translations for each variable
        foreach($variables as $var => $vals) {
            foreach($vals as $lang => $val) {
                if($lang === 'id')
                    continue;
                $this->EE->db->insert('transcribe_translations', array('content' => $val, 'variable_id' => $variables[$var]['id']));
                $this->EE->db->insert('transcribe_variables_languages', array(
                    'variable_id' => $variables[$var]['id'],
                    'translation_id' => $this->EE->db->insert_id(),
                    'language_id' => $lang_arr[$lang],
                    'site_id' => 1,
                ));
            }
        }
	}

	// ----------------------------------------------------------------------

    public function validate_email($errors, $object)
    {
        $this->EE->freeform_unique_email_lib->validate_email($errors, $object);
        return;
    }

	/**
	 * Disable Extension
	 *
	 * This method removes information from the exp_extensions table
	 *
	 * @return void
	 */
	function disable_extension()
	{
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->delete('extensions');
	}

	// ----------------------------------------------------------------------

	/**
	 * Update Extension
	 *
	 * This function performs any necessary db updates when the extension
	 * page is visited
	 *
	 * @return 	mixed	void on update / false if none
	 */
	function update_extension($current = '')
	{
		if ($current == '' OR $current == $this->version)
		{
			return FALSE;
		}
	}

  /**
   * For code completion
   *
   * @return Devkit_code_completion_helper
   */
  function EE() {if(!isset($this->EE)){$this->EE =& get_instance();}return $this->EE;}

	// ----------------------------------------------------------------------
}

/* End of file ext.freeform_unique_email.php */
/* Location: /system/expressionengine/third_party/freeform_unique_email/ext.freeform_unique_email.php */
