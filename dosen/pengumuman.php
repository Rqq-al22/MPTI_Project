<?php
require_once "../auth/auth_check.php";
require_role('dosen');
require_once "../config/db.php";

$current_page  = 'pengumuman.php';
$page_title    = "Pengumuman";
$asset_prefix  = "../";
$logout_prefix = "../";

/* ===============================
   PROSES TAMBAH / EDIT
   =============================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_jadwal  = $_POST['id_jadwal'] ?? null;
    $judul      = trim($_POST['judul'] ?? '');
    $deskripsi  = trim($_POST['deskripsi'] ?? '');
    $tanggal    = $_POST['tanggal'] ?? '';
    $id_instansi = $_POST['id_instansi'] ?: null;
    $id_kp       = $_POST['id_kp'] ?: null;

    /* VALIDASI TANGGAL MANUAL */
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
        die("Format tanggal tidak valid");
    }

    if ($judul && $deskripsi && $tanggal) {
        if ($id_jadwal) {
            // UPDATE
            $stmt = $conn->prepare("
                UPDATE jadwal_pengumuman
                SET judul=?, deskripsi=?, tanggal=?, id_instansi=?, id_kp=?
                WHERE id_jadwal=?
            ");
            $stmt->bind_param(
                "sssiii",
                $judul, $deskripsi, $tanggal,
                $id_instansi, $id_kp, $id_jadwal
            );
        } else {
            // INSERT
            $stmt = $conn->prepare("
                INSERT INTO jadwal_pengumuman
                (judul, deskripsi, tanggal, id_instansi, id_kp)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "sssii",
                $judul, $deskripsi, $tanggal,
                $id_instansi, $id_kp
            );
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
    $conn->query("DELETE FROM jadwal_pengumuman WHERE id_jadwal=$id");
    header("Location: pengumuman.php");
    exit;
}

/* ===============================
   AMBIL DATA EDIT (JIKA ADA)
   =============================== */
$edit = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $edit = $conn->query(
        "SELECT * FROM jadwal_pengumuman WHERE id_jadwal=$id"
    )->fetch_assoc();
}

/* ===============================
   DATA PENGUMUMAN
   =============================== */
$data = $conn->query("
    SELECT * FROM jadwal_pengumuman
    ORDER BY tanggal DESC
");

include "../includes/layout_top.php";
include "../includes/sidebar_dosen.php";
?>

<main class="pc-container">
<?php include "../includes/header.php"; ?>

<div class="pc-content">

<h3>Pengumuman Kerja Praktik</h3>

<!-- ================= FORM ================= -->
<div class="card mb-4">
  <div class="card-header">
    <strong><?= $edit ? 'Edit' : 'Tambah' ?> Pengumuman</strong>
  </div>
  <div class="card-body">
    <form method="POST">
      <input type="hidden" name="id_jadwal" value="<?= $edit['id_jadwal'] ?? '' ?>">

      <div class="mb-3">
        <label>Judul</label>
        <input type="text" name="judul" class="form-control"
               value="<?= $edit['judul'] ?? '' ?>" required>
      </div>

      <div class="mb-3">
        <label>Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="4" required><?= $edit['deskripsi'] ?? '' ?></textarea>
      </div>

      <div class="mb-3">
        <label>Tanggal Pengumuman</label>
        <input type="date" name="tanggal" class="form-control"
               value="<?= $edit['tanggal'] ?? '' ?>" required>
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

<!-- ================= DAFTAR ================= -->
<table class="table table-bordered">
<thead>
<tr>
  <th>Judul</th>
  <th>Tanggal</th>
  <th>Aksi</th>
</tr>
</thead>
<tbody>
<?php while ($row = $data->fetch_assoc()): ?>
<tr>
  <td><?= htmlspecialchars($row['judul']) ?></td>
  <td><?= $row['tanggal'] ?></td>
  <td>
    <a href="?edit=<?= $row['id_jadwal'] ?>" class="btn btn-sm btn-warning">Edit</a>
    <a href="?hapus=<?= $row['id_jadwal'] ?>"
       onclick="return confirm('Hapus pengumuman?')"
       class="btn btn-sm btn-danger">Hapus</a>
  </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

</div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
