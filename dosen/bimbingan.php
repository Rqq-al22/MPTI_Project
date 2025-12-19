<?php
require_once "../auth/auth_check.php";
$current_page = 'bimbingan.php';

$page_title = "Mahasiswa Bimbingan";
$asset_prefix = "../";
$logout_prefix = "../";

include "../includes/layout_top.php";
include "../includes/sidebar_dosen.php";
?>

<main class="pc-container">
  <?php include "../includes/header.php"; ?>

  <div class="pc-content">
    <h3>Mahasiswa Bimbingan</h3>
    <p>Daftar mahasiswa yang sedang dibimbing dalam Kerja Praktik.</p>

    <table class="table table-bordered">
      <thead>
        <tr>
          <th>NIM</th>
          <th>Nama</th>
          <th>Instansi</th>
          <th>Status KP</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>202312345</td>
          <td>Ahmad</td>
          <td>PT Telkom</td>
          <td><span class="badge bg-success">Aktif</span></td>
        </tr>
      </tbody>
    </table>
  </div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
