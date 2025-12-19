<?php
require_once "../auth/auth_check.php";
require_role('admin');
require_once "../config/db.php";

$current_page  = "pengumuman.php";
$page_title    = "Pengumuman";
$asset_prefix  = "../";
$logout_prefix = "../";

include "../includes/layout_top.php";
include "../includes/sidebar_admin.php";
?>

<main class="pc-container">
<?php include "../includes/header.php"; ?>

<div class="pc-content">

<h3 class="fw-bold mb-4">📢 Pengumuman</h3>

<!-- ================= FORM TAMBAH PENGUMUMAN ================= -->
<div class="card shadow-sm mb-4">
  <div class="card-body">
    <h5 class="fw-semibold mb-3">Tambah Pengumuman</h5>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label fw-semibold">Judul</label>
        <input type="text" name="judul" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Isi Pengumuman</label>
        <textarea name="isi" rows="4" class="form-control" required></textarea>
      </div>

      <button type="submit" name="simpan" class="btn btn-primary">
        Simpan Pengumuman
      </button>
    </form>
  </div>
</div>

<?php
/* ================= PROSES SIMPAN ================= */
if (isset($_POST['simpan'])) {
    $judul = trim($_POST['judul']);
    $isi   = trim($_POST['isi']);
    $admin_id = $_SESSION['user_id'];

    if ($judul !== "" && $isi !== "") {
        $stmt = $conn->prepare("
            INSERT INTO pengumuman (judul, isi, dibuat_oleh)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("ssi", $judul, $isi, $admin_id);
        $stmt->execute();

        echo "<div class='alert alert-success'>Pengumuman berhasil ditambahkan.</div>";
    }
}
?>

<!-- ================= DAFTAR PENGUMUMAN ================= -->
<div class="card shadow-sm">
  <div class="card-body">
    <h5 class="fw-semibold mb-3">Daftar Pengumuman</h5>

    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th width="5%">#</th>
            <th>Judul</th>
            <th>Isi</th>
            <th width="15%">Tanggal</th>
          </tr>
        </thead>
        <tbody>

<?php
$q = $conn->query("
    SELECT id_pengumuman, judul, isi, created_at
    FROM pengumuman
    ORDER BY created_at DESC
");

if ($q->num_rows === 0):
?>
          <tr>
            <td colspan="4" class="text-center text-muted">
              Belum ada pengumuman.
            </td>
          </tr>
<?php
else:
$no = 1;
while ($r = $q->fetch_assoc()):
?>
          <tr>
            <td><?= $no++ ?></td>
            <td class="fw-semibold"><?= htmlspecialchars($r['judul']) ?></td>
            <td><?= nl2br(htmlspecialchars($r['isi'])) ?></td>
            <td><?= date('d M Y H:i', strtotime($r['created_at'])) ?></td>
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
