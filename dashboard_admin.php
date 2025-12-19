<?php
require_once "../auth/auth_check.php";
require_role('admin');
require_once "../config/db.php";

$current_page  = 'dashboard_admin.php';
$page_title    = "Dashboard Admin";
$asset_prefix  = "../";
$logout_prefix = "../";

include "../includes/layout_top.php";
include "../includes/sidebar_admin.php";
?>

<main class="pc-container">
  <?php include "../includes/header.php"; ?>

  <div class="pc-content">

    <h3 class="mb-4">Dashboard Admin</h3>

<?php
/* ======================================================
   HITUNG DATA UNTUK DASHBOARD
   ====================================================== */

// jumlah mahasiswa
$q_mhs = $conn->query("SELECT COUNT(*) AS total FROM mahasiswa");
$total_mhs = $q_mhs->fetch_assoc()['total'] ?? 0;

// jumlah dosen
$q_dosen = $conn->query("SELECT COUNT(*) AS total FROM dosen");
$total_dosen = $q_dosen->fetch_assoc()['total'] ?? 0;

// jumlah pengajuan KP
$q_kp = $conn->query("SELECT COUNT(*) AS total FROM kp");
$total_kp = $q_kp->fetch_assoc()['total'] ?? 0;

// jumlah relasi bimbingan
$q_bimbingan = $conn->query("SELECT COUNT(*) AS total FROM bimbingan");
$total_bimbingan = $q_bimbingan->fetch_assoc()['total'] ?? 0;
?>

    <div class="row">

      <!-- Mahasiswa -->
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body">
            <h5>Mahasiswa</h5>
            <h2><?= $total_mhs ?></h2>
            <a href="data_mahasiswa.php" class="btn btn-sm btn-primary mt-2">
              Kelola
            </a>
          </div>
        </div>
      </div>

      <!-- Dosen -->
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body">
            <h5>Dosen</h5>
            <h2><?= $total_dosen ?></h2>
            <a href="data_dosen.php" class="btn btn-sm btn-success mt-2">
              Kelola
            </a>
          </div>
        </div>
      </div>

      <!-- KP -->
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body">
            <h5>Kerja Praktik</h5>
            <h2><?= $total_kp ?></h2>
            <a href="data_kp.php" class="btn btn-sm btn-warning mt-2">
              Lihat
            </a>
          </div>
        </div>
      </div>

      <!-- Bimbingan -->
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body">
            <h5>Bimbingan</h5>
            <h2><?= $total_bimbingan ?></h2>
            <a href="assign_bimbingan.php" class="btn btn-sm btn-info mt-2">
              Atur
            </a>
          </div>
        </div>
      </div>

    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h5>Aksi Cepat</h5>

            <a href="assign_bimbingan.php" class="btn btn-outline-primary me-2">
              Assign Dosen Pembimbing
            </a>

            <a href="pengumuman.php" class="btn btn-outline-secondary me-2">
              Buat Pengumuman
            </a>

            <a href="laporan_rekap.php" class="btn btn-outline-success">
              Rekap Laporan KP
            </a>
          </div>
        </div>
      </div>
    </div>

  </div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
