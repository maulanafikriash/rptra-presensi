<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property CI_Loader $load
 * @property CI_Form_validation $form_validation
 * @property CI_Session $session
 * @property CI_upload $upload
 * @property CI_DB $db
 * @property CI_Input $input
 * @property Public_model $Public_model
 * @property Admin_model $Admin_model
 */

class Master extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    is_logged_in();
    $this->load->library('form_validation');
    $this->load->model('Public_model');
    $this->load->model('Admin_model');
  }
  public function index()
  {
    // Department Data
    $d['title'] = 'Department';
    $d['department'] = $this->db->get('department')->result_array();
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata['username']);

    $this->load->view('templates/table_header', $d);
    $this->load->view('templates/sidebar');
    $this->load->view('templates/topbar');
    $this->load->view('master/department/index', $d); // Department Page
    $this->load->view('templates/table_footer');
  }
  public function a_dept()
  {
    // Add Department
    $d['title'] = 'Department';
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata['username']);
    // Form Validation
    $this->form_validation->set_rules('d_id', 'Department ID', 'required|trim|exact_length[3]|alpha');
    $this->form_validation->set_rules('d_name', 'Department Name', 'required|trim');

    if ($this->form_validation->run() == false) {
      $this->load->view('templates/header', $d);
      $this->load->view('templates/sidebar');
      $this->load->view('templates/topbar');
      $this->load->view('master/department/a_dept', $d); // Add Department Page
      $this->load->view('templates/footer');
    } else {
      $this->_addDept();
    }
  }

  private function _addDept()
  {
    $data = [
      'id' => $this->input->post('d_id'),
      'name' => $this->input->post('d_name')
    ];

    $checkId = $this->db->get_where('department', ['id' => $data['id']])->num_rows();
    if ($checkId > 0) {
      $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
      Gagal ditambahkan, ID telah digunakan!</div>');
    } else {
      $this->db->insert('department', $data);
      $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
        Berhasil menambahkan department baru!</div>');
    }
    redirect('master');
  }

  public function e_dept($d_id)
  {
    // Edit Department
    $d['title'] = 'Department';
    $d['d_old'] = $this->db->get_where('department', ['id' => $d_id])->row_array();
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata['username']);
    // Form Validation
    $this->form_validation->set_rules('d_name', 'Department Name', 'required|trim');

    if ($this->form_validation->run() == false) {
      $this->load->view('templates/header', $d);
      $this->load->view('templates/sidebar');
      $this->load->view('templates/topbar');
      $this->load->view('master/department/e_dept', $d); // Edit Department Page
      $this->load->view('templates/footer');
    } else {
      $name = $this->input->post('d_name');
      $this->_editDept($d_id, $name);
    }
  }
  private function _editDept($d_id, $name)
  {
    $data = ['name' => $name];
    $this->db->update('department', $data, ['id' => $d_id]);
    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
        Berhasil mengedit department!</div>');
    redirect('master');
  }

  public function d_dept($d_id)
  {
    // Hapus semua data di tabel attendance yang terkait dengan department_id tersebut
    $this->db->delete('attendance', ['department_id' => $d_id]);

    // Hapus department setelah menghapus data terkait
    $this->db->delete('department', ['id' => $d_id]);

    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Berhasil menghapus department!</div>');
    redirect('master');
  }

  // End of department

  public function shift()
  {
    // Shift Data
    $d['title'] = 'Shift';
    $d['shift'] = $this->db->get('shift')->result_array();
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata['username']);

    $this->load->view('templates/table_header', $d);
    $this->load->view('templates/sidebar');
    $this->load->view('templates/topbar');
    $this->load->view('master/shift/index', $d); // shift Page
    $this->load->view('templates/table_footer');
  }
  public function a_shift()
  {
    $generateID = $this->db->get('shift')->num_rows();
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata['username']);
    // Add shift
    $d['title'] = 'Shift';
    $d['s_id'] = $generateID + 1;

    // Form Validation
    $this->form_validation->set_rules('s_start_h', 'Hour', 'required|trim');
    $this->form_validation->set_rules('s_start_m', 'Minutes', 'required|trim');
    $this->form_validation->set_rules('s_start_s', 'Seconds', 'required|trim');
    $this->form_validation->set_rules('s_end_h', 'Hour', 'required|trim');
    $this->form_validation->set_rules('s_end_m', 'Minutes', 'required|trim');
    $this->form_validation->set_rules('s_end_s', 'Seconds', 'required|trim');

    if ($this->form_validation->run() == false) {
      $this->load->view('templates/header', $d);
      $this->load->view('templates/sidebar');
      $this->load->view('templates/topbar');
      $this->load->view('master/shift/a_shift', $d); // Add shift Page
      $this->load->view('templates/footer');
    } else {
      $this->_addShift();
    }
  }
  private function _addShift()
  {
    // Start Time
    $sHour = $this->input->post('s_start_h');
    $sMinutes = $this->input->post('s_start_m');
    $sSeconds = $this->input->post('s_start_s');

    // End Time
    $eHour = $this->input->post('s_end_h');
    $eMinutes = $this->input->post('s_end_m');
    $eSeconds = $this->input->post('s_end_s');

    $data = [
      'start' => $sHour . ':' . $sMinutes . ':' . $sSeconds,
      'end' => $eHour . ':' . $eMinutes . ':' . $eSeconds,
    ];

    $this->db->insert('shift', $data);
    $affectedRow = $this->db->affected_rows();
    if ($affectedRow > 0) {
      $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
        Berhasil menambahkan shift baru!</div>');
    } else {
      $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
      Gagal menambahkan shift baru!</div>');
    }
    redirect('master/shift');
  }

  public function e_shift($s_id)
  {
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata['username']);

    $data = $this->db->get_where('shift', ['id' => $s_id])->row_array();
    $start = explode(':', $data['start']);
    $end = explode(':', $data['end']);

    // Edit shift
    $d['title'] = 'Shift';
    $d['s_id'] = $data['id'];
    $d['s_sh'] = $start[0];
    $d['s_sm'] = $start[1];
    $d['s_ss'] = $start[2];
    $d['s_eh'] = $end[0];
    $d['s_em'] = $end[1];
    $d['s_es'] = $end[2];

    // Form Validation
    $this->form_validation->set_rules('s_start_h', 'Shift Start Hour', 'required|trim');
    $this->form_validation->set_rules('s_start_m', 'Shift Start Minutes', 'required|trim');
    $this->form_validation->set_rules('s_start_s', 'Shift Start Seconds', 'required|trim');
    $this->form_validation->set_rules('s_end_h', 'Shift End Hour', 'required|trim');
    $this->form_validation->set_rules('s_end_m', 'Shift End Minutes', 'required|trim');
    $this->form_validation->set_rules('s_end_s', 'Shift End Seconds', 'required|trim');

    if ($this->form_validation->run() == false) {
      $this->load->view('templates/header', $d);
      $this->load->view('templates/sidebar');
      $this->load->view('templates/topbar');
      $this->load->view('master/shift/e_shift', $d); // Edit shift Page
      $this->load->view('templates/footer');
    } else {
      // Start Time
      $sHour = $this->input->post('s_start_h');
      $sMinutes = $this->input->post('s_start_m');
      $sSeconds = $this->input->post('s_start_s');

      // End Time
      $eHour = $this->input->post('s_end_h');
      $eMinutes = $this->input->post('s_end_m');
      $eSeconds = $this->input->post('s_end_s');

      $set = [
        'start' => $sHour . ':' . $sMinutes . ':' . $sSeconds,
        'end' => $eHour . ':' . $eMinutes . ':' . $eSeconds,
      ];
      $this->_editShift($s_id, $set);
    }
  }
  private function _editShift($s_id, $set)
  {
    $this->db->where('id', $s_id);
    $this->db->update('shift', $set, ['id' => $s_id]);
    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
        Berhasil mengedit shift!</div>');
    redirect('master/shift');
  }

  public function d_shift($s_id)
  {
    // Update shift_id di tabel attendance yang terkait
    $this->db->where('shift_id', $s_id);
    $this->db->update('attendance', ['shift_id' => NULL]);

    // Hapus data shift setelah memperbarui data terkait
    $this->db->delete('shift', ['id' => $s_id]);

    $query = 'ALTER TABLE `shift` AUTO_INCREMENT = 1';
    $this->db->query($query);

    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Berhasil menghapus shift!</div>');
    redirect('master/shift');
  }

  // End of Shift

  public function employee()
  {
    // Employee Data
    $d['title'] = 'Pegawai';
    $d['employee'] = $this->db->get('employee')->result_array();
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata['username']);

    $this->load->view('templates/table_header', $d);
    $this->load->view('templates/sidebar');
    $this->load->view('templates/topbar');
    $this->load->view('master/employee/index', $d); // Employee Page
    $this->load->view('templates/table_footer');
  }

  public function a_employee()
  {
    // Add Employee
    $d['title'] = 'Pegawai';
    $d['department'] = $this->db->get('department')->result_array();
    $d['shift'] = $this->db->get('shift')->result_array();
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata['username']);

    // Form Validation
    $this->form_validation->set_rules('e_name', 'Employee Name', 'required|trim');
    $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
    $this->form_validation->set_rules('s_id', 'Shift', 'required|trim');
    $this->form_validation->set_rules('e_gender', 'Gender', 'required');
    $this->form_validation->set_rules('e_birth_date', 'Birth Date', 'required|trim');
    $this->form_validation->set_rules('e_hire_date', 'Hire Date', 'required|trim');

    if ($this->form_validation->run() == false) {
      $this->load->view('templates/header', $d);
      $this->load->view('templates/sidebar');
      $this->load->view('templates/topbar');
      $this->load->view('master/employee/a_employee', $d); // Add Employee Page
      $this->load->view('templates/footer');
    } else {
      $this->_addEmployee();
    }
  }

  private function _addEmployee()
  {
    $name = $this->input->post('e_name');
    $department = $this->input->post('d_id');
    $email = $this->input->post('email');
    $gender = $this->input->post('e_gender');
    $birth_date = $this->input->post('e_birth_date');
    $hire_date = $this->input->post('e_hire_date');
    $shift_id = $this->input->post('s_id');

    // Check Email
    $query = "SELECT * FROM employee WHERE email = '$email'";
    $checkEmail = $this->db->query($query)->num_rows();

    if ($checkEmail > 0) {
      $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
      Email sudah digunakan!</div>');
      redirect('master/a_employee');
    }

    // Config Upload Image
    $config['upload_path'] = './images/pp/';
    $config['allowed_types'] = 'jpg|png|jpeg';
    $config['max_size'] = '2048';
    $config['file_name'] = 'item-' . date('ymd') . '-' . substr(md5(rand()), 0, 10);

    // load library upload and pass config
    $this->load->library('upload', $config);

    if ($_FILES['image']['name']) {
      if ($this->upload->do_upload('image')) {
        $image = $this->upload->data('file_name');
      }
    } else {
      $image = 'default.png';
    }

    $data = [
      'name' => $name,
      'email' => $email,
      'gender' => $gender,
      'image' => $image,
      'birth_date' => $birth_date,
      'hire_date' => $hire_date,
      'shift_id' => $shift_id
    ];

    $this->db->insert('employee', $data);
    $getEmp = $this->db->get_where('employee', ['email' => $data['email']])->row_array();
    // var_dump($getEmp);
    // die;
    $e_id = $getEmp['id'];
    $d = [
      'department_id' => $department,
      'employee_id' => $e_id
    ];

    $this->db->insert('employee_department', $d);
    $rows = $this->db->affected_rows();
    if ($rows > 0) {
      $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
        Berhasil menambahkan pegawai baru!</div>');
    } else {
      $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
        Gagal menambahkan data!</div>');
    }
    redirect('master/employee');
  }

  public function e_employee($e_id)
  {
    $d['title'] = 'Pegawai';
    $d['employee'] = $this->db->get_where('employee', ['id' => $e_id])->row_array();
    $d['department_current'] = $this->db->get_where('employee_department', ['employee_id' => $e_id])->row_array();
    $d['department'] = $this->db->get('department')->result_array();
    // Jika department saat ini tidak ada (telah dihapus), set department default ke department pertama yang tersedia
    if (!$d['department_current']) {
      $d['department_current'] = [
        'department_id' => $d['department'][0]['id'] // Pilih department pertama
      ];
    }

    $this->db->select('id, start, end');
    $d['shift'] = $this->db->get('shift')->result_array();
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata['username']);

    $this->form_validation->set_rules('e_name', 'Name', 'required|trim');
    $this->form_validation->set_rules('e_gender', 'Gender', 'required');
    $this->form_validation->set_rules('e_birth_date', 'Birth Date', 'required|trim');
    $this->form_validation->set_rules('e_hire_date', 'Hire Date', 'required|trim');
    $this->form_validation->set_rules('s_id', 'Shift', 'required|trim');
    $this->form_validation->set_rules('d_id', 'Department', 'required|trim');

    if ($this->form_validation->run() == false) {
      $this->load->view('templates/header', $d);
      $this->load->view('templates/sidebar');
      $this->load->view('templates/topbar');
      $this->load->view('master/employee/e_employee', $d); // Edit Employee Page
      $this->load->view('templates/footer');
    } else {
      $name = $this->input->post('e_name');
      $gender = $this->input->post('e_gender');
      $birth_date = $this->input->post('e_birth_date');
      $hire_date = $this->input->post('e_hire_date');
      $d_id = $this->input->post('d_id');
      $s_id = $this->input->post('s_id');

      // Config Upload Image
      $config['upload_path'] = './images/pp/';
      $config['allowed_types'] = 'jpg|png|jpeg';
      $config['max_size'] = '2048';
      $config['file_name'] = 'item-' . date('ymd') . '-' . substr(md5(rand()), 0, 10);

      // load library upload and pass config
      $this->load->library('upload', $config);

      if ($_FILES['image']['name']) {
        if ($this->upload->do_upload('image')) {
          $image = $this->upload->data('file_name');
          $old_image = $d['employee']['image'];
          if ($old_image != 'default.png') {
            unlink('./images/pp/' . $old_image);
          }
        }
      } else {
        // Jika tidak ada gambar baru, gunakan gambar lama
        $image = $d['employee']['image'];
      }

      $data = [
        'name' => $name,
        'gender' => $gender,
        'image' => $image,
        'birth_date' => $birth_date,
        'hire_date' => $hire_date,
        'shift_id' => $s_id
      ];
      $department = [
        'department_id' => $d_id
      ];
      $this->_editEmployee($e_id, $data, $department);
    }
  }
  private function _editEmployee($e_id, $data, $department)
  {
    $this->db->update('employee', $data, ['id' => $e_id]);
    $upd1 = $this->db->affected_rows();

    // Cek apakah karyawan memiliki department yang terkait
    $current_department = $this->db->get_where('employee_department', ['employee_id' => $e_id])->row_array();

    if ($current_department) {
      if ($current_department['department_id'] != $department['department_id']) {
        // Reset username menjadi unique (misalnya "reset_[old_username]") dan password di-reset
        $new_username = 'Reset' . $current_department['department_id'] . $e_id;
        $this->db->update('users', ['username' => $new_username, 'password' => NULL], ['employee_id' => $e_id]);
      }
      $this->db->update('employee_department', $department, ['employee_id' => $e_id]);
    } else {
      // Jika tidak ada department terkait, lakukan insert department baru
      $department['employee_id'] = $e_id;
      $this->db->insert('employee_department', $department);
    }

    $upd2 = $this->db->affected_rows();

    if ($upd1 == 0 && $upd2 == 0) {
      $this->session->set_flashdata('message', '<div class="alert alert-info" role="alert">Tidak ada perubahan yang dilakukan pada data pegawai.</div>');
      redirect('master/e_employee/' . $e_id);
    } else {
      $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Berhasil memperbarui data pegawai!</div>');
      redirect('master/employee');
    }
  }

  public function detail_employee($e_id)
  {
      $d['title'] = 'Detail Pegawai';
      $d['employee'] = $this->db->get_where('employee', ['id' => $e_id])->row_array();
  
      // Ambil data department yang terkait dengan pegawai
      $this->db->select('d.id as department_id, d.name as department_name');
      $this->db->from('department d');
      $this->db->join('employee_department ed', 'ed.department_id = d.id');
      $this->db->where('ed.employee_id', $e_id);
      $d['department_current'] = $this->db->get()->row_array();

       // Ambil shift berdasarkan shift_id yang ada di employee
    $this->db->select('s.id as shift_id, s.start, s.end');
    $this->db->from('shift s');
    $this->db->where('s.id', $d['employee']['shift_id']);
    $d['shift_current'] = $this->db->get()->row_array();
      
      // Jika department tidak ditemukan, set department default
      if (!$d['department_current']) {
          $d['department_current'] = [
              'department_id' => $d['department'][0]['id'],
              'department_name' => $d['department'][0]['name']
          ];
      }
  
      // Ambil data shift pegawai
      $d['shift'] = $this->db->get('shift')->result_array();
  
      // Ambil akun admin yang sedang login
      $d['account'] = $this->Admin_model->getAdmin($this->session->userdata['username']);
  
      // Tampilkan halaman detail pegawai
      $this->load->view('templates/header', $d);
      $this->load->view('templates/sidebar');
      $this->load->view('templates/topbar');
      $this->load->view('master/employee/detail_employee', $d); // Menampilkan halaman detail pegawai
      $this->load->view('templates/footer');
  }
  
  



  public function d_employee($e_id)
  {
    // Ambil username dari users berdasarkan employee_id
    $user = $this->db->get_where('users', ['employee_id' => $e_id])->row_array();

    if ($user) {
      // Hapus data kehadiran (attendance) yang terkait dengan username tersebut
      $this->db->delete('attendance', ['username' => $user['username']]);

      // Hapus data di tabel users yang terkait dengan employee_id tersebut
      $this->db->delete('users', ['employee_id' => $e_id]);
    }

    $this->db->delete('employee', ['id' => $e_id]);

    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Berhasil menghapus data pegawai!</div>');
    redirect('master/employee');
  }

  public function location()
  {
    $d['title'] = 'Lokasi';
    $d['location'] = $this->db->get('location')->result_array();
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata['username']);

    $this->load->view('templates/table_header', $d);
    $this->load->view('templates/sidebar');
    $this->load->view('templates/topbar');
    $this->load->view('master/location/index', $d);
    $this->load->view('templates/table_footer');
  }
  public function a_location()
  {
    $d['title'] = 'Lokasi';
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata['username']);

    $this->form_validation->set_rules('l_name', 'Location Name', 'required|trim');

    if ($this->form_validation->run() == false) {
      $this->load->view('templates/header', $d);
      $this->load->view('templates/sidebar');
      $this->load->view('templates/topbar');
      $this->load->view('master/location/a_location', $d);
      $this->load->view('templates/footer');
    } else {
      $data['name'] = $this->input->post('l_name');
      $this->_addLocation($data);
    }
  }
  private function _addLocation($data)
  {
    $this->db->insert('location', $data);
    $rows = $this->db->affected_rows();

    if ($rows > 0) {
      $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
        Berhasil menambahkan lokasi baru!</div>');
    } else {
      $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
        Gagal menambahkan data!</div>');
    }
    redirect('master/location');
  }
  public function e_location($l_id)
  {
    // Edit Location
    $d['title'] = 'Lokasi';
    $d['l_old'] = $this->db->get_where('location', ['id' => $l_id])->row_array();
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata['username']);

    // Form Validation
    $this->form_validation->set_rules('l_name', 'Location Name', 'required|trim');

    if ($this->form_validation->run() == false) {
      $this->load->view('templates/header', $d);
      $this->load->view('templates/sidebar');
      $this->load->view('templates/topbar');
      $this->load->view('master/Location/e_location', $d); // Edit Location Page
      $this->load->view('templates/footer');
    } else {
      $name = $this->input->post('l_name');
      $this->_editLocation($l_id, $name);
    }
  }
  private function _editLocation($l_id, $name)
  {
    $data = ['name' => $name];
    $this->db->update('location', $data, ['id' => $l_id]);
    $rows = $this->db->affected_rows();

    if ($rows > 0) {
      $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
          Berhasil mengedit lokasi!</div>');
    } else {
      $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
        Gagal mengedit data!</div>');
    }

    redirect('master/location');
  }
  public function d_location($l_id)
  {
    $query = 'ALTER TABLE `location` AUTO_INCREMENT = 1';
    $this->db->query($query);
    $this->db->delete('location', ['id' => $l_id]);
    $rows = $this->db->affected_rows();

    if ($rows > 0) {
      $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
        Successfully deleted a location!</div>');
    } else {
      $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
        Failed to delete a data!</div>');
    }

    redirect('master/employee');
  }
  // end of location

  public function users()
  {
    $query = "SELECT employee_department.employee_id AS e_id,
                     employee_department.department_id AS d_id,
                     users.username AS u_username,
                     employee.name AS e_name
                FROM employee_department
           LEFT JOIN users
                  ON employee_department.employee_id = users.employee_id
          INNER JOIN employee
                  ON employee_department.employee_id = employee.id
          ";
    $d['title'] = 'Users';
    $d['data'] = $this->db->query($query)->result_array();
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata['username']);

    $this->load->view('templates/table_header', $d);
    $this->load->view('templates/sidebar');
    $this->load->view('templates/topbar');
    $this->load->view('master/users/index', $d);
    $this->load->view('templates/table_footer');
  }

  public function a_users($e_id)
  {
    $empDep = $this->db->get_where('employee_department', ['employee_id' => $e_id])->row_array();
    $user = $this->db->get_where('users', ['employee_id' => $e_id])->row_array();
    $d['title'] = 'Users';
    $d['username'] = $empDep['department_id'] . $empDep['employee_id'];
    $d['e_id'] = $empDep['employee_id'];
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata['username']);

    $this->form_validation->set_rules('u_username', 'Username', 'required|trim|min_length[6]');
    $this->form_validation->set_rules('u_password', 'Password', 'required|trim|min_length[6]');

    if ($this->form_validation->run() == false) {
      $this->load->view('templates/header', $d);
      $this->load->view('templates/sidebar');
      $this->load->view('templates/topbar');
      $this->load->view('master/users/a_users', $d);
      $this->load->view('templates/footer');
    } else {
      $username = $this->input->post('u_username');
      if ($empDep['department_id'] != 'ADM') {
        $role_id = 2;
      } else {
        $role_id = 1;
      }
      $data = [
        'username' => $username,
        'password' => password_hash($this->input->post('u_password'), PASSWORD_DEFAULT),
        'employee_id' => $this->input->post('e_id'),
        'role_id' => $role_id
      ];
      // Cek apakah user sudah ada
      if ($user) {
        // Update user jika sudah ada
        $this->_editUsers($data, $user['username']);
      } else {
        // Tambah user baru jika belum ada
        $this->_addUsers($data);
      }
    }
  }
  private function _addUsers($data)
  {
    $this->db->insert('users', $data);
    $rows = $this->db->affected_rows();
    if ($rows > 0) {
      $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
          Berhasil membuat akun!</div>');
    } else {
      $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
        Gagal membuat akun!</div>');
    }
    redirect('master/users');
  }

  public function e_users($username)
  {
    $d['title'] = 'Users';
    $d['users'] = $this->db->get_where('users', ['username' => $username])->row_array();
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata['username']);

    $this->form_validation->set_rules('password', 'Password', 'required|trim|min_length[6]');

    if ($this->form_validation->run() == false) {
      $this->load->view('templates/header', $d);
      $this->load->view('templates/sidebar');
      $this->load->view('templates/topbar');
      $this->load->view('master/users/e_users', $d);
      $this->load->view('templates/footer');
    } else {
      $data = ['password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT)];
      $this->_editUsers($data, $username);
    }
  }
  private function _editUsers($data, $username)
  {
    $this->db->update('users', $data, ['username' => $username]);
    $rows = $this->db->affected_rows();
    if ($rows > 0) {
      $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
          Berhasil mengedit akun!</div>');
    } else {
      $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
        Gagal mengedit akun!</div>');
    }
    redirect('master/users');
  }

  public function d_users($username)
  {
    $this->db->delete('users', ['username' => $username]);
    $rows = $this->db->affected_rows();
    if ($rows > 0) {
      $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
          Berhasil menghapus akun!</div>');
    } else {
      $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
        Gagal menghapus akun!</div>');
    }
    redirect('master/users');
  }
}
