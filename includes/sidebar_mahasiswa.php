<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['id_role']) || $_SESSION['id_role'] != 3) {
    exit('Akses ditolak (bukan mahasiswa)');
}

$current_page = basename($_SERVER['PHP_SELF']);

// PAKSA ada asset_prefix
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
        <li class="pc-item <?= $current_page=='dashboard_mahasiswa.php'?'active':'' ?>">
          <a href="<?= $asset_prefix ?>mahasiswa/dashboard_mahasiswa.php" class="pc-link">
            <i class="ph ph-house-line pc-micon"></i>
            <span class="pc-mtext">Dashboard</span>
          </a>
        </li>

        <li class="pc-item pc-caption"><label>KERJA PRAKTIK</label></li>

        <li class="pc-item <?= $current_page=='data_kp.php'?'active':'' ?>">
          <a href="<?= $asset_prefix ?>mahasiswa/data_kp.php" class="pc-link">
            <i class="ph ph-briefcase pc-micon"></i>
            <span class="pc-mtext">Data KP</span>
          </a>
        </li>

        <li class="pc-item <?= $current_page=='presensi.php'?'active':'' ?>">
          <a href="<?= $asset_prefix ?>mahasiswa/presensi.php" class="pc-link">
            <i class="ph ph-fingerprint pc-micon"></i>
            <span class="pc-mtext">Presensi</span>
          </a>
        </li>

        <li class="pc-item <?= $current_page=='laporan_mingguan.php'?'active':'' ?>">
          <a href="<?= $asset_prefix ?>mahasiswa/laporan_mingguan.php" class="pc-link">
            <i class="ph ph-file-text pc-micon"></i>
            <span class="pc-mtext">Laporan Mingguan</span>
          </a>
        </li>

        <li class="pc-item <?= $current_page=='nilai.php'?'active':'' ?>">
          <a href="<?= $asset_prefix ?>mahasiswa/nilai.php" class="pc-link">
            <i class="ph ph-star pc-micon"></i>
            <span class="pc-mtext">Nilai & Evaluasi</span>
          </a>
        </li>

        <li class="pc-item <?= $current_page=='dokumen_akhir.php'?'active':'' ?>">
          <a href="<?= $asset_prefix ?>mahasiswa/dokumen_akhir.php" class="pc-link">
            <i class="ph ph-upload pc-micon"></i>
            <span class="pc-mtext">Dokumen Akhir</span>
          </a>
        </li>

        <li class="pc-item pc-caption"><label>INFORMASI</label></li>

        <li class="pc-item <?= $current_page=='pengumuman.php'?'active':'' ?>">
          <a href="<?= $asset_prefix ?>mahasiswa/pengumuman.php" class="pc-link">
            <i class="ph ph-megaphone pc-micon"></i>
            <span class="pc-mtext">Pengumuman</span>
          </a>
        </li>

        <li class="pc-item pc-caption"><label>AKUN</label></li>

        <li class="pc-item <?= $current_page=='profile.php'?'active':'' ?>">
          <a href="<?= $asset_prefix ?>profile/profile.php" class="pc-link">
            <i class="ph ph-user pc-micon"></i>
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
