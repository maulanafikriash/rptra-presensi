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
 */

class Attendance extends CI_Controller
{
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
        log_message('info', 'POST data: ' . print_r($_POST, true));
        $d['title'] = 'Form Presensi';
        $d['account'] = $this->Public_model->getAccount($this->session->userdata('username'));
        $d['shift'] = $this->db->get('shift')->result_array();

        // Set weekends flag (true if weekend, false otherwise)
        $currentDay = date('w');
        $d['weekends'] = ($currentDay == 0);

        $shift = $d['account']['shift'];
        $shiftData = $this->db->get_where('shift', ['shift_id' => $shift])->row_array();

        date_default_timezone_set('Asia/Jakarta');
        $currentTime = time();

        $shiftStart = strtotime($shiftData['start_time']);
        $shiftEnd = strtotime($shiftData['end_time']);

        if ($shiftEnd < $shiftStart) {
            $shiftEnd = strtotime($shiftData['end_time'] . ' +1 day');
        }

        // Kondisi shift belum dimulai, sedang berjalan, atau sudah selesai
        if ($currentTime < $shiftStart) {
            $d['shift_status'] = 'belum mulai';
            $d['can_check_in'] = false;
            $d['attendance_status'] = 'Tidak Hadir';
        } elseif ($currentTime >= $shiftStart && $currentTime <= $shiftEnd) {
            $d['shift_status'] = 'presensi masuk';
            $d['can_check_in'] = true;
            $d['attendance_status'] = 'Hadir';
        } else {
            $d['shift_status'] = 'sudah selesai';
            $d['can_check_in'] = false;
            $d['attendance_status'] = 'Tidak Hadir';
        }

        $employee_id = $d['account']['id'];
        $today = date('Y-m-d');
        $attendance = $this->db->get_where('attendance', [
            'employee_id' => $employee_id,
            'attendance_date' => $today
        ])->row_array();

        // Validasi presensi keluar otomatis
        $outGracePeriod = strtotime($shiftData['end_time'] . ' +15 minutes');
        if (!empty($attendance) && !is_null($attendance['out_time'])) {
            $d['already_checked_out'] = true;
            $d['auto_checkout_message'] = 'Sudah presensi keluar';

            // Cek apakah presensi keluar dilakukan dalam rentang waktu 15 menit
            $actualOutTime = strtotime($attendance['out_time']);
            if ($actualOutTime <= $outGracePeriod) {
                $this->db->where('employee_id', $employee_id)
                    ->where('attendance_date', $today)
                    ->update('attendance', [
                        'out_time' => date('H:i:s'),
                        'out_status' => 'Tepat Waktu'
                    ]);
            }
        } elseif ($currentTime > $outGracePeriod) {
            $d['already_checked_out'] = true;
            $d['auto_checkout_message'] = 'Keluar otomatis';

            // Update status di database jika belum presensi keluar
            if (is_null($attendance['out_time'])) {
                $this->db->where('employee_id', $employee_id)
                    ->where('attendance_date', $today)
                    ->update('attendance', [
                        'out_time' => date('H:i:s', $outGracePeriod),
                        'out_status' => 'Otomatis'
                    ]);
            }
        } else {
            $d['already_checked_out'] = false;
            $d['auto_checkout_message'] = '';
        }

        $d['already_checked_in'] = !empty($attendance);
        $d['already_checked_out'] = !empty($attendance) && !is_null($attendance['out_time']);
        log_message('info', 'Check-in query: ' . $this->db->last_query());
        log_message('info', 'Already checked in: ' . ($d['already_checked_in'] ? 'Yes' : 'No'));

        $d['in'] = is_checked_in();

        // Menangani dua aksi: check-in dan check-out
        if ($this->input->post('check_in')) {
            log_message('info', 'Tombol Check-In diklik');
            $this->handleCheckIn($d);
        } elseif ($this->input->post('check_out')) {
            $this->checkOut();
        }

