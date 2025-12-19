<?php
require_once "../auth/auth_check.php";
$current_page = 'pengumuman.php';

$page_title = "Pengumuman";
$asset_prefix = "../";
$logout_prefix = "../";

include "../includes/layout_top.php";
include "../includes/sidebar_mahasiswa.php"; 

?>

<main class="pc-container">
  <?php include "../includes/header.php"; ?>

  <div class="pc-content">
    <h3>Pengumuman</h3>
    <p>Informasi dan pengumuman terkait Kerja Praktik.</p>
  </div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
