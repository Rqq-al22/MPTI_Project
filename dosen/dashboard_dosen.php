<?php
require_once "../auth/auth_check.php"; // pastikan role = dosen
require_role('dosen');
$current_page = 'dashboard_dosen.php';

$page_title = "Dashboard Dosen";
$asset_prefix = "../";
$logout_prefix = "../";

include "../includes/layout_top.php";
include "../includes/sidebar_dosen.php";
?>

<main class="pc-container">
  <?php include "../includes/header.php"; ?>

  <div class="pc-content">
    <h3>Dashboard Dosen</h3>
    <p>Selamat datang, <strong><?= htmlspecialchars($_SESSION['username']); ?></strong></p>

    <div class="row">
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h5>Mahasiswa Bimbingan</h5>
            <p>Daftar mahasiswa KP yang Anda bimbing.</p>
            <a href="bimbingan.php" class="btn btn-sm btn-primary">Lihat</a>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h5>Laporan Masuk</h5>
            <p>Laporan mingguan yang perlu direview.</p>
            <a href="laporan_mhs.php" class="btn btn-sm btn-warning">Review</a>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h5>Penilaian</h5>
            <p>Input dan kelola nilai KP mahasiswa.</p>
            <a href="penilaian.php" class="btn btn-sm btn-success">Nilai</a>
          </div>
        </div>
      </div>
    </div>

  </div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