        // Load tampilan presensi
        $this->load->view('templates/header', $d);
        $this->load->view('templates/sidebar');
        $this->load->view('templates/topbar');
        $this->load->view('attendance/index', $d);
        $this->load->view('templates/footer');
    }

    // Method untuk menangani presensi masuk
    private function handleCheckIn($d)
    {
        // Ambil data shift
        $shift = $d['account']['shift'];
        $queryShift = "SELECT * FROM `shift` WHERE `shift_id` = $shift";
        $resultShift = $this->db->query($queryShift)->row_array();
        $startTime = $resultShift['start_time'];

        // Ambil data dari sesi dan input form
        $username = $this->session->userdata('username');
        $employee_id = $d['account']['id'];
        $department_id = $d['account']['department_id'];

        // Cek jika department_id null
        if (is_null($department_id)) {
            log_message('error', 'Department ID is null. Cannot proceed with check-in.');
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Department ID tidak ditemukan!</div>');
            redirect('attendance');
            return; // Hentikan eksekusi lebih lanjut
        }

        $shift_id = $this->input->post('work_shift');
        $in_time = date('H:i:s');
        $today = date('Y-m-d');
        $latitude = $this->input->post('latitude');
        $longitude = $this->input->post('longitude');

        log_message('info', 'In time: ' . $in_time);

        // Tentukan in_status dan presence_status berdasarkan 10 menit setelah startTime
        $allowedTime = date('H:i:s', strtotime($startTime . ' +10 minutes'));
        $inStatus = (strtotime($in_time) <= strtotime($allowedTime)) ? 'Tepat Waktu' : 'Terlambat';
        $presence_status = 1; // Hadir
        log_message('info', 'Attendance status: ' . $inStatus);

        $attendanceData = [
            'username' => $username,
            'employee_id' => $employee_id,
            'attendance_date' => $today,
            'department_id' => $department_id,
            'shift_id' => $shift_id,
            'in_time' => $in_time,
            'in_status' => $inStatus,
            'presence_status' => $presence_status,
            'check_in_latitude' => $latitude,
            'check_in_longitude' => $longitude
        ];

        log_message('info', 'Data yang akan diinsert: ' . print_r($attendanceData, true));
        $this->db->insert('attendance', $attendanceData);
        $rows = $this->db->affected_rows();

        log_message('info', 'Insert query: ' . $this->db->last_query());

        // Cek hasil insert
        if ($rows > 0) {
            log_message('info', 'Attendance data inserted successfully');
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Berhasil Presensi Masuk!</div>');
        } else {
            log_message('error', 'Failed to insert attendance data');
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Gagal Presensi Masuk!</div>');
        }

        // Redirect setelah proses selesai
        redirect('attendance');
    }

    // Method untuk menangani presensi keluar (check-out)
    public function checkOut()
    {
        log_message('info', 'checkOut method called');
        $d['account'] = $this->Public_model->getAccount($this->session->userdata('username'));

        // Ambil data employee_id
        $employee_id = $d['account']['id'];
        $today = date('Y-m-d', time());
        $outTime = date('H:i:s');
        $latitude = $this->input->post('latitude');
        $longitude = $this->input->post('longitude');

        // Cek data presensi keluar
        $attendance = $this->db->get_where('attendance', [
            'employee_id' => $employee_id,
            'attendance_date' => $today
        ])->row_array();

        if ($attendance) {
            if (!is_null($attendance['out_time'])) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Anda sudah melakukan presensi keluar!</div>');
                redirect('attendance');
            }

            // Tentukan status keluar
            $endShiftTime = strtotime($attendance['shift_end']);
            $outGracePeriod = $endShiftTime + 900;

            if ($outTime <= $outGracePeriod) {
                $outStatus = 'Tepat Waktu';
            } else {
                $outStatus = 'Otomatis';
            }

            $value = [
                'out_time' => date('H:i:s', $outTime),
                'out_status' => $outStatus,
                'presence_status' => 1, // Tetap hadir
                'check_out_latitude' => $latitude,
                'check_out_longitude' => $longitude
            ];

            $this->db->where('employee_id', $employee_id);
            $this->db->where('attendance_date', $today);
            $this->db->update('attendance', $value);

            if ($this->db->affected_rows() > 0) {
                $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Berhasil Presensi Keluar!</div>');
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Gagal Presensi Keluar!</div>');
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Tidak ada catatan presensi masuk untuk hari ini!</div>');
        }

        redirect('attendance');
    }

    public function history()
    {
        $data['title'] = 'Riwayat Presensi';
        $data['account'] = $this->Public_model->getAccount($this->session->userdata('username'));

        $employee_id = $data['account']['id'];

        if (!$employee_id) {
            show_error('Employee ID is required but not found.', 400);
        }

        // get pegawai berdasarkan employee_id
        $data['employee'] = $this->db->get_where('employee', ['employee_id' => $employee_id])->row_array();

        if (!$data['employee']) {
            show_error('Employee not found', 404);
        }

        // Ambil bulan dan tahun dari request, default ke bulan dan tahun sekarang
        $month = $this->input->get('month') ? $this->input->get('month') : date('m');
        $year = $this->input->get('year') ? $this->input->get('year') : date('Y');

        // Simpan ke data untuk digunakan di view
        $data['month'] = $month;
        $data['year'] = $year;

        // Ambil data kehadiran (presence_status) dan attendance_date berdasarkan employee_id, bulan, dan tahun
        $this->db->select('attendance_date AS date, presence_status');
        $this->db->from('attendance');
        $this->db->where('employee_id', $employee_id);
        $this->db->where('MONTH(attendance_date)', $month);
        $this->db->where('YEAR(attendance_date)', $year);
        $attendance = $this->db->get()->result_array();

        $attendanceData = [];
        foreach ($attendance as $att) {
            if (is_array($att) && isset($att['date']) && isset($att['presence_status'])) {
                // Ambil hari dari tanggal
                $attendanceData[$att['date']] = $att['presence_status']; // Simpan status presensi berdasarkan tanggal
            }
        }
        $data['attendance'] = $attendanceData;

        // Load views
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('templates/topbar');
        $this->load->view('attendance/history/index', $data);
        $this->load->view('templates/footer');
    }

    public function change_password()
    {
        $data['title'] = 'Change Password';
        $data['account'] = $this->Public_model->getAccount($this->session->userdata('username'));

        $employee_id = $data['account']['id'];

        if (!$employee_id) {
            show_error('Employee ID is required but not found.', 400);
        }

        // data pegawai berdasarkan employee_id
        $data['employee'] = $this->db->get_where('employee', ['employee_id' => $employee_id])->row_array();

        if (!$data['employee']) {
            show_error('Employee not found', 404);
        }

        $this->form_validation->set_rules('current_password', 'Password Aktif', 'required');
        $this->form_validation->set_rules('new_password', 'Password Baru', 'required|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password Baru', 'required|matches[new_password]');

        $this->form_validation->set_message('min_length', '{field} harus berisi minimal {param} karakter.');
        $this->form_validation->set_message('matches', '{field} harus sama dengan Password Baru.');

        if ($this->form_validation->run() == FALSE) {
            // Jika validasi gagal
            if (validation_errors()) {
                $this->session->set_flashdata('error', validation_errors());
            }
            // Jika validasi gagal, tampilkan halaman ubah password dengan error
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar');
            $this->load->view('templates/topbar');
            $this->load->view('attendance/change_password/index', $data);
            $this->load->view('templates/footer');
        } else {
            $username = $this->session->userdata('username');
            $current_password = $this->input->post('current_password');
            $new_password = $this->input->post('new_password');

            // Query untuk mendapatkan hash password saat ini dari database
            $this->db->select('password');
            $this->db->where('username', $username);
            $user = $this->db->get('user_accounts')->row();

            // Validasi password aktif (password lama) harus cocok dengan password di database
            if ($user && password_verify($current_password, $user->password)) {
                // password baru tidak sama dengan password aktif (lama)
                if (password_verify($new_password, $user->password)) {
                    $this->session->set_flashdata('error', 'Password baru tidak boleh sama dengan password aktif.');
                    redirect('attendance/change_password/index');
                } else {
                    // Hash password baru dan lakukan update
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $this->db->set('password', $hashed_password);
                    $this->db->where('username', $username);
                    $this->db->update('user_accounts');

                    $this->session->set_flashdata('success', 'Password berhasil diubah.');
                    redirect('attendance/change_password/index');
                }
            } else {
                $this->session->set_flashdata('error', 'Password aktif tidak sesuai.');
                redirect('attendance/change_password/index');
            }
        }
    }
}
