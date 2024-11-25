<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property CI_Loader $load
 * @property CI_Session $session
 * @property CI_DB $db
 * @property Admin_model $Admin_model
 */

class Admin extends CI_Controller
{
  // Constructor
  public function __construct()
  {
    parent::__construct();
    is_weekends();
    is_logged_in();
    is_checked_in();
    is_checked_out();
    $this->load->library('form_validation');
    $this->load->model('Public_model');
    $this->load->model('Admin_model');
  }

  public function index()
  {
    // jumlah pegawai berdasarkan departemen
    $dquery = "
 SELECT 
   d.department_id AS d_id, 
   d.department_name AS d_name, 
   COUNT(e.employee_id) AS qty 
 FROM department d
 LEFT JOIN employee e ON d.department_id = e.department_id
 GROUP BY d.department_id
 ORDER BY d.department_id ASC
";
    $d['d_list'] = $this->db->query($dquery)->result_array();

    // jumlah pegawai berdasarkan shift
    $squery = "
 SELECT 
   s.shift_id AS s_id, 
   CONCAT(s.start_time, ' - ', s.end_time) AS shift_time, 
   COUNT(e.employee_id) AS qty 
 FROM shift s
 LEFT JOIN employee e ON s.shift_id = e.shift_id
 GROUP BY s.shift_id
 ORDER BY s.shift_id ASC
";
    $d['s_list'] = $this->db->query($squery)->result_array();

    // Dashboard
    $d['title'] = 'Dashboard';
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata('username'));
    $d['display'] = $this->Admin_model->getDataForDashboard();

    $this->load->view('templates/dashboard_header', $d);
    $this->load->view('templates/sidebar');
    $this->load->view('templates/topbar');
    $this->load->view('admin/index', $d);
    $this->load->view('templates/dashboard_footer');
  }
}
