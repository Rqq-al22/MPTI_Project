<?php
require_once "../auth/auth_check.php";
require_role('mahasiswa');
require_once "../config/db.php";

/* ================== KONFIG ================== */
$current_page  = 'dashboard_mahasiswa.php';
$page_title    = "Dashboard Mahasiswa";
$asset_prefix  = "../";
$logout_prefix = "../";

/* ================== LAYOUT ================== */
include "../includes/layout_top.php";
include "../includes/sidebar_mahasiswa.php";
include "../includes/header.php";

/* ================== SESSION ================== */
$nim = $_SESSION['nim'] ?? null;
if (!$nim) {
    echo '<div class="alert alert-danger">NIM tidak ditemukan di sesi.</div>';
    include "../includes/layout_bottom.php";
    exit;
}

/* ================== AMBIL KP + DOSEN ================== */
$stmt = $conn->prepare("
    SELECT 
        kp.*,
        d.nama AS nama_dosen
    FROM kp
    LEFT JOIN dosen d ON kp.nidn = d.nidn
    WHERE kp.nim = ?
      AND kp.status IN ('Pengajuan','Diterima','Berlangsung','Selesai')
    ORDER BY kp.created_at DESC
    LIMIT 1
");
$stmt->bind_param("s", $nim);
$stmt->execute();
$kp = $stmt->get_result()->fetch_assoc();
$stmt->close();

function e($v){ return htmlspecialchars((string)$v); }

/* ================== DEFAULT ================== */
$progress = 0;
$total_laporan = 0;
$total_presensi = 0;
$dokumen_akhir_status = "Belum";

/* ================== HITUNG PROGRESS ================== */
if ($kp) {
    $id_kp = (int)$kp['id_kp'];

    /* ---- Aktivitas ---- */
    $q1 = $conn->prepare("SELECT COUNT(*) total FROM laporan_mingguan WHERE id_kp = ?");
    $q1->bind_param("i", $id_kp);
    $q1->execute();
    $total_laporan = (int)$q1->get_result()->fetch_assoc()['total'];
    $q1->close();

    $q2 = $conn->prepare("SELECT COUNT(*) total FROM presensi WHERE nim = ? AND validasi = 'Approve'");
    $q2->bind_param("s", $nim);
    $q2->execute();
    $total_presensi = (int)$q2->get_result()->fetch_assoc()['total'];
    $q2->close();

    $q3 = $conn->prepare("SELECT COUNT(*) total FROM dokumen_akhir WHERE id_kp = ?");
    $q3->bind_param("i", $id_kp);
    $q3->execute();
    $dok = (int)$q3->get_result()->fetch_assoc()['total'];
    $q3->close();
    $dokumen_akhir_status = ($dok > 0) ? "Sudah" : "Belum";

    /* ---- 1. Status (40%) ---- */
    $statusScore = match ($kp['status']) {
        'Pengajuan'   => 10,
        'Diterima'    => 20,
        'Berlangsung' => 30,
        'Selesai'     => 40,
        default       => 0
    };

    /* ---- 2. Waktu (40%) ---- */
    $timeScore = 0;
    if ($kp['tgl_mulai'] && $kp['tgl_selesai']) {
        $start = strtotime($kp['tgl_mulai']);
        $end   = strtotime($kp['tgl_selesai']);
        $now   = time();

        if ($now > $start) {
            $elapsed = min($now, $end) - $start;
            $total   = max(1, $end - $start);
            $timeScore = min(40, round(($elapsed / $total) * 40));
        }
    }

    /* ---- 3. Aktivitas (20%) ---- */
    $activityScore  = min(10, $total_laporan * 2);      // max 10
    $activityScore += min(7,  $total_presensi);         // max 7
    $activityScore += ($dokumen_akhir_status === "Sudah") ? 3 : 0;

    $progress = min(100, $statusScore + $timeScore + $activityScore);
}
?>

<main class="pc-container">
<div class="pc-content">

<h3 class="mb-1">Dashboard Mahasiswa</h3>
<p class="text-muted mb-4">Ringkasan progres Kerja Praktik Anda.</p>

<?php if (!$kp): ?>
  <div class="alert alert-warning">
    Anda belum memiliki data Kerja Praktik.
  </div>
<?php else: ?>

<div class="row g-3">
  <!-- PROGRESS -->
  <div class="col-md-8">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <h5 class="mb-1">Progres Kerja Praktik</h5>
        <p class="text-muted mb-3">Status saat ini: <strong><?= e($kp['status']) ?></strong></p>

        <div class="progress" style="height:14px;">
          <div class="progress-bar"
               style="width: <?= $progress ?>%;"
               aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100">
          </div>
        </div>

        <div class="d-flex justify-content-between mt-2">
          <small class="text-muted">0%</small>
          <small class="text-muted"><strong><?= $progress ?>%</strong></small>
          <small class="text-muted">100%</small>
        </div>

        <hr>

        <div class="row text-center">
          <div class="col">
            <div class="fw-bold"><?= $total_laporan ?></div>
            <div class="text-muted">Laporan Mingguan</div>
          </div>
          <div class="col">
            <div class="fw-bold"><?= $total_presensi ?></div>
            <div class="text-muted">Presensi Disetujui</div>
          </div>
          <div class="col">
            <div class="fw-bold"><?= $dokumen_akhir_status ?></div>
            <div class="text-muted">Dokumen Akhir</div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- RINGKASAN -->
  <div class="col-md-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <h6 class="mb-3">Ringkasan KP Aktif</h6>

        <div class="mb-2">
          <span class="text-muted">Instansi:</span><br>
          <strong><?= e($kp['nama_instansi']) ?></strong>
        </div>

        <div class="mb-2">
          <span class="text-muted">Posisi:</span><br>
          <strong><?= e($kp['posisi']) ?></strong>
        </div>

        <div class="mb-2">
          <span class="text-muted">Dosen Pembimbing:</span><br>
          <?php if ($kp['nama_dosen']): ?>
            <strong><?= e($kp['nama_dosen']) ?></strong>
            <div class="text-muted">NIDN: <?= e($kp['nidn']) ?></div>
          <?php else: ?>
            <span class="text-muted">Belum ditetapkan</span>
          <?php endif; ?>
        </div>

        <div class="mb-2">
          <span class="text-muted">Periode:</span><br>
          <strong>
            <?= date('d M Y', strtotime($kp['tgl_mulai'])) ?>
            s/d
            <?= date('d M Y', strtotime($kp['tgl_selesai'])) ?>
          </strong>
        </div>

      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . "/timeline_kp_component.php"; ?>

<?php endif; ?>

</div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
