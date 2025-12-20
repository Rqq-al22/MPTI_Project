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

/* ================== AMBIL KP AKTIF ================== */
$stmt = $conn->prepare("
    SELECT *
    FROM kp
    WHERE nim = ?
      AND status IN ('Pengajuan','Diterima','Berlangsung','Selesai')
    ORDER BY 
      FIELD(status,'Berlangsung','Diterima','Pengajuan','Selesai','Ditolak'),
      created_at DESC
    LIMIT 1
");
$stmt->bind_param("s", $nim);
$stmt->execute();
$kp = $stmt->get_result()->fetch_assoc();
$stmt->close();

function e($s){ return htmlspecialchars((string)$s); }

$progress = 0;
$total_laporan = 0;
$total_presensi = 0;
$dokumen_akhir_status = "Belum";

/* ================== HITUNG PROGRESS ================== */
if ($kp) {
    $id_kp = (int)$kp['id_kp'];

    // total laporan mingguan
    $q1 = $conn->prepare("SELECT COUNT(*) AS total FROM laporan_mingguan WHERE id_kp = ?");
    $q1->bind_param("i", $id_kp);
    $q1->execute();
    $total_laporan = (int)($q1->get_result()->fetch_assoc()['total'] ?? 0);
    $q1->close();

    // total presensi (yang sudah divalidasi approve)
    // jika sistem Anda belum pakai validasi, ganti WHERE validasi='Approve' jadi COUNT(*)
    $q2 = $conn->prepare("SELECT COUNT(*) AS total FROM presensi WHERE nim = ? AND validasi = 'Approve'");
    $q2->bind_param("s", $nim);
    $q2->execute();
    $total_presensi = (int)($q2->get_result()->fetch_assoc()['total'] ?? 0);
    $q2->close();

    // dokumen akhir
    $q3 = $conn->prepare("SELECT COUNT(*) AS total FROM dokumen_akhir WHERE id_kp = ?");
    $q3->bind_param("i", $id_kp);
    $q3->execute();
    $dok = (int)($q3->get_result()->fetch_assoc()['total'] ?? 0);
    $q3->close();

    $dokumen_akhir_status = ($dok > 0) ? "Sudah" : "Belum";

    // ======= Rumus progress sederhana tapi realistis =======
    // Base menurut status:
    // Pengajuan: 15%
    // Diterima : 35%
    // Berlangsung: 55% + kontribusi laporan/presensi/dok akhir
    // Selesai: 100%
    $status = $kp['status'];

    if ($status === 'Pengajuan') {
        $progress = 15;
    } elseif ($status === 'Diterima') {
        $progress = 35;
    } elseif ($status === 'Berlangsung') {
        // kontribusi komponen:
        // laporan: max +20 (anggap target 8 laporan)
        // presensi: max +15 (anggap target 20 hari approve)
        // dok akhir: +10 jika sudah
        $p_laporan  = min(20, (int)round(($total_laporan / 8) * 20));
        $p_presensi = min(15, (int)round(($total_presensi / 20) * 15));
        $p_dok      = ($dokumen_akhir_status === "Sudah") ? 10 : 0;

        $progress = 55 + $p_laporan + $p_presensi + $p_dok;
        $progress = min(95, $progress); // Berlangsung tidak 100%
    } elseif ($status === 'Selesai') {
        $progress = 100;
    } else {
        $progress = 0;
    }
}
?>

<main class="pc-container">
  <div class="pc-content">

    <h3 class="mb-1">Dashboard Mahasiswa</h3>
    <p class="text-muted mb-4">Ringkasan progres Kerja Praktik Anda.</p>

    <?php if (!$kp): ?>
      <div class="alert alert-warning">
        Anda belum memiliki data KP. Silakan ajukan pada menu <strong>Data KP</strong>.
      </div>
    <?php else: ?>

      <div class="row g-3">
        <div class="col-md-8">
          <div class="card shadow-sm border-0">
            <div class="card-body">
              <h5 class="mb-1">Progres KP</h5>
              <p class="text-muted mb-3">Status saat ini: <strong><?= e($kp['status']) ?></strong></p>

              <div class="progress" style="height: 14px;">
                <div class="progress-bar" role="progressbar"
                     style="width: <?= (int)$progress ?>%;"
                     aria-valuenow="<?= (int)$progress ?>" aria-valuemin="0" aria-valuemax="100">
                </div>
              </div>

              <div class="d-flex justify-content-between mt-2">
                <small class="text-muted">0%</small>
                <small class="text-muted"><strong><?= (int)$progress ?>%</strong></small>
                <small class="text-muted">100%</small>
              </div>

              <hr class="my-3">

              <div class="row text-center">
                <div class="col">
                  <div class="fw-bold"><?= (int)$total_laporan ?></div>
                  <div class="text-muted">Laporan Mingguan</div>
                </div>
                <div class="col">
                  <div class="fw-bold"><?= (int)$total_presensi ?></div>
                  <div class="text-muted">Presensi Approved</div>
                </div>
                <div class="col">
                  <div class="fw-bold"><?= e($dokumen_akhir_status) ?></div>
                  <div class="text-muted">Dokumen Akhir</div>
                </div>
              </div>

            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card shadow-sm border-0">
            <div class="card-body">
              <h6 class="mb-3">Ringkasan KP Aktif</h6>
              <div class="mb-2"><span class="text-muted">Instansi:</span> <strong><?= e($kp['nama_instansi'] ?? '-') ?></strong></div>
              <div class="mb-2"><span class="text-muted">Posisi:</span> <strong><?= e($kp['posisi'] ?? '-') ?></strong></div>
              <div class="mb-2"><span class="text-muted">Dosen Pembimbing (NIDN):</span> <strong><?= e($kp['nidn'] ?? '-') ?></strong></div>
              <div class="mb-2"><span class="text-muted">Periode:</span>
                <strong>
                  <?= $kp['tgl_mulai'] ? date('d M Y', strtotime($kp['tgl_mulai'])) : '-' ?>
                  s/d
                  <?= $kp['tgl_selesai'] ? date('d M Y', strtotime($kp['tgl_selesai'])) : '-' ?>
                </strong>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- TIMELINE -->
      <?php include __DIR__ . "/timeline_kp_component.php"; ?>

    <?php endif; ?>

  </div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
