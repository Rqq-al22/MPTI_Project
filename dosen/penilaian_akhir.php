<?php
require_once "../auth/auth_check.php";
require_role('dosen');
require_once "../config/db.php";

/* ===============================
   KONFIGURASI
   =============================== */
$current_page  = 'penilaian_akhir.php';
$page_title    = "Penilaian Akhir";
$asset_prefix  = "../";
$logout_prefix = "../";

/* ===============================
   AMBIL NIDN DOSEN LOGIN
   =============================== */
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT nidn FROM dosen WHERE id_user = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$dosen = $stmt->get_result()->fetch_assoc();
if (!$dosen) die("Data dosen tidak ditemukan");
$nidn = $dosen['nidn'];

/* ===============================
   PROSES SIMPAN / UPDATE
   =============================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_kp            = intval($_POST['id_kp']);
    $nilai_presensi   = intval($_POST['nilai_presensi']);
    $nilai_laporan    = intval($_POST['nilai_laporan']);
    $nilai_presentasi = intval($_POST['nilai_presentasi']);
    $komentar         = trim($_POST['komentar'] ?? '');

    foreach ([$nilai_presensi, $nilai_laporan, $nilai_presentasi] as $n) {
        if ($n < 0 || $n > 100) die("Nilai tidak valid");
    }

    // HITUNG NILAI AKHIR (BOBOT)
    $nilai_akhir = round(
        (0.2 * $nilai_presensi) +
        (0.4 * $nilai_laporan) +
        (0.4 * $nilai_presentasi)
    );

    // CEK SUDAH ADA / BELUM
    $cek = $conn->prepare("
        SELECT id_penilaian_akhir
        FROM penilaian_akhir
        WHERE id_kp = ?
        LIMIT 1
    ");
    $cek->bind_param("i", $id_kp);
    $cek->execute();
    $ada = $cek->get_result()->fetch_assoc();

    if ($ada) {
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
    }

    header("Location: penilaian_akhir.php?success=1");
    exit;
}

/* ===============================
   AMBIL KP MAHASISWA BIMBINGAN
   =============================== */
$kp = $conn->prepare("
    SELECT
        k.id_kp,
        m.nim,
        m.nama,
        pa.nilai_akhir
    FROM kp k
    JOIN mahasiswa m ON k.nim = m.nim
    LEFT JOIN penilaian_akhir pa ON pa.id_kp = k.id_kp
    WHERE
        k.nidn = ?
        AND k.status = 'Selesai'
    ORDER BY m.nama
");
$kp->bind_param("s", $nidn);
$kp->execute();
$kp_list = $kp->get_result();

/* ===============================
   LAYOUT
   =============================== */
include "../includes/layout_top.php";
include "../includes/sidebar_dosen.php";
?>

<main class="pc-container">
<?php include "../includes/header.php"; ?>

<div class="pc-content">

<h3 class="fw-bold mb-2">Penilaian Akhir Kerja Praktik</h3>
<p class="text-muted mb-4">
  Penilaian akhir mahasiswa Kerja Praktik (Presensi, Laporan, Presentasi).
</p>

<!-- FORM -->
<div class="card mb-4">
<div class="card-body">
<form method="POST">

<div class="row g-3">

<div class="col-md-6">
  <label class="form-label">Mahasiswa KP</label>
  <select name="id_kp" class="form-select" required>
    <option value="">-- Pilih Mahasiswa --</option>
    <?php while ($m = $kp_list->fetch_assoc()): ?>
      <option value="<?= $m['id_kp'] ?>">
        <?= htmlspecialchars($m['nama']) ?> (<?= $m['nim'] ?>)
        <?= $m['nilai_akhir'] !== null ? '✔' : '' ?>
      </option>
    <?php endwhile; ?>
  </select>
</div>

<div class="col-md-2">
  <label class="form-label">Presensi</label>
  <input type="number" name="nilai_presensi" class="form-control" min="0" max="100" required>
</div>

<div class="col-md-2">
  <label class="form-label">Laporan</label>
  <input type="number" name="nilai_laporan" class="form-control" min="0" max="100" required>
</div>

<div class="col-md-2">
  <label class="form-label">Presentasi</label>
  <input type="number" name="nilai_presentasi" class="form-control" min="0" max="100" required>
</div>

<div class="col-md-12">
  <label class="form-label">Komentar</label>
  <textarea name="komentar" class="form-control" rows="3"></textarea>
</div>

</div>

<button class="btn btn-primary mt-3">
  Simpan Penilaian Akhir
</button>

</form>
</div>
</div>

</div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
