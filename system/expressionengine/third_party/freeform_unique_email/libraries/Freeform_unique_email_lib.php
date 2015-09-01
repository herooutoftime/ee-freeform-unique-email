<?php
/**
 * Created by PhpStorm.
 * User: hoot
 * Date: 05/08/15
 * Time: 18:32
 */

class Freeform_unique_email_lib {

  public $EE;

  public function __construct()
  {
    $this->EE =& get_instance();
    $this->EE->lang->loadfile('freeform_unique_email');
  }

  /**
   *
   *
   * @param
   * @return
   */
  public function validate_email($errors = null, $object = null)
  {

    //have other extensions already manipulated?
    if ($this->EE->extensions->last_call !== FALSE)
    {
      $errors = $this->EE->extensions->last_call;
    }
    // Disable validation temporarily
    return;

    $email = $this->EE->input->post('email');

    if(!filter_var($email, FILTER_VALIDATE_EMAIL))
      return false;

    if($this->get_uniqueness($email))
      return;

    $object->field_errors['email_exists'] = 'E-Mail exists';

    return;

    // Add Code for the email_module_send_email_end hook here.
  }

  public function validate_email_json()
  {
    $email = $this->EE->input->get('email');

    if(!filter_var($email, FILTER_VALIDATE_EMAIL))
      return array('success' => false, 'message' => sprintf(lang('freeform_unique_email_invalid'), $email), 'email' => $email);

    if(!$this->get_uniqueness($email))
      return array('success' => false, 'message' => sprintf(lang('freeform_unique_email_taken'), $email), 'email' => $email);

    return array('success' => true, 'message' => sprintf(lang('freeform_unique_email_available'), $email), 'email' => $email);
  }

  public function get_uniqueness($value, $field = 'email')
  {

    if(empty($value))
      return;

    $field_name = 'form_field_' . $this->get_real_fieldname($field);

    $results = $this->EE->db
      ->select('COUNT(*) AS count')
      ->from('freeform_form_entries_4')
      ->where($field_name, $value)
      ->get();

    if($results->row('count') > 0)
      return false;

    return true;
  }

  public function get_real_fieldname($field)
  {

    $field_id = $this->EE->db
      ->select('field_id')
      ->from('freeform_fields')
      ->where('field_name', $field)
      ->get()
      ->row('field_id');
    return $field_id;
  }

}