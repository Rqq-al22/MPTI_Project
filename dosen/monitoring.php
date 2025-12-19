<?php
require_once "../auth/auth_check.php";
require_role('dosen');

$current_page = 'monitoring.php';
$page_title = "Monitoring KP";
$asset_prefix = "../";
$logout_prefix = "../";

include "../includes/layout_top.php";
include "../includes/sidebar_dosen.php";
?>

<main class="pc-container">
<?php include "../includes/header.php"; ?>

<div class="pc-content">
  <h3>Monitoring Kerja Praktik</h3>
  <p>Monitoring progres mahasiswa selama Kerja Praktik.</p>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Mahasiswa</th>
        <th>Instansi</th>
        <th>Minggu</th>
        <th>Catatan Progres</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Ahmad</td>
        <td>PT Telkom</td>
        <td>5</td>
        <td>Implementasi modul laporan</td>
        <td><span class="badge bg-success">On Track</span></td>
      </tr>
    </tbody>
  </table>
</div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
