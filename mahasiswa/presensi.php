<?php
require_once "../auth/auth_check.php";
require_role('mahasiswa');
require_once "../config/db.php";

/* ================== KONFIGURASI ================== */
$current_page  = 'presensi.php';
$page_title    = "Presensi Mahasiswa";
$asset_prefix  = "../";
$logout_prefix = "../";

/* ================== AMBIL NIM ================== */
$nim = $_SESSION['nim'] ?? null;
if (!$nim) {
    die("NIM tidak ditemukan dalam sesi login.");
}

$tanggal_hari_ini = date('Y-m-d');
$error  = '';
$sukses = '';

/* =========================================================
   0) DETEKSI STRUKTUR TABEL PRESENSI: PAKAI id_kp ATAU nim
   ========================================================= */
$useIdKp = false;
$cekKolom = $conn->prepare("
    SELECT COUNT(*) AS cnt
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'presensi'
      AND COLUMN_NAME = 'id_kp'
");
$cekKolom->execute();
$resKolom = $cekKolom->get_result()->fetch_assoc();
$cekKolom->close();
$useIdKp = ((int)($resKolom['cnt'] ?? 0) > 0);

/* =========================================================
   1) CEK KP AKTIF (WAJIB BERLANGSUNG)
   ========================================================= */
$stmt = $conn->prepare("
    SELECT id_kp, nama_instansi, posisi
    FROM kp
    WHERE nim = ?
      AND status = 'Berlangsung'
    LIMIT 1
");
$stmt->bind_param("s", $nim);
$stmt->execute();
$kp = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$kp) {
    $error = "Presensi hanya dapat dilakukan jika KP berstatus BERLANGSUNG.";
}

/* =========================================================
   2) CEK PRESENSI HARI INI
   ========================================================= */
$presensi_hari_ini = null;
$sudah_presensi = false;

if ($kp) {
    if ($useIdKp) {
        // presensi berbasis id_kp
        $cek = $conn->prepare("
            SELECT *
            FROM presensi
            WHERE id_kp = ?
              AND tanggal = ?
            LIMIT 1
        ");
        $cek->bind_param("is", $kp['id_kp'], $tanggal_hari_ini);
    } else {
        // presensi berbasis nim
        $cek = $conn->prepare("
            SELECT *
            FROM presensi
            WHERE nim = ?
              AND tanggal = ?
            LIMIT 1
        ");
        $cek->bind_param("ss", $nim, $tanggal_hari_ini);
    }

    $cek->execute();
    $presensi_hari_ini = $cek->get_result()->fetch_assoc();
    $cek->close();

    $sudah_presensi = $presensi_hari_ini ? true : false;
}

/* =========================================================
   3) PROSES SUBMIT
   ========================================================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$sudah_presensi && $kp) {

    $status    = $_POST['status'] ?? '';
    $latitude  = $_POST['latitude'] ?? null;
    $longitude = $_POST['longitude'] ?? null;

    if ($status === '') {
        $error = "Status kehadiran wajib dipilih.";
    }

    // Normalisasi gps kosong jadi null
    if ($latitude === '' || $latitude === null) $latitude = null;
    if ($longitude === '' || $longitude === null) $longitude = null;

    $foto_name = null;

    if ($status === 'Hadir') {

        if (!isset($_FILES['bukti_foto']) || $_FILES['bukti_foto']['error'] !== 0) {
            $error = "Foto bukti kehadiran wajib diunggah.";
        }

        if ($latitude === null || $longitude === null) {
            $error = "GPS wajib diaktifkan untuk presensi hadir.";
        }

        if ($error === '') {
            $upload_dir = "../uploads/presensi/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $ext = strtolower(pathinfo($_FILES['bukti_foto']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','webp'];
            if (!in_array($ext, $allowed)) {
                $error = "Format foto harus jpg/jpeg/png/webp.";
            } else {
                $foto_name = "presensi_{$nim}_" . time() . "." . $ext;

                move_uploaded_file(
                    $_FILES['bukti_foto']['tmp_name'],
                    $upload_dir . $foto_name
                );
            }
        }
    }

    if ($error === '') {
        if ($useIdKp) {
            // INSERT berbasis id_kp
            $stmt = $conn->prepare("
                INSERT INTO presensi
                (id_kp, tanggal, status, bukti_foto, latitude, longitude)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            // Catatan: null untuk double akan jadi 0 di mysqli; kalau ingin NULL beneran, perlu set query dinamis.
            $lat = $latitude ?? 0;
            $lng = $longitude ?? 0;

            $stmt->bind_param(
                "isssdd",
                $kp['id_kp'],
                $tanggal_hari_ini,
                $status,
                $foto_name,
                $lat,
                $lng
            );
        } else {
            // INSERT berbasis nim
            $stmt = $conn->prepare("
                INSERT INTO presensi
                (nim, tanggal, status, bukti_foto, latitude, longitude)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $lat = $latitude ?? 0;
            $lng = $longitude ?? 0;

            $stmt->bind_param(
                "ssssdd",
                $nim,
                $tanggal_hari_ini,
                $status,
                $foto_name,
                $lat,
                $lng
            );
        }

        if ($stmt->execute()) {
            $sukses = "Presensi berhasil disimpan.";
            $stmt->close();
            header("Refresh:1");
        } else {
            $error = "Gagal menyimpan presensi.";
            $stmt->close();
        }
    }
}
?>

<?php include "../includes/layout_top.php"; ?>
<?php include "../includes/sidebar_mahasiswa.php"; ?>

<main class="pc-container">
<?php include "../includes/header.php"; ?>

<div class="pc-content">

<h3 class="mb-3">Presensi Mahasiswa</h3>

<!-- INFO -->
<div class="card mb-3">
  <div class="card-body">
    <div class="row">
      <div class="col-md-4"><b>NIM</b><br><?= htmlspecialchars($nim) ?></div>
      <div class="col-md-4"><b>Tanggal</b><br><?= date('d M Y') ?></div>
      <div class="col-md-4">
        <b>Status Hari Ini</b><br>
        <?php if ($sudah_presensi): ?>
          <span class="badge bg-success"><?= htmlspecialchars($presensi_hari_ini['status']) ?></span>
        <?php else: ?>
          <span class="badge bg-warning text-dark">Belum Presensi</span>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php if ($error): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($sukses): ?>
  <div class="alert alert-success"><?= htmlspecialchars($sukses) ?></div>
<?php endif; ?>

<?php if ($kp && $sudah_presensi): ?>

  <!-- DETAIL PRESENSI -->
  <div class="card mb-4">
    <div class="card-body">
      <h5>Detail Presensi Hari Ini</h5>
      <p class="mb-2">
        <b>Status:</b> <?= htmlspecialchars($presensi_hari_ini['status']) ?><br>
        <?php if (!empty($presensi_hari_ini['latitude'])): ?>
          <b>Lokasi:</b> <?= htmlspecialchars($presensi_hari_ini['latitude']) ?> , <?= htmlspecialchars($presensi_hari_ini['longitude']) ?><br>
        <?php endif; ?>
      </p>

      <?php if (!empty($presensi_hari_ini['bukti_foto'])): ?>
        <img src="../uploads/presensi/<?= htmlspecialchars($presensi_hari_ini['bukti_foto']) ?>"
             class="img-thumbnail mt-2"
             style="max-width:200px">
      <?php endif; ?>
    </div>
  </div>

<?php elseif ($kp): ?>

  <!-- FORM PRESENSI -->
  <form method="POST" enctype="multipart/form-data" class="card p-4 col-md-6">

    <div class="mb-3">
      <label>Status Kehadiran</label>
      <select name="status" class="form-control" required>
        <option value="">-- Pilih --</option>
        <option value="Hadir">Hadir</option>
        <option value="Izin">Izin</option>
        <option value="Alpha">Alpha</option>
      </select>
    </div>

    <div class="mb-3">
      <label>Bukti Foto (Wajib jika Hadir)</label>
      <input type="file" name="bukti_foto" class="form-control" accept="image/*">
    </div>

    <input type="hidden" name="latitude" id="latitude">
    <input type="hidden" name="longitude" id="longitude">

    <button class="btn btn-primary">Kirim Presensi</button>

  </form>

<?php endif; ?>

</div>
</main>

<?php include "../includes/layout_bottom.php"; ?>

<script>
navigator.geolocation.getCurrentPosition(
  function(pos) {
    document.getElementById('latitude').value  = pos.coords.latitude;
    document.getElementById('longitude').value = pos.coords.longitude;
  },
  function() {
    // jangan blokir untuk izin/alpha, hanya alert
    // alert("GPS harus diaktifkan untuk presensi.");
  }
);
</script>
