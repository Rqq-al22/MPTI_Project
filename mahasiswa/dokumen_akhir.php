<?php
require_once "../auth/auth_check.php";
require_role('mahasiswa');
require_once "../config/db.php";

/* ================= KONFIG ================= */
$current_page  = 'dokumen_akhir.php';
$page_title    = "Dokumen Akhir";
$asset_prefix  = "../";
$logout_prefix = "../";

/* ================= AMBIL NIM ================= */
$nim = $_SESSION['nim'] ?? null;
if (!$nim) {
    die("NIM tidak ditemukan di sesi.");
}

/* ================= AMBIL KP AKTIF ================= */
$q_kp = $conn->prepare("
    SELECT id_kp 
    FROM kp 
    WHERE nim = ?
    ORDER BY created_at DESC
    LIMIT 1
");
$q_kp->bind_param("s", $nim);
$q_kp->execute();
$kp = $q_kp->get_result()->fetch_assoc();

$error = '';
$sukses = '';

/* ================= PROSES UPLOAD ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $kp) {

    $catatan = trim($_POST['catatan'] ?? '');

    if (
        !isset($_FILES['file_laporan_akhir']) ||
        !isset($_FILES['file_ppt'])
    ) {
        $error = "Semua file wajib diunggah.";
    }

    if (!$error) {
        $dir = "../uploads/dokumen_akhir/";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        // === LAPORAN AKHIR ===
        $ext_laporan = pathinfo($_FILES['file_laporan_akhir']['name'], PATHINFO_EXTENSION);
        $file_laporan_akhir = "laporan_akhir_{$nim}_" . time() . "." . $ext_laporan;

        // === PPT ===
        $ext_ppt = pathinfo($_FILES['file_ppt']['name'], PATHINFO_EXTENSION);
        $file_ppt = "ppt_{$nim}_" . time() . "." . $ext_ppt;

        move_uploaded_file($_FILES['file_laporan_akhir']['tmp_name'], $dir . $file_laporan_akhir);
        move_uploaded_file($_FILES['file_ppt']['tmp_name'], $dir . $file_ppt);

        // CEK SUDAH ADA ATAU BELUM
        $cek = $conn->prepare("
            SELECT id_dokumen_akhir 
            FROM dokumen_akhir 
            WHERE id_kp = ?
            LIMIT 1
        ");
        $cek->bind_param("i", $kp['id_kp']);
        $cek->execute();
        $exist = $cek->get_result()->fetch_assoc();

        if ($exist) {
            // UPDATE
            $stmt = $conn->prepare("
                UPDATE dokumen_akhir
                SET
                    file_laporan_akhir = ?,
                    file_ppt = ?,
                    tanggal_upload = CURDATE(),
                    catatan = ?
                WHERE id_kp = ?
            ");
            $stmt->bind_param(
                "sssi",
                $file_laporan_akhir,
                $file_ppt,
                $catatan,
                $kp['id_kp']
            );
        } else {
            // INSERT
            $stmt = $conn->prepare("
                INSERT INTO dokumen_akhir
                (id_kp, file_laporan_akhir, file_ppt, tanggal_upload, catatan)
                VALUES (?, ?, ?, CURDATE(), ?)
            ");
            $stmt->bind_param(
                "isss",
                $kp['id_kp'],
                $file_laporan_akhir,
                $file_ppt,
                $catatan
            );
        }

        if ($stmt->execute()) {
            $sukses = "Dokumen akhir berhasil diunggah.";
        } else {
            $error = "Gagal menyimpan dokumen.";
        }
    }
}

/* ================= DATA DOKUMEN AKHIR ================= */
$data = null;
if ($kp) {
    $q = $conn->prepare("
        SELECT * 
        FROM dokumen_akhir
        WHERE id_kp = ?
        LIMIT 1
    ");
    $q->bind_param("i", $kp['id_kp']);
    $q->execute();
    $data = $q->get_result()->fetch_assoc();
}

/* ================= LAYOUT ================= */
include "../includes/layout_top.php";
include "../includes/sidebar_mahasiswa.php";
include "../includes/header.php";
?>

<main class="pc-container">
<div class="pc-content">

<h3 class="fw-bold mb-3">Dokumen Akhir Kerja Praktik</h3>
<p class="text-muted mb-4">
Upload laporan akhir dan file presentasi Kerja Praktik.
</p>

<?php if (!$kp): ?>
<div class="alert alert-warning">
    Anda belum memiliki data Kerja Praktik.
</div>

<?php else: ?>

<?php if ($error): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<?php if ($sukses): ?>
<div class="alert alert-success"><?= $sukses ?></div>
<?php endif; ?>

<!-- FORM UPLOAD -->
<div class="card mb-4">
<div class="card-body">
<h5>Upload Dokumen Akhir</h5>

<form method="POST" enctype="multipart/form-data">

<div class="mb-3">
    <label>Laporan Akhir (PDF/DOC)</label>
    <input type="file" name="file_laporan_akhir" class="form-control" required>
</div>

<div class="mb-3">
    <label>File Presentasi (PPT/PPTX)</label>
    <input type="file" name="file_ppt" class="form-control" required>
</div>

<div class="mb-3">
    <label>Catatan (opsional)</label>
    <textarea name="catatan" class="form-control" rows="3"></textarea>
</div>

<button class="btn btn-primary">Simpan Dokumen</button>

</form>
</div>
</div>

<!-- RIWAYAT -->
<?php if ($data): ?>
<div class="card">
<div class="card-body">
<h5>Dokumen Terunggah</h5>

<table class="table table-bordered">
<tr>
    <th>Laporan Akhir</th>
    <td>
        <a href="../uploads/dokumen_akhir/<?= $data['file_laporan_akhir'] ?>" target="_blank">
            Lihat
        </a>
    </td>
</tr>
<tr>
    <th>File PPT</th>
    <td>
        <a href="../uploads/dokumen_akhir/<?= $data['file_ppt'] ?>" target="_blank">
            Lihat
        </a>
    </td>
</tr>
<tr>
    <th>Tanggal Upload</th>
    <td><?= date('d M Y', strtotime($data['tanggal_upload'])) ?></td>
</tr>
<tr>
    <th>Catatan</th>
    <td><?= nl2br(htmlspecialchars($data['catatan'] ?? '-')) ?></td>
</tr>
</table>

</div>
</div>
<?php endif; ?>

<?php endif; ?>

</div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
