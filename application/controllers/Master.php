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
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata('username'));

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
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata('username'));
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
      'department_id' => $this->input->post('d_id'),
      'department_name' => $this->input->post('d_name')
    ];

    $checkId = $this->db->get_where('department', ['department_id' => $data['department_id']])->num_rows();
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
    $d['d_old'] = $this->db->get_where('department', ['department_id' => $d_id])->row_array();
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata('username'));
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
    $data = ['department_name' => $name];
    $this->db->update('department', $data, ['department_id' => $d_id]);
    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Berhasil mengedit department!</div>');
    redirect('master');
  }

  public function d_dept($d_id)
  {
    // Hapus semua data di tabel attendance yang terkait dengan department_id tersebut
    $this->db->delete('attendance', ['department_id' => $d_id]);

    // Hapus department setelah menghapus data terkait
    $this->db->delete('department', ['department_id' => $d_id]);

    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Berhasil menghapus department!</div>');
    redirect('master');
  }

  // End of department

  public function shift()
  {
    // Shift Data
    $d['title'] = 'Shift';
    $d['shift'] = $this->db->get('shift')->result_array();
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata('username'));

    $this->load->view('templates/table_header', $d);
    $this->load->view('templates/sidebar');
    $this->load->view('templates/topbar');
    $this->load->view('master/shift/index', $d); // Shift Page
    $this->load->view('templates/table_footer');
  }

  public function a_shift()
  {
    $generateID = $this->db->get('shift')->num_rows();
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata('username'));
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
      'start_time' => $sHour . ':' . $sMinutes . ':' . $sSeconds,
      'end_time' => $eHour . ':' . $eMinutes . ':' . $eSeconds,
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
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata('username'));

    $data = $this->db->get_where('shift', ['shift_id' => $s_id])->row_array();
    $start = explode(':', $data['start_time']);
    $end = explode(':', $data['end_time']);

    // Edit shift
    $d['title'] = 'Shift';
    $d['s_id'] = $data['shift_id'];
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
        'start_time' => $sHour . ':' . $sMinutes . ':' . $sSeconds,
        'end_time' => $eHour . ':' . $eMinutes . ':' . $eSeconds,
      ];
      $this->_editShift($s_id, $set);
    }
  }

  private function _editShift($s_id, $set)
  {
    $this->db->where('shift_id', $s_id);
    $this->db->update('shift', $set);
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
    $this->db->delete('shift', ['shift_id' => $s_id]);

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
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata('username'));

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
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata('username'));

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
    $department = $this->input->post('d_id'); // pastikan mengambil department_id dari form input
    $email = $this->input->post('email');
    $gender = $this->input->post('e_gender');
    $birth_date = $this->input->post('e_birth_date');
    $hire_date = $this->input->post('e_hire_date');
    $shift_id = $this->input->post('s_id');

    // Validasi jika email sudah ada
    $query = "SELECT * FROM employee WHERE email = '$email'";
    $checkEmail = $this->db->query($query)->num_rows();
    if ($checkEmail > 0) {
      $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
              Email sudah digunakan!</div>');
      redirect('master/a_employee');
    }

    // Upload foto jika ada
    $config['upload_path'] = './images/pp/';
    $config['allowed_types'] = 'jpg|png|jpeg';
    $config['max_size'] = '2048';
    $config['file_name'] = 'item-' . date('ymd') . '-' . substr(md5(rand()), 0, 10);
    $this->load->library('upload', $config);
    $image = ($_FILES['image']['name'] && $this->upload->do_upload('image'))
      ? $this->upload->data('file_name') : 'default.png';

    // Persiapkan data yang akan disimpan
    $data = [
      'employee_name' => $name,
      'email' => $email,
      'gender' => $gender,
      'image' => $image,
      'birth_date' => $birth_date,
      'hire_date' => $hire_date,
      'shift_id' => $shift_id,
      'department_id' => $department  // masukkan department_id ke data
    ];

    // Insert ke tabel employee
    $this->db->insert('employee', $data);

    // Cek jika data berhasil disimpan
    if ($this->db->affected_rows() > 0) {
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
    $d['employee'] = $this->db->get_where('employee', ['employee_id' => $e_id])->row_array();

    // Ambil data department saat ini
    $this->db->select('department_id, department_name');
    $this->db->from('department');
    $this->db->where('department_id', $d['employee']['department_id']);
    $d['department_current'] = $this->db->get()->row_array();

    // Ambil semua department untuk dropdown
    $d['department'] = $this->db->get('department')->result_array();
    if (!$d['department_current']) {
      $d['department_current'] = [
        'department_id' => $d['department'][0]['department_id']
      ];
    }

    $this->db->select('shift_id, start_time, end_time');
    $d['shift'] = $this->db->get('shift')->result_array();
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata('username'));

    // Form validation
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
      $this->load->view('master/employee/e_employee', $d);
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
        $image = $d['employee']['image'];
      }

      // Update data pegawai
      $data = [
        'employee_name' => $name,
        'gender' => $gender,
        'image' => $image,
        'birth_date' => $birth_date,
        'hire_date' => $hire_date,
        'shift_id' => $s_id,
        'department_id' => $d_id // Masukkan department_id langsung ke tabel employee
      ];

      $this->_editEmployee($e_id, $data);
    }
  }


  private function _editEmployee($e_id, $data)
  {
    // Update data di tabel employee berdasarkan employee_id
    $this->db->update('employee', $data, ['employee_id' => $e_id]);
    $rows_affected = $this->db->affected_rows();

    if ($rows_affected > 0) {
      // Ambil data employee terbaru termasuk department_id dan employee_id
      $employee = $this->db->get_where('employee', ['employee_id' => $e_id])->row_array();

      if ($employee) {
        // Bentuk username baru dengan format department_id + employee_id
        $new_username = $employee['department_id'] . str_pad($employee['employee_id'], 3, '0', STR_PAD_LEFT);

        // Update username di user_accounts
        $this->db->update('user_accounts', ['username' => $new_username], ['employee_id' => $e_id]);

        // Update username dan department_id di attendance
        $this->db->update('attendance', [
          'username' => $new_username,
          'department_id' => $employee['department_id']
        ], ['employee_id' => $e_id]);
      }

      $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Berhasil memperbarui data pegawai!</div>');
    } else {
      $this->session->set_flashdata('message', '<div class="alert alert-info" role="alert">Tidak ada perubahan pada data pegawai.</div>');
    }
    redirect('master/employee');
  }


  public function detail_employee($e_id)
  {
    $d['title'] = 'Detail Pegawai';
    $d['employee'] = $this->db->get_where('employee', ['employee_id' => $e_id])->row_array();

    // Ambil data department yang terkait dengan pegawai
    $this->db->select('d.department_id as department_id, d.department_name as department_name');
    $this->db->from('department d');

    // Pastikan employee_department ada di sini. Jika tidak, gunakan department_id di employee
    $this->db->where('d.department_id', $d['employee']['department_id']);
    $d['department_current'] = $this->db->get()->row_array();

    // Ambil shift berdasarkan shift_id yang ada di employee
    $this->db->select('s.shift_id, s.start_time as start, s.end_time as end');
    $this->db->from('shift s');
    $this->db->where('s.shift_id', $d['employee']['shift_id']);
    $d['shift_current'] = $this->db->get()->row_array();

    // Jika department tidak ditemukan, set department default (tangani jika department tidak ada di employee)
    if (!$d['department_current']) {
      $d['department_current'] = [
        'department_id' => null, // atau ID department default yang Anda inginkan
        'department_name' => 'Tidak Diketahui' // Atau nama default
      ];
    }

    // Ambil data shift pegawai
    $d['shift'] = $this->db->get('shift')->result_array();

    // Ambil akun admin yang sedang login
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata('username'));

    // Tampilkan halaman detail pegawai
    $this->load->view('templates/header', $d);
    $this->load->view('templates/sidebar');
    $this->load->view('templates/topbar');
    $this->load->view('master/employee/detail_employee', $d);
    $this->load->view('templates/footer');
  }


  public function attendance_history($employee_id)
  {
    $data['title'] = 'Riwayat Kehadiran Pegawai';

    // Dapatkan data pegawai berdasarkan ID
    $data['employee'] = $this->db->get_where('employee', ['employee_id' => $employee_id])->row_array();
    $data['employee_id'] = $employee_id;

    // Ambil department berdasarkan `department_id` dari `employee`
    $this->db->select('department_id, department_name');
    $this->db->from('department');
    $this->db->where('department_id', $data['employee']['department_id']);
    $data['department_current'] = $this->db->get()->row_array();

    // Ambil shift berdasarkan `shift_id` dari `employee`
    $this->db->select('shift_id, start_time, end_time');
    $this->db->from('shift');
    $this->db->where('shift_id', $data['employee']['shift_id']);
    $data['shift_current'] = $this->db->get()->row_array();

    // Jika department atau shift tidak ditemukan, tampilkan pesan default
    if (!$data['department_current']) {
      $data['department_current'] = [
        'department_id' => 'Not assigned',
        'department_name' => 'Department not assigned'
      ];
    }
    if (!$data['shift_current']) {
      $data['shift_current'] = ['shift_id' => 'Shift not assigned'];
    }

    // Ambil bulan dan tahun dari request atau gunakan bulan dan tahun saat ini
    $month = $this->input->get('month') ?: date('m');
    $year = $this->input->get('year') ?: date('Y');

    // Simpan ke data untuk digunakan di view
    $data['month'] = $month;
    $data['year'] = $year;

    // Ambil data kehadiran berdasarkan `employee_id`, `month`, dan `year`
    $this->db->select('attendance_date AS date, presence_status, check_in_latitude, check_in_longitude, check_out_latitude, check_out_longitude');
    $this->db->from('attendance');
    $this->db->where('employee_id', $employee_id);
    $this->db->where('MONTH(attendance_date)', $month);
    $this->db->where('YEAR(attendance_date)', $year);
    $attendance = $this->db->get()->result_array();

    // Proses data kehadiran berdasarkan hari
    $attendanceData = [];
    foreach ($attendance as $att) {
      if (isset($att['date'])) {
        $day = (int) date('j', strtotime($att['date']));
        $attendanceData[$day] = [
          'presence_status' => $att['presence_status'],
          'check_in_latitude' => $att['check_in_latitude'],
          'check_in_longitude' => $att['check_in_longitude'],
          'check_out_latitude' => $att['check_out_latitude'],
          'check_out_longitude' => $att['check_out_longitude']
        ];
      }
    }
    $data['attendance'] = $attendanceData;

    $data['account'] = $this->Admin_model->getAdmin($this->session->userdata('username'));

    // Load views
    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar');
    $this->load->view('templates/topbar');
    $this->load->view('master/employee/attendance_history', $data);
    $this->load->view('templates/footer');
  }


  public function update_attendance_history()
  {
    // Ambil data dari form
    $employee_id = $this->input->post('employee_id');
    $date = $this->input->post('date');
    $presence_status = $this->input->post('presence_status');

    // Ambil data department_id dan shift_id langsung dari tabel employee
    $employee = $this->db->get_where('employee', ['employee_id' => $employee_id])->row_array();
    $department_id = $employee['department_id'];
    $shift_id = $employee['shift_id'];

    // Cek apakah data presensi untuk `employee_id` dan `date` sudah ada
    $this->db->where('employee_id', $employee_id);
    $this->db->where('attendance_date', $date);
    $query = $this->db->get('attendance');

    if ($query->num_rows() > 0) {
      // Jika data ada, update `presence_status`
      $this->db->where('employee_id', $employee_id);
      $this->db->where('attendance_date', $date);
      $updated = $this->db->update('attendance', [
        'presence_status' => $presence_status,
        'in_time' => date('H:i:s')
    ]);

      if ($updated) {
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Status presensi berhasil diperbarui!</div>');
      } else {
        $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Gagal memperbarui status presensi!</div>');
      }
    } else {
      // Jika data tidak ada, tambahkan data presensi baru
      $inserted = $this->db->insert('attendance', [
        'employee_id' => $employee_id,
        'attendance_date' => $date,
        'presence_status' => $presence_status,
        'username' => $this->session->userdata('username'),
        'department_id' => $department_id,
        'shift_id' => $shift_id,
        'in_status' => 'via admin',
        'in_time' => date('H:i:s')
      ]);

      if ($inserted) {
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Status presensi berhasil ditambahkan!</div>');
      } else {
        $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Gagal menambahkan status presensi!</div>');
      }
    }

    redirect('master/attendance_history/' . $employee_id);
  }


  public function d_employee($e_id)
  {
    // Hapus data kehadiran (attendance) yang terkait dengan employee_id
    $this->db->delete('attendance', ['employee_id' => $e_id]);

    // Ambil username dari user_accounts berdasarkan employee_id
    $user = $this->db->get_where('user_accounts', ['employee_id' => $e_id])->row_array();

    if ($user) {
      // Hapus data di tabel user_accounts yang terkait dengan employee_id tersebut
      $this->db->delete('user_accounts', ['employee_id' => $e_id]);
    }

    // Hapus data pegawai dari tabel employee
    $this->db->delete('employee', ['employee_id' => $e_id]);
    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Berhasil menghapus data pegawai!</div>');
    redirect('master/employee');
  }

  public function users()
  {
    $query = "SELECT user_accounts.username AS u_username,
    employee.employee_id AS e_id,
    employee.employee_name AS e_name,
    employee.department_id AS d_id
    FROM employee
    LEFT JOIN user_accounts ON user_accounts.employee_id = employee.employee_id";

    $d['title'] = 'User Accounts';
    $d['data'] = $this->db->query($query)->result_array();
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata('username'));

    $this->load->view('templates/table_header', $d);
    $this->load->view('templates/sidebar');
    $this->load->view('templates/topbar');
    $this->load->view('master/users/index', $d);
    $this->load->view('templates/table_footer');
  }

  public function a_users($e_id)
  {
    $emp = $this->db->get_where('employee', ['employee_id' => $e_id])->row_array();
    $user = $this->db->get_where('user_accounts', ['employee_id' => $e_id])->row_array(); // Ambil data akun berdasarkan employee_id
    $d['title'] = 'Users';
    $d['e_id'] = $e_id; // Simpan employee_id untuk keperluan form
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata('username'));
    $d['username'] = $emp['department_id'] . $emp['employee_id'];

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
      $role_id = ($emp['department_id'] === 'ADM') ? 1 : 2;

      $data = [
        'username' => $username,
        'password' => password_hash($this->input->post('u_password'), PASSWORD_DEFAULT),
        'employee_id' => $e_id,
        'user_role_id' => $role_id
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
    $this->db->insert('user_accounts', $data);
    $rows = $this->db->affected_rows();
    if ($rows > 0) {
      $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Berhasil membuat akun!</div>');
    } else {
      $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Gagal membuat akun!</div>');
    }
    redirect('master/users');
  }


  public function e_users($username)
  {
    $d['title'] = 'Users';
    $d['users'] = $this->db->get_where('user_accounts', ['username' => $username])->row_array();
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata('username'));

    // Validasi password
    $this->form_validation->set_rules('password', 'Password', 'required|trim|min_length[6]');

    if ($this->form_validation->run() == false) {
      $this->load->view('templates/header', $d);
      $this->load->view('templates/sidebar');
      $this->load->view('templates/topbar');
      $this->load->view('master/users/e_users', $d);
      $this->load->view('templates/footer');
    } else {
      // Menyiapkan data untuk update
      $data = [
        'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT)
      ];
      $this->_editUsers($data, $username);
    }
  }


  private function _editUsers($data, $username)
  {
    $this->db->update('user_accounts', $data, ['username' => $username]);
    $rows = $this->db->affected_rows();

    if ($rows > 0) {
      $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Berhasil mengedit akun!</div>');
    } else {
      $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Gagal mengedit akun!</div>');
    }

    redirect('master/users');
  }

  public function d_users($username)
  {
    // Set nilai username di tabel attendance menjadi NULL
    $this->db->set('username', NULL);
    $this->db->where('username', $username);
    $this->db->update('attendance');

    // Hapus data pengguna dari tabel user_accounts
    $this->db->delete('user_accounts', ['username' => $username]);

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
