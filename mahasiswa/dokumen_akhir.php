<?php
require_once "../auth/auth_check.php";
$current_page = 'dokumen_akhir.php';

$page_title = "Dokumen Akhir";
$asset_prefix = "../";
$logout_prefix = "../";

include "../includes/layout_top.php";
include "../includes/sidebar_mahasiswa.php";
?>

<main class="pc-container">
  <?php include "../includes/header.php"; ?>

  <div class="pc-content">
    <h3>Dokumen Akhir</h3>
    <p>Upload laporan akhir dan file presentasi Kerja Praktik.</p>
  </div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
