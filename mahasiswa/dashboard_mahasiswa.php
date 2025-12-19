<?php
require_once "../auth/auth_check.php";
require_role('mahasiswa');

$current_page = 'dashboard_mahasiswa.php';
$page_title = "Dashboard Mahasiswa";
$asset_prefix = "../";
$logout_prefix = "../";

include "../includes/layout_top.php";
include "../includes/sidebar_mahasiswa.php"; // ✅ FIX
?>

<main class="pc-container">
  <?php include "../includes/header.php"; ?>

  <div class="pc-content">
    <h3>Dashboard Dosen</h3>
    <p>Selamat datang, <strong><?= htmlspecialchars($_SESSION['username']); ?></strong></p>

    <!-- konten dashboard -->
  </div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
