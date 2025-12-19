<?php
require_once "../auth/auth_check.php";
require_role('dosen');
require_once "../config/db.php";

$current_page  = 'penilaian_akhir.php';
$page_title    = "Penilaian Akhir";
$asset_prefix  = "../";
$logout_prefix = "../";

/**
 * Asumsi:
 * - username dosen = NIDN
 * Jika Anda punya session khusus nidn, silakan ganti.
 */
$nidn = $_SESSION['username'] ?? '';
if ($nidn === '') {
    header("Location: ../auth/login_form.php");
    exit;
}

/* =====================================================
   SIMPAN / UPDATE PENILAIAN AKHIR
   ===================================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_kp             = (int)($_POST['id_kp'] ?? 0);
    $nilai_presensi    = (int)($_POST['nilai_presensi'] ?? -1);
    $nilai_laporan     = (int)($_POST['nilai_laporan'] ?? -1);
    $nilai_presentasi  = (int)($_POST['nilai_presentasi'] ?? -1);
    $komentar          = trim($_POST['komentar'] ?? '');

    // Validasi dasar
    if ($id_kp <= 0) {
        header("Location: penilaian_akhir.php?error=id_kp");
        exit;
    }
    foreach ([$nilai_presensi, $nilai_laporan, $nilai_presentasi] as $n) {
        if ($n < 0 || $n > 100) {
            header("Location: penilaian_akhir.php?error=nilai");
            exit;
        }
    }

    // Hitung nilai akhir (bobot bisa dijelaskan di laporan)
    $nilai_akhir = round(
        (0.2 * $nilai_presensi) +
        (0.4 * $nilai_laporan) +
        (0.4 * $nilai_presentasi)
    );

    // Cek apakah sudah ada penilaian akhir untuk KP ini
    $cek = $conn->prepare("
        SELECT id_penilaian_akhir
        FROM penilaian_akhir
        WHERE id_kp = ?
        LIMIT 1
    ");
    $cek->bind_param("i", $id_kp);
    $cek->execute();
    $res = $cek->get_result();
    $existing = $res->fetch_assoc();
    $cek->close();

    if ($existing) {
        // UPDATE
        $stmt = $conn->prepare("
            UPDATE penilaian_akhir
            SET
              nilai_presensi   = ?,
              nilai_laporan    = ?,
              nilai_presentasi = ?,
              nilai_akhir      = ?,
              komentar         = ?
            WHERE id_kp = ?
        ");
        $stmt->bind_param(
            "iiiisi",
            $nilai_presensi,
            $nilai_laporan,
            $nilai_presentasi,
            $nilai_akhir,
            $komentar,
            $id_kp
        );
        $stmt->execute();
        $stmt->close();

        header("Location: penilaian_akhir.php?success=update");
        exit;

    } else {
        // INSERT
        $stmt = $conn->prepare("
            INSERT INTO penilaian_akhir
            (id_kp, nidn, nilai_presensi, nilai_laporan, nilai_presentasi, nilai_akhir, komentar)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "isiiiis",
            $id_kp,
            $nidn,
            $nilai_presensi,
            $nilai_laporan,
            $nilai_presentasi,
            $nilai_akhir,
            $komentar
        );
        $stmt->execute();
        $stmt->close();

        header("Location: penilaian_akhir.php?success=insert");
        exit;
    }
}

/* =====================================================
   DATA PENILAIAN AKHIR
   ===================================================== */
$data = $conn->query("
    SELECT
      id_penilaian_akhir,
      id_kp,
      nidn,
      nilai_presensi,
      nilai_laporan,
      nilai_presentasi,
      nilai_akhir,
      komentar,
      created_at
    FROM penilaian_akhir
    ORDER BY created_at DESC
");

include "../includes/layout_top.php";
include "../includes/sidebar_dosen.php";
?>

<main class="pc-container">
<?php include "../includes/header.php"; ?>

<div class="pc-content">

<h3>Penilaian Akhir Kerja Praktik</h3>

<!-- FORM INPUT -->
<div class="card mb-4">
  <div class="card-header"><strong>Input / Update Penilaian Akhir</strong></div>
  <div class="card-body">
    <form method="POST">
      <div class="row g-3">

        <div class="col-md-3">
          <label class="form-label">ID KP</label>
          <input type="number" name="id_kp" class="form-control" min="1" required>
        </div>

        <div class="col-md-3">
          <label class="form-label">Nilai Presensi</label>
          <input type="number" name="nilai_presensi" class="form-control" min="0" max="100" required>
        </div>

        <div class="col-md-3">
          <label class="form-label">Nilai Laporan</label>
          <input type="number" name="nilai_laporan" class="form-control" min="0" max="100" required>
        </div>

        <div class="col-md-3">
          <label class="form-label">Nilai Presentasi</label>
          <input type="number" name="nilai_presentasi" class="form-control" min="0" max="100" required>
        </div>

        <div class="col-md-12">
          <label class="form-label">Komentar</label>
          <textarea name="komentar" class="form-control" rows="3"></textarea>
        </div>

      </div>

      <button class="btn btn-primary mt-3">Simpan Penilaian Akhir</button>
    </form>
  </div>
</div>

<!-- LIST DATA -->
<div class="card">
  <div class="card-header"><strong>Daftar Penilaian Akhir</strong></div>
  <div class="card-body table-responsive">
    <table class="table table-bordered align-middle">
      <thead>
        <tr>
          <th>ID KP</th>
          <th>Presensi</th>
          <th>Laporan</th>
          <th>Presentasi</th>
          <th>Nilai Akhir</th>
          <th>Komentar</th>
          <th>Tanggal</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($data && $data->num_rows > 0): ?>
          <?php while ($row = $data->fetch_assoc()): ?>
          <tr>
            <td><?= (int)$row['id_kp'] ?></td>
            <td><?= (int)$row['nilai_presensi'] ?></td>
            <td><?= (int)$row['nilai_laporan'] ?></td>
            <td><?= (int)$row['nilai_presentasi'] ?></td>
            <td><strong><?= (int)$row['nilai_akhir'] ?></strong></td>
            <td><?= nl2br(htmlspecialchars($row['komentar'] ?? '')) ?></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
          </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="7" class="text-muted">Belum ada penilaian akhir.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

</div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
