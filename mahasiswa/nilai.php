<?php
require_once "../auth/auth_check.php";
$current_page = 'nilai.php';

$page_title = "Nilai & Evaluasi";
$asset_prefix = "../";
$logout_prefix = "../";

include "../includes/layout_top.php";
include "../includes/sidebar_mahasiswa.php";
?>

<main class="pc-container">
  <?php include "../includes/header.php"; ?>

  <div class="pc-content">
    <h3>Nilai & Evaluasi</h3>
    <p>Halaman penilaian dan evaluasi Kerja Praktik oleh dosen.</p>
  </div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
