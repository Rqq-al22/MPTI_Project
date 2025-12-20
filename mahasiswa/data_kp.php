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
if (!$nim) {
    echo '<div class="alert alert-danger">NIM tidak ditemukan di session.</div>';
    include "../includes/layout_bottom.php";
    exit;
}

function safe($v){ return ($v !== null && $v !== '') ? htmlspecialchars($v) : '-'; }
function tgl($d){ return $d ? date('d M Y', strtotime($d)) : '-'; }

/* KP AKTIF: Pengajuan/Diterima/Berlangsung (kalau tidak ada, ambil terakhir) */
$stmt = $conn->prepare("
    SELECT *
    FROM kp
    WHERE nim = ?
    ORDER BY 
      (status IN ('Pengajuan','Diterima','Berlangsung')) DESC,
      created_at DESC
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
      $badge = match ($data['status']) {
          'Pengajuan'   => 'secondary',
          'Diterima'    => 'info',
          'Berlangsung' => 'primary',
          'Selesai'     => 'success',
          'Ditolak'     => 'danger',
          default       => 'secondary'
      };

      $locked = ($data['status'] === 'Berlangsung'); // LOCK
      ?>

      <?php if ($data['status'] === 'Diterima'): ?>
        <div class="alert alert-info">
          KP Anda sudah <strong>Diterima</strong>. Silakan mulai pelaksanaan dan isi presensi/laporan mingguan.
        </div>
      <?php elseif ($data['status'] === 'Berlangsung'): ?>
        <div class="alert alert-primary">
          KP Anda sedang <strong>Berlangsung</strong>. Data KP dikunci untuk menjaga konsistensi.
        </div>
      <?php endif; ?>

      <table class="table table-bordered align-middle">
        <tr><th width="30%">Instansi</th><td><?= safe($data['nama_instansi']) ?></td></tr>
        <tr><th>Alamat Instansi</th><td><?= safe($data['alamat_instansi']) ?></td></tr>
        <tr><th>Kontak Instansi</th><td><?= safe($data['kontak_instansi']) ?></td></tr>
        <tr><th>Email Instansi</th><td><?= safe($data['email_instansi'] ?? '-') ?></td></tr>
        <tr><th>Pembimbing / Mentor Instansi</th><td><?= safe($data['pembimbing_instansi']) ?></td></tr>
        <tr><th>Posisi KP</th><td><?= safe($data['posisi']) ?></td></tr>
        <tr><th>Dosen Pembimbing (NIDN)</th><td><?= safe($data['nidn']) ?></td></tr>
        <tr><th>Periode</th><td><?= tgl($data['tgl_mulai']) ?> s/d <?= tgl($data['tgl_selesai']) ?></td></tr>
        <tr>
          <th>Status</th>
          <td><span class="badge bg-<?= $badge ?>"><?= safe($data['status']) ?></span></td>
        </tr>

        <?php if (!empty($data['surat_diterima_file'])): ?>
        <tr>
          <th>Surat Diterima</th>
          <td>
            <a href="../uploads/surat/<?= htmlspecialchars($data['surat_diterima_file']) ?>"
               target="_blank" class="btn btn-sm btn-primary">Lihat Surat</a>
          </td>
        </tr>
        <?php endif; ?>
      </table>

      <div class="mt-3">
        <?php if ($locked): ?>
          <button class="btn btn-warning" disabled title="KP Berlangsung dikunci">
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
