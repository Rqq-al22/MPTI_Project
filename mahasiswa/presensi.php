<?php
require_once "../auth/auth_check.php";
require_role('mahasiswa');
require_once "../config/db.php";

if (session_status() === PHP_SESSION_NONE) session_start();

/* ===============================
   KONFIGURASI
================================ */
$current_page  = 'presensi.php';
$page_title    = "Presensi Kerja Praktik";
$asset_prefix  = "../";
$logout_prefix = "../";

/**
 * Agar "akurasi tinggi", turunkan angka ini.
 * Rekomendasi:
 * - HP (GPS): 20–50 meter realistis
 * - Laptop/PC tanpa GPS: sering gagal <50m
 */
$MAX_ACCURACY_M       = 50;   // VALIDASI SERVER (meter) -> lebih ketat dari 80
$MAX_LOCATION_AGE_SEC = 120;  // lokasi harus baru (detik)

$UPLOAD_DIR = __DIR__ . "/../uploads/presensi/";
$UPLOAD_URL = $asset_prefix . "uploads/presensi/";

/* ===============================
   MODE AJAX (SINGLE FILE)
================================ */
if (isset($_GET['action']) && $_GET['action'] === 'store_location') {
    header('Content-Type: application/json');

    $raw  = file_get_contents("php://input");
    $data = json_decode($raw, true);

    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(["ok" => false, "message" => "Payload tidak valid"]);
        exit;
    }

    $lat = $data['lat'] ?? null;
    $lng = $data['lng'] ?? null;
    $acc = $data['accuracy'] ?? null;

    if (!is_numeric($lat) || !is_numeric($lng) || !is_numeric($acc)) {
        http_response_code(400);
        echo json_encode(["ok" => false, "message" => "Koordinat/akurasi tidak valid"]);
        exit;
    }

    $lat = (float)$lat;
    $lng = (float)$lng;
    $acc = (float)$acc;

    if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
        http_response_code(400);
        echo json_encode(["ok" => false, "message" => "Rentang koordinat tidak valid"]);
        exit;
    }

    // Tolak akurasi yang "ngaco" / fallback ekstrem
    if ($acc <= 0 || $acc > 5000) {
        http_response_code(400);
        echo json_encode(["ok" => false, "message" => "Akurasi tidak wajar"]);
        exit;
    }

    // LOCK session sebagai sumber kebenaran (bukan input form)
    $_SESSION['geo_lock'] = [
        "lat"         => $lat,
        "lng"         => $lng,
        "accuracy"    => $acc,
        "captured_at" => date("Y-m-d H:i:s"),
        "ip"          => $_SERVER['REMOTE_ADDR'] ?? '',
        "ua"          => $_SERVER['HTTP_USER_AGENT'] ?? '',
    ];

    echo json_encode(["ok" => true, "message" => "Lokasi terkunci"]);
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'clear_location') {
    header('Content-Type: application/json');
    unset($_SESSION['geo_lock']);
    echo json_encode(["ok" => true, "message" => "Lokasi dibersihkan"]);
    exit;
}

/* ===============================
   AMBIL DATA MAHASISWA LOGIN
================================ */
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) die("Sesi tidak valid. Silakan login ulang.");

