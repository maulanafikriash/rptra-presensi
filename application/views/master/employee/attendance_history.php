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

    <div class="d-flex justify-content-end mb-2">
        <a href="<?= base_url('report/print_pdf_attendance_history/' . $employee['employee_id'] . '?month=' . $month . '&year=' . $year); ?>"
            class="btn btn-danger btn-icon-split mr-2"
            target="_blank">
            <span class="icon text-white">
                <i class="fas fa-file-pdf"></i>
            </span>
            <span class="text">Cetak PDF</span>
        </a>
        <a href="<?= base_url('report/print_excel_attendance_history/' . $employee['employee_id'] . '?month=' . $month . '&year=' . $year); ?>"
            class="btn btn-success btn-icon-split">
            <span class="icon text-white">
                <i class="fas fa-file-excel"></i>
            </span>
            <span class="text">Cetak Excel</span>
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

                            if (isset($attendance[$dayCounter]) && $attendance[$dayCounter]['presence_status'] == 1) {

                                echo "<div class='d-flex justify-content-start align-items-center gap-2'>";
                                // Tampilkan ikon lokasi presensi masuk
                                $checkInLocationEmpty = empty($attendance[$dayCounter]['check_in_latitude']) || empty($attendance[$dayCounter]['check_in_longitude']);
                                if (!$checkInLocationEmpty) {
                                    echo "<a href='#' data-target='#mapModal' title='Lihat Lokasi Presensi Masuk' class='mx-1' onclick=\"showMap(" . ($attendance[$dayCounter]['check_in_latitude'] ?? 'null') . ", " . ($attendance[$dayCounter]['check_in_longitude'] ?? 'null') . ", '{$employee['employee_name']} - Check In')\">
                            <i class='fas fa-map-marker-alt text-success'></i>
                        </a>";
                                } else {
                                    echo "<a href='#' class='mx-1' onclick=\"showAlert('Presensi ini dilakukan melalui admin (via admin), sehingga lokasi presensi masuk tidak tersedia.');\">
                                        <i class='fas fa-map-marker-alt text-success'></i>
                                    </a>";
                                }

                                // Ambil waktu sekarang dan waktu shift berakhir
                                $current_time = date('H:i:s'); // Waktu sekarang
                                $shift_end_time = date('H:i:s', strtotime($att['end_time']));
                                $shift_end_plus_15 = date('H:i:s', strtotime('+15 minutes', strtotime($shift_end_time))); // Waktu akhir shift + 15 menit
                                // Ambil tanggal sekarang dan tanggal kehadiran
                                $today = date('Y-m-d');
                                $attendance_date = $attendance[$dayCounter]['date'] ? $attendance[$dayCounter]['date'] : null;

                                // Periksa apakah kehadiran adalah hari ini
                                if ($attendance_date === $today) {
                                    // Skenario 1: Waktu sekarang sebelum akhir shift
                                    if ($current_time < $shift_end_time) {
                                        echo "<a href='#' class='mx-1' onclick=\"showAlert('Waktu shift belum berakhir, sehingga pegawai belum dapat melakukan presensi keluar.');\">
            <i class='fas fa-map-marker-alt text-danger'></i>
        </a>";
                                    }
                                    // Skenario 2: Waktu sekarang sudah lewat akhir shift tetapi belum melakukan presensi keluar, dan masih dalam 15 menit
                                    elseif ($current_time >= $shift_end_time && $current_time < $shift_end_plus_15 && empty($attendance[$dayCounter]['check_out_latitude']) && empty($attendance[$dayCounter]['check_out_longitude'])) {
                                        echo "<a href='#' class='mx-1' onclick=\"showAlert('Pegawai belum melakukan presensi keluar, namun masih dalam waktu 15 menit setelah shift berakhir.');\">
            <i class='fas fa-map-marker-alt text-danger'></i>
        </a>";
                                    }
                                    // Skenario 3: Waktu sudah melewati 15 menit setelah shift berakhir, tetapi pegawai belum presensi keluar
                                    elseif ($current_time >= $shift_end_plus_15 && empty($attendance[$dayCounter]['check_out_latitude']) && empty($attendance[$dayCounter]['check_out_longitude'])) {
                                        echo "<a href='#' class='mx-1' onclick=\"showAlert('Pegawai tidak melakukan presensi keluar, sehingga otomatis tercatat pada waktu 15 menit setelah shift berakhir.');\">
            <i class='fas fa-map-marker-alt text-danger'></i>
        </a>";
                                    }
                                    // Skenario 4: Presensi keluar sudah dilakukan
                                    elseif (!empty($attendance[$dayCounter]['check_out_latitude']) && !empty($attendance[$dayCounter]['check_out_longitude'])) {
                                        echo "<a href='#' data-target='#mapModal' title='Lihat Lokasi Presensi Keluar' class='mx-1' onclick=\"showMap(" . ($attendance[$dayCounter]['check_out_latitude'] ?? 'null') . ", " . ($attendance[$dayCounter]['check_out_longitude'] ?? 'null') . ", '{$employee['employee_name']} - Check Out');\">
            <i class='fas fa-map-marker-alt text-danger'></i>
        </a>";
                                    }
                                } else {
                                    // Skenario berlaku jika bukan hari ini (misalnya untuk hari sebelumnya)
                                    // Skenario 1: Jika pegawai belum melakukan presensi keluar, otomatis tercatat pada waktu 15 menit setelah shift berakhir
                                    if (empty($attendance[$dayCounter]['check_out_latitude']) && empty($attendance[$dayCounter]['check_out_longitude'])) {
                                        echo "<a href='#' class='mx-1' onclick=\"showAlert('Pegawai tidak melakukan presensi keluar, sehingga otomatis tercatat pada waktu 15 menit setelah shift berakhir.');\">
            <i class='fas fa-map-marker-alt text-danger'></i>
        </a>";
                                    } else {
                                        echo "<a href='#' data-target='#mapModal' title='Lihat Lokasi Presensi Keluar' class='mx-1' onclick=\"showMap(" . ($attendance[$dayCounter]['check_out_latitude'] ?? 'null') . ", " . ($attendance[$dayCounter]['check_out_longitude'] ?? 'null') . ", '{$employee['employee_name']} - Check Out');\">
            <i class='fas fa-map-marker-alt text-danger'></i>
        </a>";
                                    }
                                }
                            }
                            echo "</div>";
                        }

                        // Cek apakah hari ini hari Minggu
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
                                        echo "<span class='badge badge-warning'>Izin</span>";
                                        break;
                                    case 3:
                                        echo "<span class='badge badge-warning'>Sakit</span>";
                                        break;
                                    case 4:
                                        echo "<span class='badge badge-dark'>Cuti</span>";
                                        break;
                                    case 5:
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