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

          <?= form_open_multipart('master/e_employee/' . $employee['id']); ?>
          <div class="col-lg p-0">
            <div class="row">
              <div class="col-lg-3">
                <div class="card" style="width: 100%; height: 100%">
                  <img src="<?= base_url('images/pp/') . $employee['image']; ?>" class="card-img-top w-75 mx-auto pt-3">
                  <div class="card-body mt-3">
                    <label for="image">Ubah Foto Pegawai</label>
                    <input type="file" name="image" id="image" class="mt-2">
                  </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="card">
                  <h5 class="card-header">Pegawai Master Data</h5>
                  <div class="card-body">
                    <h5 class="card-title">Edit Pegawai</h5>
                    <p class="card-text">Form edit Pegawai di sistem</p>
                    <div class="row">
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label for="employee_id" class="col-form-label">ID Pegawai</label>
                          <input type="text" readonly class="form-control-plaintext" name="e_id" value="<?= $employee['id']; ?>">
                        </div>
                      </div>
                      <div class="col-lg-8">
                        <div class="form-group">
                          <label for="e_name" class="col-form-label">Nama Pegawai</label>
                          <input type="text" class="form-control" name="e_name" id="e_name" value="<?= $employee['name']; ?>">
                          <?= form_error('e_name', '<small class="text-danger">', '</small>') ?>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label for="e_gender" class="col-form-label">Jenis kelamin</label>
                          <div class="row col-lg">
                            <div class="form-check form-check-inline my-0">
                              <input class="form-check-input" type="radio" name="e_gender" id="l" value="L" <?php if ($employee['gender'] == 'L') {
                                                                                                              echo 'checked';
                                                                                                            }; ?>>
                              <label class="form-check-label" for="l">
                                Laki-Laki
                              </label>
                              <?= form_error('e_gender', '<small class="text-danger">', '</small>') ?>
                            </div>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="e_gender" id="p" value="P" <?php if ($employee['gender'] == 'P') {
                                                                                                              echo 'checked';
                                                                                                            }; ?>>
                              <label class="form-check-label" for="p">
                                Perempuan
                              </label>
                            </div>
                          </div>
                          <?= form_error('e_gender', '<small class="text-danger">', '</small>') ?>
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label for="e_birth_date" class="col-form-label">Tanggal lahir</label>
                          <div class="col-lg p-0">
                            <input type="date" class="form-control" name="e_birth_date" id="e_birth_date" min="1950-01-01" max="2005-01-01" value="<?= $employee['birth_date']; ?>">
                            <?= form_error('e_birth_date', '<small class="text-danger">', '</small>') ?>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label for="e_hire_date" class="col-form-label">Tanggal Bergabung</label>
                          <div class="col-lg p-0">
                            <input type="date" class="form-control" name="e_hire_date" id="e_hire_date" min="2004-04-16" max="<?= date('Y-m-d', time()); ?>" value="<?= $employee['hire_date']; ?>">
                            <?= form_error('e_hire_date', '<small class="text-danger">', '</small>') ?>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label for="s_id" class="col-form-label">Shift</label>
                          <select class="form-control" name="s_id" id="s_id">
                            <?php foreach ($shift as $sft) : ?>
                              <option value="<?= $sft['id'] ?>" <?php if ($sft['id'] == $employee['shift_id']) echo 'selected'; ?>>
                                Shift <?= $sft['id'] ?> = <?= date('H:i', strtotime($sft['start'])) . ' - ' . date('H:i', strtotime($sft['end'])) ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label for="d_id" class="col-form-label">Department</label>
                          <select class="form-control" name="d_id" id="d_id">
                            <?php foreach ($department as $dpt) : ?>
                              <option value="<?= $dpt['id'] ?>" <?php if ($dpt['id'] == $department_current['department_id']) echo 'selected'; ?>>
                                <?= $dpt['id'] ?> - <?= $dpt['name'] ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                      </div>
                    </div>
                    <button type="submit" class="btn btn-success btn-icon-split mt-4 float-right">
                      <span class="icon text-white">
                        <i class="fas fa-check"></i>
                      </span>
                      <span class="text">Simpan Perubahan</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?= form_close(); ?>
        </div>
        <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->