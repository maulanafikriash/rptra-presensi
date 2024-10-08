        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>
          <div class="row">
            <div class="col-lg-3">
              <a href="<?= base_url('master/employee'); ?>" class="btn btn-secondary btn-icon-split mb-4">
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
          <div class="col-lg-10 p-0">
            <?= form_open_multipart('master/a_employee'); ?>
            <div class="card">
              <h5 class="card-header">Pegawai Master Data</h5>
              <div class="card-body">
                <h5 class="card-title">Tambah Pegawai Baru</h5>
                <p class="card-text">Form untuk menambahkan pegawai baru di sistem</p>
                <div class="row">
                  <div class="col-lg-6">
                    <div class="form-group row">
                      <label for="e_name" class="col-form-label col-lg-4">Nama Pegawai</label>
                      <div class="col p-0">
                        <input type="text" class="form-control col-lg" name="e_name" id="e_name" autofocus>
                        <?= form_error('e_name', '<small class="text-danger">', '</small>') ?>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="d_id" class="col-form-label col-lg-4">Nama Department</label>
                      <div class="col p-0">
                        <select class="form-control" name="d_id" id="d_id">
                          <?php foreach ($department as $dpt) : ?>
                            <option value="<?= $dpt['id'] ?>"><?= $dpt['name']; ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="email" class="col-form-label col-lg-4">Email</label>
                      <div class="col p-0">
                        <input type="text" class="form-control col-lg" name="email" id="email">
                        <?= form_error('email', '<small class="text-danger">', '</small>') ?>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="e_gender" class="col-form-label col-lg-4">Jenis Kelamin</label>
                      <div class="form-check form-check-inline my-0">
                        <input class="form-check-input" type="radio" name="e_gender" id="l" value="L" checked>
                        <label class="form-check-label" for="m">
                          Laki-Laki
                        </label>
                        <?= form_error('e_gender', '<small class="text-danger">', '</small>') ?>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="e_gender" id="p" value="P">
                        <label class="form-check-label" for="f">
                          Perempuan
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group row">
                      <label for="image" class="col-form-label col-lg-4">Foto Pegawai</label>
                      <div class="col-lg-8 p-0">
                        <input type="file" name="image" id="image">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="s_id" class="col-form-label col-lg-4">Shift</label>
                      <div class="col p-0">
                        <select class="form-control" name="s_id" id="s_id">
                          <?php foreach ($shift as $sft) : ?>
                            <option value="<?= $sft['id'] ?>"><?= $sft['id'] . ' = ' . $sft['start'] . '-' . $sft['end']; ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="e_birth_date" class="col-form-label col-lg-4">Tanggal Lahir</label>
                      <div class="col-lg p-0">
                        <input type="date" class="form-control col-lg" name="e_birth_date" id="e_birth_date" min="1950-01-01" max="2004-01-01">
                        <?= form_error('e_birth_date', '<small class="text-danger">', '</small>') ?>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="e_hire_date" class="col-form-label col-lg-4">Tanggal Bergabung</label>
                      <div class="col-lg p-0">
                        <input type="date" class="form-control col-lg" name="e_hire_date" id="e_hire_date" min="2004-04-16" max="<?= date('Y-m-d', time()); ?>">
                        <?= form_error('e_hire_date', '<small class="text-danger">', '</small>') ?>
                      </div>
                    </div>
                  </div>
                </div>
                <button type="submit" class="btn btn-success btn-icon-split mt-4 float-right">
                  <span class="icon text-white">
                    <i class="fas fa-plus-circle"></i>
                  </span>
                  <span class="text">Simpan</span>
                </button>
                <?= form_close(); ?>
              </div>
            </div>
          </div>
          </form>
        </div>
        <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->

        <script>
          // Add the following code if you want the name of the file appear on select
          $(".custom-file-input").on("change", function() {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
          });
        </script>