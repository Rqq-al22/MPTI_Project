<?php
require_once "../includes/auth.php";
require_role('mahasiswa');
require_once "../config/db.php";

$page_title = "Judul Halaman";
$asset_prefix = "../";
$menu_prefix  = "../";
$logout_prefix = "../";

include "../includes/layout_top.php";
include "../includes/sidebar.php";
?>

<main class="main">
  <?php include "../includes/header.php"; ?>

  <div class="container">
    <div class="page-header">
      <h4><?= $page_title ?></h4>
      <p class="text-muted">Deskripsi halaman</p>
    </div>

    <!-- ISI HALAMAN -->
    <div class="card">
      <div class="card-body">
        Konten halaman di sini
      </div>
    </div>

  </div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
