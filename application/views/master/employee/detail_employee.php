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

    <div class="col-lg p-0">
        <div class="row">
            <div class="col-lg-3">
                <div class="card" style="width: 100%; height: 100%">
                    <img src="<?= base_url('images/pp/') . $employee['image']; ?>" class="card-img-top w-75 mx-auto pt-3">
                    <div class="card-body mt-3">
                        <h5 class="card-title text-center"><?= $employee['employee_name']; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <h5 class="card-header">Detail Pegawai</h5>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4">
                                <p><strong>ID Pegawai :</strong></p>
                            </div>
                            <div class="col-lg-8">
                                <p><?= $employee['employee_id']; ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <p><strong>Nama :</strong></p>
                            </div>
                            <div class="col-lg-8">
                                <p><?= $employee['employee_name']; ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <p><strong>Email :</strong></p>
                            </div>
                            <div class="col-lg-8">
                                <p><?= $employee['email']; ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <p><strong>Jenis Kelamin :</strong></p>
                            </div>
                            <div class="col-lg-8">
                                <p><?= $employee['gender'] == 'L' ? 'Laki-Laki' : 'Perempuan'; ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <p><strong>Tanggal Lahir :</strong></p>
                            </div>
                            <div class="col-lg-8">
                                <p><?= $employee['birth_date']; ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <p><strong>Tanggal Bergabung :</strong></p>
                            </div>
                            <div class="col-lg-8">
                                <p><?= $employee['hire_date']; ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <p><strong>Shift :</strong></p>
                            </div>
                            <div class="col-lg-8">
                                <p><?= $shift_current['shift_id'] . ' = ' . date('H:i', strtotime($shift_current['start'])) . ' - ' . date('H:i', strtotime($shift_current['end'])); ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <p><strong>Department :</strong></p>
                            </div>
                            <div class="col-lg-8">
                                <p><?= $department_current['department_id'] . ' - ' . $department_current['department_name']; ?></p>
                            </div>
                        </div>
                        <a href="<?= base_url('master/attendance_history/') . $employee['employee_id'] ?>" class="btn btn-success btn-icon-split mt-4 float-right">
                            <span class="icon text-white" title="Edit">
                                <i class="fas fas fa-calendar-check"></i>
                            </span>
                            <span class="text">Riwayat Kehadiran</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->
</div>