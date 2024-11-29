<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
</head>

<body>
  <div class="container">
    <?php
    function formatTanggalIndonesia($tanggal)
    {
      $fmt = new IntlDateFormatter(
        'id_ID',
        IntlDateFormatter::FULL,
        IntlDateFormatter::NONE,
        'Asia/Jakarta',
        IntlDateFormatter::GREGORIAN
      );
      $fmt->setPattern('EEEE, dd MMMM yyyy');
      return $fmt->format(new DateTime($tanggal));
    }

    function getCheckoutTime($shift_end_time)
    {
      $shift_end = new DateTime($shift_end_time);
      $shift_end->modify('+15 minutes');
      return $shift_end->format('H:i:s');
    }
    ?>
    <div class="header-section">
      <h2>Laporan Kehadiran Pegawai</h2>
    </div>
    <div class="department-info">
      <p><strong>Department :</strong> <?= $dept_name ?></p>
      <p><strong>ID Department :</strong> <?= $dept ?></p>
    </div>
    <div class="date-range">
      <?php if ($start != null || $end != null) : ?>
        <p><strong>Dari tanggal:</strong> <?= formatTanggalIndonesia($start); ?> <strong>sampai</strong> <?= formatTanggalIndonesia($end); ?></p>
      <?php else : ?>
        <p>Semua tanggal</p>
      <?php endif; ?>
    </div>

    <table border="1" cellpadding="5" cellspacing="0">
      <thead>
        <tr>
          <th>No</th>
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
        <?php $i = 1; ?>
        <?php foreach ($attendance as $date => $attendances) : // Looping berdasarkan tanggal 
        ?>
          <?php foreach ($attendances as $index => $atd) : ?>
            <tr>
              <?php if ($index === 0) : ?>
                <td rowspan="<?= count($attendances); ?>"><?= $i++; ?></td>
                <td rowspan="<?= count($attendances); ?>"><?= formatTanggalIndonesia($date); ?></td>
              <?php endif; ?>
              <td><?= $atd['employee_name']; ?></td>
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
              <td><?= $atd['in_time'] ? date('H:i:s', strtotime($atd['in_time'])) : 'Belum check in'; ?></td>
              <td><?= $atd['in_status']; ?></td>

              <?php
              // Variabel waktu
              $current_time = date('H:i:s');
              $shift_end_time = date('H:i:s', strtotime($atd['shift_end']));
              $shift_end_plus_15 = date('H:i:s', strtotime('+15 minutes', strtotime($shift_end_time)));

              // Cek apakah attendance_date sama dengan hari ini
              $attendance_date = strtotime($date);
              $today = strtotime(date('Y-m-d'));

              // Skenario hanya berlaku jika attendance_date adalah hari ini
              if ($attendance_date === $today) {
                // Skenario 1: Waktu sekarang kurang dari akhir shift
                if ($current_time < $shift_end_time) {
                  $checkout = '-';
                  $out_status = 'Belum waktunya';

                  // Skenario 2: Waktu sekarang melewati akhir shift, tapi pegawai belum check out
                } elseif ($current_time >= $shift_end_time && $current_time < $shift_end_plus_15 && empty($atd['out_time'])) {
                  $checkout = '-';
                  $out_status = 'Belum check out';

                  // Skenario 3: Waktu sekarang melewati akhir shift +15 menit, tapi pegawai belum check out
                } elseif ($current_time >= $shift_end_plus_15 && empty($atd['out_time'])) {
                  $checkout = $shift_end_plus_15;
                  $out_status = 'Otomatis';

                  // Skenario 4: Pegawai sudah check out
                } else {
                  $checkout = $atd['out_time'] ? date('H:i:s', strtotime($atd['out_time'])) : '-';
                  $out_status = $atd['out_status'] ?: '-';
                }
              } else {
                // Skenario 3 berlaku untuk setiap waktu tanggal dan tahun
                // Skenario 3: Waktu sekarang melewati akhir shift +15 menit, tapi pegawai belum check out
                if (empty($atd['out_time'])) {
                  $checkout = $shift_end_plus_15;
                  $out_status = 'Otomatis';
                } else {
                  $checkout = $atd['out_time'] ? date('H:i:s', strtotime($atd['out_time'])) : '-';
                  $out_status = $atd['out_status'] ?: '-';
                }
              }
              ?>
              <td><?= $checkout; ?></td>
              <td><?= $out_status; ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>

</html>