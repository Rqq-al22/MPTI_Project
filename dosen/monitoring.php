<?php
/************************************************************
 * FILE : dosen/monitoring.php
 * FUNGSI :
 * - Menampilkan monitoring Kerja Praktik mahasiswa bimbingan
 * - Data diambil dari tabel monitoring_kp (hasil trigger)
 ************************************************************/

require_once "../auth/auth_check.php";
require_role('dosen');
require_once "../config/db.php";

/* ===============================
   KONFIGURASI HALAMAN
================================ */
$current_page  = 'monitoring.php';
$page_title    = 'Monitoring Kerja Praktik';
$asset_prefix  = "../";
$logout_prefix = "../";

/* ===============================
   AMBIL NIDN DOSEN LOGIN
================================ */
$user_id = $_SESSION['user_id'] ?? 0;

$stmt = $conn->prepare("
    SELECT nidn 
    FROM dosen 
    WHERE id_user = ? 
    LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$dosen = $stmt->get_result()->fetch_assoc();

if (!$dosen) {
    die("Data dosen tidak ditemukan.");
}
$nidn = $dosen['nidn'];

/* ===============================
   AMBIL DATA MONITORING KP
   (SUMBER UTAMA: monitoring_kp)
================================ */
$stmt = $conn->prepare("
    SELECT
        mhs.nim,
        mhs.nama AS nama_mhs,
        k.nama_instansi,

        mk.total_laporan,
        mk.total_presensi,
        mk.dokumen_akhir,
        mk.status_kp,
        mk.last_update

    FROM monitoring_kp mk
    JOIN kp k           ON mk.id_kp = k.id_kp
    JOIN mahasiswa mhs  ON k.nim = mhs.nim

    WHERE k.nidn = ?
    ORDER BY mhs.nama ASC
");
$stmt->bind_param("s", $nidn);
$stmt->execute();
$result = $stmt->get_result();

/* ===============================
   HELPER BADGE STATUS
================================ */
function badgeStatus($status)
{
    switch ($status) {
        case 'Pengajuan':
            return ['secondary', 'Pengajuan'];
        case 'Diterima':
            return ['info', 'Diterima'];
        case 'Berlangsung':
            return ['success', 'Berlangsung'];
        case 'Selesai':
            return ['primary', 'Selesai'];
        default:
            return ['dark', 'Tidak Diketahui'];
    }
}

function badgeDokumen($status)
{
    return $status === 'Sudah'
        ? ['success', 'Sudah']
        : ['secondary', 'Belum'];
}

/* ===============================
   LAYOUT
================================ */
include "../includes/layout_top.php";
include "../includes/sidebar_dosen.php";
?>

<main class="pc-container">
<?php include "../includes/header.php"; ?>

<div class="pc-content">

  <h3 class="fw-bold mb-1">Monitoring Kerja Praktik</h3>
  <p class="text-muted mb-4">
    Monitoring perkembangan Kerja Praktik mahasiswa bimbingan
    berdasarkan laporan, presensi, dan dokumen akhir.
  </p>

  <div class="card shadow-sm border-0">
    <div class="card-body">

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th width="12%">NIM</th>
              <th width="20%">Mahasiswa</th>
              <th>Instansi</th>
              <th class="text-center" width="12%">Laporan</th>
              <th class="text-center" width="12%">Presensi</th>
              <th class="text-center" width="14%">Dokumen Akhir</th>
              <th class="text-center" width="14%">Status KP</th>
              <th width="16%">Update Terakhir</th>
            </tr>
          </thead>

          <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <?php
                [$badgeStatus, $labelStatus] = badgeStatus($row['status_kp']);
                [$badgeDok, $labelDok] = badgeDokumen($row['dokumen_akhir']);
              ?>
              <tr>
                <td class="fw-semibold">
                  <?= htmlspecialchars($row['nim']) ?>
                </td>

                <td>
                  <?= htmlspecialchars($row['nama_mhs']) ?>
                </td>

                <td>
                  <?= htmlspecialchars($row['nama_instansi']) ?>
                </td>

                <td class="text-center">
                  <span class="badge bg-info">
                    <?= (int)$row['total_laporan'] ?>
                  </span>
                </td>

                <td class="text-center">
                  <span class="badge bg-info">
                    <?= (int)$row['total_presensi'] ?>
                  </span>
                </td>

                <td class="text-center">
                  <span class="badge bg-<?= $badgeDok ?>">
                    <?= $labelDok ?>
                  </span>
                </td>

                <td class="text-center">
                  <span class="badge bg-<?= $badgeStatus ?>">
                    <?= $labelStatus ?>
                  </span>
                </td>

                <td>
                  <?= date('d M Y H:i', strtotime($row['last_update'])) ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="text-center py-4 text-muted">
                Belum ada data monitoring Kerja Praktik.
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
