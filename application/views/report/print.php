<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

  <title>Laporan Kehadiran</title>
</head>

<body>
  <div class="container border">
    <div class="row mb-2">
      <div class="col">
        <h2 class="text-center">Laporan Kehadiran Pegawai</h2>
      </div>
    </div>
    <div class="row mb-3">
      <div class="col-6">
        <h1 class="h5">Kode Department : <?= $dept ?></h1>
      </div>
      <div class="col-6 text-right">
        <?php if ($start != null || $end != null) : ?>
          <h1 class="h5">From: <i><?= $start; ?></i> To: <i><?= $end; ?></i></h1>
        <?php else : ?>
          <h1 class="h5">All</h1>
        <?php endif; ?>
      </div>
    </div>
    <table class="table table-bordered" width="100%" cellspacing="0">
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
        <?php
        $i = 1;
        foreach ($attendance as $atd) :
        ?>
          <tr <?php if (date('l', strtotime($atd['attendance_date'])) == 'Saturday' || date('l', strtotime($atd['attendance_date'])) == 'Sunday') {
                echo "class ='bg-secondary text-white'";
              } ?>>
            <!-- Kolom 1 -->
            <td><?= $i++; ?></td>
            <?php
            // Jika hari Sabtu atau Minggu
            if (date('l', strtotime($atd['attendance_date'])) == 'Saturday' || date('l', strtotime($atd['attendance_date'])) == 'Sunday') : ?>
              <!-- Kolom 2 -->
              <td colspan="8" class="text-center">OFF</td>
            <?php else : ?>
              <!-- Kolom 2 (Date) -->
              <td><?= date('l, d F Y', strtotime($atd['attendance_date'])); ?></td>

              <!-- Kolom 3 (Nama) -->
              <td><?= $atd['employee_name']; ?></td>

              <!-- Kolom 4 (Shift) -->
              <td><?= $atd['shift_id']; ?></td>

              <!-- Kolom 5 (Check In) -->
              <td><?= $atd['in_time'] ? date('H:i:s', strtotime($atd['in_time'])) : 'Belum check in'; ?></td>

              <!-- Kolom 6 (Catatan) -->
              <td><?= $atd['notes'] ?: 'Tidak ada catatan'; ?></td>

              <!-- Kolom 7 (Status Masuk) -->
              <td><?= $atd['in_status']; ?></td>

              <!-- Kolom 8 (Check Out) -->
              <td><?= $atd['out_time'] ? date('H:i:s', strtotime($atd['out_time'])) : "Belum check out"; ?></td>

              <!-- Kolom 9 (Status Keluar) -->
              <td><?= $atd['out_status'] ?: 'Belum check out'; ?></td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Optional JavaScript -->
  <script>
    window.print();
  </script>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
</body>

</html>