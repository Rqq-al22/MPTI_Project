<?php
require_once "../auth/auth_check.php";
require_role('dosen');
require_once "../config/db.php";

$current_page  = 'penilaian_mingguan.php';
$page_title    = "Penilaian Mingguan";
$asset_prefix  = "../";
$logout_prefix = "../";

/**
 * Sumber nidn:
 * - Jika Anda menyimpan NIDN di session lain (mis. $_SESSION['nidn']), ganti di sini.
 * - Untuk sementara saya pakai username sebagai nidn (sesuai login Anda sebelumnya).
 */
$nidn = $_SESSION['username'] ?? '';

if ($nidn === '') {
    // Jika nidn kosong, lebih aman logout paksa
    header("Location: ../auth/login_form.php?error=session");
    exit;
}

/* =========================================================
   SIMPAN / UPDATE PENILAIAN MINGGUAN
   - Sesuai tabel: id_laporan_mingguan, nidn, nilai, komentar
   - created_at otomatis oleh DB
   ========================================================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_laporan_mingguan = (int)($_POST['id_laporan_mingguan'] ?? 0);
    $nilai               = (int)($_POST['nilai'] ?? -1);
    $komentar            = trim($_POST['komentar'] ?? '');

    // Validasi minimal
    if ($id_laporan_mingguan <= 0) {
        header("Location: penilaian_mingguan.php?error=id_laporan");
        exit;
    }
    if ($nilai < 0 || $nilai > 100) {
        header("Location: penilaian_mingguan.php?error=nilai");
        exit;
    }

    // Cek apakah penilaian untuk laporan ini sudah ada (unik per id_laporan_mingguan)
    $cek = $conn->prepare("
        SELECT id_penilaian_mingguan
        FROM penilaian_mingguan
        WHERE id_laporan_mingguan = ?
        LIMIT 1
    ");
    $cek->bind_param("i", $id_laporan_mingguan);
    $cek->execute();
    $res = $cek->get_result();
    $existing = $res->fetch_assoc();
    $cek->close();

    if ($existing) {
        // UPDATE: tidak mengubah nidn (optional), tapi bisa juga diubah jika Anda mau
        $stmt = $conn->prepare("
            UPDATE penilaian_mingguan
            SET nilai = ?, komentar = ?
            WHERE id_laporan_mingguan = ?
        ");
        $stmt->bind_param("isi", $nilai, $komentar, $id_laporan_mingguan);
        $stmt->execute();
        $stmt->close();

        header("Location: penilaian_mingguan.php?success=update");
        exit;
    } else {
        // INSERT
        $stmt = $conn->prepare("
            INSERT INTO penilaian_mingguan (id_laporan_mingguan, nidn, nilai, komentar)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("isis", $id_laporan_mingguan, $nidn, $nilai, $komentar);
        $stmt->execute();
        $stmt->close();

        header("Location: penilaian_mingguan.php?success=insert");
        exit;
    }
}

/* =========================================================
   DATA PENILAIAN (Sesuai tabel penilaian_mingguan)
   ========================================================= */
$data = $conn->query("
    SELECT
        id_penilaian_mingguan,
        id_laporan_mingguan,
        nidn,
        nilai,
        komentar,
        created_at
    FROM penilaian_mingguan
    ORDER BY created_at DESC
");

include "../includes/layout_top.php";
include "../includes/sidebar_dosen.php";
?>

<main class="pc-container">
<?php include "../includes/header.php"; ?>

<div class="pc-content">

  <h3>Penilaian Mingguan</h3>
  <p>Input penilaian berdasarkan <b>ID Laporan Mingguan</b> (sesuai tabel penilaian_mingguan).</p>

  <!-- FORM INPUT -->
  <div class="card mb-4">
    <div class="card-header"><strong>Input / Update Penilaian</strong></div>
    <div class="card-body">
      <form method="POST">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">ID Laporan Mingguan</label>
            <input type="number" name="id_laporan_mingguan" class="form-control" min="1" required>
            <small class="text-muted">Isi sesuai laporan_mingguan.id_laporan_mingguan</small>
          </div>

          <div class="col-md-4">
            <label class="form-label">Nilai (0-100)</label>
            <input type="number" name="nilai" class="form-control" min="0" max="100" required>
          </div>

          <div class="col-md-12">
            <label class="form-label">Komentar</label>
            <textarea name="komentar" class="form-control" rows="3" placeholder="Catatan dosen (opsional)"></textarea>
          </div>
        </div>

        <button class="btn btn-primary mt-3">Simpan</button>
      </form>
    </div>
  </div>

  <!-- LIST DATA -->
  <div class="card">
    <div class="card-header"><strong>Daftar Penilaian Mingguan</strong></div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered align-middle">
          <thead>
            <tr>
              <th>ID Penilaian</th>
              <th>ID Laporan</th>
              <th>NIDN</th>
              <th>Nilai</th>
              <th>Komentar</th>
              <th>Created</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($data && $data->num_rows > 0): ?>
              <?php while ($row = $data->fetch_assoc()): ?>
                <tr>
                  <td><?= (int)$row['id_penilaian_mingguan'] ?></td>
                  <td><?= (int)$row['id_laporan_mingguan'] ?></td>
                  <td><?= htmlspecialchars($row['nidn']) ?></td>
                  <td><?= (int)$row['nilai'] ?></td>
                  <td><?= nl2br(htmlspecialchars($row['komentar'] ?? '')) ?></td>
                  <td><?= htmlspecialchars($row['created_at']) ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="6" class="text-muted">Belum ada data penilaian.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
