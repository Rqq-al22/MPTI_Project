<?php
require_once __DIR__ . "/../auth/auth_check.php";
require_role('mahasiswa');
require_once __DIR__ . "/../config/db.php";

/* ================== KONFIG ================== */
$current_page  = 'dashboard_mahasiswa.php';
$page_title    = "Dashboard Mahasiswa";
$asset_prefix  = "../";
$logout_prefix = "../";

/* ================== UTIL ================== */
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function has_table(mysqli $conn, string $table): bool {
    $sql = "SELECT COUNT(*) c
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?";
    $st = $conn->prepare($sql);
    $st->bind_param("s", $table);
    $st->execute();
    $row = $st->get_result()->fetch_assoc();
    $st->close();
    return (int)($row['c'] ?? 0) > 0;
}

function has_column(mysqli $conn, string $table, string $col): bool {
    $sql = "SELECT COUNT(*) c
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND COLUMN_NAME = ?";
    $st = $conn->prepare($sql);
    $st->bind_param("ss", $table, $col);
    $st->execute();
    $row = $st->get_result()->fetch_assoc();
    $st->close();
    return (int)($row['c'] ?? 0) > 0;
}

function pick_col(mysqli $conn, string $table, array $candidates, ?string $fallback = null): ?string {
    foreach ($candidates as $c) {
        if (has_column($conn, $table, $c)) return $c;
    }
    return $fallback;
}

function q_count(mysqli $conn, string $sql, string $types = "", array $params = []): int {
    $st = $conn->prepare($sql);
    if ($types !== "") $st->bind_param($types, ...$params);
    $st->execute();
    $row = $st->get_result()->fetch_row();
    $st->close();
    return (int)($row[0] ?? 0);
}

/* ================== SESSION ================== */
$nim = $_SESSION['nim'] ?? null;
if (!$nim) {
    include __DIR__ . "/../includes/layout_top.php";
    include __DIR__ . "/../includes/sidebar_mahasiswa.php";
    ?>
    <main class="pc-container">
      <?php include __DIR__ . "/../includes/header.php"; ?>
      <div class="pc-content">
        <div class="alert alert-danger">NIM tidak ditemukan di sesi.</div>
      </div>
    </main>
    <?php
    include __DIR__ . "/../includes/layout_bottom.php";
    exit;
}

/* ================== DETEKSI TABLE & KOLOM ================== */
$hasMahasiswa   = has_table($conn, 'mahasiswa');
$hasUsers       = has_table($conn, 'users');
$hasKP          = has_table($conn, 'kp');
$hasDosen       = has_table($conn, 'dosen');
$hasPresensi    = has_table($conn, 'presensi');
$hasLaporan     = has_table($conn, 'laporan_mingguan');
$hasDokAkhir    = has_table($conn, 'dokumen_akhir');
$hasBimbingan   = has_table($conn, 'bimbingan');
$hasPengumuman  = has_table($conn, 'pengumuman');
$hasPenilaian   = has_table($conn, 'penilaian_akhir');

/* mahasiswa cols */
$mNamaCol      = $hasMahasiswa ? pick_col($conn,'mahasiswa',['nama','nama_mahasiswa'],'nama') : null;
$mJurusanCol   = $hasMahasiswa ? pick_col($conn,'mahasiswa',['jurusan'],'jurusan') : null;
$mAngkatanCol  = $hasMahasiswa ? pick_col($conn,'mahasiswa',['angkatan'],'angkatan') : null;
$mIdUserCol    = $hasMahasiswa ? pick_col($conn,'mahasiswa',['id_user','user_id'],'id_user') : null;

/* kp cols */
$kpIdCol       = $hasKP ? pick_col($conn,'kp',['id_kp','id'],'id_kp') : null;
$kpNimCol      = $hasKP ? pick_col($conn,'kp',['nim'],'nim') : null;
$kpNidnCol     = $hasKP ? pick_col($conn,'kp',['nidn'],'nidn') : null;
$kpInstansiCol = $hasKP ? pick_col($conn,'kp',['nama_instansi','instansi'],'nama_instansi') : null;
$kpPosisiCol   = $hasKP ? pick_col($conn,'kp',['posisi','jabatan'],'posisi') : null;
$kpStatusCol   = $hasKP ? pick_col($conn,'kp',['status'],'status') : null;
$kpMulaiCol    = $hasKP ? pick_col($conn,'kp',['tgl_mulai','tanggal_mulai','mulai'],'tgl_mulai') : null;
$kpSelesaiCol  = $hasKP ? pick_col($conn,'kp',['tgl_selesai','tanggal_selesai','selesai'],'tgl_selesai') : null;
$kpCreatedCol  = $hasKP ? pick_col($conn,'kp',['created_at','tanggal_pengajuan','tgl_pengajuan'],'created_at') : null;

