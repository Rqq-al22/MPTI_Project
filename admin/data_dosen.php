<?php
require_once "../auth/auth_check.php";
require_role('admin');
require_once "../config/db.php";

$current_page = 'data_dosen.php';
$page_title   = "Data Dosen";
$asset_prefix = "../";

include "../includes/layout_top.php";
include "../includes/sidebar_admin.php";
?>

<main class="pc-container">
<?php include "../includes/header.php"; ?>

<div class="pc-content">
  <h3>Data Dosen</h3>

  <table class="table table-bordered mt-3">
    <thead>
      <tr>
        <th>NIDN</th>
        <th>Nama</th>
      </tr>
    </thead>
    <tbody>
<?php
$q = $conn->query("SELECT nidn, nama FROM dosen ORDER BY nama");
while ($r = $q->fetch_assoc()):
?>
      <tr>
        <td><?= htmlspecialchars($r['nidn']) ?></td>
        <td><?= htmlspecialchars($r['nama']) ?></td>
      </tr>
<?php endwhile; ?>
    </tbody>
  </table>
</div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
