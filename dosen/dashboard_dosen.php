<?php
require_once __DIR__ . "/../auth/auth_check.php";
require_role('dosen');
require_once __DIR__ . "/../config/db.php";

$current_page  = "dashboard_dosen.php";
$page_title    = "Dashboard Dosen";
$asset_prefix  = "../";
$logout_prefix = "../";

/* =========================================================
   UTILITIES
   ========================================================= */
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

function has_column(mysqli $conn, string $table, string $column): bool {
    $sql = "SELECT COUNT(*) c
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND COLUMN_NAME = ?";
    $st = $conn->prepare($sql);
    $st->bind_param("ss", $table, $column);
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

function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

/* =========================================================
   SESSION USER
   ========================================================= */
$user_id = (int)($_SESSION['user_id'] ?? 0);
if ($user_id <= 0) die("Session user tidak valid.");

/* =========================================================
   AMBIL DATA DOSEN (AMAN TERHADAP PERBEDAAN KOLOM)
   ========================================================= */
if (!has_table($conn, 'dosen')) die("Tabel dosen tidak ditemukan.");

$dosenNamaCol     = pick_col($conn, 'dosen', ['nama','nama_dosen'], 'nama');
$dosenNidnCol     = pick_col($conn, 'dosen', ['nidn'], 'nidn');
$dosenNipCol      = pick_col($conn, 'dosen', ['nip'], null);
$dosenJurusanCol  = pick_col($conn, 'dosen', ['jurusan'], null);
$dosenPeminatanCol= pick_col($conn, 'dosen', ['peminatan','keahlian'], null);

$select = [];
$select[] = "id_user";
$select[] = "{$dosenNidnCol} AS nidn";
$select[] = "{$dosenNamaCol} AS nama";
if ($dosenNipCol)       $select[] = "{$dosenNipCol} AS nip";
if ($dosenJurusanCol)   $select[] = "{$dosenJurusanCol} AS jurusan";
if ($dosenPeminatanCol) $select[] = "{$dosenPeminatanCol} AS peminatan";

$sqlDosen = "SELECT " . implode(", ", $select) . " FROM dosen WHERE id_user=? LIMIT 1";
$st = $conn->prepare($sqlDosen);
$st->bind_param("i", $user_id);
$st->execute();
$dosen = $st->get_result()->fetch_assoc();
$st->close();

if (!$dosen) die("Data dosen tidak ditemukan untuk user ini.");

$nidn      = $dosen['nidn'] ?? '';
$namaDosen = $dosen['nama'] ?? ($_SESSION['username'] ?? 'Dosen');

/* =========================================================
   FOTO USER (OPSIONAL)
   ========================================================= */
$userFoto = null;
if (has_table($conn, 'users') && has_column($conn, 'users', 'foto')) {
    $st = $conn->prepare("SELECT username, foto FROM users WHERE id_user=? LIMIT 1");
    $st->bind_param("i", $user_id);
    $st->execute();
    $u = $st->get_result()->fetch_assoc();
    $st->close();
    if (!empty($u['username'])) $_SESSION['username'] = $u['username'];
    $userFoto = $u['foto'] ?? null;
}

$fotoRel = !empty($userFoto)
    ? "../uploads/profile/" . $userFoto
    : "../uploads/profile/default.png";

/* =========================================================
   DETEKSI KOLOM KP (tgl_mulai/tanggal_mulai, dll)
   ========================================================= */
$hasKP = has_table($conn, 'kp');
$kpMulaiCol   = $hasKP ? pick_col($conn, 'kp', ['tgl_mulai','tanggal_mulai','mulai','tanggal_mulai_kp'], null) : null;
$kpSelesaiCol = $hasKP ? pick_col($conn, 'kp', ['tgl_selesai','tanggal_selesai','selesai','tanggal_selesai_kp'], null) : null;
if (!$kpMulaiCol)   $kpMulaiCol = 'tgl_mulai';   // default aman (kalau ternyata beda, tetap tidak dipakai tanpa deteksi)
if (!$kpSelesaiCol) $kpSelesaiCol = 'tgl_selesai';

/* =========================================================
   DETEKSI TABLE LAIN
   ========================================================= */
$hasMahasiswa       = has_table($conn, 'mahasiswa');
$hasLaporanMingguan = has_table($conn, 'laporan_mingguan');
$hasBimbingan       = has_table($conn, 'bimbingan');
$hasPenilaianAkhir  = has_table($conn, 'penilaian_akhir');
$hasPengumuman      = has_table($conn, 'pengumuman');

/* =========================================================
   DETEKSI KOLOM laporan_mingguan (INILAH YANG BIKIN ERROR KAMU)
   ========================================================= */
$lmIdCol      = $hasLaporanMingguan ? pick_col($conn, 'laporan_mingguan', ['id_laporan_mingguan','id_laporan','id_laporan_kp','id'], null) : null;
$lmKpCol      = $hasLaporanMingguan ? pick_col($conn, 'laporan_mingguan', ['id_kp','kp_id'], 'id_kp') : null;
$lmMingguCol  = $hasLaporanMingguan ? pick_col($conn, 'laporan_mingguan', ['minggu_ke','minggu','pekan_ke','week'], null) : null;
$lmStatusCol  = $hasLaporanMingguan ? pick_col($conn, 'laporan_mingguan', ['status','status_laporan','status_verifikasi','verifikasi'], null) : null;
$lmTanggalCol = $hasLaporanMingguan ? pick_col($conn, 'laporan_mingguan', ['tanggal_upload','tgl_upload','tanggal','created_at','waktu_upload'], null) : null;
$lmFileCol    = $hasLaporanMingguan ? pick_col($conn, 'laporan_mingguan', ['file_laporan','file','lampiran','berkas','dokumen','nama_file','path_file'], null) : null;
$lmTextCol    = $hasLaporanMingguan ? pick_col($conn, 'laporan_mingguan', [
    'isi_laporan','kegiatan','uraian','deskripsi','keterangan','catatan','laporan','ringkasan','judul','progress','kendala','hasil'
], null) : null;

/* =========================================================
   KPI (AMAN)
   ========================================================= */
$totalBimbingan   = 0;
$aktifBimbingan   = 0;
$laporanCount     = 0;
$nilaiAkhirBelum  = 0;

if ($hasKP) {
    $totalBimbingan = q_count($conn, "SELECT COUNT(*) FROM kp WHERE nidn = ?", "s", [$nidn]);
    $aktifBimbingan = q_count($conn, "SELECT COUNT(*) FROM kp WHERE nidn = ? AND status = 'Berlangsung'", "s", [$nidn]);

    if ($hasLaporanMingguan && $lmKpCol) {
        // Jika ada status, hitung yang Menunggu; jika tidak ada, hitung total laporan (supaya tidak salah query)
        if ($lmStatusCol) {
            $laporanCount = q_count(
                $conn,
                "SELECT COUNT(*)
                 FROM laporan_mingguan lm
                 JOIN kp k ON k.id_kp = lm.$lmKpCol
                 WHERE k.nidn = ? AND lm.$lmStatusCol = 'Menunggu'",
                "s",
                [$nidn]
            );
        } else {
            $laporanCount = q_count(
                $conn,
                "SELECT COUNT(*)
                 FROM laporan_mingguan lm
                 JOIN kp k ON k.id_kp = lm.$lmKpCol
                 WHERE k.nidn = ?",
                "s",
                [$nidn]
            );
        }
    }

    if ($hasPenilaianAkhir && has_column($conn, 'penilaian_akhir', 'id_kp')) {
        $nilaiAkhirBelum = q_count(
            $conn,
            "SELECT COUNT(*)
             FROM kp
             WHERE kp.nidn = ?
               AND kp.status = 'Selesai'
               AND NOT EXISTS (
                  SELECT 1 FROM penilaian_akhir pa WHERE pa.id_kp = kp.id_kp
               )",
            "s",
            [$nidn]
        );
    }
}

/* =========================================================
   LIST LAPORAN (AMAN - TANPA KOLOM YANG TIDAK ADA)
   ========================================================= */
$rowsLaporan = [];
if ($hasKP && $hasMahasiswa && $hasLaporanMingguan && $lmKpCol) {

    $sel = [];
    if ($lmIdCol)      $sel[] = "lm.$lmIdCol AS id_laporan";
    else               $sel[] = "0 AS id_laporan";

    $sel[] = "lm.$lmKpCol AS id_kp";
    $sel[] = "m.nim";
    $sel[] = "m.nama AS nama_mhs";

    if ($lmMingguCol)  $sel[] = "lm.$lmMingguCol AS minggu_ke";
    else               $sel[] = "NULL AS minggu_ke";

    if ($lmTanggalCol) $sel[] = "lm.$lmTanggalCol AS tanggal_upload";
    else               $sel[] = "NULL AS tanggal_upload";

    if ($lmStatusCol)  $sel[] = "lm.$lmStatusCol AS status";
    else               $sel[] = "NULL AS status";

    if ($lmFileCol)    $sel[] = "lm.$lmFileCol AS file_laporan";
    else               $sel[] = "NULL AS file_laporan";

    if ($lmTextCol) {
        // Ringkasan text 60 karakter (tanpa error)
        $sel[] = "LEFT(REPLACE(REPLACE(lm.$lmTextCol, '\n', ' '), '\r', ' '), 60) AS ringkas";
    } else {
        $sel[] = "NULL AS ringkas";
    }

    $sql = "
        SELECT " . implode(", ", $sel) . "
        FROM laporan_mingguan lm
        JOIN kp k ON k.id_kp = lm.$lmKpCol
        JOIN mahasiswa m ON m.nim = k.nim
        WHERE k.nidn = ?
    ";

    // Filter Menunggu hanya kalau kolom status ada
    if ($lmStatusCol) {
        $sql .= " AND lm.$lmStatusCol = 'Menunggu' ";
    }

    // Order by tanggal jika ada, else by id_laporan jika ada, else by id_kp
    if ($lmTanggalCol) {
        $sql .= " ORDER BY lm.$lmTanggalCol DESC ";
    } elseif ($lmIdCol) {
        $sql .= " ORDER BY lm.$lmIdCol DESC ";
    } else {
        $sql .= " ORDER BY lm.$lmKpCol DESC ";
    }

    $sql .= " LIMIT 8 ";

    $st = $conn->prepare($sql);
    $st->bind_param("s", $nidn);
    $st->execute();
    $rowsLaporan = $st->get_result()->fetch_all(MYSQLI_ASSOC);
    $st->close();
}

/* =========================================================
   LIST BIMBINGAN (AMAN)
   ========================================================= */
$rowsBimbingan = [];
if ($hasBimbingan) {
    $bId   = pick_col($conn, 'bimbingan', ['id_bimbingan','id'], null);
    $bNim  = pick_col($conn, 'bimbingan', ['nim'], 'nim');
    $bNidn = pick_col($conn, 'bimbingan', ['nidn'], 'nidn');
    $bTopik= pick_col($conn, 'bimbingan', ['topik','judul'], null);
    $bTgl  = pick_col($conn, 'bimbingan', ['tanggal','tgl','created_at'], null);
    $bCat  = pick_col($conn, 'bimbingan', ['catatan','keterangan'], null);

    $sel = [];
    $sel[] = ($bId ? "b.$bId AS id_bimbingan" : "0 AS id_bimbingan");
    $sel[] = "b.$bNim AS nim";
    $sel[] = ($bTopik ? "b.$bTopik AS topik" : "NULL AS topik");
    $sel[] = ($bTgl ? "b.$bTgl AS tanggal" : "NULL AS tanggal");
    $sel[] = ($bCat ? "b.$bCat AS catatan" : "NULL AS catatan");

    $sql = "SELECT " . implode(", ", $sel) . " FROM bimbingan b WHERE b.$bNidn = ? ";
    if ($bTgl) $sql .= " ORDER BY b.$bTgl DESC ";
    elseif ($bId) $sql .= " ORDER BY b.$bId DESC ";
    $sql .= " LIMIT 8 ";

    $st = $conn->prepare($sql);
    $st->bind_param("s", $nidn);
    $st->execute();
    $rowsBimbingan = $st->get_result()->fetch_all(MYSQLI_ASSOC);
    $st->close();
}

/* =========================================================
   LIST MAHASISWA BIMBINGAN (AMAN - PAKAI tgl_mulai/tgl_selesai)
   ========================================================= */
$rowsMhsAktif = [];
if ($hasKP && $hasMahasiswa) {
    // gunakan alias agar tampilan konsisten
    $sql = "
        SELECT
            kp.id_kp,
            kp.nim,
            m.nama AS nama_mhs,
            kp.nama_instansi,
            kp.posisi,
            kp.$kpMulaiCol   AS tgl_mulai,
            kp.$kpSelesaiCol AS tgl_selesai,
            kp.status
        FROM kp
        JOIN mahasiswa m ON m.nim = kp.nim
        WHERE kp.nidn = ?
          AND kp.status IN ('Berlangsung','Diterima','Selesai','Menunggu')
        ORDER BY
          (kp.status='Berlangsung') DESC,
          kp.$kpMulaiCol DESC,
          kp.id_kp DESC
        LIMIT 10
    ";
    $st = $conn->prepare($sql);
    $st->bind_param("s", $nidn);
    $st->execute();
    $rowsMhsAktif = $st->get_result()->fetch_all(MYSQLI_ASSOC);
    $st->close();
}

/* =========================================================
   PENGUMUMAN (AMAN)
   ========================================================= */
$rowsPengumuman = [];
if ($hasPengumuman) {
    $pId   = pick_col($conn, 'pengumuman', ['id_pengumuman','id'], null);
    $pJud  = pick_col($conn, 'pengumuman', ['judul','title'], null);
    $pDate = pick_col($conn, 'pengumuman', ['created_at','tanggal','tgl'], null);

    $sel = [];
    $sel[] = ($pId ? "$pId AS id_pengumuman" : "0 AS id_pengumuman");
    $sel[] = ($pJud ? "$pJud AS judul" : "NULL AS judul");
    $sel[] = ($pDate ? "$pDate AS created_at" : "NULL AS created_at");

    $sql = "SELECT " . implode(", ", $sel) . " FROM pengumuman ";
    if ($pDate) $sql .= " ORDER BY $pDate DESC ";
    elseif ($pId) $sql .= " ORDER BY $pId DESC ";
    $sql .= " LIMIT 5 ";

    $st = $conn->prepare($sql);
    $st->execute();
    $rowsPengumuman = $st->get_result()->fetch_all(MYSQLI_ASSOC);
    $st->close();
}

include __DIR__ . "/../includes/layout_top.php";
include __DIR__ . "/../includes/sidebar_dosen.php";
?>

<style>
  .kpi-card .kpi-value{font-size:1.7rem;font-weight:800;line-height:1.1}
  .kpi-card .kpi-label{color:#6c757d}
  .table td, .table th{vertical-align:middle}
  .badge-soft{background:#eef2ff;color:#2f3a9e;border:1px solid #dbe3ff}
</style>

<main class="pc-container">
  <?php include __DIR__ . "/../includes/header.php"; ?>

  <div class="pc-content">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
      <div>
        <h3 class="fw-bold mb-1">Dashboard Dosen</h3>
        <div class="text-muted">Ringkasan aktivitas (<?= date('d M Y'); ?>)</div>
      </div>
      <div class="d-flex gap-2">
        <a href="<?= $asset_prefix ?>dosen/bimbingan.php" class="btn btn-outline-primary btn-sm">Kelola Bimbingan</a>
        <a href="<?= $asset_prefix ?>dosen/laporan_mhs.php" class="btn btn-outline-warning btn-sm">Review Laporan</a>
        <a href="<?= $asset_prefix ?>dosen/penilaian.php" class="btn btn-outline-success btn-sm">Input Nilai</a>
      </div>
    </div>

    <!-- PROFIL SINGKAT -->
    <div class="card mb-3 shadow-sm">
      <div class="card-body">
        <div class="row align-items-center g-3">
          <div class="col-md-1 text-center">
            <img src="<?= h($fotoRel) ?>" class="img-thumbnail"
                 style="width:72px;height:72px;object-fit:cover;border-radius:14px;">
          </div>
          <div class="col-md-7">
            <div class="fw-bold" style="font-size:1.05rem;"><?= h($namaDosen) ?></div>
            <div class="text-muted">
              NIDN: <span class="badge badge-soft"><?= h($nidn) ?></span>
              <?php if (!empty($dosen['nip'])): ?>
                <span class="ms-2">NIP: <span class="badge badge-soft"><?= h($dosen['nip']) ?></span></span>
              <?php endif; ?>
            </div>
            <div class="text-muted mt-1">
              <?= !empty($dosen['jurusan']) ? "Jurusan: " . h($dosen['jurusan']) : "Jurusan: -"; ?>
              <?php if (!empty($dosen['peminatan'])): ?>
                <span class="ms-2">Peminatan: <span class="fw-semibold"><?= h($dosen['peminatan']) ?></span></span>
              <?php endif; ?>
            </div>
          </div>
          <div class="col-md-4">
            <div class="d-flex justify-content-md-end gap-2">
              <a class="btn btn-primary btn-sm" href="<?= $asset_prefix ?>profile/profile.php">Profil</a>
              <a class="btn btn-outline-secondary btn-sm" href="<?= $asset_prefix ?>dosen/monitoring.php">Monitoring KP</a>
              <a class="btn btn-outline-dark btn-sm" href="<?= $asset_prefix ?>logout.php">Logout</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- KPI -->
    <div class="row g-3 mb-3">
      <div class="col-md-3">
        <div class="card kpi-card shadow-sm">
          <div class="card-body">
            <div class="kpi-label">Mahasiswa Bimbingan (Total)</div>
            <div class="kpi-value"><?= (int)$totalBimbingan ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card kpi-card shadow-sm">
          <div class="card-body">
            <div class="kpi-label">KP Aktif (Berlangsung)</div>
            <div class="kpi-value"><?= (int)$aktifBimbingan ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card kpi-card shadow-sm">
          <div class="card-body">
            <div class="kpi-label">
              <?= ($lmStatusCol ? "Laporan Menunggu Review" : "Laporan Masuk (Total)") ?>
            </div>
            <div class="kpi-value">
              <?= (int)$laporanCount ?>
              <?php if ($lmStatusCol && $laporanCount > 0): ?>
                <span class="badge bg-danger ms-1">Perlu tindakan</span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card kpi-card shadow-sm">
          <div class="card-body">
            <div class="kpi-label">Nilai Akhir Belum Diisi</div>
            <div class="kpi-value"><?= (int)$nilaiAkhirBelum ?></div>
          </div>
        </div>
      </div>
    </div>

    <!-- GRID -->
    <div class="row g-3">

      <!-- LAPORAN -->
      <div class="col-lg-7">
        <div class="card shadow-sm h-100">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div class="fw-bold">
              <?= ($lmStatusCol ? "Laporan Mingguan Menunggu Review" : "Laporan Mingguan Terbaru") ?>
            </div>
            <a href="<?= $asset_prefix ?>dosen/laporan_mhs.php" class="btn btn-sm btn-outline-warning">Buka Halaman</a>
          </div>
          <div class="card-body">
            <?php if (!$hasLaporanMingguan || !$hasKP || !$hasMahasiswa): ?>
              <div class="alert alert-warning mb-0">Modul laporan belum siap (tabel belum tersedia).</div>
            <?php elseif (empty($rowsLaporan)): ?>
              <div class="text-muted">Belum ada data laporan.</div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0">
                  <thead class="table-light">
                    <tr>
                      <th style="width:120px;">NIM</th>
                      <th>Mahasiswa</th>
                      <th style="width:80px;">Minggu</th>
                      <th>Ringkasan</th>
                      <th style="width:140px;">Upload</th>
                      <th style="width:110px;">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($rowsLaporan as $r): ?>
                      <tr>
                        <td><?= h($r['nim'] ?? '-') ?></td>
                        <td><?= h($r['nama_mhs'] ?? '-') ?></td>
                        <td class="text-center"><?= h($r['minggu_ke'] ?? '-') ?></td>
                        <td>
                          <?= h($r['ringkas'] ?? '-') ?>
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
                          <a class="btn btn-sm btn-warning"
                             href="<?= $asset_prefix ?>dosen/laporan_mhs.php?id=<?= (int)($r['id_laporan'] ?? 0) ?>">
                            Review
                          </a>
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

      <!-- BIMBINGAN + PENGUMUMAN -->
      <div class="col-lg-5">
        <div class="card shadow-sm mb-3">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div class="fw-bold">Bimbingan Terbaru</div>
            <a href="<?= $asset_prefix ?>dosen/bimbingan.php" class="btn btn-sm btn-outline-primary">Kelola</a>
          </div>
          <div class="card-body">
            <?php if (!$hasBimbingan): ?>
              <div class="alert alert-warning mb-0">Tabel bimbingan belum tersedia.</div>
            <?php elseif (empty($rowsBimbingan)): ?>
              <div class="text-muted">Belum ada data bimbingan.</div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                  <thead class="table-light">
                    <tr>
                      <th style="width:110px;">NIM</th>
                      <th>Topik</th>
                      <th style="width:140px;">Tanggal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($rowsBimbingan as $b): ?>
                      <tr>
                        <td><?= h($b['nim'] ?? '-') ?></td>
                        <td><?= h($b['topik'] ?? '-') ?></td>
                        <td>
                          <?php if (!empty($b['tanggal'])): ?>
                            <?= h(date('d M Y', strtotime($b['tanggal']))) ?>
                          <?php else: ?>
                            -
                          <?php endif; ?>
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
            <div class="fw-bold">Pengumuman Terbaru</div>
            <a href="<?= $asset_prefix ?>dosen/pengumuman.php" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
          </div>
          <div class="card-body">
            <?php if (!$hasPengumuman): ?>
              <div class="alert alert-warning mb-0">Tabel pengumuman belum tersedia.</div>
            <?php elseif (empty($rowsPengumuman)): ?>
              <div class="text-muted">Belum ada pengumuman.</div>
            <?php else: ?>
              <ul class="list-group list-group-flush">
                <?php foreach ($rowsPengumuman as $p): ?>
                  <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div>
                      <div class="fw-semibold"><?= h($p['judul'] ?? '-') ?></div>
                      <div class="text-muted small">
                        <?php if (!empty($p['created_at'])): ?>
                          <?= h(date('d M Y H:i', strtotime($p['created_at']))) ?>
                        <?php else: ?>
                          -
                        <?php endif; ?>
                      </div>
                    </div>
                    <span class="badge bg-light text-dark border">#<?= (int)($p['id_pengumuman'] ?? 0) ?></span>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- MAHASISWA BIMBINGAN -->
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div class="fw-bold">Mahasiswa Bimbingan (Ringkas)</div>
            <a href="<?= $asset_prefix ?>dosen/monitoring.php" class="btn btn-sm btn-outline-dark">Monitoring Detail</a>
          </div>
          <div class="card-body">
            <?php if (!$hasKP || !$hasMahasiswa): ?>
              <div class="alert alert-warning mb-0">Data KP/mahasiswa belum lengkap.</div>
            <?php elseif (empty($rowsMhsAktif)): ?>
              <div class="text-muted">Belum ada mahasiswa bimbingan yang terhubung ke NIDN Anda.</div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th style="width:120px;">NIM</th>
                      <th>Nama</th>
                      <th>Instansi</th>
                      <th>Posisi</th>
                      <th style="width:180px;">Periode</th>
                      <th style="width:120px;">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($rowsMhsAktif as $m): ?>
                      <tr>
                        <td><?= h($m['nim'] ?? '-') ?></td>
                        <td><?= h($m['nama_mhs'] ?? '-') ?></td>
                        <td><?= h($m['nama_instansi'] ?? '-') ?></td>
                        <td><?= h($m['posisi'] ?? '-') ?></td>
                        <td class="small">
                          <?= !empty($m['tgl_mulai']) ? h(date('d M Y', strtotime($m['tgl_mulai']))) : '-' ?>
                          —
                          <?= !empty($m['tgl_selesai']) ? h(date('d M Y', strtotime($m['tgl_selesai']))) : '-' ?>
                        </td>
                        <td class="text-center">
                          <?php
                            $stt = $m['status'] ?? '-';
                            $cls = 'bg-secondary';
                            if ($stt === 'Berlangsung') $cls = 'bg-success';
                            elseif ($stt === 'Diterima') $cls = 'bg-primary';
                            elseif ($stt === 'Selesai') $cls = 'bg-dark';
                            elseif ($stt === 'Menunggu') $cls = 'bg-warning text-dark';
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

    </div><!-- /row -->

  </div><!-- /pc-content -->
</main>

<?php include __DIR__ . "/../includes/layout_bottom.php"; ?>
