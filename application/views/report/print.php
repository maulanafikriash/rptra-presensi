<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
</head>

<body>
  <div class="container">
    <div class="header-section">
      <h2>Laporan Kehadiran Pegawai</h2>
    </div>
    <div class="department-info">
      <p><strong>Department :</strong> <?= $dept_name ?></p>
      <p><strong>Kode Department :</strong> <?= $dept ?></p>
    </div>
    <div class="date-range">
      <?php if ($start != null || $end != null) : ?>
        <p><strong>Dari tanggal:</strong> <?= date('j F Y', strtotime($start)); ?> <strong>sampai</strong> <?= date('j F Y', strtotime($end)); ?></p>
      <?php else : ?>
        <p>Semua tanggal</p>
      <?php endif; ?>
    </div>

    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Tanggal</th>
          <th>Nama</th>
          <th>Shift</th>
          <th>Check In</th>
          <th>Catatan</th>
          <th>Status Masuk</th>
          <th>Check Out</th>
          <th>Status Keluar</th>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1;
        foreach ($attendance as $date => $attendances) : // Looping berdasarkan tanggal 
        ?>
          <?php foreach ($attendances as $index => $atd) : ?>
            <tr>
              <?php if ($index === 0) :
              ?>
                <td rowspan="<?= count($attendances); ?>"><?= $i++; ?></td>
                <td rowspan="<?= count($attendances); ?>"><?= $date; ?></td>
              <?php endif; ?>
              <td><?= $atd['employee_name']; ?></td>
              <td><?= $atd['shift_id']; ?></td>
              <td><?= $atd['in_time'] ? date('H:i:s', strtotime($atd['in_time'])) : 'Belum check in'; ?></td>
              <td><?= $atd['notes'] ?: 'Tidak ada catatan'; ?></td>
              <td><?= $atd['in_status']; ?></td>
              <td><?= $atd['out_time'] ? date('H:i:s', strtotime($atd['out_time'])) : "Belum check out"; ?></td>
              <td><?= $atd['out_status'] ?: 'Belum check out'; ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>

</html>