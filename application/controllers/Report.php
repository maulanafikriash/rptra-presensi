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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
      // Ambil data presensi dari Public_model
      $attendance = $this->Public_model->get_attendance($start, $end, $dept);

      // Ambil data shift (start_time, end_time) untuk digunakan di view
      $this->db->select('attendance.*, shift.end_time');
      $this->db->from('attendance');
      $this->db->join('shift', 'attendance.shift_id = shift.shift_id', 'left');

      if ($dept) {
        $this->db->where('attendance.department_id', $dept);
      }
      $this->db->where('attendance.attendance_date >=', $start);
      $this->db->where('attendance.attendance_date <=', $end);

      // Ambil data presensi dengan shift
      $attendance_with_shift = $this->db->get()->result_array();

      // Gabungkan data presensi dengan data shift
      foreach ($attendance as &$atd) {
        foreach ($attendance_with_shift as $shift_data) {
          if ($shift_data['attendance_id'] == $atd['attendance_id']) {
            $atd['end_time'] = $shift_data['end_time'];
            break;
          }
        }
      }

      return $attendance;
    }
  }

  public function print_Pdf_AttendanceByDepartment($start, $end, $dept)
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
      'format' => 'A4-L',
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

  public function print_Excel_AttendanceByDepartment($start, $end, $dept)
  {
    $attendance = $this->Public_model->get_attendance($start, $end, $dept);

    // Mengambil nama departemen
    $department = $this->db->get_where('department', ['department_id' => $dept])->row_array();
    $dept_name = $department['department_name'];

    // Mengambil data shift
    $shift_data = $this->db->get('shift')->result_array();

    // Kelompokkan data berdasarkan tanggal
    $grouped_attendance = [];
    foreach ($attendance as $atd) {
      // Membuat objek DateTime untuk tanggal
      $dateObj = new DateTime($atd['attendance_date']);

      // Format tanggal dengan menggunakan bahasa Indonesia
      $date = $dateObj->format('l, d F Y'); // Format: Hari, Tanggal Bulan Tahun

      $monthNames = [
        'January' => 'Januari',
        'February' => 'Februari',
        'March' => 'Maret',
        'April' => 'April',
        'May' => 'Mei',
        'June' => 'Juni',
        'July' => 'Juli',
        'August' => 'Agustus',
        'September' => 'September',
        'October' => 'Oktober',
        'November' => 'November',
        'December' => 'Desember'
      ];
      $dayNames = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
      ];

      // Replace English month and day names with Indonesian
      $date = str_replace(array_keys($monthNames), array_values($monthNames), $date);
      $date = str_replace(array_keys($dayNames), array_values($dayNames), $date);

      // Simpan data berdasarkan tanggal
      $grouped_attendance[$date][] = $atd;
    }
    // Buat Spreadsheet baru
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set Header Excel
    $sheet->setCellValue('A1', 'Laporan Kehadiran Pegawai');
    $sheet->setCellValue('A2', 'Tanggal: ' . $start . ' - ' . $end);
    $sheet->setCellValue('A3', 'Department: ' . $dept_name);
    $sheet->setCellValue('A5', 'No');
    $sheet->setCellValue('B5', 'Tanggal');
    $sheet->setCellValue('C5', 'Nama');
    $sheet->setCellValue('D5', 'Shift');
    $sheet->setCellValue('E5', 'Check In');
    $sheet->setCellValue('F5', 'Status Masuk');
    $sheet->setCellValue('G5', 'Check Out');
    $sheet->setCellValue('H5', 'Status Keluar');

    // Menulis data ke dalam Excel
    $row = 6;
    $i = 1;
    foreach ($grouped_attendance as $date => $attendances) {
      foreach ($attendances as $atd) {
        // Proses Check Out dan Status Keluar berdasarkan waktu
        $checkout = $this->process_checkout($atd);
        $out_status = $this->process_out_status($atd);

        // Menulis data ke sel Excel
        $sheet->setCellValue('A' . $row, $i++);
        $sheet->setCellValue('B' . $row, $date);
        $sheet->setCellValue('C' . $row, $atd['employee_name']);
        $sheet->setCellValue('D' . $row, $this->get_shift_info($atd['shift_id'], $shift_data));
        $sheet->setCellValue('E' . $row, $atd['in_time']);
        $sheet->setCellValue('F' . $row, $atd['in_status']);
        $sheet->setCellValue('G' . $row, $checkout);
        $sheet->setCellValue('H' . $row, $out_status);
        $row++;
      }
    }

    // Set Header untuk download file Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Laporan_Kehadiran_Pegawai_' . date('Y-m-d_H-i-s') . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
  }

  private function process_checkout($atd)
  {
    // Ambil data shift dari database
    $shift_data = $this->db->get('shift')->result_array();

    // Variabel waktu
    $current_time = date('H:i:s'); // Waktu sekarang
    $attendance_date = strtotime($atd['attendance_date']);
    $today = strtotime(date('Y-m-d')); // Hari ini dalam format Y-m-d

    // Cari shift berdasarkan shift_id
    $shift_info = array_filter($shift_data, function ($shift) use ($atd) {
      return $shift['shift_id'] == $atd['shift_id'];
    });
    $shift_info = array_values($shift_info);
    if (!empty($shift_info)) {
      $shift = $shift_info[0];
      $shift_end_time = date('H:i:s', strtotime($shift['end_time'])); // Waktu akhir shift
      $shift_end_plus_15 = date('H:i:s', strtotime('+15 minutes', strtotime($shift_end_time))); // Waktu akhir shift + 15 menit
    } else {
      return '-';
    }

    // Skenario hanya berlaku jika attendance_date adalah hari ini
    if ($attendance_date === $today) {
      // Skenario 1: Waktu sekarang kurang dari akhir shift
      if ($current_time < $shift_end_time) {
        $checkout = '-';

        // Skenario 2: Waktu sekarang melewati akhir shift, tapi pegawai belum check out
      } elseif ($current_time >= $shift_end_time && $current_time < $shift_end_plus_15 && $atd['out_time'] === NULL) {
        $checkout = '-';

        // Skenario 3: Waktu sekarang melewati akhir shift +15 menit, tapi pegawai belum check out
      } elseif ($current_time >= $shift_end_plus_15 && $atd['out_time'] === NULL) {
        $checkout = $shift_end_plus_15;

        // Skenario 4: Pegawai sudah check out
      } else {
        $checkout = $atd['out_time'] ?: '-';
      }
    } else {
      // Skenario untuk tanggal selain hari ini
      // Skenario 3: Waktu sekarang melewati akhir shift +15 menit, tapi pegawai belum check out
      if ($atd['out_time'] === NULL) {
        $checkout = $shift_end_plus_15;
      } else {
        $checkout = $atd['out_time'] ?: '-';
      }
    }

    return $checkout;
  }

  private function process_out_status($atd)
  {
    // Ambil data shift dari database
    $shift_data = $this->db->get('shift')->result_array();

    // Variabel waktu
    $current_time = date('H:i:s'); // Waktu sekarang
    $attendance_date = strtotime($atd['attendance_date']);
    $today = strtotime(date('Y-m-d')); // Hari ini dalam format Y-m-d

    // Cari shift berdasarkan shift_id
    $shift_info = array_filter($shift_data, function ($shift) use ($atd) {
      return $shift['shift_id'] == $atd['shift_id'];
    });
    $shift_info = array_values($shift_info);
    if (!empty($shift_info)) {
      $shift = $shift_info[0];
      $shift_end_time = date('H:i:s', strtotime($shift['end_time'])); // Waktu akhir shift
      $shift_end_plus_15 = date('H:i:s', strtotime('+15 minutes', strtotime($shift_end_time))); // Waktu akhir shift + 15 menit
    } else {
      return '-';
    }

    // Skenario hanya berlaku jika attendance_date adalah hari ini
    if ($attendance_date === $today) {
      // Skenario 1: Waktu sekarang kurang dari akhir shift
      if ($current_time < $shift_end_time) {
        $out_status = 'Belum waktunya';

        // Skenario 2: Waktu sekarang melewati akhir shift, tapi pegawai belum check out
      } elseif ($current_time >= $shift_end_time && $current_time < $shift_end_plus_15 && $atd['out_time'] === NULL) {
        $out_status = 'Belum check out';

        // Skenario 3: Waktu sekarang melewati akhir shift +15 menit, tapi pegawai belum check out
      } elseif ($current_time >= $shift_end_plus_15 && $atd['out_time'] === NULL) {
        $out_status = 'Otomatis';

        // Skenario 4: Pegawai sudah check out
      } else {
        $out_status = $atd['out_status'] ?: '-';
      }
    } else {
      // Skenario untuk tanggal selain hari ini
      // Skenario 3: Waktu sekarang melewati akhir shift +15 menit, tapi pegawai belum check out
      if ($atd['out_time'] === NULL) {
        $out_status = 'Otomatis';
      } else {
        $out_status = $atd['out_status'] ?: '-';
      }
    }

    return $out_status;
  }

  private function get_shift_info($shift_id, $shift_data)
  {
    foreach ($shift_data as $shift) {
      if ($shift['shift_id'] == $shift_id) {
        return $shift['shift_id'] . " = " . date('H:i', strtotime($shift['start_time'])) . " - " . date('H:i', strtotime($shift['end_time']));
      }
    }
    return "Shift Tidak Ditemukan";
  }

  public function print_pdf_attendance_history($employee_id)
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
          $attendance[$date] = 'Izin';
          break;
        case 3:
          $attendance[$date] = 'Sakit';
          break;
        case 4:
          $attendance[$date] = 'Cuti';
          break;
        case 5:
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

  public function print_excel_attendance_history($employee_id)
  {
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

    // array dengan semua tanggal di bulan yang dipilih
    $attendance = [];
    for ($day = 1; $day <= $daysInMonth; $day++) {
      $date = sprintf('%04d-%02d-%02d', $year, $month, $day);

      // status berdasarkan hari dan tanggal
      if (date('w', strtotime($date)) == 0) { // Hari Minggu
        $attendance[$date] = 'Libur';
      } elseif ($date <= $currentDate) {
        $attendance[$date] = 'Tidak Hadir'; // Set default untuk hari yang sudah lewat
      } else {
        $attendance[$date] = 'Tidak Ada Data'; // Set default untuk hari yang belum tiba
      }
    }

    // array dengan data yang ada
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
          $attendance[$date] = 'Izin';
          break;
        case 3:
          $attendance[$date] = 'Sakit';
          break;
        case 4:
          $attendance[$date] = 'Cuti';
          break;
        case 5:
          $attendance[$date] = 'Libur';
          break;
        default:
          $attendance[$date] = 'Tidak Ada Data';
      }
    }

    // Buat file Excel
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set header file Excel
    $sheet->setCellValue('A1', 'Riwayat Presensi Pegawai');
    $sheet->setCellValue('A2', 'Nama Pegawai: ' . $employee['employee_name']);
    $sheet->setCellValue('A3', 'Bulan: ' . date('F', mktime(0, 0, 0, $month, 1)) . " $year");

    // Set header tabel
    $sheet->setCellValue('A5', 'Tanggal');
    $sheet->setCellValue('B5', 'Status Presensi');

    // Isi data tabel
    $row = 6;
    foreach ($attendance as $date => $status) {
      $sheet->setCellValue('A' . $row, date('d-m-Y', strtotime($date)));
      $sheet->setCellValue('B' . $row, $status);
      $row++;
    }

    $filename = "Riwayat_Presensi_{$employee['employee_name']}_{$month}_{$year}.xlsx";

    // Konfigurasi header untuk download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
  }
}
