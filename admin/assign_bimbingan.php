<?php
require_once "../auth/auth_check.php";
require_role('admin');
require_once "../config/db.php";
require_once "../helpers/log_helper.php";

log_activity(
  $conn,
  "Menetapkan dosen pembimbing untuk mahasiswa NIM {$kp['nim']}"
);


/* ===============================
   KONFIGURASI HALAMAN
   =============================== */
$current_page  = 'assign_bimbingan.php';
$page_title    = "Assign Dosen Pembimbing";
$asset_prefix  = "../";
$logout_prefix = "../";

/* ===============================
   VALIDASI PARAMETER ID KP
   =============================== */
$id_kp = $_GET['id_kp'] ?? null;

// Jika tidak ada id_kp → kembalikan ke Data KP (AMAN)
if (!$id_kp || !is_numeric($id_kp)) {
    header("Location: data_kp.php?error=invalid_id");
    exit;
}

/* ===============================
   AMBIL DATA KP + MAHASISWA
   =============================== */
$stmt = $conn->prepare("
    SELECT 
        k.id_kp,
        k.nim,
        k.nama_instansi,
        k.tgl_mulai,
        k.tgl_selesai,
        m.nama AS nama_mhs
    FROM kp k
    JOIN mahasiswa m ON k.nim = m.nim
    WHERE k.id_kp = ?
");
$stmt->bind_param("i", $id_kp);
$stmt->execute();
$kp = $stmt->get_result()->fetch_assoc();

// Jika KP tidak ditemukan → aman redirect
if (!$kp) {
    header("Location: data_kp.php?error=data_not_found");
    exit;
}

/* ===============================
   PROSES SIMPAN ASSIGN
   =============================== */
$error = '';

if (isset($_POST['simpan'])) {

    $nidn = $_POST['nidn'] ?? '';

    if ($nidn === '') {
        $error = "Dosen pembimbing wajib dipilih.";
    } else {

        $stmt = $conn->prepare("
            UPDATE kp
            SET nidn = ?, status = 'Disetujui'
            WHERE id_kp = ?
        ");
        $stmt->bind_param("si", $nidn, $id_kp);
        $stmt->execute();

        header("Location: data_kp.php?assign=success");
        exit;
    }
}

/* ===============================
   AMBIL DATA DOSEN
   =============================== */
$dosen = $conn->query("
    SELECT nidn, nama, jurusan
    FROM dosen
    ORDER BY nama ASC
");

include "../includes/layout_top.php";
include "../includes/sidebar_admin.php";
?>

<main class="pc-container">
<?php include "../includes/header.php"; ?>

<div class="pc-content">

<h3 class="fw-bold mb-4">Assign Dosen Pembimbing</h3>

<div class="row">

<!-- ===============================
     DATA MAHASISWA
     =============================== -->
<div class="col-md-6">
<div class="card shadow-sm mb-3">
<div class="card-body">

<h5 class="fw-bold mb-3">Data Mahasiswa KP</h5>

<table class="table table-bordered table-sm">
<tr>
  <th width="35%">NIM</th>
  <td><?= htmlspecialchars($kp['nim']) ?></td>
</tr>
<tr>
  <th>Nama</th>
  <td><?= htmlspecialchars($kp['nama_mhs']) ?></td>
</tr>
<tr>
  <th>Instansi</th>
  <td><?= htmlspecialchars($kp['nama_instansi']) ?></td>
</tr>
<tr>
  <th>Periode</th>
  <td>
    <?= date('d M Y', strtotime($kp['tgl_mulai'])) ?>
    –
    <?= date('d M Y', strtotime($kp['tgl_selesai'])) ?>
  </td>
</tr>
</table>

</div>
</div>
</div>

<!-- ===============================
     FORM ASSIGN DOSEN
     =============================== -->
<div class="col-md-6">
<div class="card shadow-sm">
<div class="card-body">

<h5 class="fw-bold mb-3">Pilih Dosen Pembimbing</h5>

<?php if ($error): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="post">

<div class="mb-3">
<label class="form-label fw-semibold">Dosen Pembimbing</label>
<select name="nidn" class="form-select" required>
<option value="">-- Pilih Dosen --</option>
<?php while ($d = $dosen->fetch_assoc()): ?>
<option value="<?= $d['nidn'] ?>">
<?= htmlspecialchars($d['nama']) ?> — <?= htmlspecialchars($d['jurusan']) ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div class="d-flex gap-2">
<button type="submit" name="simpan" class="btn btn-success">
Simpan Assign
</button>
<a href="data_kp.php" class="btn btn-secondary">
Kembali
</a>
</div>

</form>

</div>
</div>
</div>

</div>
</div>

</main>

<?php include "../includes/layout_bottom.php"; ?>
