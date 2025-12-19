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

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="fw-bold text-dark">Data Dosen</h3>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">

        <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle text-dark">
            <thead class="table-primary text-center">
              <tr>
                <th width="15%">NIDN</th>
                <th>Nama Dosen</th>
                <th width="20%">Jurusan</th>
                <th width="20%">Keahlian</th>
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
                <td colspan="5" class="text-center text-muted py-4">
                  Belum ada data dosen.
                </td>
              </tr>
<?php
else:
while ($r = $q->fetch_assoc()):
?>
              <tr>
                <td class="text-center fw-semibold">
                  <?= htmlspecialchars($r['nidn']) ?>
                </td>
                <td>
                  <?= htmlspecialchars($r['nama']) ?>
                </td>
                <td>
                  <?= htmlspecialchars($r['jurusan'] ?: '-') ?>
                </td>
                <td>
                  <?= htmlspecialchars($r['keahlian'] ?: '-') ?>
                </td>
                <td class="text-center">
                  <span class="badge bg-secondary">
                    <?= htmlspecialchars($r['id_user']) ?>
                  </span>
                </td>
              </tr>
<?php
endwhile;
endif;
?>

            </tbody>
          </table>
        </div>

      </div>
    </div>

  </div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
