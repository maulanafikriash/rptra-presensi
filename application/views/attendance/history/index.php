<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <!-- Form Filter Bulan dan Tahun -->
    <form action="" method="get" class="mb-3">
        <select name="month" class="form-control d-inline w-auto">
            <?php
            $bulanIndonesia = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember'
            ];

            foreach ($bulanIndonesia as $m => $bulan) {
                $selected = ($m == $month) ? 'selected' : '';
                echo "<option value='$m' $selected>$bulan</option>";
            }
            ?>
        </select>

        <select name="year" class="form-control d-inline w-auto">
            <?php
            $currentYear = date('Y');
            for ($y = $currentYear - 5; $y <= $currentYear + 5; $y++) {
                $selected = ($y == $year) ? 'selected' : '';
                echo "<option value='$y' $selected>$y</option>";
            }
            ?>
        </select>

        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <!-- Tabel Riwayat Presensi -->
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Hari</th>
                    <th>Tanggal</th>
                    <th>Status Presensi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Menghitung jumlah hari dalam bulan yang dipilih
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                $attendanceData = [];

                // Simpan status presensi berdasarkan tanggal
                foreach ($attendance as $att) {
                    // Pastikan kita hanya menyimpan data jika ada tanggal dan presence_status
                    if (isset($att['date']) && isset($att['presence_status'])) {
                        $attendanceData[$att['date']] = $att['presence_status'];
                    }
                }

                // Mendapatkan tanggal hari ini
                $today = date('Y-m-d');

                // Loop untuk menampilkan tanggal dan status presensi
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = date('Y-m-d', strtotime("$year-$month-$day"));
                    $dayName = date('l', strtotime($date)); // Nama hari

                    // Mengatur nama hari dalam bahasa Indonesia
                    $hariIndonesia = [
                        'Sunday' => 'Minggu',
                        'Monday' => 'Senin',
                        'Tuesday' => 'Selasa',
                        'Wednesday' => 'Rabu',
                        'Thursday' => 'Kamis',
                        'Friday' => 'Jumat',
                        'Saturday' => 'Sabtu',
                    ];
                    $dayName = $hariIndonesia[$dayName];

                    // Status presensi default
                    $status = 'Tidak Hadir'; // Set status default
                    $statusClass = 'danger'; // Set kelas default untuk "Tidak Hadir"

                    // Cek apakah hari ini adalah hari Minggu
                    if (date('w', strtotime($date)) == 0) { // 0 = Minggu
                        $status = 'Libur';
                        $statusClass = 'primary';
                    } else if ($date > $today) { // Tanggal belum terjadi
                        $status = 'Tidak Ada Data';
                        $statusClass = 'secondary';
                    } else { // Jika tanggal sudah terjadi
                        // Cek status presensi untuk tanggal ini
                        if (isset($attendance[$date])) {
                            switch ($attendance[$date]) {
                                case 1:
                                    $status = 'Hadir';
                                    $statusClass = 'success';
                                    break;
                                case 2:
                                    $status = 'Libur';
                                    $statusClass = 'primary';
                                    break;
                            }
                        }
                    }

                ?>
                    <tr>
                        <td><?= $dayName; ?></td>
                        <td><?= date('d-m-Y', strtotime($date)); ?></td>
                        <td><span class="badge badge-<?= $statusClass; ?>"><?= $status; ?></span></td>
                    </tr>
                <?php } ?>
            </tbody>



        </table>
    </div>
</div>