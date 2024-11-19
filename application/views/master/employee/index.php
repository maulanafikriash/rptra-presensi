        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

          <div class="row">
            <div class="col-lg-3">
              <a href="<?= base_url('master/a_employee'); ?>" class="btn btn-primary btn-icon-split mb-4">
                <span class="icon text-white-600">
                  <i class="fas fa-plus-circle"></i>
                </span>
                <span class="text">Tambah Pegawai Baru</span>
              </a>
            </div>
            <div class="col-lg-5 offset-lg-4">
              <?= $this->session->flashdata('message'); ?>
            </div>
          </div>

          <!-- Data Table employee-->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Data Tables Pegawai</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>ID</th>
                      <th>Nama</th>
                      <th>Shift</th>
                      <th>Jenis Kelamin</th>
                      <th>Foto</th>
                      <th>Tgl Lahir</th>
                      <th>Tgl Bergabung</th>
                      <th>Actions</th>
                    </tr>
                  </thead>

                  <tbody>
                    <?php
                    $i = 1;
                    foreach ($employee as $emp) :
                    ?>
                      <?php if ($emp['shift_id'] == 0) {
                        continue;
                      } ?>
                      <tr>
                        <td class=" align-middle"><?= $i++; ?></td>
                        <td class=" align-middle"><?= $emp['employee_id']; ?></td>
                        <td class=" align-middle"><?= $emp['employee_name']; ?></td>
                        <td class=" align-middle"><?= $emp['shift_id']; ?></td>
                        <td class=" align-middle"><?php if ($emp['gender'] == 'L') {
                                                    echo 'Laki-Laki';
                                                  } else {
                                                    echo 'Perempuan';
                                                  }; ?></td>
                        <td class="text-center"><img src="<?= base_url('images/pp/') . $emp['image']; ?>" style="width: 55px; height:55px" class="img-rounded"></td>
                        <td class=" align-middle"><?= $emp['birth_date']; ?></td>
                        <td class=" align-middle"><?= $emp['hire_date']; ?></td>
                        <td class="text-center align-middle">
                          <a href="<?= base_url('master/detail_employee/') . $emp['employee_id'] ?>" class="btn btn-success btn-circle">
                            <span class="icon" title="Details">
                              <i class="fas fa-info"></i>
                            </span>
                          </a> |
                          <a href="<?= base_url('master/e_employee/') . $emp['employee_id'] ?>" class="btn btn-primary btn-circle">
                            <span class="icon text-white" title="Edit">
                              <i class="fas fa-edit"></i>
                            </span>
                          </a> |
                          <a href="<?= base_url('master/d_employee/') . $emp['employee_id'] ?>" class="btn btn-danger btn-circle" onclick="return confirm('Pegawai yang dihapus akan hilang selamanya. Yakin ingin menghapus ?')">
                            <span class="icon text-white" title="Delete">
                              <i class="fas fa-trash-alt"></i>
                            </span>
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
        <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->