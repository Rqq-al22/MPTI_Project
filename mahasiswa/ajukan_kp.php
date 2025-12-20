<?php
require_once "../auth/auth_check.php";
require_role('mahasiswa');
require_once "../config/db.php";

$current_page = 'ajukan_kp.php';
$page_title   = "Ajukan Kerja Praktik";
$asset_prefix = "../";
$logout_prefix = "../";

include "../includes/layout_top.php";
include "../includes/sidebar_mahasiswa.php";
include "../includes/header.php";

$nim = $_SESSION['nim'] ?? null;
if (!$nim) {
    echo "<div class='alert alert-danger'>Session mahasiswa tidak valid.</div>";
    include "../includes/layout_bottom.php";
    exit;
}

/* ===============================
   CEK KP AKTIF
   =============================== */
$stmt = $conn->prepare("
    SELECT *
    FROM kp
    WHERE nim = ?
      AND status IN ('Pengajuan','Diterima','Berlangsung')
    ORDER BY FIELD(status,'Berlangsung','Diterima','Pengajuan')
    LIMIT 1
");
$stmt->bind_param("s", $nim);
$stmt->execute();
$kp_aktif = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* ===============================
   MODE HALAMAN
   =============================== */
$mode = 'create';

if (isset($_GET['edit']) && $kp_aktif) {
    if ($kp_aktif['status'] === 'Berlangsung') {
        echo "<div class='alert alert-warning'>
              KP sedang <strong>Berlangsung</strong> dan tidak dapat diedit.
              </div>";
        include "../includes/layout_bottom.php";
        exit;
    }
    $mode = 'edit';
}
?>

<main class="pc-container">
<div class="pc-content">

<h3 class="mb-4">
  <?= $mode === 'edit' ? 'Edit Pengajuan Kerja Praktik' : 'Ajukan Kerja Praktik' ?>
</h3>

<?php if ($kp_aktif && $mode === 'create'): ?>
  <div class="alert alert-info">
    Anda sudah memiliki pengajuan KP dengan status:
    <strong><?= htmlspecialchars($kp_aktif['status']) ?></strong>.
    <br>Gunakan menu <strong>Edit KP</strong>.
  </div>
<?php endif; ?>

<form method="POST" action="ajukan_kp_process.php">

  <input type="hidden" name="mode" value="<?= $mode ?>">
  <input type="hidden" name="id_kp" value="<?= $kp_aktif['id_kp'] ?? '' ?>">

  <div class="mb-3">
    <label>Nama Instansi</label>
    <input type="text" name="nama_instansi" class="form-control"
           value="<?= htmlspecialchars($kp_aktif['nama_instansi'] ?? '') ?>"
           required>
  </div>

  <div class="mb-3">
    <label>Alamat Instansi</label>
    <input type="text" name="alamat_instansi" class="form-control"
           value="<?= htmlspecialchars($kp_aktif['alamat_instansi'] ?? '') ?>">
  </div>

  <div class="mb-3">
    <label>Kontak Instansi</label>
    <input type="text" name="kontak_instansi" class="form-control"
           value="<?= htmlspecialchars($kp_aktif['kontak_instansi'] ?? '') ?>">
  </div>

  <div class="mb-3">
    <label>Posisi KP</label>
    <input type="text" name="posisi" class="form-control"
           value="<?= htmlspecialchars($kp_aktif['posisi'] ?? '') ?>">
  </div>

  <div class="mb-3">
    <label>Pembimbing / Mentor Instansi</label>
    <input type="text" name="pembimbing_instansi" class="form-control"
           value="<?= htmlspecialchars($kp_aktif['pembimbing_instansi'] ?? '') ?>">
  </div>

  <button class="btn btn-primary">
    <?= $mode === 'edit' ? 'Update KP' : 'Ajukan KP' ?>
  </button>

  <a href="data_kp.php" class="btn btn-secondary">Kembali</a>

</form>

</div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
