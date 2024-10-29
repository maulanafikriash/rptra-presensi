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
    // Menghitung jumlah karyawan berdasarkan departemen
    $dquery = "SELECT department.department_id AS d_id, COUNT(attendance.employee_id) AS qty 
                   FROM attendance 
                   INNER JOIN department ON attendance.department_id = department.department_id 
                   GROUP BY department.department_id";
    $d['d_list'] = $this->db->query($dquery)->result_array();

    // Menghitung jumlah karyawan berdasarkan shift
    $squery = "SELECT shift.shift_id AS s_id, COUNT(employee.employee_id) AS qty 
                    FROM employee 
                    INNER JOIN attendance ON employee.employee_id = attendance.employee_id 
                    INNER JOIN shift ON employee.shift_id = shift.shift_id 
                    GROUP BY shift.shift_id";
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
