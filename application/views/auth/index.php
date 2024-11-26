<div class="container">
  <div class="row justify-content-center align-items-center vh-100">
    <div class="col-lg-5 col-md-8 col-sm-10 col-12">
      <div class="card o-hidden border-0 shadow-lg">
        <div class="card-body p-0">
          <div class="text-center pt-4 pb-2 px-3 bg-primary text-white">
            <div class="logo-container">
              <img src="<?= base_url('images/logo/logo-ppapp.png'); ?>" alt="Company Logo">
            </div>
            <h4 class="font-weight-bold">Sistem Presensi Pegawai</h4>
            <p class="font-weight-bold">RPTRA Jakarta Timur</p>
          </div>
          <div class="p-4">
            <?= $this->session->flashdata('message'); ?>
            <form class="user" method="post" action="<?= base_url('auth'); ?>">
              <div class="form-group">
                <label for="username">Username<span class="text-danger">*</span></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="fas fa-user"></i>
                    </span>
                  </div>
                  <input type="text" class="form-control" id="username" name="username" placeholder="Enter Username" value="<?= set_value('username'); ?>" autocomplete="off">
                </div>
                <?= form_error('username', '<small class="text-danger">', '</small>') ?>
              </div>

              <div class="form-group">
                <label for="password">Password<span class="text-danger">*</span></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="fas fa-lock"></i>
                    </span>
                  </div>
                  <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password">
                </div>
                <?= form_error('password', '<small class="text-danger">', '</small>') ?>
              </div>
              <button class="btn btn-primary btn-user btn-block" type="submit">
                Login
              </button>
            </form>
            <div class="text-center mt-3">
              <small class="text-muted">&copy; <?= date('Y'); ?> RPTRA | Jakarta Timur</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>