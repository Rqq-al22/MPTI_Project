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

/* ===============================
   VALIDASI SESSION
   =============================== */
$nim = $_SESSION['nim'] ?? null;
if (!$nim) {
    echo '<div class="alert alert-danger">Session mahasiswa tidak valid.</div>';
    include "../includes/layout_bottom.php";
    exit;
}

/* ===============================
   HELPER
   =============================== */
function safe($v){
    return ($v !== null && $v !== '') ? htmlspecialchars($v) : '-';
}
function tgl($d){
    return $d ? date('d M Y', strtotime($d)) : '-';
}

/* ===============================
   AMBIL DATA KP + DOSEN
   =============================== */
$stmt = $conn->prepare("
    SELECT 
        kp.*,
        d.nama AS nama_dosen
    FROM kp
    LEFT JOIN dosen d ON kp.nidn = d.nidn
    WHERE kp.nim = ?
    ORDER BY kp.created_at DESC
    LIMIT 1
");
$stmt->bind_param("s", $nim);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<main class="pc-container">
<div class="pc-content">

<h3 class="mb-4">Data Kerja Praktik</h3>

<?php if (!$data): ?>
  <div class="alert alert-warning">
    <strong>Belum ada pengajuan Kerja Praktik.</strong><br>
    <a href="ajukan_kp.php" class="btn btn-primary mt-3">Ajukan Kerja Praktik</a>
  </div>
<?php else: ?>

<?php
/* ===============================
   STATUS
   =============================== */
$status = $data['status'];

$badgeMap = [
    'Pengajuan'   => ['secondary', 'Pengajuan (Belum di-ACC)'],
    'Diterima'    => ['info',      'Diterima'],
    'Berlangsung' => ['primary',   'Berlangsung'],
    'Selesai'     => ['success',   'Selesai'],
    'Ditolak'     => ['danger',    'Ditolak']
];

[$badgeClass, $badgeText] = $badgeMap[$status] ?? ['secondary', 'Tidak diketahui'];

$locked = in_array($status, ['Berlangsung','Selesai']);
?>

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

  <!-- 🔴 DOSEN PEMBIMBING -->
  <tr>
    <th>Dosen Pembimbing</th>
    <td>
      <?php if (!empty($data['nidn']) && !empty($data['nama_dosen'])): ?>
        <?= htmlspecialchars($data['nama_dosen']) ?>
        <span class="text-muted">(NIDN: <?= htmlspecialchars($data['nidn']) ?>)</span>
      <?php else: ?>
        <span class="text-muted">
          Belum ditetapkan oleh program studi
        </span>
      <?php endif; ?>
    </td>
  </tr>

  <tr>
    <th>Periode KP</th>
    <td><?= tgl($data['tgl_mulai']) ?> s/d <?= tgl($data['tgl_selesai']) ?></td>
  </tr>

  <tr>
    <th>Status</th>
    <td>
      <span class="badge bg-<?= $badgeClass ?>">
        <?= $badgeText ?>
      </span>
    </td>
  </tr>

  <tr>
    <th>Surat Penerimaan KP</th>
    <td>
      <?php if (!empty($data['surat_diterima_file'])): ?>
        <a href="../uploads/surat/<?= htmlspecialchars($data['surat_diterima_file']) ?>"
           target="_blank"
           class="btn btn-sm btn-success">
          Lihat Surat Penerimaan
        </a>
      <?php else: ?>
        <span class="text-muted">Belum diunggah</span>
      <?php endif; ?>
    </td>
  </tr>

  <?php if (!empty($data['catatan_admin'])): ?>
  <tr>
    <th>Catatan Admin</th>
    <td><?= nl2br(htmlspecialchars($data['catatan_admin'])) ?></td>
  </tr>
  <?php endif; ?>
</table>

<div class="mt-3">
  <?php if ($locked): ?>
    <button class="btn btn-warning" disabled>
      Edit / Ajukan Ulang KP
    </button>
  <?php else: ?>
    <a href="ajukan_kp.php?edit=1" class="btn btn-warning">
      Edit / Ajukan Ulang KP
    </a>
  <?php endif; ?>
</div>

<?php endif; ?>

</div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
