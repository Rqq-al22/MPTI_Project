<?php
require_once "../auth/auth_check.php";
require_role('mahasiswa');
require_once "../config/db.php";

/* ===============================
   KONFIGURASI HALAMAN
   =============================== */
$current_page  = 'data_kp.php';
$page_title    = "Data Kerja Praktik";
$asset_prefix  = "../";
$logout_prefix = "../";

/* ===============================
   LAYOUT
   =============================== */
include "../includes/layout_top.php";
include "../includes/sidebar_mahasiswa.php";
include "../includes/header.php";

/* ===============================
   SESSION MAHASISWA
   =============================== */
$nim = $_SESSION['nim'] ?? null;
if (!$nim) {
    echo '<div class="alert alert-danger">NIM tidak ditemukan di sesi.</div>';
    include "../includes/layout_bottom.php";
    exit;
}

/* ===============================
   AMBIL KP AKTIF (VALID)
   =============================== */
$stmt = $conn->prepare("
    SELECT
        k.id_kp,
        k.nama_instansi,
        k.alamat_instansi,
        k.kontak_instansi,
        k.pembimbing_instansi,
        k.posisi,
        k.nidn,
        k.tgl_mulai,
        k.tgl_selesai,
        k.status,
        k.surat_diterima_file,
        k.created_at
    FROM kp k
    WHERE k.nim = ?
      AND k.status IN ('Pengajuan','Diterima','Berlangsung')
      AND k.nama_instansi IS NOT NULL
    ORDER BY 
      FIELD(k.status,'Berlangsung','Diterima','Pengajuan'),
      k.created_at DESC
    LIMIT 1
");
$stmt->bind_param("s", $nim);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* ===============================
   HELPER
   =============================== */
function safe($v) {
    return ($v !== null && $v !== '') ? htmlspecialchars($v) : '-';
}

function tgl($d) {
    return $d ? date('d M Y', strtotime($d)) : '-';
}
?>

<main class="pc-container">
<div class="pc-content">

<h3 class="mb-4">Data Kerja Praktik</h3>

<?php if (!$data): ?>

<div class="alert alert-warning">
    <strong>Belum ada Kerja Praktik aktif.</strong><br>
    Silakan ajukan Kerja Praktik terlebih dahulu.
    <br>
    <a href="ajukan_kp.php" class="btn btn-primary mt-3">
        Ajukan Kerja Praktik
    </a>
</div>

<?php else: ?>

<?php if ($data['status'] === 'Berlangsung'): ?>
<div class="alert alert-info">
    <strong>KP Anda sedang Berlangsung.</strong><br>
    Data KP dikunci untuk menjaga konsistensi.
</div>
<?php endif; ?>

<table class="table table-bordered align-middle">

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
    <th>Pembimbing / Mentor Instansi</th>
    <td><?= safe($data['pembimbing_instansi']) ?></td>
</tr>

<tr>
    <th>Posisi KP</th>
    <td><?= safe($data['posisi']) ?></td>
</tr>

<tr>
    <th>Dosen Pembimbing (NIDN)</th>
    <td><?= safe($data['nidn']) ?></td>
</tr>

<tr>
    <th>Periode</th>
    <td>
        <?= tgl($data['tgl_mulai']) ?> s/d <?= tgl($data['tgl_selesai']) ?>
    </td>
</tr>

<tr>
    <th>Status</th>
    <td>
        <?php
        $badge = match ($data['status']) {
            'Pengajuan'   => 'secondary',
            'Diterima'    => 'info',
            'Berlangsung' => 'primary',
            'Selesai'     => 'success',
            default       => 'secondary'
        };
        ?>
        <span class="badge bg-<?= $badge ?>">
            <?= safe($data['status']) ?>
        </span>
    </td>
</tr>

<?php if (!empty($data['surat_diterima_file'])): ?>
<tr>
    <th>Surat Diterima</th>
    <td>
        <a href="../uploads/surat/<?= htmlspecialchars($data['surat_diterima_file']) ?>"
           target="_blank"
           class="btn btn-sm btn-success">
            Lihat Surat
        </a>
    </td>
</tr>
<?php endif; ?>

</table>

<div class="mt-4">
<?php if ($data['status'] !== 'Berlangsung'): ?>
    <a href="ajukan_kp.php?edit=1&id_kp=<?= (int)$data['id_kp'] ?>"
       class="btn btn-warning">
        Edit / Ajukan Ulang KP
    </a>
<?php else: ?>
    <button class="btn btn-secondary" disabled>
        KP Sedang Berlangsung (Edit Dikunci)
    </button>
<?php endif; ?>
</div>

<?php endif; ?>

</div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
