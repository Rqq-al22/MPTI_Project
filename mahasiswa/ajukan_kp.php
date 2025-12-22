<?php
require_once "../auth/auth_check.php";
require_role('mahasiswa');
require_once "../config/db.php";

$current_page  = 'ajukan_kp.php';
$page_title    = "Ajukan Kerja Praktik";
$asset_prefix  = "../";
$logout_prefix = "../";

include "../includes/layout_top.php";
include "../includes/sidebar_mahasiswa.php";
include "../includes/header.php";

/* =====================================================
   VALIDASI SESSION
   ===================================================== */
$nim = $_SESSION['nim'] ?? null;
if (!$nim) {
    echo "<div class='alert alert-danger'>Session mahasiswa tidak valid.</div>";
    include "../includes/layout_bottom.php";
    exit;
}

/* =====================================================
   CEK DATA KP TERAKHIR MAHASISWA
   ===================================================== */
$stmt = $conn->prepare("
    SELECT *
    FROM kp
    WHERE nim = ?
      AND status IN ('Pengajuan','Diterima','Berlangsung','Ditolak')
    ORDER BY FIELD(status,'Berlangsung','Diterima','Pengajuan','Ditolak')
    LIMIT 1
");
$stmt->bind_param("s", $nim);
$stmt->execute();
$kp_aktif = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* =====================================================
   MODE HALAMAN
   ===================================================== */
$mode = 'create';

/* MODE EDIT JIKA ADA ?edit=1 */
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

/* =====================================================
   KUNCI SUBMIT JIKA SUDAH ADA KP & BUKAN EDIT
   ===================================================== */
$disableSubmit = ($kp_aktif && $mode === 'create');
?>

<main class="pc-container">
<div class="pc-content">

<h3 class="mb-4">
  <?= $mode === 'edit'
      ? 'Edit / Ajukan Ulang Kerja Praktik'
      : 'Ajukan Kerja Praktik' ?>
</h3>

<?php if ($kp_aktif && $mode === 'create'): ?>
  <div class="alert alert-info">
    Anda sudah memiliki KP dengan status:
    <strong><?= htmlspecialchars($kp_aktif['status']) ?></strong>.
    <br>
    Silakan gunakan menu <strong>Edit / Ajukan Ulang KP</strong>.
  </div>
<?php endif; ?>

<form method="POST"
      action="ajukan_kp_process.php"
      enctype="multipart/form-data">

  <input type="hidden" name="mode" value="<?= $mode ?>">
  <input type="hidden" name="id_kp" value="<?= $kp_aktif['id_kp'] ?? '' ?>">

  <div class="mb-3">
    <label>Nama Instansi</label>
    <input type="text" name="nama_instansi" class="form-control"
           value="<?= htmlspecialchars($kp_aktif['nama_instansi'] ?? '') ?>"
           required <?= $disableSubmit ? 'readonly' : '' ?>>
  </div>

  <div class="mb-3">
    <label>Alamat Instansi</label>
    <input type="text" name="alamat_instansi" class="form-control"
           value="<?= htmlspecialchars($kp_aktif['alamat_instansi'] ?? '') ?>"
           <?= $disableSubmit ? 'readonly' : '' ?>>
  </div>

  <div class="mb-3">
    <label>Kontak Instansi</label>
    <input type="text" name="kontak_instansi" class="form-control"
           value="<?= htmlspecialchars($kp_aktif['kontak_instansi'] ?? '') ?>"
           <?= $disableSubmit ? 'readonly' : '' ?>>
  </div>

  <div class="mb-3">
    <label>Posisi KP</label>
    <input type="text" name="posisi" class="form-control"
           value="<?= htmlspecialchars($kp_aktif['posisi'] ?? '') ?>"
           <?= $disableSubmit ? 'readonly' : '' ?>>
  </div>

  <div class="mb-3">
    <label>Pembimbing / Mentor Instansi</label>
    <input type="text" name="pembimbing_instansi" class="form-control"
           value="<?= htmlspecialchars($kp_aktif['pembimbing_instansi'] ?? '') ?>"
           <?= $disableSubmit ? 'readonly' : '' ?>>
  </div>

  <div class="row">
    <div class="col-md-6 mb-3">
      <label>Tanggal Mulai KP</label>
      <input type="date" name="tgl_mulai" class="form-control"
             value="<?= $kp_aktif['tgl_mulai'] ?? '' ?>"
             required <?= $disableSubmit ? 'readonly' : '' ?>>
    </div>
    <div class="col-md-6 mb-3">
      <label>Tanggal Selesai KP</label>
      <input type="date" name="tgl_selesai" class="form-control"
             value="<?= $kp_aktif['tgl_selesai'] ?? '' ?>"
             required <?= $disableSubmit ? 'readonly' : '' ?>>
    </div>
  </div>

  <!-- UPLOAD SURAT -->
  <div class="mb-3">
    <label>
      Surat Penerimaan KP
      <span class="text-danger">*</span>
    </label>

    <input type="file"
           name="surat_diterima"
           class="form-control"
           accept=".pdf,.jpg,.jpeg,.png"
           <?= ($mode === 'create') ? 'required' : '' ?>
           <?= $disableSubmit ? 'disabled' : '' ?>>

    <?php if (!empty($kp_aktif['surat_diterima_file'])): ?>
      <div class="mt-2">
        <a href="../uploads/surat/<?= htmlspecialchars($kp_aktif['surat_diterima_file']) ?>"
           target="_blank"
           class="btn btn-sm btn-outline-primary">
          Lihat Surat
        </a>
      </div>
    <?php endif; ?>
  </div>

  <button type="submit"
          class="btn btn-primary"
          <?= $disableSubmit ? 'disabled' : '' ?>>
    <?= $mode === 'edit' ? 'Update KP' : 'Ajukan KP' ?>
  </button>

  <a href="data_kp.php" class="btn btn-secondary">Kembali</a>

</form>

</div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
