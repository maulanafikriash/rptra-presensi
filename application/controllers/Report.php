<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property CI_Loader $load
 * @property CI_Session $session
 * @property CI_upload $upload
 * @property CI_DB $db
 * @property CI_Input $input
 * @property Public_model $Public_model
 * @property Admin_model $Admin_model
 */

use \Mpdf\Mpdf;

class Report extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->library('form_validation');
    $this->load->model('Public_model');
    $this->load->model('Admin_model');
  }

  public function index()
  {
    $d['title'] = 'Laporan Presensi';
    $d['account'] = $this->Admin_model->getAdmin($this->session->userdata['username']);
    $d['department'] = $this->db->get('department')->result_array();
    $d['start'] = $this->input->get('start');
    $d['end'] = $this->input->get('end');
    $d['dept_code'] = $this->input->get('dept');
    $d['attendance'] = $this->_attendanceDetails($d['start'], $d['end'], $d['dept_code']);
    $d['shift_data'] = $this->db->get('shift')->result_array();

    $this->load->view('templates/table_header', $d);
    $this->load->view('templates/sidebar');
    $this->load->view('templates/topbar');
    $this->load->view('report/index', $d);
    $this->load->view('templates/table_footer');
  }

  private function _attendanceDetails($start, $end, $dept)
  {
    if (!$start || !$end) {
      return false;
    } else {
      return $this->Public_model->get_attendance($start, $end, $dept);
    }
  }

  public function print($start, $end, $dept)
  {
    $d['start'] = $start;
    $d['end'] = $end;
    $attendance = $this->Public_model->get_attendance($start, $end, $dept);
    $d['dept'] = $dept;

    // Mengambil nama departemen
    $department = $this->db->get_where('department', ['department_id' => $dept])->row_array();
    $d['dept_name'] = $department['department_name'];

    // Mengambil data shift
    $shift_data = $this->db->get('shift')->result_array();
    $d['shift_data'] = $shift_data;

    // Kelompokkan data berdasarkan tanggal
    $grouped_attendance = [];
    foreach ($attendance as $atd) {
      $date = date('l, d F Y', strtotime($atd['attendance_date']));
      $grouped_attendance[$date][] = $atd; // Simpan data berdasarkan tanggal
    }

    $d['attendance'] = $grouped_attendance;

    // Load view ke dalam variabel
    $html = $this->load->view('report/print', $d, true);

    // Inisialisasi mPDF dengan orientasi landscape
    $mpdf = new \Mpdf\Mpdf([
      'mode' => 'utf-8',
      'format' => 'A4-L', // 'L' untuk landscape
      'default_font_size' => 12,
      'default_font' => 'Arial'
    ]);

    // Tambahkan Header dan Footer
    $mpdf->SetHeader('RPTRA Jakarta Timur');
    $mpdf->SetFooter('Dicetak pada: {DATE j-m-Y H:i:s}');

    // Load CSS dari file eksternal
    $css = file_get_contents(base_url('assets/css/report.css'));

    // Tambahkan CSS dan HTML ke mPDF
    $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
    $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

    // Output PDF
    $mpdf->Output('Laporan_Kehadiran_Pegawai.pdf', 'I');
  }

  public function print_attendance_history($employee_id)
  {
    // Ambil data pegawai
    $employee = $this->db->get_where('employee', ['employee_id' => $employee_id])->row_array();

    if (!$employee) {
      show_error('Employee not found', 404);
    }

    // Ambil parameter bulan dan tahun
    $month = $this->input->get('month') ?: date('m');
    $year = $this->input->get('year') ?: date('Y');

    // Ambil jumlah hari dalam bulan
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $currentDate = date('Y-m-d'); // Tanggal hari ini

    // Ambil data presensi sesuai bulan dan tahun
    $this->db->select('attendance_date AS date, presence_status');
    $this->db->from('attendance');
    $this->db->where('employee_id', $employee_id);
    $this->db->where('MONTH(attendance_date)', $month);
    $this->db->where('YEAR(attendance_date)', $year);
    $attendanceData = $this->db->get()->result_array();

    // Buat array dengan semua tanggal di bulan yang dipilih
    $attendance = [];
    for ($day = 1; $day <= $daysInMonth; $day++) {
      $date = sprintf('%04d-%02d-%02d', $year, $month, $day);

      // Tentukan status berdasarkan hari dan tanggal
      if (date('w', strtotime($date)) == 0) { // Hari Minggu
        $attendance[$date] = 'Libur';
      } elseif ($date <= $currentDate) {
        $attendance[$date] = 'Tidak Hadir'; // Set default untuk hari yang sudah lewat
      } else {
        $attendance[$date] = 'Tidak Ada Data'; // Set default untuk hari yang belum tiba
      }
    }

    // Isi array dengan data yang ada
    foreach ($attendanceData as $att) {
      $date = $att['date'];
      switch ($att['presence_status']) {
        case 1:
          $attendance[$date] = 'Hadir';
          break;
        case 0:
          $attendance[$date] = 'Tidak Hadir';
          break;
        case 2:
          $attendance[$date] = 'Libur';
          break;
        default:
          $attendance[$date] = 'Tidak Ada Data';
      }
    }

    $data['employee'] = $employee;
    $data['attendance'] = $attendance;
    $data['month'] = $month;
    $data['year'] = $year;

    // Load tampilan PDF
    $html = $this->load->view('report/print_attendance_history', $data, true);

    // Pengaturan mPDF
    $pdf = new \Mpdf\Mpdf();
    $pdf->SetHeader('RPTRA Jakarta Timur');
    $pdf->SetFooter('Dicetak pada: {DATE j-m-Y H:i:s}');
    $pdf->WriteHTML($html);
    $pdf->Output("Riwayat_Presensi_{$employee['employee_name']}_{$month}_{$year}.pdf", 'I');
  }
}
