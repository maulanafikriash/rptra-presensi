<div class="container">
  <!-- Outer Row -->
  <div class="row justify-content-center align-items-center vh-100">

    <div class="col-xl-5 col-lg-6 col-md-8">
      <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
          <!-- Nested Row within Card Body -->
          <div class="row">
            <div class="col-lg">
              <div class="py-2 px-5">
                <!-- Logo Company -->
                <div class="text-center">
                  <img src="<?= base_url('images/logo/logo-ppapp.png'); ?>" alt="Company Logo" width="100" class="mb-4">
                </div>
                <!-- Title -->
                <div class="text-center">
                  <h3 class="h4 text-gray-900 mb-4">Sistem Presensi Pegawai RPTRA Jakarta Timur</h1>
                  <hr class="mb-4">
                </div>
                <!-- Flash Message -->
                <?= $this->session->flashdata('message'); ?>
                <!-- Login Form -->
                <form class="user" method="post" action="<?= base_url(); ?>">
                  <div class="form-group mt-4">
                    <input type="text" class="form-control form-control-user" name="username" placeholder="Username (contoh: QWE026)" autocomplete="off">
                    <?= form_error('username', '<small class="text-danger pl-3">', '</small>') ?>
                  </div>
                  <div class="form-group mt-4 mb-4">
                    <input type="password" class="form-control form-control-user" name="password" placeholder="Password">
                    <?= form_error('password', '<small class="text-danger pl-3">', '</small>') ?>
                  </div>
                  <button class="btn btn-primary btn-user btn-block mt-4" type="submit">
                    Login
                  </button>
                </form>
                <!-- Footer Text -->
                <div class="text-center mt-4">
                  <small class="text-muted">© <?= date('Y'); ?> RPTRA | All Rights Reserved</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

</div>
