<?php
require_once "../auth/auth_check.php";
require_role('mahasiswa');
require_once "../config/db.php";

/* ===============================
   KONFIGURASI HALAMAN
   =============================== */
$current_page  = 'nilai.php';
$page_title    = "Nilai & Evaluasi";
$asset_prefix  = "../";
$logout_prefix = "../";

/* ===============================
   AMBIL NIM LOGIN
   =============================== */
$nim = $_SESSION['nim'] ?? null;
if (!$nim) {
    die("NIM tidak ditemukan.");
}

/* ===============================
   AMBIL KP MAHASISWA
   =============================== */
$q_kp = $conn->prepare("
    SELECT 
        k.id_kp,
        k.status,
        d.nama AS nama_dosen
    FROM kp k
    JOIN dosen d ON k.nidn = d.nidn
    WHERE k.nim = ?
    ORDER BY k.created_at DESC
    LIMIT 1
");
$q_kp->bind_param("s", $nim);
$q_kp->execute();
$kp = $q_kp->get_result()->fetch_assoc();

include "../includes/layout_top.php";
include "../includes/sidebar_mahasiswa.php";
include "../includes/header.php";
?>

<main class="pc-container">
<div class="pc-content">

<h3 class="fw-bold mb-1">Nilai & Evaluasi Kerja Praktik</h3>
<p class="text-muted mb-4">
    Hasil penilaian Kerja Praktik oleh dosen pembimbing.
</p>

<?php if (!$kp): ?>
<div class="alert alert-warning">
    Anda belum memiliki data Kerja Praktik.
</div>

<?php else: ?>

<!-- ================= NILAI AKHIR ================= -->
<?php
$q_akhir = $conn->prepare("
    SELECT 
        nilai_presensi,
        nilai_laporan,
        nilai_presentasi,
        nilai_akhir,
        komentar
    FROM penilaian_akhir
    WHERE id_kp = ?
    LIMIT 1
");
$q_akhir->bind_param("i", $kp['id_kp']);
$q_akhir->execute();
$nilai_akhir = $q_akhir->get_result()->fetch_assoc();
?>

<div class="card mb-4">
<div class="card-header fw-bold">Nilai Akhir</div>
<div class="card-body">

<p><strong>Dosen Pembimbing:</strong> <?= htmlspecialchars($kp['nama_dosen']) ?></p>
<p><strong>Status KP:</strong> <?= htmlspecialchars($kp['status']) ?></p>

<?php if (!$nilai_akhir): ?>
<div class="alert alert-info mt-3">
    Nilai akhir belum diberikan.
</div>
<?php else: ?>

<div class="row text-center mt-3">
    <div class="col-md-3">
        <div class="border p-3 rounded">
            <small>Presensi</small>
            <h4><?= (int)$nilai_akhir['nilai_presensi'] ?></h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="border p-3 rounded">
            <small>Laporan</small>
            <h4><?= (int)$nilai_akhir['nilai_laporan'] ?></h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="border p-3 rounded">
            <small>Presentasi</small>
            <h4><?= (int)$nilai_akhir['nilai_presentasi'] ?></h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="border p-3 rounded bg-light">
            <small>Nilai Akhir</small>
            <h3 class="fw-bold"><?= (int)$nilai_akhir['nilai_akhir'] ?></h3>
        </div>
    </div>
</div>

<?php if (!empty($nilai_akhir['komentar'])): ?>
<hr>
<strong>Komentar Dosen:</strong>
<p><?= nl2br(htmlspecialchars($nilai_akhir['komentar'])) ?></p>
<?php endif; ?>

<?php endif; ?>
</div>
</div>

<!-- ================= NILAI MINGGUAN ================= -->
<?php
$q_mingguan = $conn->prepare("
    SELECT 
        lm.minggu_ke,
        pm.nilai,
        pm.komentar,
        pm.created_at
    FROM penilaian_mingguan pm
    JOIN laporan_mingguan lm 
        ON pm.id_laporan_mingguan = lm.id_laporan_mingguan
    WHERE lm.id_kp = ?
    ORDER BY lm.minggu_ke ASC
");
$q_mingguan->bind_param("i", $kp['id_kp']);
$q_mingguan->execute();
$nilai_mingguan = $q_mingguan->get_result();
?>

<div class="card">
<div class="card-header fw-bold">Penilaian Mingguan</div>
<div class="card-body table-responsive">

<table class="table table-bordered align-middle">
<thead class="table-light">
<tr>
    <th>Minggu</th>
    <th>Nilai</th>
    <th>Komentar</th>
    <th>Tanggal</th>
</tr>
</thead>
<tbody>

<?php if ($nilai_mingguan->num_rows == 0): ?>
<tr>
    <td colspan="4" class="text-center text-muted">
        Belum ada penilaian mingguan.
    </td>
</tr>
<?php else: ?>
<?php while ($n = $nilai_mingguan->fetch_assoc()): ?>
<tr>
    <td>Minggu <?= (int)$n['minggu_ke'] ?></td>
    <td class="fw-bold"><?= (int)$n['nilai'] ?></td>
    <td><?= nl2br(htmlspecialchars($n['komentar'] ?? '-')) ?></td>
    <td><?= date('d M Y', strtotime($n['created_at'])) ?></td>
</tr>
<?php endwhile; ?>
<?php endif; ?>

</tbody>
</table>

</div>
</div>

<?php endif; ?>

</div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
