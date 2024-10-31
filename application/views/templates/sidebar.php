<?php
$role_id = $this->session->userdata('user_role_id');
?>

<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-info sidebar sidebar-dark accordion" id="accordionSidebar">
  <!-- Sidebar - Brand -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="">
    <div class="sidebar-brand-icon">
      <i class="fas fa-user-check"></i>
    </div>
    <div class="sidebar-brand-text">RPTRA <br> Jakarta Timur</div>
  </a>

  <!-- Query Menu -->
  <?php
  $queryMenu = "SELECT user_menu.user_menu_id, user_menu.menu 
                  FROM user_menu 
                  JOIN user_access 
                  ON user_menu.user_menu_id = user_access.user_menu_id 
                  WHERE user_access.user_role_id = $role_id 
                  ORDER BY user_access.user_menu_id ASC";

  $menu = $this->db->query($queryMenu)->result_array();

  foreach ($menu as $mn) :
  ?>
    <div class="sidebar-heading">
      <?= $mn['menu']; ?>
    </div>

    <?php
    $menuId = $mn['user_menu_id'];

    $querySubMenu = "SELECT * FROM `user_submenu` 
                               WHERE `user_menu_id` = $menuId 
                               AND `is_active` = 1";

    $subMenu = $this->db->query($querySubMenu)->result_array();

    foreach ($subMenu as $sm) :
      if ($title == $sm['title']) :
    ?>
        <li class="nav-item active">
        <?php else : ?>
        <li class="nav-item">
        <?php endif; ?>

        <a class="nav-link pb-0" href="<?= base_url($sm['url']); ?>">
          <i class="<?= $sm['icon']; ?>"></i>
          <span><?= $sm['title']; ?></span></a>
        </li>

      <?php endforeach; ?>

      <hr class="sidebar-divider mt-3">
    <?php endforeach; ?>
    </li>

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center">
      <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>

<?php if ($role_id == 2): ?>
  <!-- Bottom Menu untuk Employee -->
  <div class="bottom-menu bg-light d-flex justify-content-around py-2" style="display: none;">
    <a href="<?= base_url('attendance'); ?>" class="text-center">
      <i class="fas fa-calendar-alt"></i><br>
      <small>Presensi</small>
    </a>
    <a href="<?= base_url('attendance/history'); ?>" class="text-center">
      <i class="fas fa-file-alt"></i><br>
      <small>Riwayat</small>
    </a>
    <a href="<?= base_url('profile'); ?>" class="text-center">
      <i class="fas fa-user"></i><br>
      <small>Profil</small>
    </a>
    <a href="<?= base_url('attendance/change_password'); ?>" class="text-center">
      <i class="fas fa-lock"></i><br>
      <small>Setting</small>
    </a>
  </div>
<?php endif; ?>

<style>
  /* Tampilkan bottom menu hanya pada layar kecil untuk employee */
  <?php if ($role_id == 2): ?>@media (max-width: 500px) {
    .bottom-menu {
      display: flex !important;
      position: fixed;
      bottom: 0;
      width: 100%;
      z-index: 1000;
      border-top: 1px solid #ddd;
    }

    /* Sembunyikan sidebar pada layar kecil */
    .sidebar {
      display: none;
    }
  }

  <?php endif; ?>
  /* Tampilkan sidebar pada layar di atas 500px */
  @media (min-width: 501px) {
    .sidebar {
      display: block !important;
    }

    <?php if ($role_id == 2): ?>.bottom-menu {
      display: none !important;
      <?php endif; ?>
    }
  }
</style>