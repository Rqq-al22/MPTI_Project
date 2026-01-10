<?php
require_once "../auth/auth_check.php";
require_role('admin');
require_once "../config/db.php";

$current_page  = 'dashboard_admin.php';
$page_title    = "Dashboard Admin";
$asset_prefix  = "../";
$logout_prefix = "../";

/* =========================================================
   UTIL: CEK TABEL / KOLOM (ANTI ERROR)
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
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?";
    $st = $conn->prepare($sql);
    $st->bind_param("ss", $table, $column);
    $st->execute();
    $row = $st->get_result()->fetch_assoc();
    $st->close();
    return (int)($row['c'] ?? 0) > 0;
}

function q_scalar(mysqli $conn, string $sql): int {
    $res = $conn->query($sql);
    if (!$res) return 0;
    $row = $res->fetch_row();
    return (int)($row[0] ?? 0);
}

function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

/* =========================================================
   CEK TABEL YANG DIPAKAI ADMIN (SESUAI SIDEBAR)
   ========================================================= */
$hasMahasiswa  = has_table($conn, 'mahasiswa');
$hasDosen      = has_table($conn, 'dosen');
$hasKP         = has_table($conn, 'kp');
$hasUsers      = has_table($conn, 'users');
$hasPengumuman = has_table($conn, 'pengumuman');

/* =========================================================
   KPI: MASTER DATA
   ========================================================= */
$total_mhs   = $hasMahasiswa ? q_scalar($conn, "SELECT COUNT(*) FROM mahasiswa") : 0;
$total_dosen = $hasDosen     ? q_scalar($conn, "SELECT COUNT(*) FROM dosen")     : 0;

/* =========================================================
   KPI: KERJA PRAKTIK (KP) - status
   ========================================================= */
$total_kp = $hasKP ? q_scalar($conn, "SELECT COUNT(*) FROM kp") : 0;

$kp_status_col = ($hasKP && has_column($conn, 'kp', 'status')) ? 'status' : null;

$kp_pengajuan   = 0;
$kp_diterima    = 0;
$kp_berlangsung = 0;
$kp_selesai     = 0;

if ($hasKP && $kp_status_col) {
    $kp_pengajuan   = q_scalar($conn, "SELECT COUNT(*) FROM kp WHERE status='Pengajuan'");
    $kp_diterima    = q_scalar($conn, "SELECT COUNT(*) FROM kp WHERE status='Diterima'");
    $kp_berlangsung = q_scalar($conn, "SELECT COUNT(*) FROM kp WHERE status='Berlangsung'");
    $kp_selesai     = q_scalar($conn, "SELECT COUNT(*) FROM kp WHERE status='Selesai'");
}

/* =========================================================
   KPI: MONITORING SISTEM (USERS)
   ========================================================= */
$total_user = $hasUsers ? q_scalar($conn, "SELECT COUNT(*) FROM users") : 0;

$user_admin = 0; $user_dosen = 0; $user_mhs = 0;
if ($hasUsers && has_column($conn, 'users', 'id_role')) {
    $user_admin = q_scalar($conn, "SELECT COUNT(*) FROM users WHERE id_role=1");
    $user_dosen = q_scalar($conn, "SELECT COUNT(*) FROM users WHERE id_role=2");
    $user_mhs   = q_scalar($conn, "SELECT COUNT(*) FROM users WHERE id_role=3");
}

/* =========================================================
   LIST: KP TERBARU (MONITORING KP)
   - dibuat dinamis agar aman terhadap kolom yg berbeda
   ========================================================= */
$kp_rows = [];
if ($hasKP) {
    $kp_created_col = has_column($conn, 'kp', 'created_at') ? 'created_at' : (has_column($conn, 'kp', 'tanggal') ? 'tanggal' : null);

    $kp_cols = [];
    $kp_cols[] = "kp.id_kp";

    // nim
    $kp_has_nim = has_column($conn, 'kp', 'nim');
    if ($kp_has_nim) $kp_cols[] = "kp.nim";

    // status
    if ($kp_status_col) $kp_cols[] = "kp.status";

    // created
    if ($kp_created_col) $kp_cols[] = "kp.$kp_created_col AS created_at";

    // instansi & posisi (opsional)
    $kp_has_instansi = has_column($conn, 'kp', 'nama_instansi');
    $kp_has_posisi   = has_column($conn, 'kp', 'posisi');
    if ($kp_has_instansi) $kp_cols[] = "kp.nama_instansi";
    if ($kp_has_posisi)   $kp_cols[] = "kp.posisi";

    // join mahasiswa untuk nama (opsional)
    $joinMhs = ($hasMahasiswa && $kp_has_nim && has_column($conn, 'mahasiswa', 'nim') && has_column($conn, 'mahasiswa', 'nama'));
    if ($joinMhs) $kp_cols[] = "m.nama AS nama_mhs";

    $select = implode(", ", $kp_cols);
    $sql = "SELECT $select FROM kp kp " . ($joinMhs ? "LEFT JOIN mahasiswa m ON m.nim = kp.nim " : "");

    if ($kp_created_col) $sql .= "ORDER BY kp.$kp_created_col DESC, kp.id_kp DESC ";
    else $sql .= "ORDER BY kp.id_kp DESC ";

    $sql .= "LIMIT 8";

    $res = $conn->query($sql);
    if ($res) $kp_rows = $res->fetch_all(MYSQLI_ASSOC);
}

