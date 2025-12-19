<?php
require_once "../auth/auth_check.php";
require_role('mahasiswa');
require_once "../config/db.php";

$current_page  = 'data_kp.php';
$page_title    = "Data Kerja Praktik";
$asset_prefix  = "../";
$logout_prefix = "../";

include "../includes/layout_top.php";
include "../includes/sidebar_mahasiswa.php";
include "../includes/header.php";

$nim = $_SESSION['nim'] ?? null;
?>

<main class="pc-container">
<div class="pc-content">

<h3 class="mb-4">Data Kerja Praktik</h3>

<?php
if (!$nim) {
    echo '<div class="alert alert-danger">NIM tidak ditemukan.</div>';
    include "../includes/layout_bottom.php";
    exit;
}

$stmt = $conn->prepare("
    SELECT * FROM kp
    WHERE nim = ?
    ORDER BY created_at DESC
    LIMIT 1
");
$stmt->bind_param("s", $nim);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

function safe($v) {
    return $v ? htmlspecialchars($v) : '-';
}

if (!$data):
?>

<div class="alert alert-warning">
    <strong>Belum ada pengajuan KP.</strong>
    <br>
    <a href="ajukan_kp.php" class="btn btn-primary mt-3">
        Ajukan Kerja Praktik
    </a>
</div>

<?php else: ?>

<table class="table table-bordered kp-table">

<tr>
    <th width="30%">Instansi</th>
    <td><?= safe($data['nama_instansi']) ?></td>
</tr>

<tr>
    <th>Alamat Instansi</th>
    <td><?= safe($data['alamat_instansi']) ?></td>
</tr>

<tr>
    <th>Kontak Instansi</th>
    <td><?= safe($data['kontak_instansi']) ?></td>
</tr>

<tr>
    <th>Posisi KP</th>
    <td><?= safe($data['posisi']) ?></td>
</tr>

<tr>
    <th>Pembimbing Instansi</th>
    <td><?= safe($data['pembimbing_instansi']) ?></td>
</tr>

<tr>
    <th>Dosen Pembimbing</th>
    <td><?= safe($data['nidn']) ?></td>
</tr>

<tr>
    <th>Periode</th>
    <td>
        <?= date('d M Y', strtotime($data['tgl_mulai'])) ?>
        s/d
        <?= date('d M Y', strtotime($data['tgl_selesai'])) ?>
    </td>
</tr>

<tr>
    <th>Status</th>
    <td>
        <span class="badge bg-secondary">
            <?= safe($data['status']) ?>
        </span>
    </td>
</tr>

<?php if ($data['surat_diterima_file']): ?>
<tr>
    <th>Surat Diterima</th>
    <td>
        <a href="../uploads/surat/<?= safe($data['surat_diterima_file']) ?>"
           class="btn btn-sm btn-primary" target="_blank">
            Lihat Surat
        </a>
    </td>
</tr>
<?php endif; ?>

</table>

<a href="ajukan_kp.php?edit=1" class="btn btn-warning mt-3">
    Edit / Ajukan Ulang KP
</a>

<?php endif; ?>

</div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
