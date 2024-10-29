<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- Page Heading -->
  <div class="row">
    <div class="col-sm-10">
      <h1 class="h1 mb-4 text-gray-900 account-name"><?= $account['name'] ?></h1>
    </div>
  </div>
  <div class="row">

    <!-- left -->
    <div class="col-sm-10 col-md-5 col-lg-4 col-xl-3 offset-sm-1 offset-md-0 offset-lg-0 offset-xl-0">
      <img src="<?= base_url('images/pp/') . $account['image']; ?>" class="rounded-circle img-thumbnail account-image">
    </div>

    <!-- right -->
    <div class="col-sm-10 col-md-6 offset-sm-1">
      <h1 class="h3 text-white bg-info px-3 py-2 rounded mt-1 mb-3 data-pegawai">Data Pegawai</h1>
      <table class="table">
        <tbody>
          <tr>
            <th scope="row">ID Pegawai</th>
            <td>: <?= $account['id']; ?></td>
          </tr>
          <tr>
            <th scope="row">Email</th>
            <td>: <?= $account['email']; ?></td>
          </tr>
          <tr>
            <th scope="row">Jenis Kelamin</th>
            <td>: <?php if ($account['gender'] == 'L') {
                    echo 'Laki-Laki';
                  } else {
                    echo 'Perempuan';
                  }; ?></td>
          </tr>
          <tr>
            <th scope="row">Department</th>
            <td>: <?= $account['department'] ?></td>
          </tr>
          <tr>
            <th scope="row">Tanggal Lahir</th>
            <td>: <?= $account['birth_date']; ?></td>
          </tr>
          <tr>
            <th scope="row">Bergabung Sejak</th>
            <td>: <?= $account['hire_date'] ?></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->