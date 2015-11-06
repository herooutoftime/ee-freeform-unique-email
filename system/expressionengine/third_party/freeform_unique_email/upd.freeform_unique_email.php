<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Freeform_unique_email_upd {
  var $version = '0.1';
  var $name = 'Freeform_unique_email';

  public function __construct() {
    $this->EE =& get_instance();
  }

  public function install() {
    $this->EE->load->dbforge();

    $data = array(
      'module_name' => $this->name,
      'module_version' => $this->version,
      'has_cp_backend' => 'n',
      'has_publish_fields' => 'n'
    );

    $this->EE->db->insert('modules', $data);

    return TRUE;
  }

  public function update($current = '') {
    return FALSE;
  }

  public function uninstall() {
    $this->EE->load->dbforge();

    $this->EE->db->select('module_id');
    $query = $this->EE->db->get_where('modules', array('module_name' => $this->name));

    $this->EE->db->where('module_id', $query->row('module_id'));
    $this->EE->db->delete('module_member_groups');

    $this->EE->db->where('module_name', $this->name);
    $this->EE->db->delete('modules');

    return TRUE;
  }

}