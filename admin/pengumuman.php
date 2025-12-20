<?php
require_once "../auth/auth_check.php";
require_role('admin');
require_once "../config/db.php";

/* ===============================
   KONFIGURASI HALAMAN
   =============================== */
$current_page  = "pengumuman.php";
$page_title    = "Pengumuman";
$asset_prefix  = "../";
$logout_prefix = "../";

/* ===============================
   PROSES SIMPAN (INSERT / UPDATE)
   =============================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_pengumuman = $_POST['id_pengumuman'] ?? null;
    $judul = trim($_POST['judul'] ?? '');
    $isi   = trim($_POST['isi'] ?? '');
    $admin_id = $_SESSION['user_id'] ?? null;

    if ($judul !== '' && $isi !== '') {

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
            $stmt->bind_param("ssi", $judul, $isi, $admin_id);
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
include "../includes/sidebar_admin.php";
include "../includes/header.php";
?>

<main class="pc-container">
<div class="pc-content">

<h3 class="fw-bold mb-4">📢 Pengumuman</h3>

<!-- ================= FORM ================= -->
<div class="card shadow-sm mb-4">
  <div class="card-body">
    <h5 class="fw-semibold mb-3">
      <?= $edit ? 'Edit' : 'Tambah' ?> Pengumuman
    </h5>

    <form method="POST">
      <input type="hidden" name="id_pengumuman"
             value="<?= $edit['id_pengumuman'] ?? '' ?>">

      <div class="mb-3">
        <label class="form-label fw-semibold">Judul</label>
        <input type="text" name="judul" class="form-control"
               value="<?= htmlspecialchars($edit['judul'] ?? '') ?>"
               required>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Isi Pengumuman</label>
        <textarea name="isi" rows="4" class="form-control" required><?= htmlspecialchars($edit['isi'] ?? '') ?></textarea>
      </div>

      <button class="btn btn-primary">
        <?= $edit ? 'Update' : 'Simpan Pengumuman' ?>
      </button>

      <?php if ($edit): ?>
        <a href="pengumuman.php" class="btn btn-secondary">Batal</a>
      <?php endif; ?>
    </form>
  </div>
</div>

<!-- ================= DAFTAR ================= -->
<div class="card shadow-sm">
  <div class="card-body">
    <h5 class="fw-semibold mb-3">Daftar Pengumuman</h5>

    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th width="5%">#</th>
            <th width="20%">Judul</th>
            <th>Isi</th>
            <th width="15%">Tanggal</th>
            <th width="20%">Aksi</th>
          </tr>
        </thead>
        <tbody>

<?php
if ($data->num_rows === 0):
?>
          <tr>
            <td colspan="5" class="text-center text-muted">
              Belum ada pengumuman.
            </td>
          </tr>
<?php
else:
$no = 1;
while ($r = $data->fetch_assoc()):
?>
          <tr>
            <td><?= $no++ ?></td>
            <td class="fw-semibold"><?= htmlspecialchars($r['judul']) ?></td>
            <td><?= nl2br(htmlspecialchars($r['isi'])) ?></td>
            <td><?= date('d M Y H:i', strtotime($r['created_at'])) ?></td>
            <td>
              <a href="?edit=<?= $r['id_pengumuman'] ?>"
                 class="btn btn-sm btn-warning">Edit</a>

              <a href="?hapus=<?= $r['id_pengumuman'] ?>"
                 onclick="return confirm('Yakin hapus pengumuman ini?')"
                 class="btn btn-sm btn-danger">Hapus</a>
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
