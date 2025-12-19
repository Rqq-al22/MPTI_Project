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
   AMBIL MAHASISWA BIMBINGAN
   =============================== */
$stmt = $conn->prepare("
    SELECT 
        k.nim,
        m.nama AS nama_mhs,
        k.nama_instansi,
        k.status
    FROM kp k
    JOIN mahasiswa m ON k.nim = m.nim
    WHERE k.nidn = ?
    ORDER BY k.tgl_mulai DESC
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
        case 'Disetujui':
        case 'Aktif':
            return ['success', 'Aktif'];
        case 'Selesai':
            return ['primary', 'Selesai'];
        case 'Ditolak':
            return ['danger', 'Ditolak'];
        default:
            return ['secondary', 'Belum Ditentukan'];
    }
}

/* ===============================
   LAYOUT
   =============================== */
include "../includes/layout_top.php";
include "../includes/sidebar_dosen.php";
?>

<main class="pc-container">
<?php include "../includes/header.php"; ?>

<div class="pc-content">

  <h3 class="fw-bold mb-1">Mahasiswa Bimbingan</h3>
  <p class="text-muted mb-4">
    Daftar mahasiswa Kerja Praktik yang telah ditetapkan oleh admin.
  </p>

  <div class="card shadow-sm border-0">
    <div class="card-body">

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th width="15%">NIM</th>
              <th width="30%">Nama Mahasiswa</th>
              <th>Instansi</th>
              <th width="18%" class="text-center">Status KP</th>
            </tr>
          </thead>

          <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <?php [$badge, $label] = badgeStatusKP($row['status']); ?>
              <tr>
                <td class="fw-semibold">
                  <?= htmlspecialchars($row['nim']) ?>
                </td>
                <td>
                  <div class="fw-semibold">
                    <?= htmlspecialchars($row['nama_mhs']) ?>
                  </div>
                </td>
                <td>
                  <?= htmlspecialchars($row['nama_instansi']) ?>
                </td>
                <td class="text-center">
                  <span class="badge bg-<?= $badge ?> px-3 py-2">
                    <?= $label ?>
                  </span>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" class="text-center py-4 text-muted">
                Belum ada mahasiswa bimbingan
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
