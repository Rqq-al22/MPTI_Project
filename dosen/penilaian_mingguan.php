<?php
require_once "../auth/auth_check.php";
require_role('dosen');
require_once "../config/db.php";

/* ===============================
   KONFIGURASI
   =============================== */
$current_page  = 'penilaian_mingguan.php';
$page_title    = "Penilaian Mingguan";
$asset_prefix  = "../";
$logout_prefix = "../";

/* ===============================
   AMBIL NIDN DOSEN
   =============================== */
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT nidn FROM dosen WHERE id_user = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$dosen = $stmt->get_result()->fetch_assoc();
if (!$dosen) die("Data dosen tidak ditemukan");
$nidn = $dosen['nidn'];

/* ===============================
   PROSES POST
   =============================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $aksi = $_POST['aksi'] ?? '';
    $id_laporan = intval($_POST['id_laporan_mingguan'] ?? 0);

    // UBAH STATUS
    if ($aksi === 'ubah_status') {
        $status_baru = $_POST['status'] ?? 'Menunggu';

        $stmt = $conn->prepare("
            UPDATE laporan_mingguan
            SET status = ?
            WHERE id_laporan_mingguan = ?
        ");
        $stmt->bind_param("si", $status_baru, $id_laporan);
        $stmt->execute();
    }

    // SIMPAN / UPDATE NILAI
    if ($aksi === 'nilai') {
        $nilai = intval($_POST['nilai']);
        $komentar = trim($_POST['komentar'] ?? '');

        $stmt = $conn->prepare("
            INSERT INTO penilaian_mingguan (id_laporan_mingguan, nilai, komentar)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE
              nilai = VALUES(nilai),
              komentar = VALUES(komentar),
              updated_at = CURRENT_TIMESTAMP
        ");
        $stmt->bind_param("iis", $id_laporan, $nilai, $komentar);
        $stmt->execute();
    }

    header("Location: penilaian_mingguan.php");
    exit;
}

/* ===============================
   AMBIL DATA
   =============================== */
$stmt = $conn->prepare("
    SELECT
        lm.id_laporan_mingguan,
        lm.minggu_ke,
        lm.judul,
        lm.file_laporan,
        lm.status AS status_laporan,
        m.nim,
        m.nama AS nama_mhs,
        pm.nilai,
        pm.komentar
    FROM laporan_mingguan lm
    JOIN kp k ON lm.id_kp = k.id_kp
    JOIN mahasiswa m ON k.nim = m.nim
    LEFT JOIN penilaian_mingguan pm
      ON pm.id_laporan_mingguan = lm.id_laporan_mingguan
    WHERE k.nidn = ?
      AND k.status = 'Berlangsung'
    ORDER BY lm.minggu_ke DESC
");
$stmt->bind_param("s", $nidn);
$stmt->execute();
$result = $stmt->get_result();

/* ===============================
   HELPER
   =============================== */
function badgeStatus($status) {
    return match ($status) {
        'Menunggu'  => ['warning','Menunggu'],
        'Disetujui' => ['success','Disetujui'],
        'Ditolak'   => ['danger','Ditolak'],
        default     => ['secondary','-']
    };
}

include "../includes/layout_top.php";
include "../includes/sidebar_dosen.php";
?>

<main class="pc-container">
<?php include "../includes/header.php"; ?>

<div class="pc-content">

<h3 class="fw-bold mb-2">Penilaian Mingguan Mahasiswa</h3>
<p class="text-muted mb-4">
  ACC laporan, ubah status, dan beri nilai progres mingguan mahasiswa bimbingan.
</p>

<div class="card shadow-sm">
<div class="card-body">

<div class="table-responsive">
<table class="table table-hover align-middle">
<thead class="table-light">
<tr>
  <th>Minggu</th>
  <th>Mahasiswa</th>
  <th>Judul</th>
  <th>Status</th>
  <th>File</th>
  <th>Nilai</th>
  <th width="38%">Aksi</th>
</tr>
</thead>

<tbody>
<?php if ($result->num_rows > 0): ?>
<?php while ($row = $result->fetch_assoc()): ?>
<?php [$b,$l] = badgeStatus($row['status_laporan']); ?>

<tr>
<td><?= $row['minggu_ke'] ?></td>

<td>
  <b><?= htmlspecialchars($row['nama_mhs']) ?></b><br>
  <small><?= $row['nim'] ?></small>
</td>

<td><?= htmlspecialchars($row['judul']) ?></td>

<td>
  <span class="badge bg-<?= $b ?>"><?= $l ?></span>
</td>

<td>
  <a target="_blank"
     class="btn btn-sm btn-outline-primary"
     href="<?= $asset_prefix ?>uploads/laporan/<?= $row['file_laporan'] ?>">
     Lihat
  </a>
</td>

<td class="text-center">
  <?= $row['nilai'] !== null ? $row['nilai'] : '-' ?>
</td>

<td>

<!-- FORM UBAH STATUS -->
<form method="post" class="row g-1 mb-1">
  <input type="hidden" name="aksi" value="ubah_status">
  <input type="hidden" name="id_laporan_mingguan" value="<?= $row['id_laporan_mingguan'] ?>">

  <div class="col-4">
    <select name="status" class="form-select form-select-sm">
      <option value="Menunggu"  <?= $row['status_laporan']=='Menunggu'?'selected':'' ?>>Menunggu</option>
      <option value="Disetujui" <?= $row['status_laporan']=='Disetujui'?'selected':'' ?>>Disetujui</option>
      <option value="Ditolak"   <?= $row['status_laporan']=='Ditolak'?'selected':'' ?>>Ditolak</option>
    </select>
  </div>

  <div class="col-3 d-grid">
    <button class="btn btn-warning btn-sm">Edit</button>
  </div>
</form>

<!-- FORM NILAI (HANYA JIKA DISETUJUI) -->
<?php if ($row['status_laporan'] === 'Disetujui'): ?>
<form method="post" class="row g-1">
  <input type="hidden" name="aksi" value="nilai">
  <input type="hidden" name="id_laporan_mingguan" value="<?= $row['id_laporan_mingguan'] ?>">

  <div class="col-3">
    <input type="number"
           name="nilai"
           min="0" max="100"
           value="<?= $row['nilai'] ?? '' ?>"
           class="form-control form-control-sm"
           required>
  </div>

  <div class="col-5">
    <input type="text"
           name="komentar"
           value="<?= htmlspecialchars($row['komentar'] ?? '') ?>"
           class="form-control form-control-sm"
           placeholder="Komentar">
  </div>

  <div class="col-4 d-grid">
    <button class="btn btn-primary btn-sm">
      <?= $row['nilai'] !== null ? 'Update Nilai' : 'Simpan Nilai' ?>
    </button>
  </div>
</form>
<?php endif; ?>

</td>
</tr>

<?php endwhile; ?>
<?php else: ?>
<tr>
<td colspan="7" class="text-center text-muted py-4">
  Belum ada laporan mingguan.
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