/* dosen cols */
$dNamaCol      = $hasDosen ? pick_col($conn,'dosen',['nama','nama_dosen'],'nama') : null;
$dNidnCol      = $hasDosen ? pick_col($conn,'dosen',['nidn'],'nidn') : null;
$dPeminatanCol = $hasDosen ? pick_col($conn,'dosen',['peminatan','keahlian'],null) : null;

/* presensi cols */
$pTanggalCol   = $hasPresensi ? pick_col($conn,'presensi',['tanggal','tgl','created_at'],'tanggal') : null;
$pStatusCol    = $hasPresensi ? pick_col($conn,'presensi',['status'], 'status') : null;
$pValidCol     = $hasPresensi ? pick_col($conn,'presensi',['validasi','status_validasi','verifikasi'], null) : null;
$pLatCol       = $hasPresensi ? pick_col($conn,'presensi',['latitude','lat'], null) : null;
$pLngCol       = $hasPresensi ? pick_col($conn,'presensi',['longitude','lng','long'], null) : null;
$usePresensiIdKp = $hasPresensi && has_column($conn,'presensi','id_kp');

/* laporan cols */
$lmIdCol       = $hasLaporan ? pick_col($conn,'laporan_mingguan',['id_laporan_mingguan','id_laporan','id'],'id_laporan_mingguan') : null;
$lmKpCol       = $hasLaporan ? pick_col($conn,'laporan_mingguan',['id_kp','kp_id'],'id_kp') : null;
$lmMingguCol   = $hasLaporan ? pick_col($conn,'laporan_mingguan',['minggu_ke','minggu','pekan_ke'], null) : null;
$lmJudulCol    = $hasLaporan ? pick_col($conn,'laporan_mingguan',['judul','title'], null) : null;
$lmStatusCol   = $hasLaporan ? pick_col($conn,'laporan_mingguan',['status','status_laporan','status_verifikasi','verifikasi'], null) : null;
$lmTanggalCol  = $hasLaporan ? pick_col($conn,'laporan_mingguan',['tanggal_upload','tgl_upload','tanggal','created_at'], null) : null;
$lmFileCol     = $hasLaporan ? pick_col($conn,'laporan_mingguan',['file_laporan','file','lampiran','berkas','dokumen','nama_file','path_file'], null) : null;

/* dok akhir cols */
$daKpCol       = $hasDokAkhir ? pick_col($conn,'dokumen_akhir',['id_kp','kp_id'],'id_kp') : null;

/* bimbingan cols */
$bNimCol       = $hasBimbingan ? pick_col($conn,'bimbingan',['nim'],'nim') : null;
$bTanggalCol   = $hasBimbingan ? pick_col($conn,'bimbingan',['tanggal','tgl','created_at'], null) : null;
$bTopikCol     = $hasBimbingan ? pick_col($conn,'bimbingan',['topik','judul'], null) : null;
$bStatusCol    = $hasBimbingan ? pick_col($conn,'bimbingan',['status'], null) : null;

/* pengumuman cols */
$pgJudulCol    = $hasPengumuman ? pick_col($conn,'pengumuman',['judul','title'], null) : null;
$pgDateCol     = $hasPengumuman ? pick_col($conn,'pengumuman',['created_at','tanggal','tgl'], null) : null;

/* ================== AMBIL PROFIL MAHASISWA ================== */
$profilMhs = [
  'nama' => '',
  'jurusan' => '',
  'angkatan' => '',
  'foto' => null,
  'username' => ($_SESSION['username'] ?? '')
];

