<?php
require_once "../auth/auth_check.php";
$current_page = 'laporan_mhs.php';

$page_title = "Laporan Mahasiswa";
$asset_prefix = "../";
$logout_prefix = "../";

include "../includes/layout_top.php";
include "../includes/sidebar_dosen.php";
?>

<main class="pc-container">
  <?php include "../includes/header.php"; ?>

  <div class="pc-content">
    <h3>Laporan Mingguan Mahasiswa</h3>
    <p>Daftar laporan mingguan yang dikirim mahasiswa.</p>

    <table class="table table-striped">
      <thead>
        <tr>
          <th>Minggu</th>
          <th>Mahasiswa</th>
          <th>File</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>3</td>
          <td>Ahmad</td>
          <td><a href="#">Lihat</a></td>
          <td><span class="badge bg-warning">Menunggu Review</span></td>
        </tr>
      </tbody>
    </table>
  </div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
