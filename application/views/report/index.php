<!-- Begin Page Content -->
<div class="container-fluid">
  <!-- Page Heading -->
  <div class="row">
    <div class="col-lg">
      <h1 class="h1 mb-4 text-gray-900"><?= $title; ?></h1>
      <a href="<?= base_url('admin'); ?>" class="btn btn-secondary btn-icon-split mb-4">
        <span class="icon text-white">
          <i class="fas fa-chevron-left"></i>
        </span>
        <span class="text">Kembali</span>
      </a>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-7 ml-auto mb-3 float-right">
      <form action="" method="GET">
        <div class="row">
          <div class="col-3 offset-lg-1">
            <input type="date" name="start" class="form-control">
            <?= form_error('start', '<small class="text-danger pl-3">', '</small>') ?>
          </div>
          <div class="col-3">
            <input type="date" name="end" class="form-control">
            <?= form_error('end', '<small class="text-danger pl-3">', '</small>') ?>
          </div>
          <div class="col-3">
            <select class="form-control" name="dept">
              <option disabled>Department</option>
              <?php foreach ($department as $d) : ?>
                <option value="<?= $d['department_id']; ?>"><?= $d['department_name']; ?></option>
              <?php endforeach; ?>
            </select>
            <?= form_error('dept', '<small class="text-danger pl-3">', '</small>') ?>
          </div>
          <div class="col-2 d-flex justify-content-end">
            <button type="submit" class="btn btn-success btn-fill" style="width: 100px; padding: 5px 10px;">Tampilkan</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!-- End of row show -->
  <?php if (!$attendance) : ?>
    <h4>Tidak Ada Data, <br> Silakan Pilih Tanggal dan Department</h4>
  <?php else : ?>
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Data Kehadiran</h6>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead class="bg-primary text-white">
              <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>Nama</th>
                <th>Shift</th>
                <th>Check In</th>
                <th>Status Masuk</th>
                <th>Check Out</th>
                <th>Status Keluar</th>
              </tr>
            </thead>
            <tbody>
              <?php $i = 1;
              foreach ($attendance as $atd) :
                // Cek apakah attendance_date sama dengan hari ini
                $attendance_date = strtotime($atd['attendance_date']);
                $today = strtotime(date('Y-m-d')); // Hari ini dalam format Y-m-d
              ?>
                <tr>
                  <th><?= $i++ ?></th>
                  <td><?= $atd['attendance_date'] ?></td>
                  <td><?= $atd['employee_name'] ?></td>
                  <td>
                    <?php
                    // Mencari shift berdasarkan shift_id
                    $shift_info = array_filter($shift_data, function ($shift) use ($atd) {
                      return $shift['shift_id'] == $atd['shift_id'];
                    });
                    $shift_info = array_values($shift_info);
                    if (!empty($shift_info)) {
                      $shift = $shift_info[0];
                      echo $shift['shift_id'] . " = " . date('H:i', strtotime($shift['start_time'])) . " - " . date('H:i', strtotime($shift['end_time']));
                    } else {
                      echo "Shift Tidak Ditemukan";
                    }
                    ?>
                  </td>

                  <td><?= $atd['in_time'] ?></td>
                  <td><?= $atd['in_status']; ?></td>

                  <?php
                  // Variabel waktu
                  $current_time = date('H:i:s'); // Waktu sekarang
                  $shift_end_time = date('H:i:s', strtotime($atd['end_time'])); // Waktu akhir shift
                  $shift_end_plus_15 = date('H:i:s', strtotime('+15 minutes', strtotime($shift_end_time))); // Waktu akhir shift + 15 menit

                  // Skenario hanya berlaku jika attendance_date adalah hari ini
                  if ($attendance_date === $today) {
                    // Skenario 1: Waktu sekarang kurang dari akhir shift
                    if ($current_time < $shift_end_time) {
                      $checkout = '-';
                      $out_status = 'Belum waktunya';

                      // Skenario 2: Waktu sekarang melewati akhir shift, tapi pegawai belum check out
                    } elseif ($current_time >= $shift_end_time && $current_time < $shift_end_plus_15 && $atd['out_time'] === NULL) {
                      $checkout = '-';
                      $out_status = 'Belum check out';

                      // Skenario 3: Waktu sekarang melewati akhir shift +15 menit, tapi pegawai belum check out
                    } elseif ($current_time >= $shift_end_plus_15 && $atd['out_time'] === NULL) {
                      $checkout = $shift_end_plus_15;
                      $out_status = 'Otomatis';

                      // Skenario 4: Pegawai sudah check out
                    } else {
                      $checkout = $atd['out_time'] ?: '-';
                      $out_status = $atd['out_status'] ?: '-';
                    }
                  } else {
                    // Skenario 3 berlaku untuk setiap waktu tanggal dan tahun
                    // Skenario 3: Waktu sekarang melewati akhir shift +15 menit, tapi pegawai belum check out
                    if ($atd['out_time'] === NULL) {
                      $checkout = $shift_end_plus_15;
                      $out_status = 'Otomatis';
                    } else {
                      $checkout = $atd['out_time'] ?: '-';
                      $out_status = $atd['out_status'] ?: '-';
                    }
                  }
                  ?>
                  <td><?= $checkout; ?></td>
                  <td><?= $out_status; ?></td>
                </tr>
              <?php endforeach; ?>
              <!-- Tombol Cetak PDF -->
              <a href="<?= base_url('report/print_Pdf_AttendanceByDepartment/') . $start . '/' . $end . '/' . $dept_code ?>" target="_blank" class="btn btn-danger btn-sm ml-2 shadow-sm d-sm-inline-block float-right">
                <i class="fas fa-file-pdf"></i> Cetak PDF
              </a>
              <!-- Tombol Cetak Excel -->
              <a href="<?= base_url('report/print_Excel_AttendanceByDepartment/') . $start . '/' . $end . '/' . $dept_code ?>" class="btn btn-success btn-sm ml-4 shadow-sm d-sm-inline-block float-right">
                <i class="fas fa-file-excel"></i> Cetak Excel
              </a>
            </tbody>
          </table>
        </div>
        <div class="mt-4">
          <h5>Keterangan Kolom Status Masuk:</h5>
          <ul>
            <li><strong>Tepat Waktu</strong>: Pegawai melakukan presensi dalam rentang waktu 10 menit setelah waktu mulai shift.</li>
            <li><strong>Terlambat</strong>: Pegawai melakukan presensi setelah 10 menit dari waktu mulai shift.</li>
          </ul>
          <h5>Keterangan Kolom Status Keluar:</h5>
          <ul>
            <li><strong>Belum Waktunya</strong>: Waktu shift belum berakhir, sehingga pegawai belum dapat melakukan presensi keluar.</li>
            <li><strong>Belum Check Out</strong>: Waktu shift telah berakhir, namun pegawai belum melakukan presensi keluar.</li>
            <li><strong>Tepat Waktu</strong>: Pegawai melakukan presensi keluar dalam waktu 15 menit setelah shift berakhir.</li>
            <li><strong>Otomatis</strong>: Pegawai tidak melakukan presensi keluar, sehingga otomatis tercatat pada waktu 15 menit setelah shift berakhir.</li>
          </ul>
        </div>
      </div>
    </div>
  <?php endif; ?>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->