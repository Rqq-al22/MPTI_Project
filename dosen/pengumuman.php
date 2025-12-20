<?php
require_once "../auth/auth_check.php";
require_role('dosen');
require_once "../config/db.php";

/* ===============================
   KONFIGURASI HALAMAN
   =============================== */
$current_page  = 'pengumuman.php';
$page_title    = "Pengumuman Kerja Praktik";
$asset_prefix  = "../";
$logout_prefix = "../";

/* ===============================
   PROSES SIMPAN (INSERT / UPDATE)
   =============================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_pengumuman = $_POST['id_pengumuman'] ?? null;
    $judul         = trim($_POST['judul'] ?? '');
    $isi           = trim($_POST['isi'] ?? '');

    if ($judul && $isi) {

        if ($id_pengumuman) {
            // UPDATE
            $stmt = $conn->prepare("
                UPDATE pengumuman
                SET judul = ?, isi = ?
                WHERE id_pengumuman = ?
            ");
            $stmt->bind_param("ssi", $judul, $isi, $id_pengumuman);
        } else {
            // INSERT
            $stmt = $conn->prepare("
                INSERT INTO pengumuman (judul, isi, dibuat_oleh)
                VALUES (?, ?, ?)
            ");
            $dibuat_oleh = $_SESSION['user_id'] ?? null;
            $stmt->bind_param("ssi", $judul, $isi, $dibuat_oleh);
        }

        $stmt->execute();
        $stmt->close();

        header("Location: pengumuman.php");
        exit;
    }
}

/* ===============================
   PROSES HAPUS
   =============================== */
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $stmt = $conn->prepare("DELETE FROM pengumuman WHERE id_pengumuman = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: pengumuman.php");
    exit;
}

/* ===============================
   DATA EDIT
   =============================== */
$edit = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM pengumuman WHERE id_pengumuman = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

/* ===============================
   DATA LIST
   =============================== */
$data = $conn->query("
    SELECT *
    FROM pengumuman
    ORDER BY created_at DESC
");

/* ===============================
   LAYOUT
   =============================== */
include "../includes/layout_top.php";
include "../includes/sidebar_dosen.php";
include "../includes/header.php";
?>

<main class="pc-container">
<div class="pc-content">

<h3 class="mb-4">Pengumuman Kerja Praktik</h3>

<!-- ================= FORM ================= -->
<div class="card mb-4">
  <div class="card-header">
    <strong><?= $edit ? 'Edit' : 'Tambah' ?> Pengumuman</strong>
  </div>
  <div class="card-body">

    <form method="POST">
      <input type="hidden" name="id_pengumuman"
             value="<?= $edit['id_pengumuman'] ?? '' ?>">

      <div class="mb-3">
        <label class="form-label">Judul</label>
        <input type="text" name="judul" class="form-control"
               value="<?= htmlspecialchars($edit['judul'] ?? '') ?>"
               required>
      </div>

      <div class="mb-3">
        <label class="form-label">Isi Pengumuman</label>
        <textarea name="isi" class="form-control" rows="4" required><?= htmlspecialchars($edit['isi'] ?? '') ?></textarea>
      </div>

      <button class="btn btn-primary">
        <?= $edit ? 'Update' : 'Simpan' ?>
      </button>

      <?php if ($edit): ?>
        <a href="pengumuman.php" class="btn btn-secondary">Batal</a>
      <?php endif; ?>
    </form>

  </div>
</div>

<!-- ================= TABEL ================= -->
<table class="table table-bordered align-middle">
<thead class="table-light">
<tr>
  <th width="20%">Judul</th>
  <th>Isi Pengumuman</th>
  <th width="15%">Tanggal</th>
  <th width="20%">Aksi</th>
</tr>
</thead>
<tbody>

<?php if ($data->num_rows == 0): ?>
<tr>
  <td colspan="4" class="text-center text-muted">
    Belum ada pengumuman.
  </td>
</tr>
<?php endif; ?>

<?php while ($row = $data->fetch_assoc()): ?>
<tr>
  <td><strong><?= htmlspecialchars($row['judul']) ?></strong></td>

  <td><?= nl2br(htmlspecialchars($row['isi'])) ?></td>

  <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>

  <td>
    <a href="?edit=<?= $row['id_pengumuman'] ?>"
       class="btn btn-sm btn-warning">Edit</a>

    <a href="?hapus=<?= $row['id_pengumuman'] ?>"
       onclick="return confirm('Yakin hapus pengumuman ini?')"
       class="btn btn-sm btn-danger">Hapus</a>
  </td>
</tr>
<?php endwhile; ?>

</tbody>
</table>

</div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