/* =========================================================
   LIST: PENGUMUMAN TERBARU (INFORMASI)
   ========================================================= */
$peng_rows = [];
if ($hasPengumuman) {
    $peng_created_col = has_column($conn, 'pengumuman', 'created_at') ? 'created_at'
                    : (has_column($conn, 'pengumuman', 'tanggal') ? 'tanggal' : null);

    $cols = ["id_pengumuman", "judul"];
    if ($peng_created_col) $cols[] = "$peng_created_col AS created_at";

    $sql = "SELECT " . implode(", ", $cols) . " FROM pengumuman ";
    if ($peng_created_col) $sql .= "ORDER BY $peng_created_col DESC, id_pengumuman DESC ";
    else $sql .= "ORDER BY id_pengumuman DESC ";
    $sql .= "LIMIT 5";

    $res = $conn->query($sql);
    if ($res) $peng_rows = $res->fetch_all(MYSQLI_ASSOC);
}

include "../includes/layout_top.php";
include "../includes/sidebar_admin.php";
?>

<style>
  .kpi-title{color:#6c757d;font-size:.9rem}
  .kpi-value{font-size:1.7rem;font-weight:800;line-height:1.1}
  .card-soft{border:0;box-shadow:0 .25rem .75rem rgba(0,0,0,.06)}
  .badge-soft{background:#eef2ff;color:#2f3a9e;border:1px solid #dbe3ff}
</style>

<main class="pc-container">
  <?php include "../includes/header.php"; ?>

  <div class="pc-content">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
      <div>
        <h3 class="fw-bold mb-1">Dashboard Admin</h3>

      </div>
      <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary btn-sm" href="<?= $asset_prefix ?>profile/profile.php">Profil</a>
        <a class="btn btn-outline-dark btn-sm" href="<?= $asset_prefix ?>auth\logout.php">Logout</a>
      </div>
    </div>

    <!-- ===========================
         SECTION: MASTER DATA
         =========================== -->
    <div class="card card-soft mb-3">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div class="fw-bold">MASTER DATA</div>
        <div class="text-muted small">Kelola data inti sistem</div>
      </div>
      <div class="card-body">
        <div class="row g-3">

          <div class="col-md-6">
            <div class="card card-soft">
              <div class="card-body">
                <div class="kpi-title">Total Mahasiswa</div>
                <div class="kpi-value"><?= (int)$total_mhs ?></div>
                <div class="text-muted small">Tabel: mahasiswa</div>
                <a href="data_mahasiswa.php" class="btn btn-sm btn-primary mt-2">Kelola Mahasiswa</a>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="card card-soft">
              <div class="card-body">
                <div class="kpi-title">Total Dosen</div>
                <div class="kpi-value"><?= (int)$total_dosen ?></div>
                <div class="text-muted small">Tabel: dosen</div>
                <a href="data_dosen.php" class="btn btn-sm btn-success mt-2">Kelola Dosen</a>
              </div>
            </div>
          </div>

          <?php if (!$hasMahasiswa || !$hasDosen): ?>
            <div class="col-12">
              <div class="alert alert-warning mb-0">
                Sebagian modul Master Data belum siap: 
                <?= !$hasMahasiswa ? "tabel <b>mahasiswa</b> tidak ditemukan. " : "" ?>
                <?= !$hasDosen ? "tabel <b>dosen</b> tidak ditemukan. " : "" ?>
              </div>
            </div>
          <?php endif; ?>

        </div>
      </div>
    </div>

    <!-- ===========================
         SECTION: KERJA PRAKTIK
         =========================== -->
    <div class="card card-soft mb-3">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div class="fw-bold">KERJA PRAKTIK</div>
        <a href="data_kp.php" class="btn btn-sm btn-outline-warning">Buka Data KP</a>
      </div>
      <div class="card-body">

        <div class="row g-3 mb-3">
          <div class="col-md-3">
            <div class="card card-soft">
              <div class="card-body">
                <div class="kpi-title">Total Data KP</div>
                <div class="kpi-value"><?= (int)$total_kp ?></div>
                <div class="text-muted small">Tabel: kp</div>
              </div>
            </div>
          </div>

          <div class="col-md-9">
            <div class="card card-soft">
              <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                  <span class="badge badge-soft">Pengajuan: <?= (int)$kp_pengajuan ?></span>
                  <span class="badge badge-soft">Diterima: <?= (int)$kp_diterima ?></span>
                  <span class="badge badge-soft">Berlangsung: <?= (int)$kp_berlangsung ?></span>
                  <span class="badge badge-soft">Selesai: <?= (int)$kp_selesai ?></span>
                </div>
                <div class="text-muted small mt-2">
                  Status KP akan tampil jika kolom <b>kp.status</b> tersedia.
                </div>
              </div>
            </div>
          </div>

          <?php if (!$hasKP): ?>
            <div class="col-12">
              <div class="alert alert-warning mb-0">Tabel <b>kp</b> tidak ditemukan.</div>
            </div>
          <?php endif; ?>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="fw-bold">KP Terbaru (Monitoring KP)</div>
          <a href="monitoring_kp.php" class="btn btn-sm btn-outline-dark">Monitoring KP</a>
        </div>

        <?php if (empty($kp_rows)): ?>
          <div class="text-muted">Belum ada data KP yang bisa ditampilkan.</div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th style="width:90px;">ID</th>
                  <th style="width:130px;">NIM</th>
                  <th>Mahasiswa</th>
                  <th>Instansi</th>
                  <th>Posisi</th>
                  <th style="width:140px;">Status</th>
                  <th style="width:170px;">Dibuat</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($kp_rows as $r): ?>
                  <tr>
                    <td><?= (int)($r['id_kp'] ?? 0) ?></td>
                    <td><?= h($r['nim'] ?? '-') ?></td>
                    <td><?= h($r['nama_mhs'] ?? '-') ?></td>
                    <td><?= h($r['nama_instansi'] ?? '-') ?></td>
                    <td><?= h($r['posisi'] ?? '-') ?></td>
                    <td class="text-center">
                      <?php
                        $st = $r['status'] ?? '-';
                        $cls = 'bg-secondary';
                        if ($st === 'Pengajuan') $cls = 'bg-warning text-dark';
                        elseif ($st === 'Diterima') $cls = 'bg-primary';
                        elseif ($st === 'Berlangsung') $cls = 'bg-success';
                        elseif ($st === 'Selesai') $cls = 'bg-dark';
                      ?>
                      <span class="badge <?= $cls ?>"><?= h($st) ?></span>
                    </td>
                    <td><?= !empty($r['created_at']) ? h(date('d M Y H:i', strtotime($r['created_at']))) : '-' ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>

      </div>
    </div>

    <!-- ===========================
         SECTION: MONITORING
         =========================== -->
    <div class="card card-soft mb-3">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div class="fw-bold">MONITORING</div>
        <div class="d-flex gap-2">
          <a href="monitoring_kp.php" class="btn btn-sm btn-outline-dark">Monitoring KP</a>
          <a href="monitoring_sistem.php" class="btn btn-sm btn-outline-secondary">Monitoring Sistem</a>
        </div>
      </div>
      <div class="card-body">
        <div class="row g-3">

          <div class="col-md-4">
            <div class="card card-soft">
              <div class="card-body">
                <div class="kpi-title">Total Users Login</div>
                <div class="kpi-value"><?= (int)$total_user ?></div>
                <div class="text-muted small">Tabel: users</div>
              </div>
            </div>
          </div>

          <div class="col-md-8">
            <div class="card card-soft">
              <div class="card-body">
                <div class="fw-semibold mb-2">Distribusi Role</div>
                <div class="d-flex flex-wrap gap-2">
                  <span class="badge badge-soft">Admin: <?= (int)$user_admin ?></span>
                  <span class="badge badge-soft">Dosen: <?= (int)$user_dosen ?></span>
                  <span class="badge badge-soft">Mahasiswa: <?= (int)$user_mhs ?></span>
                </div>
                <div class="text-muted small mt-2">
                  Jika kolom <b>users.id_role</b> tidak ada, distribusi role tidak ditampilkan akurat.
                </div>
              </div>
            </div>
          </div>

          <?php if (!$hasUsers): ?>
            <div class="col-12">
              <div class="alert alert-warning mb-0">Tabel <b>users</b> tidak ditemukan.</div>
            </div>
          <?php endif; ?>

        </div>
      </div>
    </div>

    <!-- ===========================
         SECTION: INFORMASI
         =========================== -->
    <div class="card card-soft">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div class="fw-bold">INFORMASI</div>
        <a href="pengumuman.php" class="btn btn-sm btn-outline-primary">Kelola Pengumuman</a>
      </div>
      <div class="card-body">
        <?php if (!$hasPengumuman): ?>
          <div class="alert alert-warning mb-0">Tabel <b>pengumuman</b> tidak ditemukan.</div>
        <?php elseif (empty($peng_rows)): ?>
          <div class="text-muted">Belum ada pengumuman.</div>
        <?php else: ?>
          <div class="list-group">
            <?php foreach ($peng_rows as $p): ?>
              <div class="list-group-item d-flex justify-content-between align-items-start">
                <div>
                  <div class="fw-semibold"><?= h($p['judul'] ?? '-') ?></div>
                  <div class="text-muted small">
                    <?= !empty($p['created_at']) ? h(date('d M Y H:i', strtotime($p['created_at']))) : '—' ?>
                  </div>
                </div>
                <span class="badge bg-light text-dark border">#<?= (int)($p['id_pengumuman'] ?? 0) ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
