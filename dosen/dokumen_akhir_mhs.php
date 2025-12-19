<?php
require_once "../auth/auth_check.php";
require_role('dosen');

$current_page = 'dokumen_akhir_mhs.php';
$page_title = "Dokumen Akhir Mahasiswa";
$asset_prefix = "../";
$logout_prefix = "../";

include "../includes/layout_top.php";
include "../includes/sidebar_dosen.php";
?>

<main class="pc-container">
<?php include "../includes/header.php"; ?>

<div class="pc-content">
  <h3>Dokumen Akhir Mahasiswa</h3>
  <p>Daftar dokumen akhir yang diunggah mahasiswa.</p>

  <table class="table table-striped">
    <thead>
      <tr>
        <th>Mahasiswa</th>
        <th>Laporan Akhir</th>
        <th>Presentasi</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Ahmad</td>
        <td><a href="#">Unduh</a></td>
        <td><a href="#">Unduh</a></td>
        <td><span class="badge bg-info">Menunggu Verifikasi</span></td>
      </tr>
    </tbody>
  </table>
</div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
