<?php
require_once "../auth/auth_check.php";
require_role('dosen');
require_once "../config/db.php";

$current_page  = 'presensi_mhs.php';
$page_title    = "Presensi Mahasiswa";
$asset_prefix  = "../";
$logout_prefix = "../";

include "../includes/layout_top.php";
include "../includes/sidebar_dosen.php";

/* ======================================================
   AMBIL NIDN DOSEN DARI SESSION (LEWAT USERS)
   ====================================================== */
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
    echo "<div class='alert alert-danger m-3'>Data dosen tidak ditemukan.</div>";
    include "../includes/layout_bottom.php";
    exit;
}

$nidn = $dosen['nidn'];
?>

<main class="pc-container">
  <?php include "../includes/header.php"; ?>

  <div class="pc-content">

    <h3 class="mb-4">Presensi Mahasiswa Bimbingan</h3>

<?php
/* ======================================================
   AMBIL PRESENSI MAHASISWA BIMBINGAN DOSEN
   ====================================================== */
$q = $conn->prepare("
    SELECT 
        p.id_presensi,
        p.nim,
        m.nama,
        p.tanggal,
        p.status,
        p.bukti_foto,
        p.latitude,
        p.longitude,
        p.validasi
    FROM presensi p
    JOIN mahasiswa m ON m.nim = p.nim
    JOIN bimbingan b ON b.nim = p.nim
    WHERE b.nidn = ?
    ORDER BY p.tanggal DESC
");
$q->bind_param("s", $nidn);
$q->execute();
$res = $q->get_result();

if ($res->num_rows === 0):
?>

    <div class="alert alert-info">
        Belum ada data presensi mahasiswa bimbingan.
    </div>

<?php else: ?>

    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-light">
          <tr>
            <th>NIM / Nama</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Foto</th>
            <th>Lokasi</th>
            <th>Validasi</th>
          </tr>
        </thead>
        <tbody>

<?php while ($r = $res->fetch_assoc()): ?>
          <tr>
            <td>
              <?= htmlspecialchars($r['nim']) ?><br>
              <small><?= htmlspecialchars($r['nama']) ?></small>
            </td>

            <td><?= date('d M Y', strtotime($r['tanggal'])) ?></td>

            <td>
              <span class="badge bg-info">
                <?= htmlspecialchars($r['status']) ?>
              </span>
            </td>

            <td>
<?php if (!empty($r['bukti_foto'])): ?>
              <img src="../uploads/presensi/<?= htmlspecialchars($r['bukti_foto']) ?>"
                   width="80" class="img-thumbnail">
<?php else: ?>
              -
<?php endif; ?>
            </td>

            <td>
              <?= htmlspecialchars($r['latitude']) ?><br>
              <?= htmlspecialchars($r['longitude']) ?>
            </td>

            <td>
<?php if ($r['validasi'] === 'Pending'): ?>
              <a href="validasi_presensi.php?id=<?= (int)$r['id_presensi'] ?>&aksi=approve"
                 class="btn btn-success btn-sm"
                 onclick="return confirm('Setujui presensi ini?')">
                 Approve
              </a>

              <a href="validasi_presensi.php?id=<?= (int)$r['id_presensi'] ?>&aksi=reject"
                 class="btn btn-danger btn-sm"
                 onclick="return confirm('Tolak presensi ini?')">
                 Reject
              </a>
<?php elseif ($r['validasi'] === 'Approve'): ?>
              <span class="badge bg-success">Approved</span>
<?php else: ?>
              <span class="badge bg-danger">Rejected</span>
<?php endif; ?>
            </td>
          </tr>
<?php endwhile; ?>

        </tbody>
      </table>
    </div>

<?php endif; ?>

  </div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
