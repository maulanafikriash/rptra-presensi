        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

          <a href="<?= base_url('master/location'); ?>" class="btn btn-secondary btn-icon-split mb-4">
            <span class="icon text-white">
              <i class="fas fa-chevron-left"></i>
            </span>
            <span class="text">Kembali</span>
          </a>

          <form action="" method="POST" class="col-lg-5  p-0">
            <div class="card">
              <h5 class="card-header">Lokasi Master Data</h5>
              <div class="card-body">
                <h5 class="card-title">Edit Lokasi</h5>
                <p class="card-text">Form edit lokasi di sistem</p>
                <div class="form-group">
                  <label class="col-form-label-lg">ID Lokasi</label>
                  <input type="text" readonly class="form-control-plaintext form-control-lg" name="l_id" value="<?= $l_old['id']; ?>">
                </div>
                <div class="form-group">
                  <label for="l_name" class="col-form-label-lg">Nama Lokasi</label>
                  <input type="text" class="form-control form-control-lg" name="l_name" id="l_name" value="<?= $l_old['name']; ?>">
                  <?= form_error('l_name', '<small class="text-danger">', '</small>') ?>
                </div>
                <button type="submit" class="btn btn-success btn-icon-split mt-4 float-right">
                  <span class="icon text-white">
                    <i class="fas fa-check"></i>
                  </span>
                  <span class="text">Simpan Perubahan</span>
                </button>
          </form>
        </div>
        </div>
        </form>
        </div>
        <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->