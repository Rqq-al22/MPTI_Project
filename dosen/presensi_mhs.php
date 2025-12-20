<?php
require_once "../auth/auth_check.php";
require_role('dosen');
require_once "../config/db.php";

/* ===============================
   KONFIGURASI
   =============================== */
$current_page  = 'presensi_mhs.php';
$page_title    = "Presensi Mahasiswa Bimbingan";
$asset_prefix  = "../";
$logout_prefix = "../";

/* ===============================
   AMBIL NIDN DOSEN LOGIN
   =============================== */
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT nidn 
    FROM dosen 
    WHERE id_user = ?
    LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$dosen = $stmt->get_result()->fetch_assoc();

if (!$dosen) {
    die("Data dosen tidak ditemukan");
}
$nidn = $dosen['nidn'];

/* ===============================
   AMBIL PRESENSI MAHASISWA BIMBINGAN
   =============================== */
$stmt = $conn->prepare("
    SELECT 
        p.id_presensi,
        p.tanggal,
        p.status AS status_presensi,
        p.bukti_foto,
        p.latitude,
        p.longitude,
        p.validasi,
        p.catatan_dosen,
        m.nim,
        m.nama AS nama_mhs
    FROM presensi p
    JOIN mahasiswa m ON p.nim = m.nim
    JOIN kp k ON m.nim = k.nim
    WHERE 
        k.nidn = ?
        AND k.status = 'Berlangsung'
    ORDER BY p.tanggal DESC
");
$stmt->bind_param("s", $nidn);
$stmt->execute();
$result = $stmt->get_result();

/* ===============================
   HELPER BADGE
   =============================== */
function badgePresensi($status)
{
    return match ($status) {
        'Hadir' => ['success', 'Hadir'],
        'Izin'  => ['warning', 'Izin'],
        'Alpha' => ['danger', 'Alpha'],
        default => ['secondary', '-']
    };
}

function badgeValidasi($status)
{
    return match ($status) {
        'Approve' => ['success', 'Disetujui'],
        'Reject'  => ['danger', 'Ditolak'],
        default   => ['warning', 'Pending']
    };
}

/* ===============================
   LAYOUT
   =============================== */
include "../includes/layout_top.php";
include "../includes/sidebar_dosen.php";
?>

<main class="pc-container">
<?php include "../includes/header.php"; ?>

<div class="pc-content">

<h3 class="fw-bold mb-1">Presensi Mahasiswa Bimbingan</h3>
<p class="text-muted mb-4">
  Dosen dapat memvalidasi presensi mahasiswa Kerja Praktik.
</p>

<div class="card shadow-sm border-0">
<div class="card-body">

<div class="table-responsive">
<table class="table table-hover align-middle">
<thead class="table-light">
<tr>
    <th>Tanggal</th>
    <th>NIM</th>
    <th>Nama</th>
    <th>Status</th>
    <th>Bukti</th>
    <th>Lokasi</th>
    <th>Validasi</th>
    <th class="text-center">Aksi</th>
</tr>
</thead>

<tbody>
<?php if ($result->num_rows > 0): ?>
<?php while ($row = $result->fetch_assoc()): ?>
<?php 
[$badgePres, $labelPres] = badgePresensi($row['status_presensi']);
[$badgeVal, $labelVal]   = badgeValidasi($row['validasi']);
?>

<tr>
<td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
<td><?= htmlspecialchars($row['nim']) ?></td>
<td><?= htmlspecialchars($row['nama_mhs']) ?></td>

<td>
    <span class="badge bg-<?= $badgePres ?>">
        <?= $labelPres ?>
    </span>
</td>

<td>
<?php if ($row['bukti_foto']): ?>
    <a href="<?= $asset_prefix ?>uploads/presensi/<?= $row['bukti_foto'] ?>"
       target="_blank"
       class="btn btn-sm btn-outline-primary">
       Lihat
    </a>
<?php else: ?>
    -
<?php endif; ?>
</td>

<td>
<?php if ($row['latitude'] && $row['longitude']): ?>
    <a href="https://maps.google.com/?q=<?= $row['latitude'] ?>,<?= $row['longitude'] ?>"
       target="_blank">
       Maps
    </a>
<?php else: ?>
    -
<?php endif; ?>
</td>

<td>
    <span class="badge bg-<?= $badgeVal ?>">
        <?= $labelVal ?>
    </span>
</td>

<td class="text-center">
<?php if ($row['validasi'] === 'Pending'): ?>
<form method="post" action="validasi_presensi.php" class="d-inline">
    <input type="hidden" name="id_presensi" value="<?= $row['id_presensi'] ?>">
    <input type="hidden" name="aksi" value="approve">
    <button class="btn btn-sm btn-success">ACC</button>
</form>

<form method="post" action="validasi_presensi.php" class="d-inline">
    <input type="hidden" name="id_presensi" value="<?= $row['id_presensi'] ?>">
    <input type="hidden" name="aksi" value="reject">
    <button class="btn btn-sm btn-danger">Tolak</button>
</form>
<?php else: ?>
    —
<?php endif; ?>
</td>
</tr>

<?php endwhile; ?>
<?php else: ?>
<tr>
<td colspan="8" class="text-center text-muted py-4">
    Belum ada data presensi mahasiswa bimbingan.
</td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>

</div>
</div>

</div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