if ($hasMahasiswa) {
    $cols = ["m.$mNamaCol AS nama"];
    if ($mJurusanCol)  $cols[] = "m.$mJurusanCol AS jurusan";
    if ($mAngkatanCol) $cols[] = "m.$mAngkatanCol AS angkatan";
    if ($mIdUserCol)   $cols[] = "m.$mIdUserCol AS id_user";

    $sql = "SELECT " . implode(", ", $cols) . " FROM mahasiswa m WHERE m.nim=? LIMIT 1";
    $st = $conn->prepare($sql);
    $st->bind_param("s", $nim);
    $st->execute();
    $row = $st->get_result()->fetch_assoc();
    $st->close();

    if ($row) {
        $profilMhs['nama'] = $row['nama'] ?? '';
        $profilMhs['jurusan'] = $row['jurusan'] ?? '';
        $profilMhs['angkatan'] = $row['angkatan'] ?? '';
        $id_user_mhs = (int)($row['id_user'] ?? 0);

        if ($hasUsers && $id_user_mhs > 0 && has_column($conn,'users','foto')) {
            $st = $conn->prepare("SELECT username, foto FROM users WHERE id_user=? LIMIT 1");
            $st->bind_param("i", $id_user_mhs);
            $st->execute();
            $u = $st->get_result()->fetch_assoc();
            $st->close();
            if (!empty($u['username'])) $profilMhs['username'] = $u['username'];
            $profilMhs['foto'] = $u['foto'] ?? null;
        }
    }
}

$fotoRel = !empty($profilMhs['foto'])
  ? "../uploads/profile/" . $profilMhs['foto']
  : "../uploads/profile/default.png";

/* ================== AMBIL KP TERAKHIR + DOSEN ================== */
$kp = null;
if ($hasKP && $kpNimCol && $kpStatusCol) {
    $select = [];
    $select[] = "kp.$kpIdCol AS id_kp";
    $select[] = "kp.$kpNimCol AS nim";
    $select[] = "kp.$kpStatusCol AS status";
    if ($kpInstansiCol) $select[] = "kp.$kpInstansiCol AS nama_instansi";
    if ($kpPosisiCol)   $select[] = "kp.$kpPosisiCol AS posisi";
    if ($kpNidnCol)     $select[] = "kp.$kpNidnCol AS nidn";
    if ($kpMulaiCol)    $select[] = "kp.$kpMulaiCol AS tgl_mulai";
    if ($kpSelesaiCol)  $select[] = "kp.$kpSelesaiCol AS tgl_selesai";
    if ($kpCreatedCol)  $select[] = "kp.$kpCreatedCol AS created_at";

    $dosenJoin = "";
    if ($hasDosen && $kpNidnCol && $dNidnCol && $dNamaCol) {
        $dosenJoin = "LEFT JOIN dosen d ON kp.$kpNidnCol = d.$dNidnCol";
        $select[]  = "d.$dNamaCol AS nama_dosen";
        if ($dPeminatanCol) $select[] = "d.$dPeminatanCol AS peminatan_dosen";
    } else {
        $select[] = "NULL AS nama_dosen";
        $select[] = "NULL AS peminatan_dosen";
    }

    // order aman
    $orderBy = $kpCreatedCol ? "kp.$kpCreatedCol DESC" : "kp.$kpIdCol DESC";

    $sql = "
      SELECT " . implode(", ", $select) . "
      FROM kp
      $dosenJoin
      WHERE kp.$kpNimCol = ?
        AND kp.$kpStatusCol IN ('Pengajuan','Pengajuan KP','Diterima','Berlangsung','Selesai','Menunggu')
      ORDER BY $orderBy
      LIMIT 1
    ";
    $st = $conn->prepare($sql);
    $st->bind_param("s", $nim);
    $st->execute();
    $kp = $st->get_result()->fetch_assoc();
    $st->close();
}

/* ================== DEFAULT ================== */
$progress = 0;
$total_laporan = 0;
$total_presensi_approve = 0;
$total_presensi_total = 0;
$dokumen_akhir_status = "Belum";
$nilai_akhir_status = "Belum";
$today = date('Y-m-d');

$rowsLaporanTerbaru = [];
$rowsPresensiTerbaru = [];
$rowsBimbingan = [];
$rowsPengumuman = [];