$stmt = $conn->prepare("SELECT nim, nama FROM mahasiswa WHERE id_user = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$mhs = $stmt->get_result()->fetch_assoc();
if (!$mhs) die("Data mahasiswa tidak ditemukan.");

$nim  = $mhs['nim'];
$nama = $mhs['nama'] ?? '-';

/* ===============================
   SUBMIT PRESENSI
================================ */
$alert = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_submit'])) {

    $status = $_POST['status'] ?? '';
    $allowedStatus = ['Hadir', 'Izin', 'Alpha'];
    if (!in_array($status, $allowedStatus, true)) {
        $alert = ["type" => "danger", "msg" => "Status presensi tidak valid."];
        goto render_page;
    }

    if (empty($_SESSION['geo_lock'])) {
        $alert = ["type" => "danger", "msg" => "Lokasi belum terkunci. Ambil lokasi terlebih dahulu."];
        goto render_page;
    }

    $geo   = $_SESSION['geo_lock'];
    $curIp = $_SERVER['REMOTE_ADDR'] ?? '';
    $curUa = $_SERVER['HTTP_USER_AGENT'] ?? '';

    if (($geo['ip'] ?? '') !== $curIp || ($geo['ua'] ?? '') !== $curUa) {
        $alert = ["type" => "danger", "msg" => "Sesi lokasi tidak valid. Ambil ulang lokasi."];
        unset($_SESSION['geo_lock']);
        goto render_page;
    }

    $lat = (float)($geo['lat'] ?? 0);
    $lng = (float)($geo['lng'] ?? 0);
    $acc = (float)($geo['accuracy'] ?? 99999);
    $capturedAt = $geo['captured_at'] ?? null;

    if (!$capturedAt) {
        $alert = ["type" => "danger", "msg" => "Waktu lokasi tidak valid. Ambil ulang lokasi."];
        unset($_SESSION['geo_lock']);
        goto render_page;
    }

    $capTs = strtotime($capturedAt);
    if (!$capTs || (time() - $capTs) > $MAX_LOCATION_AGE_SEC) {
        $alert = ["type" => "warning", "msg" => "Lokasi sudah terlalu lama. Ambil ulang lokasi."];
        unset($_SESSION['geo_lock']);
        goto render_page;
    }

    // VALIDASI AKURASI SERVER (ketat)
    if ($acc > $MAX_ACCURACY_M) {
        $alert = ["type" => "danger", "msg" => "Akurasi lokasi belum memenuhi syarat (±" . (int)$acc . " m). Aktifkan lokasi presisi / Wi-Fi / gunakan HP, lalu ambil ulang."];
        goto render_page;
    }

    // Cegah presensi ganda hari ini
    $today = date("Y-m-d");
    $stmt = $conn->prepare("SELECT id_presensi FROM presensi WHERE nim = ? AND tanggal = ? LIMIT 1");
    $stmt->bind_param("ss", $nim, $today);
    $stmt->execute();
    if ($stmt->get_result()->fetch_assoc()) {
        $alert = ["type" => "warning", "msg" => "Anda sudah presensi hari ini."];
        goto render_page;
    }

    // Upload bukti foto (opsional)
    $bukti_foto = null;
    if (!empty($_FILES['bukti_foto']['name'])) {

        if (!is_dir($UPLOAD_DIR)) @mkdir($UPLOAD_DIR, 0775, true);

        $err = $_FILES['bukti_foto']['error'];
        if ($err !== UPLOAD_ERR_OK) {
            $alert = ["type" => "danger", "msg" => "Upload bukti foto gagal. Kode: $err"];
            goto render_page;
        }

        $tmp  = $_FILES['bukti_foto']['tmp_name'];
        $size = (int)$_FILES['bukti_foto']['size'];

        if ($size > 3 * 1024 * 1024) {
            $alert = ["type" => "danger", "msg" => "Ukuran foto maksimal 3MB."];
            goto render_page;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($tmp);

        $allowed = ["image/jpeg"=>"jpg", "image/png"=>"png", "image/webp"=>"webp"];
        if (!isset($allowed[$mime])) {
            $alert = ["type" => "danger", "msg" => "Format foto harus JPG/PNG/WEBP."];
            goto render_page;
        }

        $ext = $allowed[$mime];
        $bukti_foto = "presensi_{$nim}_" . date("Ymd_His") . "_" . bin2hex(random_bytes(4)) . ".$ext";
        if (!move_uploaded_file($tmp, $UPLOAD_DIR . $bukti_foto)) {
            $alert = ["type" => "danger", "msg" => "Gagal menyimpan file upload."];
            goto render_page;
        }
    }

    $validasi = "Pending";
    $stmt = $conn->prepare("
        INSERT INTO presensi (nim, tanggal, status, bukti_foto, latitude, longitude, accuracy, lokasi_captured_at, validasi)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssssdddss", $nim, $today, $status, $bukti_foto, $lat, $lng, $acc, $capturedAt, $validasi);

    if (!$stmt->execute()) {
        $alert = ["type" => "danger", "msg" => "Gagal menyimpan presensi: " . $conn->error];
        goto render_page;
    }

    unset($_SESSION['geo_lock']);
    $alert = ["type" => "success", "msg" => "Presensi berhasil dikirim. Menunggu validasi dosen."];
}

/* ===============================
   RIWAYAT PRESENSI
================================ */
render_page:
$stmt = $conn->prepare("
  SELECT tanggal, status, bukti_foto, latitude, longitude, accuracy, validasi
  FROM presensi
  WHERE nim = ?
  ORDER BY tanggal DESC
  LIMIT 10
");
$stmt->bind_param("s", $nim);
$stmt->execute();
$history = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

/* ===============================
   LAYOUT TEMPLATE (NAVBAR/SIDEBAR)
================================ */
include "../includes/layout_top.php";
include "../includes/sidebar_mahasiswa.php";
?>

<main class="pc-container">
<?php include "../includes/header.php"; ?>

<div class="pc-content">

  <div class="mb-3">
    <h3 class="fw-bold mb-1">Presensi Kerja Praktik</h3>
    <div class="text-muted">Mahasiswa: <b><?= htmlspecialchars($nama) ?></b> (<?= htmlspecialchars($nim) ?>)</div>
  </div>

  <?php if ($alert): ?>
    <div class="alert alert-<?= $alert['type'] ?>"><?= htmlspecialchars($alert['msg']) ?></div>
  <?php endif; ?>

  <div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">
      <div class="mb-2 fw-semibold">Ambil Lokasi Presensi </div>
    

      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="do_submit" value="1">

        <div class="row g-3">

          <div class="col-md-4">
            <label class="form-label">Status Presensi</label>
            <select name="status" class="form-select" required>
              <option value="Hadir">Hadir</option>
              <option value="Izin">Izin</option>
              <option value="Alpha">Alpha</option>
            </select>
          </div>

          <div class="col-md-8">
            <label class="form-label">Bukti Foto (Opsional)</label>
            <input type="file" name="bukti_foto" class="form-control" accept="image/jpeg,image/png,image/webp">
            <div class="form-text">JPG/PNG/WEBP, maks 3MB.</div>
          </div>

          <div class="col-md-4">
            <label class="form-label">Latitude</label>
            <input id="lat" class="form-control" readonly>
          </div>

          <div class="col-md-4">
            <label class="form-label">Longitude</label>
            <input id="lng" class="form-control" readonly>
          </div>

          <div class="col-md-4">
            <label class="form-label">Akurasi (meter)</label>
            <input id="accuracy" class="form-control" readonly>
          </div>

          <div class="col-12 d-flex gap-2 flex-wrap align-items-center">
            <button type="button" id="btnGetLoc" class="btn btn-sm btn-primary">Ambil Lokasi</button>
            <button type="button" id="btnRetry" class="btn btn-sm btn-outline-secondary">Ambil Ulang</button>

            <a id="btnOpenMapsLive" class="btn btn-sm btn-outline-primary disabled" target="_blank" href="#">
              Buka Maps ke Lokasi Saya
            </a>

            <span id="locStatus" class="text-muted small">Menunggu lokasi…</span>
          </div>

          <div class="col-12">
            <div class="ratio ratio-16x9 border rounded">
              <iframe id="mapFrame" src="about:blank" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <a id="openMaps" href="#" target="_blank" class="d-inline-block mt-2 small">Buka di Google Maps</a>
          </div>

          <div class="col-12 pt-2">
            <button type="submit" id="btnSubmit" class="btn btn-success" disabled>
              Kirim Presensi
            </button>
            <div class="text-muted small mt-2">
              Tombol kirim akan aktif setelah lokasi terkunci dan akurasi memenuhi syarat.
            </div>
          </div>

        </div>
      </form>
    </div>
  </div>

  <div class="card shadow-sm border-0">
    <div class="card-body p-4">
      <div class="fw-semibold mb-3">Riwayat Presensi (10 terakhir)</div>

      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Tanggal</th>
              <th>Status</th>
              <th>Akurasi</th>
              <th>Lokasi</th>
              <th>Bukti</th>
              <th>Validasi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($history)): ?>
              <?php foreach ($history as $h): ?>
                <tr>
                  <td><?= htmlspecialchars(date("d M Y", strtotime($h['tanggal']))) ?></td>
                  <td><?= htmlspecialchars($h['status']) ?></td>
                  <td><?= $h['accuracy'] !== null ? "±" . (int)$h['accuracy'] . " m" : "-" ?></td>
                  <td>
                    <?php if (!empty($h['latitude']) && !empty($h['longitude'])): ?>
                      <a target="_blank" href="https://maps.google.com/?q=<?= htmlspecialchars($h['latitude']) ?>,<?= htmlspecialchars($h['longitude']) ?>">Maps</a>
                    <?php else: ?> - <?php endif; ?>
                  </td>
                  <td>
                    <?php if (!empty($h['bukti_foto'])): ?>
                      <a class="btn btn-sm btn-outline-primary" target="_blank" href="<?= $UPLOAD_URL . htmlspecialchars($h['bukti_foto']) ?>">Lihat</a>
                    <?php else: ?> - <?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($h['validasi'] ?? 'Pending') ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="text-center text-muted py-4">Belum ada riwayat presensi.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>

</div>
</main>

<script>
(function () {
  const btn = document.getElementById('btnGetLoc');
  const btnRetry = document.getElementById('btnRetry');
  const submitBtn = document.getElementById('btnSubmit');

  const statusEl = document.getElementById('locStatus');
  const latEl = document.getElementById('lat');
  const lngEl = document.getElementById('lng');
  const accEl = document.getElementById('accuracy');

  const mapFrame = document.getElementById('mapFrame');
  const openMaps = document.getElementById('openMaps');
  const btnOpenMapsLive = document.getElementById('btnOpenMapsLive');

  // Bangun URL dari halaman aktif (anti masalah <base>)
  const storeUrl = new URL(window.location.href);
  storeUrl.searchParams.set('action', 'store_location');
  const clearUrl = new URL(window.location.href);
  clearUrl.searchParams.set('action', 'clear_location');

  // Target client untuk cepat lock (lebih ketat dari server)
  const TARGET_ACCURACY_M = 30;
  const HARD_TIMEOUT_MS = 12000;

  // Agar "default location" tidak lolos, minimal masih harus <= serverMaxAccuracy
  const SERVER_MAX_ACCURACY = <?= (int)$MAX_ACCURACY_M ?>;

  let watchId = null;
  let hardTimer = null;
  let best = null;
  let locking = false;

  function setStatus(t){ statusEl.textContent = t; }

  function updateMap(lat, lng) {
    const q = `${lat},${lng}`;
    mapFrame.src = `https://www.google.com/maps?q=${encodeURIComponent(q)}&z=18&output=embed`;
    openMaps.href = `https://maps.google.com/?q=${encodeURIComponent(q)}`;
    if (btnOpenMapsLive) {
      btnOpenMapsLive.classList.remove('disabled');
      btnOpenMapsLive.href = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(q)}`;
    }
  }

  function stopAll(){
    if (watchId !== null) {
      navigator.geolocation.clearWatch(watchId);
      watchId = null;
    }
    if (hardTimer) {
      clearTimeout(hardTimer);
      hardTimer = null;
    }
  }

  function geoOptions(){
    return { enableHighAccuracy: true, maximumAge: 0, timeout: 20000 };
  }

  function pickBest(coords){
    const cand = { latitude: coords.latitude, longitude: coords.longitude, accuracy: coords.accuracy };
    if (!best || cand.accuracy < best.accuracy) best = cand;
  }

  async function storeToSession(lat, lng, accuracy) {
    const res = await fetch(storeUrl.toString(), {
      method: 'POST',
      credentials: 'same-origin',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ lat, lng, accuracy })
    });

    // Baca text dulu supaya kalau respon HTML (redirect/login), tidak bikin error silent
    const text = await res.text();
    let json = {};
    try { json = JSON.parse(text); } catch (e) {}

    if (!res.ok || !json.ok) {
      throw new Error(json.message || 'Gagal mengunci lokasi (respon tidak valid)');
    }
  }

  async function clearSessionLock() {
    try {
      await fetch(clearUrl.toString(), { method: 'POST', credentials: 'same-origin' });
    } catch (e) {}
  }

  async function lockBest(reason){
    if (locking) return;
    locking = true;
    stopAll();

    if (!best) {
      locking = false;
      btn.disabled = false;
      btn.textContent = 'Ambil Lokasi';
      setStatus('Lokasi belum didapat. Pastikan izin lokasi aktif.');
      submitBtn.disabled = true;
      return;
    }

    const lat = best.latitude.toFixed(6);
    const lng = best.longitude.toFixed(6);
    const acc = Math.round(best.accuracy);

    // Jangan lock kalau akurasi masih buruk (mencegah default/IP location lolos)
    if (acc > SERVER_MAX_ACCURACY) {
      locking = false;
      btn.disabled = false;
      btn.textContent = 'Ambil Lokasi';
      submitBtn.disabled = true;
      setStatus(`Akurasi masih buruk (±${acc} m). Aktifkan lokasi presisi/Wi-Fi atau gunakan HP, lalu ambil ulang.`);
      return;
    }

    latEl.value = lat;
    lngEl.value = lng;
    accEl.value = acc;
    updateMap(lat, lng);

    try {
      await storeToSession(lat, lng, acc);
      setStatus(`Lokasi terkunci (${reason}). Akurasi ±${acc} m`);
      btn.disabled = true;
      btn.textContent = 'Lokasi Terkunci';
      submitBtn.disabled = false;
    } catch (e) {
      locking = false;
      btn.disabled = false;
      btn.textContent = 'Ambil Lokasi';
      submitBtn.disabled = true;
      setStatus('Gagal mengunci lokasi: ' + e.message);
    }
  }

  function capture(){
    if (!navigator.geolocation) {
      setStatus('Browser tidak mendukung Geolocation.');
      return;
    }

    submitBtn.disabled = true;
    btn.disabled = true;
    btn.textContent = 'Mengambil...';
    setStatus('Mengambil lokasi presisi…');

    best = null;
    locking = false;

    // Tahap 1: cepat (fresh)
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        pickBest(pos.coords);
        setStatus(`Lokasi awal didapat. Akurasi ±${Math.round(pos.coords.accuracy)} m. Menyempurnakan…`);

        // Tahap 2: refine (ambil beberapa sampel)
        watchId = navigator.geolocation.watchPosition(
          (p2) => {
            pickBest(p2.coords);
            const acc = Math.round(best.accuracy);
            setStatus(`Mendeteksi… Akurasi terbaik ±${acc} m`);

            if (best.accuracy <= TARGET_ACCURACY_M) {
              lockBest(`akurasi memenuhi ≤ ${TARGET_ACCURACY_M}m`);
            }
          },
          (err) => {
            stopAll();
            btn.disabled = false;
            btn.textContent = 'Ambil Lokasi';
            submitBtn.disabled = true;

            if (err.code === 1) setStatus('Izin lokasi ditolak. Aktifkan izin lokasi di browser.');
            else if (err.code === 2) setStatus('Lokasi tidak tersedia. Aktifkan Location Services & Wi-Fi.');
            else setStatus('Timeout lokasi. Coba ambil ulang di area terbuka.');
          },
          geoOptions()
        );

        // Hard timeout: lock hanya jika akurasi sudah memenuhi serverMaxAccuracy
        hardTimer = setTimeout(() => {
          lockBest(`timeout ${HARD_TIMEOUT_MS/1000}s`);
        }, HARD_TIMEOUT_MS);
      },
      (err) => {
        btn.disabled = false;
        btn.textContent = 'Ambil Lokasi';
        submitBtn.disabled = true;

        if (err.code === 1) setStatus('Izin lokasi ditolak. Aktifkan izin lokasi di browser.');
        else if (err.code === 2) setStatus('Lokasi tidak tersedia. Aktifkan Location Services & Wi-Fi.');
        else setStatus('Timeout lokasi. Coba ambil ulang di area terbuka.');
      },
      geoOptions()
    );
  }

  btn.addEventListener('click', capture);

  btnRetry.addEventListener('click', async () => {
    stopAll();
    await clearSessionLock();

    best = null;
    locking = false;

    latEl.value = '';
    lngEl.value = '';
    accEl.value = '';
    submitBtn.disabled = true;

    btn.disabled = false;
    btn.textContent = 'Ambil Lokasi';
    setStatus('Ambil ulang lokasi…');

    // ambil ulang langsung
    capture();
  });

  // Auto saat masuk
  window.addEventListener('load', capture);
})();
</script>

<?php include "../includes/layout_bottom.php"; ?>
