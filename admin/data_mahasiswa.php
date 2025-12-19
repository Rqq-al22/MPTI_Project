<?php
require_once "../auth/auth_check.php";
require_role('admin');
require_once "../config/db.php";

$current_page  = 'data_dosen.php';
$page_title    = "Data Dosen";
$asset_prefix  = "../";
$logout_prefix = "../";

include "../includes/layout_top.php";
include "../includes/sidebar_admin.php";
?>

<main class="pc-container">
  <?php include "../includes/header.php"; ?>

  <div class="pc-content">

    <h3 class="mb-4">Data Dosen</h3>

    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-light">
          <tr>
            <th width="15%">NIDN</th>
            <th>Nama</th>
            <th>Jurusan</th>
            <th>Keahlian</th>
            <th width="10%">ID User</th>
          </tr>
        </thead>
        <tbody>

<?php
$q = $conn->query("
    SELECT nidn, nama, jurusan, keahlian, id_user
    FROM dosen
    ORDER BY nama ASC
");

if ($q->num_rows === 0):
?>
          <tr>
            <td colspan="5" class="text-center text-muted">
              Belum ada data dosen.
            </td>
          </tr>
<?php
else:
while ($r = $q->fetch_assoc()):
?>
          <tr>
            <td><?= htmlspecialchars($r['nidn']) ?></td>
            <td><?= htmlspecialchars($r['nama']) ?></td>
            <td><?= htmlspecialchars($r['jurusan'] ?? '-') ?></td>
            <td><?= htmlspecialchars($r['keahlian']) ?></td>
            <td class="text-center"><?= htmlspecialchars($r['id_user']) ?></td>
          </tr>
<?php
endwhile;
endif;
?>

        </tbody>
      </table>
    </div>

  </div>
</main> klian

<?php include "../includes/layout_bottom.php"; ?>
