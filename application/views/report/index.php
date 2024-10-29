<!-- Begin Page Content -->
<div class="container-fluid">
  <!-- Page Heading -->
  <div class="row">
    <div class="col-lg">
      <h1 class="h1 mb-4 text-gray-900"><?= $title; ?></h1>
      <a href="<?= base_url('admin') ?>" class="btn btn-md btn-info mb-2">Kembali</a>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-7 ml-auto mb-3 float-right">
      <form action="" method="GET">
        <div class="row">
          <div class="col-3 offset-lg-1">
            <input type="date" name="start" class="form-control">
            <?= form_error('start', '<small class="text-danger pl-3">', '</small>') ?>
          </div>
          <div class="col-3">
            <input type="date" name="end" class="form-control">
            <?= form_error('end', '<small class="text-danger pl-3">', '</small>') ?>
          </div>
          <div class="col-3">
            <select class="form-control" name="dept">
              <option disabled>Department</option>
              <?php foreach ($department as $d) : ?>
                <option value="<?= $d['department_id']; ?>"><?= $d['department_name']; ?></option>
              <?php endforeach; ?>
            </select>
            <?= form_error('dept', '<small class="text-danger pl-3">', '</small>') ?>
          </div>
          <div class="col-2 d-flex justify-content-end">
            <button type="submit" class="btn btn-success btn-fill" style="width: 100px; padding: 5px 10px;">Tampilkan</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!-- End of row show -->
  <?php if (!$attendance) : ?>
    <h3>Tidak Ada Data, <br> Silakan Pilih Tanggal dan Department Anda</h3>
  <?php else : ?>
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Data Kehadiran</h6>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead class="bg-info text-white">
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
              <?php $i = 1; foreach ($attendance as $atd) : ?>
                <tr>
                  <th><?= $i++ ?></th>
                  <td><?= $atd['attendance_date'] ?></td>
                  <td><?= $atd['employee_name'] ?></td>
                  <td><?= $atd['shift_id'] ?></td>
                  <td><?= $atd['in_time'] ?></td>
                  <td><?= $atd['notes'] ?: 'Unfilled' ?></td>
                  <td><?= $atd['in_status']; ?></td>
                  <td><?= $atd['out_time'] ?: 'Belum check out' ?></td>
                  <td><?= $atd['out_status'] ?: 'Belum check out' ?></td>
                </tr>
              <?php endforeach; ?>
              <a href="<?= base_url('report/print/') . $start . '/' . $end . '/' . $dept_code ?>" target="blank" class="d-none d-sm-inline-block btn btn-sm btn-danger ml-2 shadow-sm float-right"><i class="fas fa-download fa-sm text-white"></i> Generate Report</a>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
