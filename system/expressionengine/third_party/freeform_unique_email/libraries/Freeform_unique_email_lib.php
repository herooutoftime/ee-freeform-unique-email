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
    require_once PATH_THIRD."transcribe/mod.transcribe.php";
    $transcribe = new Transcribe();
    $email = $this->EE->input->get('email');

    if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        return array('success' => false, 'message' => sprintf($transcribe->replace('freeform_unique_email_invalid'), $email), 'email' => $email);
    //   return array('success' => false, 'message' => sprintf(lang('freeform_unique_email_invalid'), $email), 'email' => $email);

    if(!$this->get_uniqueness($email))
        return array('success' => false, 'message' => sprintf($transcribe->replace('freeform_unique_email_taken'), $email), 'email' => $email);
    //   return array('success' => false, 'message' => sprintf(lang('freeform_unique_email_taken'), $email), 'email' => $email);

    return array('success' => true, 'message' => sprintf($transcribe->replace('freeform_unique_email_available'), $email), 'email' => $email);
    // return array('success' => true, 'message' => sprintf(lang('freeform_unique_email_available'), $email), 'email' => $email);
  }

  public function get_uniqueness($value, $field = 'email')
  {

    if(empty($value))
      return;

    $field_name = 'form_field_' . $this->get_real_fieldname($field);
    $tables = $this->get_forms_by_prefix('newsletter');
    $counts = array();
    foreach($tables->result() as $table) {

        $results = $this->EE->db
          ->select('COUNT(*) AS count')
          ->from('freeform_form_entries_' . $table->form_id)
          ->where($field_name, $value)
          ->get();
        $counts[$table->form_id] = (int) $results->row('count');
        $forms[$table->form_id] = array(
            'name' => $table->form_name,
            'label' => $table->form_label,
            'count' => $results->row('count')
        );
    }

    $unique = true;
    $filtered = array_filter($counts);
    if(!empty($filtered))
        $unique = false;

    return $unique;
  }

  public function get_forms_by_prefix($prefix)
  {
      $tables = $this->EE->db
        // ->select('*')
        ->from('freeform_forms')
        ->like('form_name', $prefix)
        ->get();
        return $tables;
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
