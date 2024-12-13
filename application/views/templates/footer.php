      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; RPTRA <?= date('Y'); ?> - Sistem Presensi Pegawai</span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

      </div>
      <!-- End of Content Wrapper -->

      </div>
      <!-- End of Page Wrapper -->

      <!-- Scroll to Top Button-->
      <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
      </a>

      <!-- Logout Modal-->
      <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Yakin ingin Keluar ?</h5>
              <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
            <div class="modal-body">Pilih "Logout" di bawah jika Anda ingin keluar</div>
            <div class="modal-footer">
              <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
              <a class="btn btn-info" href="<?= base_url('auth/logout') ?>">Logout</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal edit attendance -->
      <div class="modal fade" id="editAttendanceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editAttendanceModal">Edit Presensi</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form action="<?= base_url('master/update_attendance_history'); ?>" method="post">
              <div class="modal-body">
                <input type="hidden" name="employee_id" value="<?= $employee_id; ?>">
                <input type="hidden" name="date" id="attendance_date" value=" ">

                <!-- Dropdown untuk memilih status presensi -->
                <div class="form-group">
                  <label for="presence_status">Status Presensi</label>
                  <select class="form-control" id="presence_status" name="presence_status">
                    <option value="0">Tidak Hadir</option>
                    <option value="1">Hadir</option>
                    <option value="2">Izin</option>
                    <option value="3">Sakit</option>
                    <option value="4">Cuti</option>
                    <option value="5">Libur</option>
                  </select>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Modal untuk Maps -->
      <div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="mapModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="mapModalLabel">Lokasi Presensi</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <!-- Map Container -->
              <div id="map" style="width: 100%; height: 400px;"></div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
      <!-- Bootstrap core JavaScript-->
      <script src="<?= base_url('assets/'); ?>vendorss/jquery/jquery.min.js"></script>
      <script src="<?= base_url('assets/'); ?>vendorss/bootstrap/js/bootstrap.bundle.min.js"></script>

      <!-- Core plugin JavaScript-->
      <script src="<?= base_url('assets/'); ?>vendorss/jquery-easing/jquery.easing.min.js"></script>

      <!-- Custom scripts for all pages-->
      <script src="<?= base_url('assets/'); ?>js/sb-admin-2.min.js"></script>
      <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

      <script>
        $('#editAttendanceModal').on('show.bs.modal', function(event) {
          let button = $(event.relatedTarget);
          let day = button.data('day');
          let month = '<?= $month; ?>';
          let year = '<?= $year; ?>';
          let modal = $(this);

          // Tambahkan log untuk memeriksa nilai-nilai variabel
          console.log('day:', day, 'month:', month, 'year:', year);

          // Update judul modal dan input date dengan nilai yang sesuai
          modal.find('.modal-title').text('Edit Presensi - <?= $employee['employee_name']; ?> (' + day + '-' + month + '-' + year + ')');
          modal.find('#attendance_date').val(year + '-' + month + '-' + day);
        });

        // maps start
        let map;

        function showMap(lat, lng, label) {
          console.log("showMap called with lat:", lat, "lng:", lng, "label:", label); // Debugging
          document.getElementById("mapModalLabel").textContent = "Lokasi Presensi " + label;

          if (map) {
            map.remove();
          }

          map = L.map('map').setView([lat, lng], 15);

          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
          }).addTo(map);

          L.marker([lat, lng]).addTo(map)
            .bindPopup(`<b>${label}</b>`)
            .openPopup();

          $('#mapModal').modal('show');

          setTimeout(() => {
            map.invalidateSize();
          }, 200);
        }

        function showAlert(message) {
          Swal.fire({
            icon: 'info',
            title: 'Info',
            text: message,
            confirmButtonText: 'OK'
          });
        }
      </script>

      </body>

      </html>