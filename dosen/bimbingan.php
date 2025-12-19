<?php
require_once "../auth/auth_check.php";
require_role('dosen');
require_once "../config/db.php";

/* ===============================
   KONFIGURASI HALAMAN
   =============================== */
$current_page  = 'bimbingan.php';
$page_title    = "Mahasiswa Bimbingan";
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
    die("Data dosen tidak ditemukan.");
}
$nidn = $dosen['nidn'];

/* ===============================
   AMBIL MAHASISWA BIMBINGAN
   =============================== */
$stmt = $conn->prepare("
    SELECT 
        k.id_kp,
        k.nim,
        m.nama AS nama_mhs,
        k.nama_instansi,
        UPPER(TRIM(k.status)) AS status
    FROM kp k
    JOIN mahasiswa m ON k.nim = m.nim
    WHERE k.nidn = ?
    ORDER BY k.created_at DESC
");
$stmt->bind_param("s", $nidn);
$stmt->execute();
$result = $stmt->get_result();

/* ===============================
   HELPER STATUS BADGE
   =============================== */
function badgeStatusKP($status)
{
    switch ($status) {
        case 'PENGAJUAN':
            return ['warning', 'Menunggu ACC Dosen'];
        case 'BERLANGSUNG':
            return ['success', 'KP Berlangsung'];
        case 'SELESAI':
            return ['primary', 'Selesai'];
        case 'DITOLAK':
            return ['danger', 'Ditolak'];
        default:
            return ['dark', 'Belum Diproses'];
    }
}

include "../includes/layout_top.php";
include "../includes/sidebar_dosen.php";
?>

<main class="pc-container">
<?php include "../includes/header.php"; ?>

<div class="pc-content">

<h3 class="fw-bold mb-2">Mahasiswa Bimbingan</h3>
<p class="text-muted mb-4">
  Daftar mahasiswa Kerja Praktik yang menunggu persetujuan dosen.
</p>

<div class="card shadow-sm border-0">
<div class="card-body">

<div class="table-responsive">
<table class="table table-hover align-middle">
<thead class="table-light">
<tr>
  <th>NIM</th>
  <th>Nama Mahasiswa</th>
  <th>Instansi</th>
  <th class="text-center">Status KP</th>
  <th class="text-center">Aksi</th>
</tr>
</thead>

<tbody>
<?php if ($result->num_rows > 0): ?>
<?php while ($row = $result->fetch_assoc()): ?>
<?php [$badge, $label] = badgeStatusKP($row['status']); ?>

<tr>
<td><?= htmlspecialchars($row['nim']) ?></td>
<td><?= htmlspecialchars($row['nama_mhs']) ?></td>
<td><?= htmlspecialchars($row['nama_instansi']) ?></td>

<td class="text-center">
  <span class="badge bg-<?= $badge ?> px-3 py-2">
    <?= $label ?>
  </span>
</td>

<td class="text-center">

<?php if (!in_array($row['status'], ['BERLANGSUNG', 'SELESAI', 'DITOLAK'])): ?>
  <form method="post" action="bimbingan_action.php" class="d-inline">
    <input type="hidden" name="id_kp" value="<?= $row['id_kp'] ?>">
    <input type="hidden" name="aksi" value="setujui">
    <button class="btn btn-sm btn-success">
      ACC KP
    </button>
  </form>

  <form method="post" action="bimbingan_action.php" class="d-inline">
    <input type="hidden" name="id_kp" value="<?= $row['id_kp'] ?>">
    <input type="hidden" name="aksi" value="tolak">
    <button class="btn btn-sm btn-danger">
      Tolak
    </button>
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
  Tidak ada mahasiswa bimbingan
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
