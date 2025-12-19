<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['id_role']) || $_SESSION['id_role'] != 1) {
    exit('Akses ditolak');
}

$current_page = basename($_SERVER['PHP_SELF']);

// pastikan asset_prefix selalu ada
$asset_prefix = $asset_prefix ?? "../";
?>

<nav class="pc-sidebar">
  <div class="navbar-wrapper">

    <!-- LOGO -->
    <div class="m-header">
      <a href="<?= $asset_prefix ?>admin/dashboard_admin.php" class="b-brand text-primary">
        <span class="b-title">SI Rekap KP</span>
      </a>
    </div>

    <div class="navbar-content">
      <ul class="pc-navbar">

        <!-- DASHBOARD -->
        <li class="pc-item <?= ($current_page === 'dashboard_admin.php') ? 'active' : '' ?>">
          <a href="<?= $asset_prefix ?>admin/dashboard_admin.php" class="pc-link">
            <span class="pc-micon"><i class="ti ti-home"></i></span>
            <span class="pc-mtext">Dashboard</span>
          </a>
        </li>

        <!-- MASTER DATA -->
        <li class="pc-item pc-caption"><label>Master Data</label></li>

        <li class="pc-item <?= ($current_page === 'data_mahasiswa.php') ? 'active' : '' ?>">
          <a href="<?= $asset_prefix ?>admin/data_mahasiswa.php" class="pc-link">
            <span class="pc-micon"><i class="ti ti-users"></i></span>
            <span class="pc-mtext">Mahasiswa</span>
          </a>
        </li>

        <li class="pc-item <?= ($current_page === 'data_dosen.php') ? 'active' : '' ?>">
          <a href="<?= $asset_prefix ?>admin/data_dosen.php" class="pc-link">
            <span class="pc-micon"><i class="ti ti-user"></i></span>
            <span class="pc-mtext">Dosen</span>
          </a>
        </li>

        <!-- KERJA PRAKTIK -->
        <li class="pc-item pc-caption"><label>Kerja Praktik</label></li>

        <li class="pc-item <?= ($current_page === 'data_kp.php') ? 'active' : '' ?>">
          <a href="<?= $asset_prefix ?>admin/data_kp.php" class="pc-link">
            <span class="pc-micon"><i class="ti ti-briefcase"></i></span>
            <span class="pc-mtext">Data KP</span>
          </a>
        </li>

        <!-- MONITORING -->
        <li class="pc-item pc-caption"><label>Monitoring</label></li>

        <li class="pc-item <?= ($current_page === 'monitoring_kp.php') ? 'active' : '' ?>">
          <a href="<?= $asset_prefix ?>admin/monitoring_kp.php" class="pc-link">
            <span class="pc-micon"><i class="ti ti-chart-bar"></i></span>
            <span class="pc-mtext">Monitoring KP</span>
          </a>
        </li>

        <li class="pc-item <?= ($current_page === 'monitoring.php') ? 'active' : '' ?>">
          <a href="<?= $asset_prefix ?>admin/monitoring.php" class="pc-link">
            <span class="pc-micon"><i class="ti ti-chart-bar"></i></span>
            <span class="pc-mtext">Monitoring Sistem</span>
          </a>
        </li>

        <!-- INFORMASI -->
        <li class="pc-item pc-caption"><label>Informasi</label></li>

        <li class="pc-item <?= ($current_page === 'pengumuman.php') ? 'active' : '' ?>">
          <a href="<?= $asset_prefix ?>admin/pengumuman.php" class="pc-link">
            <span class="pc-micon"><i class="ti ti-speakerphone"></i></span>
            <span class="pc-mtext">Pengumuman</span>
          </a>
        </li>

        <!-- AKUN -->
        <li class="pc-item pc-caption"><label>Akun</label></li>

        <li class="pc-item <?= ($current_page === 'profile.php') ? 'active' : '' ?>">
          <a href="<?= $asset_prefix ?>profile/profile.php" class="pc-link">
            <span class="pc-micon"><i class="ti ti-user-circle"></i></span>
            <span class="pc-mtext">Profil</span>
          </a>
        </li>

        <li class="pc-item">
          <a href="<?= $asset_prefix ?>auth/logout.php" class="pc-link text-danger">
            <span class="pc-micon"><i class="ti ti-logout"></i></span>
            <span class="pc-mtext">Logout</span>
          </a>
        </li>

      </ul>
    </div>
  </div>
</nav>
