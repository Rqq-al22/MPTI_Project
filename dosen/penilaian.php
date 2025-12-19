<?php
require_once "../auth/auth_check.php";
$current_page = 'penilaian.php';

$page_title = "Penilaian Kerja Praktik";
$asset_prefix = "../";
$logout_prefix = "../";

include "../includes/layout_top.php";
include "../includes/sidebar_dosen.php";
?>

<main class="pc-container">
  <?php include "../includes/header.php"; ?>

  <div class="pc-content">
    <h3>Penilaian Kerja Praktik</h3>
    <p>Form penilaian mahasiswa Kerja Praktik.</p>

    <form>
      <div class="mb-3">
        <label>Mahasiswa</label>
        <input type="text" class="form-control" value="Ahmad" readonly>
      </div>

      <div class="mb-3">
        <label>Nilai</label>
        <input type="number" class="form-control">
      </div>

      <div class="mb-3">
        <label>Catatan Dosen</label>
        <textarea class="form-control" rows="3"></textarea>
      </div>

      <button class="btn btn-primary">Simpan Penilaian</button>
    </form>
  </div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
