<?php
require_once "../auth/auth_check.php";
require_role('dosen');
require_once "../config/db.php";

/* ===============================
   KONFIGURASI HALAMAN
   =============================== */
$current_page  = 'laporan_mhs.php';
$page_title    = "Laporan Mingguan Mahasiswa";
$asset_prefix  = "../";
$logout_prefix = "../";

/* ===============================
   AMBIL NIDN DOSEN LOGIN
   =============================== */
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT nidn FROM dosen WHERE id_user = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$dosen = $stmt->get_result()->fetch_assoc();

if (!$dosen) {
    die("Data dosen tidak ditemukan");
}
$nidn = $dosen['nidn'];

/* ===============================
   AMBIL LAPORAN MAHASISWA BIMBINGAN
   =============================== */
$stmt = $conn->prepare("
    SELECT 
        lm.id_laporan_mingguan,
        lm.minggu_ke,
        lm.file_laporan,
        lm.status,
        m.nama AS nama_mhs
    FROM laporan_mingguan lm
    JOIN kp k ON lm.id_kp = k.id_kp
    JOIN mahasiswa m ON k.nim = m.nim
    WHERE 
        k.nidn = ?
        AND k.status = 'Berlangsung'
    ORDER BY lm.minggu_ke DESC
");
$stmt->bind_param("s", $nidn);
$stmt->execute();
$result = $stmt->get_result();

/* ===============================
   HELPER STATUS
   =============================== */
function badgeStatusLaporan($status)
{
    switch ($status) {
        case 'Menunggu':
            return ['warning', 'Menunggu Review'];
        case 'Disetujui':
            return ['success', 'Disetujui'];
        case 'Ditolak':
            return ['danger', 'Ditolak'];
        default:
            return ['secondary', 'Tidak Diketahui'];
    }
}

include "../includes/layout_top.php";
include "../includes/sidebar_dosen.php";
?>

<main class="pc-container">
<?php include "../includes/header.php"; ?>

<div class="pc-content">

<h3 class="fw-bold mb-1">Laporan Mingguan Mahasiswa</h3>
<p class="text-muted mb-4">
  Daftar laporan mingguan mahasiswa bimbingan yang sedang Kerja Praktik.
</p>

<div class="card shadow-sm border-0">
<div class="card-body">

<div class="table-responsive">
<table class="table table-hover align-middle">
<thead class="table-light">
<tr>
  <th>Minggu</th>
  <th>Mahasiswa</th>
  <th>File</th>
  <th class="text-center">Status</th>
  <th class="text-center">Aksi</th>
</tr>
</thead>

<tbody>
<?php if ($result->num_rows > 0): ?>
<?php while ($row = $result->fetch_assoc()): ?>
<?php [$badge, $label] = badgeStatusLaporan($row['status']); ?>

<tr>
<td>Minggu <?= (int)$row['minggu_ke'] ?></td>
<td><?= htmlspecialchars($row['nama_mhs']) ?></td>

<td>
  <a href="<?= $asset_prefix ?>uploads/laporan/<?= htmlspecialchars($row['file_laporan']) ?>" target="_blank">
    Lihat
  </a>
</td>

<td class="text-center">
  <span class="badge bg-<?= $badge ?> px-3 py-2"><?= $label ?></span>
</td>

<td class="text-center">
<?php if ($row['status'] === 'Menunggu'): ?>
  <form method="post" action="penilaian_mingguan.php" class="d-inline">
    <input type="hidden" name="id_laporan_mingguan" value="<?= $row['id_laporan_mingguan'] ?>">
    <input type="hidden" name="aksi" value="setujui">
    <button class="btn btn-sm btn-success">ACC</button>
  </form>

  <form method="post" action="penilaian_mingguan.php" class="d-inline">
    <input type="hidden" name="id_laporan_mingguan" value="<?= $row['id_laporan_mingguan'] ?>">
    <input type="hidden" name="aksi" value="tolak">
    <button class="btn btn-sm btn-danger">Tolak</button>
  </form>
<?php else: ?>
  <span class="text-muted">—</span>
<?php endif; ?>
</td>
</tr>

<?php endwhile; ?>
<?php else: ?>
<tr>
<td colspan="5" class="text-center text-muted py-4">
  Belum ada laporan mingguan
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
