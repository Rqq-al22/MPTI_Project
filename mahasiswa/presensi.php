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
$nim = is_string($nim) ? trim($nim) : null;

if (!$nim) die("NIM tidak ditemukan dalam sesi login.");
if (!preg_match('/^[A-Za-z0-9]+$/', $nim)) die("Format NIM tidak valid.");

$tanggal_hari_ini = date('Y-m-d');
$error  = '';
$sukses = '';

/* ================== CSRF ================== */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

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
   Helper: cek kolom ada/tidak (untuk kolom optional)
   ========================================================= */
function col_exists(mysqli $conn, string $table, string $col): bool {
    $st = $conn->prepare("
        SELECT COUNT(*) AS cnt
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = ?
          AND COLUMN_NAME = ?
    ");
    $st->bind_param("ss", $table, $col);
    $st->execute();
    $r = $st->get_result()->fetch_assoc();
    $st->close();
    return ((int)($r['cnt'] ?? 0) > 0);
}

/* =========================================================
   Helper: bind_param dinamis (butuh reference)
   ========================================================= */
function bind_params(mysqli_stmt $stmt, string $types, array &$params): void {
    $refs = [];
    foreach ($params as $k => &$v) $refs[$k] = &$v;
    array_unshift($refs, $types);
    call_user_func_array([$stmt, 'bind_param'], $refs);
}

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
        $cek = $conn->prepare("SELECT * FROM presensi WHERE id_kp = ? AND tanggal = ? LIMIT 1");
        $cek->bind_param("is", $kp['id_kp'], $tanggal_hari_ini);
    } else {
        $cek = $conn->prepare("SELECT * FROM presensi WHERE nim = ? AND tanggal = ? LIMIT 1");
        $cek->bind_param("ss", $nim, $tanggal_hari_ini);
    }
    $cek->execute();
    $presensi_hari_ini = $cek->get_result()->fetch_assoc();
    $cek->close();
    $sudah_presensi = $presensi_hari_ini ? true : false;
}

