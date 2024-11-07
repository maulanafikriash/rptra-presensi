<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>
    <div class="row">
        <div class="col-lg-3">
            <a href="<?= base_url('master/detail_employee/') . $employee['employee_id'] ?>" class="btn btn-secondary btn-icon-split mb-4">
                <span class="icon text-white">
                    <i class="fas fa-chevron-left"></i>
                </span>
                <span class="text">Kembali</span>
            </a>
        </div>
        <div class="col-lg-5 offset-lg-4">
            <?= $this->session->flashdata('message'); ?>
        </div>
    </div>

    <!-- Detail Pegawai -->
    <div class="mb-5">
        <h5>Nama Pegawai : <?= $employee['employee_name']; ?></h5>
        <h6>Shift : <?= $shift_current['shift_id'] . ' = ' . date('H:i', strtotime($shift_current['start_time'])) . ' - ' . date('H:i', strtotime($shift_current['end_time'])); ?></h6>
        <h6>Department : <?= $department_current['department_name']; ?></h6>
    </div>

    <!-- Filter Bulan dan Tahun -->
    <form action="" method="get" class="form-inline mb-3">
        <div class="form-group mr-2">
            <label for="month" class="mr-2">Bulan:</label>
            <select name="month" id="month" class="form-control">
                <?php for ($m = 1; $m <= 12; $m++) : ?>
                    <option value="<?= $m; ?>" <?= ($m == $month) ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)); ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="form-group mr-2">
            <label for="year" class="mr-2">Tahun:</label>
            <select name="year" id="year" class="form-control">
                <?php for ($y = date('Y') - 5; $y <= date('Y') + 5; $y++) : ?>
                    <option value="<?= $y; ?>" <?= ($y == $year) ? 'selected' : '' ?>><?= $y; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>


    <!-- Tombol Cetak PDF -->
    <div class="text-right mb-2">
        <a href="<?= base_url('report/print_attendance_history/' . $employee['employee_id'] . '?month=' . $month . '&year=' . $year); ?>" class="btn btn-danger btn-icon-split" target="_blank">
            <span class="icon text-white">
                <i class="fas fa-file-pdf"></i>
            </span>
            <span class="text">Cetak PDF</span>
        </a>
    </div>

    <!-- Tabel Kalender -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Minggu</th>
                <th>Senin</th>
                <th>Selasa</th>
                <th>Rabu</th>
                <th>Kamis</th>
                <th>Jumat</th>
                <th>Sabtu</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);  // Jumlah hari dalam bulan yang dipilih
            $firstDayOfMonth = date('w', strtotime("$year-$month-01"));      // Hari pertama dalam bulan tersebut (0 = Minggu, 6 = Sabtu)
            $currentDate = date('Y-m-d');

            foreach ($attendance as $att) {
                if (isset($att['date']) && isset($att['presence_status'])) {
                    // Ambil hari dari tanggal
                    $day = date('j', strtotime($att['date']));
                    // Simpan status presensi berdasarkan hari
                    $attendanceData[$day] = $att['presence_status'];
                }
            }

            // Variabel untuk menghitung hari dalam bulan
            $dayCounter = 0;

            // Loop untuk menampilkan kalender (maksimal 6 minggu)
            for ($i = 0; $i < 6; $i++) {
                echo "<tr>";
                for ($j = 0; $j < 7; $j++) {
                    // Cek apakah hari ini sudah melewati hari pertama bulan yang dipilih
                    if ($i === 0 && $j < $firstDayOfMonth) {
                        echo "<td></td>";  // Kosongkan kolom sebelum hari pertama bulan
                    } elseif (++$dayCounter <= $daysInMonth) {
                        $currentLoopDate = date('Y-m-d', strtotime("$year-$month-$dayCounter"));

                        // Tampilkan tanggal dan status presensi
                        echo "<td><strong  class='h5'>$dayCounter</strong><br>";

                        // Tampilkan ikon edit dan lokasi hanya jika tanggal saat ini atau sebelumnya
                    if ($currentLoopDate <= $currentDate) {
                        echo "<a href='#' class='float-right' data-target='#editAttendanceModal' data-toggle='modal' data-day='$dayCounter'><i class='fas fa-edit'></i></a><br>";

                        echo "<div class='d-flex flex-column align-items-start'>";
                        // Tampilkan ikon lokasi presensi masuk
                        echo "<a href='#' data-target='#mapModal' title='Lihat Lokasi Presensi Masuk' onclick=\"showMap(" . ($attendance[$dayCounter]['check_in_latitude'] ?? 'null') . ", " . ($attendance[$dayCounter]['check_in_longitude'] ?? 'null') . ", '{$employee['employee_name']} - Check In')\">
                            <i class='fas fa-map-marker-alt text-success'></i>
                        </a>";

                        // Tampilkan ikon lokasi presensi keluar
                        echo "<a href='#' data-target='#mapModal' title='Lihat Lokasi Presensi Keluar' onclick=\"showMap(" . ($attendance[$dayCounter]['check_out_latitude'] ?? 'null') . ", " . ($attendance[$dayCounter]['check_out_longitude'] ?? 'null') . ", '{$employee['employee_name']} - Check Out')\">
                            <i class='fas fa-map-marker-alt text-danger'></i>
                        </a>";
                        echo "</div>";
                    }


                        // Cek apakah hari ini adalah hari Minggu
                        if (date('w', strtotime($currentLoopDate)) == 0) {
                            echo "<span class='badge badge-primary'>Libur</span>";
                        } else {
                            if ($currentLoopDate > $currentDate) {
                                echo "<span class='badge badge-secondary'>Tidak Ada Data</span>";
                            } elseif (isset($attendance[$dayCounter])) {
                                switch ($attendance[$dayCounter]['presence_status']) {
                                    case 1:
                                        echo "<span class='badge badge-success'>Hadir</span>";
                                        break;
                                    case 0:
                                        echo "<span class='badge badge-danger'>Tidak Hadir</span>";
                                        break;
                                    case 2:
                                        echo "<span class='badge badge-primary'>Libur</span>";
                                        break;
                                    default:
                                        echo "<span class='badge badge-secondary'>Tidak Ada Data</span>";
                                        break;
                                }
                            } else {
                                echo "<span class='badge badge-danger'>Tidak Hadir</span>";
                            }
                        }

                        echo "</td>";
                    } else {
                        echo "<td></td>";
                    }
                }
                echo "</tr>";
                if ($dayCounter >= $daysInMonth) {
                    break;
                }
            }
            ?>
        </tbody>
    </table>
    <div class="mt-4">
        <h5>Keterangan:</h5>
        <ul class="list-unstyled">
            <li><i class="fas fa-map-marker-alt text-success"></i> <strong>Ikon Lokasi Hijau:</strong> Lokasi Presensi Masuk</li>
            <li><i class="fas fa-map-marker-alt text-danger"></i> <strong>Ikon Lokasi Merah:</strong> Lokasi Presensi Keluar</li>
            <li><i class="fas fa-edit"></i> <strong>Ikon Edit:</strong> Edit Status Presensi</li>
        </ul>
    </div>
</div>