/* ================== HITUNG & LIST ================== */
if ($kp && !empty($kp['id_kp'])) {
    $id_kp = (int)$kp['id_kp'];

    // laporan count + list
    if ($hasLaporan && $lmKpCol) {
        $total_laporan = q_count($conn, "SELECT COUNT(*) FROM laporan_mingguan WHERE $lmKpCol = ?", "i", [$id_kp]);

        $sel = [];
        $sel[] = ($lmIdCol ? "$lmIdCol AS id_laporan" : "0 AS id_laporan");
        $sel[] = ($lmMingguCol ? "$lmMingguCol AS minggu_ke" : "NULL AS minggu_ke");
        $sel[] = ($lmJudulCol ? "$lmJudulCol AS judul" : "NULL AS judul");
        $sel[] = ($lmStatusCol ? "$lmStatusCol AS status" : "NULL AS status");
        $sel[] = ($lmTanggalCol ? "$lmTanggalCol AS tanggal_upload" : "NULL AS tanggal_upload");
        $sel[] = ($lmFileCol ? "$lmFileCol AS file_laporan" : "NULL AS file_laporan");

        $orderLm = $lmTanggalCol ? "$lmTanggalCol DESC" : ($lmIdCol ? "$lmIdCol DESC" : "$lmKpCol DESC");

        $sqlLm = "SELECT " . implode(", ", $sel) . " FROM laporan_mingguan WHERE $lmKpCol=? ORDER BY $orderLm LIMIT 5";
        $st = $conn->prepare($sqlLm);
        $st->bind_param("i", $id_kp);
        $st->execute();
        $rowsLaporanTerbaru = $st->get_result()->fetch_all(MYSQLI_ASSOC);
        $st->close();
    }

    // presensi count + list
    if ($hasPresensi) {
        // total presensi
        if ($usePresensiIdKp) {
            $total_presensi_total = q_count($conn, "SELECT COUNT(*) FROM presensi WHERE id_kp = ?", "i", [$id_kp]);
            if ($pValidCol) {
                $total_presensi_approve = q_count($conn, "SELECT COUNT(*) FROM presensi WHERE id_kp=? AND $pValidCol='Approve'", "i", [$id_kp]);
            }
        } else {
            $total_presensi_total = q_count($conn, "SELECT COUNT(*) FROM presensi WHERE nim = ?", "s", [$nim]);
            if ($pValidCol) {
                $total_presensi_approve = q_count($conn, "SELECT COUNT(*) FROM presensi WHERE nim=? AND $pValidCol='Approve'", "s", [$nim]);
            }
        }

        // list presensi terbaru
        $selP = [];
        $selP[] = ($pTanggalCol ? "$pTanggalCol AS tanggal" : "NULL AS tanggal");
        $selP[] = ($pStatusCol ? "$pStatusCol AS status" : "NULL AS status");
        $selP[] = ($pValidCol ? "$pValidCol AS validasi" : "NULL AS validasi");
        if ($pLatCol) $selP[] = "$pLatCol AS latitude";
        else $selP[] = "NULL AS latitude";
        if ($pLngCol) $selP[] = "$pLngCol AS longitude";
        else $selP[] = "NULL AS longitude";

        $whereP = $usePresensiIdKp ? "id_kp=?" : "nim=?";
        $typesP = $usePresensiIdKp ? "i" : "s";
        $paramP = $usePresensiIdKp ? $id_kp : $nim;

        $orderP = $pTanggalCol ? "$pTanggalCol DESC" : "1 DESC";
        $sqlP = "SELECT " . implode(", ", $selP) . " FROM presensi WHERE $whereP ORDER BY $orderP LIMIT 5";
        $st = $conn->prepare($sqlP);
        $st->bind_param($typesP, $paramP);
        $st->execute();
        $rowsPresensiTerbaru = $st->get_result()->fetch_all(MYSQLI_ASSOC);
        $st->close();
    }

    // dokumen akhir status
    if ($hasDokAkhir && $daKpCol) {
        $dok = q_count($conn, "SELECT COUNT(*) FROM dokumen_akhir WHERE $daKpCol = ?", "i", [$id_kp]);
        $dokumen_akhir_status = ($dok > 0) ? "Sudah" : "Belum";
    }

    // nilai akhir status
    if ($hasPenilaian && has_column($conn,'penilaian_akhir','id_kp')) {
        $n = q_count($conn, "SELECT COUNT(*) FROM penilaian_akhir WHERE id_kp=?", "i", [$id_kp]);
        $nilai_akhir_status = ($n > 0) ? "Sudah" : "Belum";
    }

    // bimbingan upcoming
    if ($hasBimbingan && $bNimCol) {
        $selB = [];
        $selB[] = ($bTanggalCol ? "$bTanggalCol AS tanggal" : "NULL AS tanggal");
        $selB[] = ($bTopikCol ? "$bTopikCol AS topik" : "NULL AS topik");
        $selB[] = ($bStatusCol ? "$bStatusCol AS status" : "NULL AS status");

        $sqlB = "SELECT " . implode(", ", $selB) . " FROM bimbingan WHERE $bNimCol=? ";
        if ($bTanggalCol) $sqlB .= "ORDER BY $bTanggalCol DESC ";
        $sqlB .= "LIMIT 5";

        $st = $conn->prepare($sqlB);
        $st->bind_param("s", $nim);
        $st->execute();
        $rowsBimbingan = $st->get_result()->fetch_all(MYSQLI_ASSOC);
        $st->close();
    }

    // progress score
    $status = (string)($kp['status'] ?? '');
    $statusScore = 0;
    switch ($status) {
        case 'Pengajuan':
        case 'Pengajuan KP':
        case 'Menunggu':
            $statusScore = 10; break;
        case 'Diterima':
            $statusScore = 20; break;
        case 'Berlangsung':
            $statusScore = 30; break;
        case 'Selesai':
            $statusScore = 40; break;
        default:
            $statusScore = 0; break;
    }

    $timeScore = 0;
    $mulai = $kp['tgl_mulai'] ?? null;
    $selesai = $kp['tgl_selesai'] ?? null;

    if (!empty($mulai) && !empty($selesai)) {
        $start = strtotime($mulai);
        $end   = strtotime($selesai);
        $now   = time();

        if ($start && $end && $end > $start && $now > $start) {
            $elapsed = min($now, $end) - $start;
            $total   = max(1, $end - $start);
            $timeScore = min(40, (int)round(($elapsed / $total) * 40));
        }
    }

    $activityScore  = min(10, $total_laporan * 2);                 // max 10
    $activityScore += min(7,  $total_presensi_approve);            // max 7
    $activityScore += ($dokumen_akhir_status === "Sudah") ? 3 : 0; // max 3

    $progress = min(100, $statusScore + $timeScore + $activityScore);
}

