<?php
require_once "../auth/auth_check.php";
require_role('mahasiswa');
require_once "../config/db.php";

/* ================= KONFIG ================= */
$current_page  = 'laporan_mingguan.php';
$page_title    = "Laporan Mingguan";
$asset_prefix  = "../";
$logout_prefix = "../";

/* ================= SESSION ================= */
$nim = $_SESSION['nim'] ?? null;
if (!$nim) {
    die("NIM tidak ditemukan di sesi login.");
}

/* ================= AMBIL KP AKTIF ================= */
$q_kp = $conn->prepare("
    SELECT id_kp 
    FROM kp 
    WHERE nim = ? AND status IN ('Berlangsung','Selesai')
    ORDER BY created_at DESC
    LIMIT 1
");
$q_kp->bind_param("s", $nim);
$q_kp->execute();
$kp = $q_kp->get_result()->fetch_assoc();

$error = '';
$sukses = '';

/* ================= PROSES SUBMIT ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $kp) {

    $minggu_ke  = intval($_POST['minggu_ke']);
    $judul      = trim($_POST['judul']);
    $ringkasan  = trim($_POST['ringkasan']);

    if (!$minggu_ke || !$judul || !$ringkasan) {
        $error = "Semua field wajib diisi.";
    }

    /* ==== UPLOAD FILE ==== */
    $file_name = null;
    if (!$error && isset($_FILES['file_laporan']) && $_FILES['file_laporan']['error'] === 0) {

        $dir = "../uploads/laporan/";
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $ext = pathinfo($_FILES['file_laporan']['name'], PATHINFO_EXTENSION);
        $file_name = "laporan_{$nim}_minggu{$minggu_ke}_" . time() . "." . $ext;

        move_uploaded_file(
            $_FILES['file_laporan']['tmp_name'],
            $dir . $file_name
        );
    } else {
        $error = "File laporan wajib diunggah.";
    }

    /* ==== SIMPAN DB ==== */
    if (!$error) {
        $stmt = $conn->prepare("
            INSERT INTO laporan_mingguan
            (id_kp, minggu_ke, judul, ringkasan, file_laporan, status, tanggal_upload)
            VALUES (?, ?, ?, ?, ?, 'Menunggu', CURDATE())
        ");
        $stmt->bind_param(
            "iisss",
            $kp['id_kp'],
            $minggu_ke,
            $judul,
            $ringkasan,
            $file_name
        );

        if ($stmt->execute()) {
            $sukses = "Laporan mingguan berhasil dikirim.";
        } else {
            $error = "Gagal menyimpan laporan.";
        }
    }
}

/* ================= AMBIL RIWAYAT LAPORAN ================= */
$laporan = [];
if ($kp) {
    $q_lap = $conn->prepare("
        SELECT minggu_ke, judul, tanggal_upload, file_laporan, status
        FROM laporan_mingguan
        WHERE id_kp = ?
        ORDER BY minggu_ke ASC
    ");
    $q_lap->bind_param("i", $kp['id_kp']);
    $q_lap->execute();
    $laporan = $q_lap->get_result();
}

/* ================= HELPER STATUS BADGE ================= */
function badgeStatus($status)
{
    switch ($status) {
        case 'Menunggu':
            return ['warning', 'Menunggu Review'];
        case 'Disetujui':
            return ['success', 'Disetujui'];
        case 'Ditolak':
            return ['danger', 'Ditolak'];
        default:
            return ['secondary', '-'];
    }
}

include "../includes/layout_top.php";
include "../includes/sidebar_mahasiswa.php";
include "../includes/header.php";
?>

<main class="pc-container">
<div class="pc-content">

<h3 class="mb-3">Laporan Mingguan Kerja Praktik</h3>

<?php if (!$kp): ?>
<div class="alert alert-warning">
    Anda belum memiliki KP yang aktif.
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
<h5>Upload Laporan Mingguan</h5>

<form method="POST" enctype="multipart/form-data">

<div class="row">
    <div class="col-md-3 mb-3">
        <label>Minggu ke-</label>
        <input type="number" name="minggu_ke" class="form-control" min="1" required>
    </div>
</div>

<div class="mb-3">
    <label>Judul Laporan</label>
    <input type="text" name="judul" class="form-control" required>
</div>

<div class="mb-3">
    <label>Ringkasan Kegiatan</label>
    <textarea name="ringkasan" class="form-control" rows="4" required></textarea>
</div>

<div class="mb-3">
    <label>File Laporan (PDF/DOC)</label>
    <input type="file" name="file_laporan" class="form-control" required>
</div>

<button class="btn btn-primary">Kirim Laporan</button>

</form>
</div>
</div>

<!-- RIWAYAT -->
<div class="card">
<div class="card-body">
<h5>Riwayat Laporan</h5>

<table class="table table-bordered align-middle">
<thead>
<tr>
    <th>Minggu</th>
    <th>Judul</th>
    <th>Tanggal</th>
    <th>Status</th>
    <th>File</th>
</tr>
</thead>
<tbody>

<?php if ($laporan->num_rows == 0): ?>
<tr>
    <td colspan="5" class="text-center">Belum ada laporan.</td>
</tr>
<?php else: while ($l = $laporan->fetch_assoc()): ?>
<?php [$badge, $label] = badgeStatus($l['status']); ?>
<tr>
    <td><?= $l['minggu_ke'] ?></td>
    <td><?= htmlspecialchars($l['judul']) ?></td>
    <td><?= date('d M Y', strtotime($l['tanggal_upload'])) ?></td>
    <td class="text-center">
        <span class="badge bg-<?= $badge ?>"><?= $label ?></span>
    </td>
    <td>
        <a href="../uploads/laporan/<?= $l['file_laporan'] ?>"
           target="_blank"
           class="btn btn-sm btn-outline-primary">
           Lihat
        </a>
    </td>
</tr>
<?php endwhile; endif; ?>

</tbody>
</table>
</div>
</div>

<?php endif; ?>

</div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
