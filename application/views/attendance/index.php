<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
  </div>

  <!-- Content Row -->
  <div class="row">
    <div class="col">
      <div class="row">
        <!-- Area Chart -->
        <div class="col-xl col-lg">
          <div class="card shadow mb-4" style="min-height: 543px">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-primary">Isi kehadiran Anda!</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">

              <!-- Menampilkan Pesan Flashdata -->
              <?php if ($this->session->flashdata('message')): ?>
                <div class="alert alert-dismissible fade show" role="alert">
                  <?= $this->session->flashdata('message'); ?>
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
              <?php endif; ?>

              <?php if ($weekends == true) : ?>
                <h1 class="text-center my-3">Terima kasih Untuk Minggu Ini!</h1>
                <h5 class="card-title text-center mb-4 px-4">Selamat beristirahat di akhir pekan <b>happy weekend</b></h5>
                <b>
                  <p class="text-center text-primary pt-3">Sampai jumpa di hari Senin!</p>
                </b>
              <?php else : ?>
                <?= form_open_multipart('attendance') ?>

                <!-- Bagian Shift dan Lokasi -->
                <div class="row mb-3">
                  <div class="col-lg-5">
                    <label for="work_shift" class="col-form-label">Work Shift</label>
                    <?php
                    $shift_display = 'Shift Not Available';
                    foreach ($shift as $sft) {
                      if ((int)$sft['shift_id'] == (int)$account['shift']) {
                        $shift_display = $sft['shift_id'] . ' = ' . $sft['start_time'] . ' - ' . $sft['end_time'];
                        $shift_id = $sft['shift_id'];
                        $start_time = $sft['start_time'];
                        $end_time = $sft['end_time'];
                        break;
                      }
                    }
                    ?>
                    <input class="form-control" type="text" placeholder="<?= $shift_display; ?>" value="<?= $shift_display; ?>" name="work_shift" readonly>
                    <input type="hidden" name="work_shift" value="<?= $shift_id; ?>">
                  </div>

                  <!-- Tombol untuk mengaktifkan lokasi -->
                  <div class="col-lg-5 offset-lg-1">
                    <label for="location" class="col-form-label">Aktifkan Lokasi Saat Ini</label>
                    <button type="button" class="btn btn-primary btn-block" id="activate-location-btn">Aktifkan Lokasi</button>

                    <!-- Menampilkan status lokasi dan menyimpan latitude/longitude -->
                    <p id="location-status" class="mt-2 text-muted text-center">Lokasi belum diaktifkan</p>
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                  </div>
                </div>

                <!-- Bagian Catatan -->
                <div class="row justify-content-center mb-3">
                  <div class="col-lg-6 text-center mt-4">
                    <label for="notes" class="float-left">Catatan (Opsional)</label>
                    <textarea maxlength="120" class="form-control mb-4" name="notes" id="notes" rows="3"></textarea>

                    <hr>
                    <div class="d-flex justify-content-around">
                      <!-- Tombol Status Presensi -->
                      <div class="text-center">
                        <button class="btn <?= $already_checked_in ? 'btn-success' : 'btn-danger' ?> btn-circle" style="font-size: 20px; width: 100px; height: 100px;" disabled>
                          <i class="fas <?= $already_checked_in ? 'fa-check' : 'fa-times' ?> fa-2x"></i>
                        </button>
                        <p class="font-weight-bold <?= $already_checked_in ? 'text-success' : 'text-danger' ?> pt-2"><?= $already_checked_in ? 'Hadir' : 'Tidak Hadir' ?></p>
                      </div>

                      <!-- Tombol Presensi Masuk/Keluar -->
                      <div class="text-center">
                        <?php if (!$already_checked_in): ?>
                          <!-- Tombol Presensi Masuk -->
                          <button type="submit" name="check_in" value="1" class="btn btn-info btn-circle" id="check-in-btn" style="font-size: 20px; width: 100px; height: 100px;"
                            <?php if ($shift_status == 'belum mulai' || $shift_status == 'sudah selesai') echo 'disabled'; ?> disabled>
                            <i class="fas fa-fw fa-sign-in-alt fa-2x"></i>
                          </button>

                          <?php if ($shift_status == 'belum mulai'): ?>
                            <p class="font-weight-bold text-primary pt-2">Shift Belum Mulai</p>
                          <?php elseif ($shift_status == 'sudah selesai'): ?>
                            <p class="text-danger pt-2" style="font-size: small;">Anda terlambat <br> Waktu Shift sudah selesai</p>
                          <?php else: ?>
                            <p class="font-weight-bold text-primary pt-2" id="check-in-status">Presensi Masuk!</p>
                          <?php endif; ?>

                        <?php else: ?>
                          <!-- Tombol Presensi Keluar -->
                          <button type="submit" name="check_out" value="1" class="btn btn-danger btn-circle" id="check-out-btn" style="font-size: 20px; width: 100px; height: 100px;"
                            <?php if ($already_checked_out || $shift_status != 'sudah selesai') echo 'disabled'; ?> disabled>
                            <i class="fas fa-fw fa-sign-out-alt fa-2x"></i>
                          </button>

                          <?php if ($already_checked_out): ?>
                            <p class="text-danger pt-2">Sudah Presensi Keluar</p>
                          <?php elseif ($shift_status == 'belum mulai'): ?>
                            <p class="text-warning pt-2">Shift Belum Mulai</p>
                          <?php elseif ($shift_status != 'sudah selesai'): ?>
                            <p class="text-danger pt-2" style="font-size: small;">Presensi keluar akan dibuka <br> jika waktu shift sudah selesai.</p>
                          <?php else: ?>
                            <p class="font-weight-bold text-danger pt-2" id="check-out-status">Presensi Keluar!</p>
                          <?php endif; ?>
                        <?php endif; ?>
                      </div>
                    </div>

                  </div>
                </div>

              <?php endif; ?>

              <?= form_close() ?>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- End of Main Content -->

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const shiftStartTime = "<?= $start_time; ?>";
    const shiftEndTime = "<?= $end_time; ?>";
    const activateLocationBtn = document.getElementById("activate-location-btn");
    const checkInBtn = document.getElementById("check-in-btn");
    const checkOutBtn = document.getElementById("check-out-btn");

    // Status apakah sudah check-out dari server
    const alreadyCheckedOut = <?= json_encode($already_checked_out); ?>;

    // Jika sudah check-out, tombol aktifkan lokasi dinonaktifkan
    if (alreadyCheckedOut) {
      activateLocationBtn.disabled = true;
    }

    function convertTo24HourTime(timeStr) {
      const [hour, minute] = timeStr.split(':');
      const date = new Date();
      date.setHours(hour);
      date.setMinutes(minute);
      date.setSeconds(0);
      return date;
    }

    const now = new Date();
    const startShift = convertTo24HourTime(shiftStartTime);
    const endShift = convertTo24HourTime(shiftEndTime);

    // Aktivasi tombol lokasi jika waktu shift dimulai
    if (!alreadyCheckedOut && now >= startShift) {
      activateLocationBtn.disabled = false;
    }

    // Aktivasi tombol presensi keluar jika waktu shift selesai dan lokasi sudah aktif
    activateLocationBtn.addEventListener("click", function() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          function(position) {
            document.getElementById("latitude").value = position.coords.latitude;
            document.getElementById("longitude").value = position.coords.longitude;
            document.getElementById("location-status").textContent = "Lokasi berhasil diaktifkan!";
            alert("Berhasil mengaktifkan lokasi");

            if (now >= startShift && now <= endShift) {
              checkInBtn.disabled = false;
            }
            if (now > endShift) {
              checkOutBtn.disabled = false;
            }
          },
          function() {
            alert("Gagal mengaktifkan lokasi. Harap izinkan akses lokasi di browser Anda.");
          }
        );
      } else {
        alert("Geolocation tidak didukung oleh browser ini.");
      }
    });
  });
</script>