/* =========================================================
   3) PROSES SUBMIT
   - Lokasi TIDAK diambil dari POST (mudah dimanipulasi).
   - Lokasi diambil dari SESSION yang dikunci via endpoint AJAX.
   ========================================================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$sudah_presensi && $kp) {

    // CSRF check
    $posted_token = $_POST['csrf_token'] ?? '';
    if (!is_string($posted_token) || !hash_equals($_SESSION['csrf_token'], $posted_token)) {
        $error = "Permintaan tidak valid (CSRF). Silakan muat ulang halaman.";
    }

    $status = $_POST['status'] ?? '';
    $status = is_string($status) ? trim($status) : '';
    $allowed_status = ['Hadir', 'Izin', 'Alpha'];

    if ($error === '' && !in_array($status, $allowed_status, true)) {
        $error = "Status kehadiran tidak valid.";
    }

    // Ambil GEO dari session (hasil “lock lokasi” via AJAX)
    $geo = $_SESSION['presensi_geo'] ?? null;
    if ($error === '' && (!is_array($geo) || empty($geo['lat']) || empty($geo['lng']) || empty($geo['ts']))) {
        $error = "Lokasi belum terkunci. Klik 'Ambil Lokasi' lalu izinkan GPS.";
    }

    // Validasi waktu & akurasi (mengurangi manipulasi sederhana)
    $latitude = null; $longitude = null; $accuracy = null;
    if ($error === '') {
        $age = time() - (int)$geo['ts'];                 // umur data lokasi
        $latitude  = (float)$geo['lat'];
        $longitude = (float)$geo['lng'];
        $accuracy  = isset($geo['acc']) ? (float)$geo['acc'] : null;

        if ($age > 180) { // 3 menit (ubah jika perlu)
            $error = "Lokasi terlalu lama. Ambil lokasi ulang agar valid.";
        }
        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            $error = "Koordinat lokasi tidak valid.";
        }
        $gps_warning = '';
        if ($accuracy !== null && $accuracy > 150) {
          $gps_warning = "Akurasi GPS rendah (±" . round($accuracy) . " m). Presensi tetap disimpan, namun lokasi kurang presisi.";
        }

    }

    // Upload foto jika Hadir
    $foto_name = null;

    if ($error === '' && $status === 'Hadir') {
        if (!isset($_FILES['bukti_foto']) || $_FILES['bukti_foto']['error'] !== 0) {
            $error = "Foto bukti kehadiran wajib diunggah.";
        } else {
            $upload_dir = "../uploads/presensi/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            $ext = strtolower(pathinfo($_FILES['bukti_foto']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','webp'];
            if (!in_array($ext, $allowed, true)) {
                $error = "Format foto harus jpg/jpeg/png/webp.";
            } else {
                $foto_name = "presensi_{$nim}_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
                if (!move_uploaded_file($_FILES['bukti_foto']['tmp_name'], $upload_dir . $foto_name)) {
                    $error = "Gagal mengunggah foto.";
                }
            }
        }
    }

    // Insert DB (kolom optional akan diisi jika ada)
    if ($error === '') {

        $has_lat  = col_exists($conn, 'presensi', 'latitude');
        $has_lng  = col_exists($conn, 'presensi', 'longitude');
        $has_acc  = col_exists($conn, 'presensi', 'accuracy');       // opsional
        $has_addr = col_exists($conn, 'presensi', 'lokasi_alamat');  // opsional
        $has_maps = col_exists($conn, 'presensi', 'maps_url');       // opsional
        $has_ip   = col_exists($conn, 'presensi', 'ip_address');     // opsional
        $has_ua   = col_exists($conn, 'presensi', 'user_agent');     // opsional

        $maps_url = "https://www.google.com/maps/search/?api=1&query=" . rawurlencode($latitude . "," . $longitude);

        // (alamat) optional: jika JS mengirim address ke session, simpan; jika tidak, skip.
        $alamat = $_SESSION['presensi_geo']['addr'] ?? null;
        $alamat = is_string($alamat) ? trim($alamat) : null;
        if ($alamat === '') $alamat = null;

        $cols = [];
        $vals = [];
        $types = "";
        $params = [];

        if ($useIdKp) {
            $cols[] = "id_kp";   $vals[] = "?"; $types .= "i"; $params[] = $kp['id_kp'];
        } else {
            $cols[] = "nim";     $vals[] = "?"; $types .= "s"; $params[] = $nim;
        }

        $cols[] = "tanggal";     $vals[] = "?"; $types .= "s"; $params[] = $tanggal_hari_ini;
        $cols[] = "status";      $vals[] = "?"; $types .= "s"; $params[] = $status;
        $cols[] = "bukti_foto";  $vals[] = "?"; $types .= "s"; $params[] = $foto_name;

        if ($has_lat) { $cols[] = "latitude";  $vals[] = "?"; $types .= "d"; $params[] = $latitude; }
        if ($has_lng) { $cols[] = "longitude"; $vals[] = "?"; $types .= "d"; $params[] = $longitude; }
        if ($has_acc && $accuracy !== null) { $cols[] = "accuracy"; $vals[] = "?"; $types .= "d"; $params[] = $accuracy; }

        if ($has_maps) { $cols[] = "maps_url"; $vals[] = "?"; $types .= "s"; $params[] = $maps_url; }
        if ($has_addr && $alamat !== null) { $cols[] = "lokasi_alamat"; $vals[] = "?"; $types .= "s"; $params[] = $alamat; }

        if ($has_ip) { $cols[] = "ip_address"; $vals[] = "?"; $types .= "s"; $params[] = ($_SERVER['REMOTE_ADDR'] ?? ''); }
        if ($has_ua) { $cols[] = "user_agent"; $vals[] = "?"; $types .= "s"; $params[] = ($_SERVER['HTTP_USER_AGENT'] ?? ''); }

        $sql = "INSERT INTO presensi (" . implode(",", $cols) . ") VALUES (" . implode(",", $vals) . ")";
        $st = $conn->prepare($sql);

        bind_params($st, $types, $params);

        if ($st->execute()) {
            $sukses = "Presensi berhasil disimpan.";

            // bersihkan geo agar tidak dipakai ulang
            unset($_SESSION['presensi_geo']);

            $st->close();
            header("Refresh:1");
        } else {
            $error = "Gagal menyimpan presensi: " . htmlspecialchars($conn->error);
            $st->close();
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

<?php if (!empty($gps_warning)): ?>
  <div class="alert alert-warning"><?= htmlspecialchars($gps_warning) ?></div>
<?php endif; ?>


<?php if ($kp && $sudah_presensi): ?>

  <!-- DETAIL PRESENSI -->
  <div class="card mb-4">
    <div class="card-body">
      <h5>Detail Presensi Hari Ini</h5>

      <?php
        $lat = $presensi_hari_ini['latitude'] ?? null;
        $lng = $presensi_hari_ini['longitude'] ?? null;
        $lat = is_numeric($lat) ? (float)$lat : null;
        $lng = is_numeric($lng) ? (float)$lng : null;
        $maps = ($lat !== null && $lng !== null)
          ? "https://www.google.com/maps/search/?api=1&query=" . rawurlencode($lat . "," . $lng)
          : null;
      ?>

      <p class="mb-2">
        <b>Status:</b> <?= htmlspecialchars($presensi_hari_ini['status']) ?><br>
        <?php if ($maps): ?>
          <b>Lokasi:</b> <?= htmlspecialchars($lat) ?> , <?= htmlspecialchars($lng) ?><br>
          <a class="btn btn-outline-primary btn-sm mt-2" target="_blank" rel="noopener" href="<?= htmlspecialchars($maps) ?>">
            Buka di Google Maps
          </a>
        <?php else: ?>
          <b>Lokasi:</b> Tidak tersedia
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

  <style>
    .map-frame { width:100%; height:260px; border:1px solid #e5e7eb; border-radius:10px; overflow:hidden; }
    .loc-pill { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
  </style>

  <!-- FORM PRESENSI -->
  <form method="POST" enctype="multipart/form-data" class="card p-4 col-md-8">

    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Status Kehadiran</label>
        <select name="status" id="status" class="form-control" required>
          <option value="">-- Pilih --</option>
          <option value="Hadir">Hadir</option>
          <option value="Izin">Izin</option>
          <option value="Alpha">Alpha</option>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Bukti Foto (Wajib jika Hadir)</label>
        <input type="file" name="bukti_foto" id="bukti_foto" class="form-control" accept="image/*">
      </div>

      <!-- LOKASI OTOMATIS -->
      <div class="col-12">
        <div class="d-flex flex-wrap gap-2 align-items-center">
          <button type="button" class="btn btn-outline-secondary" id="btnGetLoc">Ambil Lokasi</button>

          <a href="#" class="btn btn-outline-primary d-none" id="btnOpenMaps" target="_blank" rel="noopener">
            Buka di Google Maps
          </a>

          <span class="badge bg-light text-dark loc-pill" id="locInfo">Lokasi belum terkunci</span>
          <span class="badge bg-light text-dark" id="locAcc"></span>
        </div>
        <small class="text-muted d-block mt-2" id="locHint">
          Izinkan akses lokasi. Sistem akan mengunci koordinat dari perangkat dan menampilkan peta otomatis.
        </small>
      </div>

      <div class="col-12">
        <div class="map-frame">
          <iframe id="mapsFrame"
                  width="100%" height="260"
                  style="border:0"
                  loading="lazy"
                  referrerpolicy="no-referrer-when-downgrade"
                  src="about:blank"></iframe>
        </div>
      </div>

      <div class="col-12">
        <button class="btn btn-primary" id="btnSubmit" disabled>Kirim Presensi</button>
      </div>
    </div>

  </form>

<?php endif; ?>

</div>
</main>

<?php include "../includes/layout_bottom.php"; ?>

<script>
(function(){
  const csrfToken   = <?= json_encode($csrf_token) ?>;
  const statusEl    = document.getElementById('status');
  const fotoEl      = document.getElementById('bukti_foto');
  const btnGetLoc   = document.getElementById('btnGetLoc');
  const btnOpenMaps = document.getElementById('btnOpenMaps');
  const mapsFrame   = document.getElementById('mapsFrame');
  const locInfo     = document.getElementById('locInfo');
  const locAcc      = document.getElementById('locAcc');
  const btnSubmit   = document.getElementById('btnSubmit');

  let locked = false;
  let lastLat = null, lastLng = null, lastAcc = null;

  function setLockedUI(lat, lng, acc) {
    locked = true;
    lastLat = lat; lastLng = lng; lastAcc = acc;

    locInfo.textContent = `Terkunci: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
    locInfo.classList.remove('bg-light','text-dark');
    locInfo.classList.add('bg-success');

    if (typeof acc === 'number') {
      locAcc.textContent = `Akurasi: ±${Math.round(acc)} m`;
    } else {
      locAcc.textContent = '';
    }

    const gmaps = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(lat + "," + lng)}`;
    btnOpenMaps.href = gmaps;
    btnOpenMaps.classList.remove('d-none');

    // Preview Google Maps (embed tanpa API key)
    const embed = `https://www.google.com/maps?q=${encodeURIComponent(lat + "," + lng)}&z=18&output=embed`;
    mapsFrame.src = embed;

    validateSubmit();
  }

  function setUnlockedUI(msg, isError=false) {
    locked = false;
    locInfo.textContent = msg;
    locInfo.classList.remove('bg-success');
    locInfo.classList.add('bg-light','text-dark');
    locAcc.textContent = '';
    btnOpenMaps.classList.add('d-none');
    mapsFrame.src = 'about:blank';
    validateSubmit();

    if (isError) {
      // tetap tidak pakai alert keras; pesan sudah cukup di UI
      console.warn(msg);
    }
  }

  function validateSubmit() {
    // lokasi WAJIB agar “real” dan meminimalkan manipulasi manual
    if (!locked) {
      btnSubmit.disabled = true;
      return;
    }

    // foto wajib hanya jika Hadir
    const st = statusEl ? statusEl.value : '';
    if (st === 'Hadir') {
      btnSubmit.disabled = !(fotoEl && fotoEl.files && fotoEl.files.length > 0);
    } else if (st === '') {
      btnSubmit.disabled = true;
    } else {
      btnSubmit.disabled = false;
    }
  }

  async function storeGeoToServer(lat, lng, acc) {
    // Simpan ke SESSION server agar server tidak percaya input POST yang bisa diedit
    const res = await fetch("presensi_location_store.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": csrfToken
      },
      body: JSON.stringify({
        lat: lat,
        lng: lng,
        acc: acc
      })
    });

    const data = await res.json().catch(() => null);
    if (!res.ok || !data || data.ok !== true) {
      throw new Error((data && data.message) ? data.message : "Gagal mengunci lokasi di server.");
    }
  }

  async function getLocation() {
    if (!navigator.geolocation) {
      setUnlockedUI("Browser tidak mendukung GPS.", true);
      return;
    }

    setUnlockedUI("Mengambil lokasi...", false);

    navigator.geolocation.getCurrentPosition(async (pos) => {
      try {
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;
        const acc = pos.coords.accuracy;

        // kirim ke server dulu (lock), baru tampilkan “terkunci”
        await storeGeoToServer(lat, lng, acc);
        setLockedUI(lat, lng, acc);

      } catch (e) {
        setUnlockedUI(e.message || "Gagal mengunci lokasi.", true);
      }
    }, (err) => {
      let msg = "Gagal mengambil lokasi.";
      if (err && err.code === 1) msg = "Izin lokasi ditolak. Aktifkan izin GPS untuk presensi.";
      if (err && err.code === 2) msg = "Lokasi tidak tersedia. Aktifkan GPS lalu coba lagi.";
      if (err && err.code === 3) msg = "Timeout mengambil lokasi. Coba lagi.";
      setUnlockedUI(msg, true);
    }, {
      enableHighAccuracy: true,
      timeout: 15000,
      maximumAge: 0
    });
  }

  // interaksi UI
  if (btnGetLoc) btnGetLoc.addEventListener('click', getLocation);
  if (statusEl) statusEl.addEventListener('change', () => {
    // foto wajib hanya saat Hadir
    if (fotoEl) {
      if (statusEl.value === 'Hadir') {
        fotoEl.setAttribute('required', 'required');
      } else {
        fotoEl.removeAttribute('required');
      }
    }
    validateSubmit();
  });
  if (fotoEl) fotoEl.addEventListener('change', validateSubmit);

  // auto ambil lokasi saat halaman dibuka
  getLocation();
})();
</script>