/* pengumuman */
if ($hasPengumuman && $pgJudulCol) {
    $selG = [];
    $selG[] = "$pgJudulCol AS judul";
    $selG[] = ($pgDateCol ? "$pgDateCol AS created_at" : "NULL AS created_at");
    $orderG = $pgDateCol ? "$pgDateCol DESC" : "1 DESC";
    $sqlG = "SELECT " . implode(", ", $selG) . " FROM pengumuman ORDER BY $orderG LIMIT 5";
    $st = $conn->prepare($sqlG);
    $st->execute();
    $rowsPengumuman = $st->get_result()->fetch_all(MYSQLI_ASSOC);
    $st->close();
}

/* ================== LAYOUT ================== */
include __DIR__ . "/../includes/layout_top.php";
include __DIR__ . "/../includes/sidebar_mahasiswa.php";
?>

<style>
  .kpi-card .kpi-value{font-size:1.7rem;font-weight:800;line-height:1.1}
  .kpi-card .kpi-label{color:#6c757d}
  .badge-soft{background:#eef2ff;color:#2f3a9e;border:1px solid #dbe3ff}
  .section-title{font-weight:800}
  .table td,.table th{vertical-align:middle}
</style>

<main class="pc-container">
<?php include __DIR__ . "/../includes/header.php"; ?>

<div class="pc-content">

  <!-- HEADER -->
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
      <h3 class="fw-bold mb-1">Dashboard Mahasiswa</h3>
      <div class="text-muted">Ringkasan progres Kerja Praktik Anda (<?= date('d M Y'); ?>)</div>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-primary btn-sm" href="<?= $asset_prefix ?>mahasiswa/presensi.php">Presensi</a>
      <a class="btn btn-outline-warning btn-sm" href="<?= $asset_prefix ?>mahasiswa/laporan_mingguan.php">Laporan Mingguan</a>
      <a class="btn btn-outline-success btn-sm" href="<?= $asset_prefix ?>mahasiswa/dokumen_akhir.php">Dokumen Akhir</a>
    </div>
  </div>

  <!-- PROFIL -->
  <div class="card mb-3 shadow-sm">
    <div class="card-body">
      <div class="row align-items-center g-3">
        <div class="col-md-1 text-center">
          <img src="<?= h($fotoRel) ?>" class="img-thumbnail" style="width:72px;height:72px;object-fit:cover;border-radius:14px;">
        </div>
        <div class="col-md-7">
          <div class="fw-bold" style="font-size:1.05rem;"><?= h($profilMhs['nama'] ?: $nim) ?></div>
          <div class="text-muted">
            NIM: <span class="badge badge-soft"><?= h($nim) ?></span>
            <?php if (!empty($profilMhs['angkatan'])): ?>
              <span class="ms-2">Angkatan: <span class="badge badge-soft"><?= h($profilMhs['angkatan']) ?></span></span>
            <?php endif; ?>
          </div>
          <div class="text-muted mt-1">
            <?= !empty($profilMhs['jurusan']) ? "Jurusan: " . h($profilMhs['jurusan']) : "Jurusan: -"; ?>
            <?php if (!empty($profilMhs['username'])): ?>
              <span class="ms-2">Username: <span class="fw-semibold"><?= h($profilMhs['username']) ?></span></span>
            <?php endif; ?>
          </div>
        </div>
        <div class="col-md-4">
          <div class="d-flex justify-content-md-end gap-2">
            <a class="btn btn-primary btn-sm" href="<?= $asset_prefix ?>profile/profile.php">Profil</a>
            <a class="btn btn-outline-dark btn-sm" href="<?= $asset_prefix ?>logout.php">Logout</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php if (!$kp): ?>
    <div class="alert alert-warning">
      Anda belum memiliki data Kerja Praktik. Silakan lakukan pengajuan KP terlebih dahulu.
    </div>
  <?php else: ?>

  <!-- KPI -->
  <div class="row g-3 mb-3">
    <div class="col-md-3">
      <div class="card kpi-card shadow-sm">
        <div class="card-body">
          <div class="kpi-label">Status KP</div>
          <div class="kpi-value"><?= h($kp['status'] ?? '-') ?></div>
          <div class="text-muted small mt-1">Instansi & progres mengikuti status KP</div>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card kpi-card shadow-sm">
        <div class="card-body">
          <div class="kpi-label">Progres (Estimasi)</div>
          <div class="kpi-value"><?= (int)$progress ?>%</div>
          <div class="text-muted small mt-1">Status + waktu + aktivitas</div>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card kpi-card shadow-sm">
        <div class="card-body">
          <div class="kpi-label">Laporan Mingguan</div>
          <div class="kpi-value"><?= (int)$total_laporan ?></div>
          <div class="text-muted small mt-1">Total laporan yang sudah diunggah</div>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card kpi-card shadow-sm">
        <div class="card-body">
          <div class="kpi-label">Presensi Disetujui</div>
          <div class="kpi-value"><?= (int)$total_presensi_approve ?></div>
          <div class="text-muted small mt-1">Dari total presensi: <?= (int)$total_presensi_total ?></div>
        </div>
      </div>
    </div>
  </div>

  <!-- PROGRESS CARD + RINGKASAN KP -->
  <div class="row g-3 mb-3">
    <div class="col-md-8">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start gap-2">
            <div>
              <div class="section-title mb-1">Progres Kerja Praktik</div>
              <div class="text-muted">Perkiraan progres berdasarkan status, periode, dan aktivitas.</div>
            </div>
            <div class="text-end">
              <span class="badge bg-primary"><?= h($kp['status'] ?? '-') ?></span>
            </div>
          </div>

          <div class="progress mt-3" style="height:14px;">
            <div class="progress-bar" style="width: <?= (int)$progress ?>%;" aria-valuenow="<?= (int)$progress ?>" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          <div class="d-flex justify-content-between mt-2">
            <small class="text-muted">0%</small>
            <small class="text-muted"><strong><?= (int)$progress ?>%</strong></small>
            <small class="text-muted">100%</small>
          </div>

          <hr>

          <div class="row text-center">
            <div class="col">
              <div class="fw-bold"><?= (int)$total_laporan ?></div>
              <div class="text-muted">Laporan</div>
            </div>
            <div class="col">
              <div class="fw-bold"><?= (int)$total_presensi_approve ?></div>
              <div class="text-muted">Presensi Approve</div>
            </div>
            <div class="col">
              <div class="fw-bold"><?= h($dokumen_akhir_status) ?></div>
              <div class="text-muted">Dokumen Akhir</div>
            </div>
            <div class="col">
              <div class="fw-bold"><?= h($nilai_akhir_status) ?></div>
              <div class="text-muted">Nilai Akhir</div>
            </div>
          </div>

          <div class="alert alert-light border mt-3 mb-0">
            <div class="fw-semibold mb-1">Aksi cepat</div>
            <div class="d-flex flex-wrap gap-2">
              <a href="<?= $asset_prefix ?>mahasiswa/presensi.php" class="btn btn-sm btn-outline-primary">Presensi Hari Ini</a>
              <a href="<?= $asset_prefix ?>mahasiswa/laporan_mingguan.php" class="btn btn-sm btn-outline-warning">Upload Laporan</a>
              <a href="<?= $asset_prefix ?>mahasiswa/dokumen_akhir.php" class="btn btn-sm btn-outline-success">Upload Dokumen Akhir</a>
              <a href="<?= $asset_prefix ?>mahasiswa/bimbingan.php" class="btn btn-sm btn-outline-secondary">Ajukan Bimbingan</a>
            </div>
          </div>

        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="section-title mb-3">Ringkasan KP</div>

          <div class="mb-2">
            <div class="text-muted">Instansi</div>
            <div class="fw-bold"><?= h($kp['nama_instansi'] ?? '-') ?></div>
          </div>

          <div class="mb-2">
            <div class="text-muted">Posisi</div>
            <div class="fw-bold"><?= h($kp['posisi'] ?? '-') ?></div>
          </div>

          <div class="mb-2">
            <div class="text-muted">Dosen Pembimbing</div>
            <?php if (!empty($kp['nama_dosen'])): ?>
              <div class="fw-bold"><?= h($kp['nama_dosen']) ?></div>
              <div class="text-muted small">NIDN: <?= h($kp['nidn'] ?? '-') ?></div>
              <?php if (!empty($kp['peminatan_dosen'])): ?>
                <div class="text-muted small">Peminatan: <?= h($kp['peminatan_dosen']) ?></div>
              <?php endif; ?>
            <?php else: ?>
              <div class="text-muted">Belum ditetapkan</div>
            <?php endif; ?>
          </div>

          <div class="mb-2">
            <div class="text-muted">Periode</div>
            <div class="fw-bold">
              <?php if (!empty($kp['tgl_mulai']) && !empty($kp['tgl_selesai'])): ?>
                <?= h(date('d M Y', strtotime($kp['tgl_mulai']))) ?> s/d <?= h(date('d M Y', strtotime($kp['tgl_selesai']))) ?>
              <?php else: ?>
                -
              <?php endif; ?>
            </div>
          </div>

          <div class="mt-3">
            <a class="btn btn-primary btn-sm w-100" href="<?= $asset_prefix ?>mahasiswa/monitoring_kp.php">Lihat Monitoring KP</a>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- LIST TERBARU -->
  <div class="row g-3">
    <div class="col-lg-7">
      <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <div class="fw-bold">Laporan Mingguan Terbaru</div>
          <a href="<?= $asset_prefix ?>mahasiswa/laporan_mingguan.php" class="btn btn-sm btn-outline-warning">Buka</a>
        </div>
        <div class="card-body">
          <?php if (!$hasLaporan): ?>
            <div class="alert alert-warning mb-0">Tabel laporan mingguan belum tersedia.</div>
          <?php elseif (empty($rowsLaporanTerbaru)): ?>
            <div class="text-muted">Belum ada laporan mingguan.</div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th style="width:90px;">Minggu</th>
                    <th>Judul / File</th>
                    <th style="width:140px;">Tanggal</th>
                    <th style="width:120px;">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($rowsLaporanTerbaru as $r): ?>
                    <tr>
                      <td class="text-center"><?= h($r['minggu_ke'] ?? '-') ?></td>
                      <td>
                        <div class="fw-semibold"><?= h($r['judul'] ?? '-') ?></div>
                        <?php if (!empty($r['file_laporan'])): ?>
                          <div class="text-muted small">File: <?= h($r['file_laporan']) ?></div>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if (!empty($r['tanggal_upload'])): ?>
                          <?= h(date('d M Y', strtotime($r['tanggal_upload']))) ?>
                        <?php else: ?>
                          -
                        <?php endif; ?>
                      </td>
                      <td class="text-center">
                        <?php
                          $stt = (string)($r['status'] ?? '-');
                          $cls = 'bg-secondary';
                          if (stripos($stt,'menunggu') !== false) $cls = 'bg-warning text-dark';
                          elseif (stripos($stt,'setuju') !== false || stripos($stt,'approve') !== false) $cls = 'bg-success';
                          elseif (stripos($stt,'revisi') !== false) $cls = 'bg-danger';
                        ?>
                        <span class="badge <?= $cls ?>"><?= h($stt) ?></span>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card shadow-sm mb-3">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <div class="fw-bold">Presensi Terbaru</div>
          <a href="<?= $asset_prefix ?>mahasiswa/presensi.php" class="btn btn-sm btn-outline-primary">Buka</a>
        </div>
        <div class="card-body">
          <?php if (!$hasPresensi): ?>
            <div class="alert alert-warning mb-0">Tabel presensi belum tersedia.</div>
          <?php elseif (empty($rowsPresensiTerbaru)): ?>
            <div class="text-muted">Belum ada presensi.</div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-sm table-bordered mb-0">
                <thead class="table-light">
                  <tr>
                    <th style="width:120px;">Tanggal</th>
                    <th>Status</th>
                    <th style="width:120px;">Validasi</th>
                  </tr>
                </thead>
                <tbody>
                <?php foreach ($rowsPresensiTerbaru as $p): ?>
                  <tr>
                    <td>
                      <?php if (!empty($p['tanggal'])): ?>
                        <?= h(date('d M Y', strtotime($p['tanggal']))) ?>
                      <?php else: ?>
                        -
                      <?php endif; ?>
                      <?php if (!empty($p['latitude']) && !empty($p['longitude'])): ?>
                        <div class="small">
                          <a target="_blank" rel="noopener"
                             href="https://www.google.com/maps?q=<?= h($p['latitude']) ?>,<?= h($p['longitude']) ?>">
                             Lihat Lokasi
                          </a>
                        </div>
                      <?php endif; ?>
                    </td>
                    <td><?= h($p['status'] ?? '-') ?></td>
                    <td class="text-center">
                      <?php
                        $v = (string)($p['validasi'] ?? '-');
                        $cls = 'bg-secondary';
                        if (stripos($v,'approve') !== false) $cls = 'bg-success';
                        elseif (stripos($v,'reject') !== false || stripos($v,'tolak') !== false) $cls = 'bg-danger';
                        elseif (stripos($v,'menunggu') !== false) $cls = 'bg-warning text-dark';
                      ?>
                      <span class="badge <?= $cls ?>"><?= h($v) ?></span>
                    </td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <div class="fw-bold">Bimbingan Terbaru</div>
          <a href="<?= $asset_prefix ?>mahasiswa/bimbingan.php" class="btn btn-sm btn-outline-secondary">Buka</a>
        </div>
        <div class="card-body">
          <?php if (!$hasBimbingan): ?>
            <div class="alert alert-warning mb-0">Tabel bimbingan belum tersedia.</div>
          <?php elseif (empty($rowsBimbingan)): ?>
            <div class="text-muted">Belum ada bimbingan.</div>
          <?php else: ?>
            <ul class="list-group list-group-flush">
              <?php foreach ($rowsBimbingan as $b): ?>
                <li class="list-group-item">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <div class="fw-semibold"><?= h($b['topik'] ?? '-') ?></div>
                      <div class="text-muted small">
                        <?php if (!empty($b['tanggal'])): ?>
                          <?= h(date('d M Y', strtotime($b['tanggal']))) ?>
                        <?php else: ?>
                          -
                        <?php endif; ?>
                      </div>
                    </div>
                    <?php if (!empty($b['status'])): ?>
                      <span class="badge bg-light text-dark border"><?= h($b['status']) ?></span>
                    <?php endif; ?>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>

  <!-- PENGUMUMAN -->
  <div class="card shadow-sm mt-3">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <div class="fw-bold">Pengumuman Terbaru</div>
      <a href="<?= $asset_prefix ?>mahasiswa/pengumuman.php" class="btn btn-sm btn-outline-dark">Lihat Semua</a>
    </div>
    <div class="card-body">
      <?php if (!$hasPengumuman): ?>
        <div class="alert alert-warning mb-0">Tabel pengumuman belum tersedia.</div>
      <?php elseif (empty($rowsPengumuman)): ?>
        <div class="text-muted">Belum ada pengumuman.</div>
      <?php else: ?>
        <ul class="list-group list-group-flush">
          <?php foreach ($rowsPengumuman as $g): ?>
            <li class="list-group-item d-flex justify-content-between align-items-start">
              <div>
                <div class="fw-semibold"><?= h($g['judul'] ?? '-') ?></div>
                <div class="text-muted small">
                  <?php if (!empty($g['created_at'])): ?>
                    <?= h(date('d M Y H:i', strtotime($g['created_at']))) ?>
                  <?php else: ?>
                    -
                  <?php endif; ?>
                </div>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>

  <?php
    // Timeline component: include hanya jika file ada agar tidak fatal error
    $timelineFile = __DIR__ . "/timeline_kp_component.php";
    if (is_file($timelineFile)) {
        include $timelineFile;
    }
  ?>

  <?php endif; // end kp ?>

</div>
</main>

<?php include __DIR__ . "/../includes/layout_bottom.php"; ?>
