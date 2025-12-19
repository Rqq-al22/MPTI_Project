<?php
require_once __DIR__ . "/../auth/auth_check.php";
require_login();
require_once __DIR__ . "/../config/db.php";

$current_page  = "profile.php";
$page_title    = "Profil Pengguna";
$asset_prefix  = "../";
$logout_prefix = "../";

/* =========================================================
   HELPER UPLOAD FOTO (DIPASANG LANGSUNG AGAR ANTI ERROR PATH)
   ========================================================= */
function upload_foto_local(array $file, string $targetDirAbs): ?string
{
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowedExt = ['jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt)) {
        return null;
    }

    // Pastikan folder ada
    if (!is_dir($targetDirAbs)) {
        @mkdir($targetDirAbs, 0777, true);
    }

    // Nama file aman
    $filename = uniqid("profile_", true) . "." . $ext;
    $destAbs  = rtrim($targetDirAbs, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

    if (move_uploaded_file($file['tmp_name'], $destAbs)) {
        return $filename;
    }
    return null;
}

/* =========================================================
   AMBIL DATA USER
   ========================================================= */
$user_id = $_SESSION['user_id'];
$id_role = $_SESSION['id_role'] ?? null;

$qUser = $conn->prepare("SELECT username, foto FROM users WHERE id_user = ? LIMIT 1");
$qUser->bind_param("i", $user_id);
$qUser->execute();
$user = $qUser->get_result()->fetch_assoc();

if (!$user) {
    die("Data user tidak ditemukan.");
}

/* =========================================================
   AMBIL DATA PROFIL SESUAI ROLE
   ========================================================= */
$profil = null;

if ($id_role == 1) {
    // ADMIN
    $q = $conn->prepare("SELECT nama_admin AS nama, email FROM admin WHERE id_user=? LIMIT 1");
    $q->bind_param("i", $user_id);
    $q->execute();
    $profil = $q->get_result()->fetch_assoc();

    // fallback jika kolom tidak ada
    if (!$profil) $profil = ['nama' => $user['username'], 'email' => ''];

} elseif ($id_role == 2) {
    // DOSEN
    $q = $conn->prepare("SELECT nama, nidn, jurusan, keahlian FROM dosen WHERE id_user=? LIMIT 1");
    $q->bind_param("i", $user_id);
    $q->execute();
    $profil = $q->get_result()->fetch_assoc();

    if (!$profil) $profil = ['nama' => $user['username'], 'nidn' => '', 'jurusan' => '', 'keahlian' => ''];

} else {
    // MAHASISWA (default role 3)
    $q = $conn->prepare("SELECT nama, nim, jurusan, angkatan FROM mahasiswa WHERE id_user=? LIMIT 1");
    $q->bind_param("i", $user_id);
    $q->execute();
    $profil = $q->get_result()->fetch_assoc();

    if (!$profil) $profil = ['nama' => $user['username'], 'nim' => '', 'jurusan' => '', 'angkatan' => ''];
}

/* =========================================================
   PROSES UPDATE PROFIL + UPLOAD FOTO
   ========================================================= */
$success = false;
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1) Upload foto
    if (!empty($_FILES['foto']['name'])) {
        $uploadDirAbs = __DIR__ . "/../uploads/profile"; // absolut
        $fotoName = upload_foto_local($_FILES['foto'], $uploadDirAbs);

        if ($fotoName) {
            $stmt = $conn->prepare("UPDATE users SET foto=? WHERE id_user=?");
            $stmt->bind_param("si", $fotoName, $user_id);
            $stmt->execute();

            // refresh $user
            $user['foto'] = $fotoName;
        } else {
            $error = "Upload gagal. Pastikan file JPG/PNG dan tidak rusak.";
        }
    }

    // 2) Update data sesuai role
    $nama = trim($_POST['nama'] ?? '');

    if ($nama === '') {
        $error = $error ?: "Nama wajib diisi.";
    } else {

        if ($id_role == 1) {
            $email = trim($_POST['email'] ?? '');
            $stmt = $conn->prepare("UPDATE admin SET nama_admin=?, email=? WHERE id_user=?");
            $stmt->bind_param("ssi", $nama, $email, $user_id);
            $stmt->execute();

        } elseif ($id_role == 2) {
            $jurusan  = trim($_POST['jurusan'] ?? '');
            $keahlian = trim($_POST['keahlian'] ?? '');
            $stmt = $conn->prepare("UPDATE dosen SET nama=?, jurusan=?, keahlian=? WHERE id_user=?");
            $stmt->bind_param("sssi", $nama, $jurusan, $keahlian, $user_id);
            $stmt->execute();

        } else {
            $jurusan  = trim($_POST['jurusan'] ?? '');
            $angkatan = (int)($_POST['angkatan'] ?? 0);
            $stmt = $conn->prepare("UPDATE mahasiswa SET nama=?, jurusan=?, angkatan=? WHERE id_user=?");
            $stmt->bind_param("ssii", $nama, $jurusan, $angkatan, $user_id);
            $stmt->execute();
        }

        $success = ($error === "");
    }

    // Refresh profil setelah update
    header("Location: profile.php?success=" . ($success ? "1" : "0"));
    exit;
}

/* =========================================================
   PATH FOTO UNTUK DITAMPILKAN
   ========================================================= */
$fotoRel = !empty($user['foto']) ? "../uploads/profile/" . $user['foto'] : "../uploads/profile/default.png";

/* =========================================================
   LAYOUT + SIDEBAR SESUAI ROLE
   ========================================================= */
include __DIR__ . "/../includes/layout_top.php";

if ($id_role == 1) {
    include __DIR__ . "/../includes/sidebar_admin.php";
} elseif ($id_role == 2) {
    include __DIR__ . "/../includes/sidebar_dosen.php";
} else {
    include __DIR__ . "/../includes/sidebar_mahasiswa.php";
}
?>

<main class="pc-container">
<?php include __DIR__ . "/../includes/header.php"; ?>

<div class="pc-content">

<h3 class="fw-bold mb-4">Profil Pengguna</h3>

<?php if (isset($_GET['success']) && $_GET['success'] == "1"): ?>
  <div class="alert alert-success">Profil berhasil diperbarui.</div>
<?php elseif (isset($_GET['success']) && $_GET['success'] == "0"): ?>
  <div class="alert alert-warning">Profil diperbarui, namun ada bagian yang gagal diproses.</div>
<?php endif; ?>

<div class="row">
  <div class="col-md-4">
    <div class="card shadow-sm">
      <div class="card-body text-center">
        <img src="<?= htmlspecialchars($fotoRel) ?>" class="img-thumbnail mb-3" style="width:180px;height:180px;object-fit:cover;">
        <form method="POST" enctype="multipart/form-data">
          <label class="form-label fw-semibold">Ganti Foto</label>
          <input type="file" name="foto" class="form-control mb-2" accept=".jpg,.jpeg,.png">
          <button class="btn btn-primary btn-sm w-100">Upload Foto</button>
        </form>
        <hr>
        <div class="text-muted small">Username: <strong><?= htmlspecialchars($user['username']) ?></strong></div>
      </div>
    </div>
  </div>

  <div class="col-md-8">
    <div class="card shadow-sm">
      <div class="card-body">

        <h5 class="fw-bold mb-3">Informasi Profil</h5>

        <form method="POST">

          <div class="mb-3">
            <label class="form-label fw-semibold">Nama</label>
            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($profil['nama'] ?? '') ?>" required>
          </div>

          <?php if ($id_role == 1): ?>
            <div class="mb-3">
              <label class="form-label fw-semibold">Email</label>
              <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($profil['email'] ?? '') ?>">
            </div>

          <?php elseif ($id_role == 2): ?>
            <div class="mb-3">
              <label class="form-label fw-semibold">NIDN</label>
              <input type="text" class="form-control" value="<?= htmlspecialchars($profil['nidn'] ?? '') ?>" disabled>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Jurusan</label>
              <input type="text" name="jurusan" class="form-control" value="<?= htmlspecialchars($profil['jurusan'] ?? '') ?>">
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Keahlian</label>
              <input type="text" name="keahlian" class="form-control" value="<?= htmlspecialchars($profil['keahlian'] ?? '') ?>">
            </div>

          <?php else: ?>
            <div class="mb-3">
              <label class="form-label fw-semibold">NIM</label>
              <input type="text" class="form-control" value="<?= htmlspecialchars($profil['nim'] ?? '') ?>" disabled>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Jurusan</label>
              <input type="text" name="jurusan" class="form-control" value="<?= htmlspecialchars($profil['jurusan'] ?? '') ?>">
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Angkatan</label>
              <input type="number" name="angkatan" class="form-control" value="<?= htmlspecialchars($profil['angkatan'] ?? 0) ?>">
            </div>
          <?php endif; ?>

          <button class="btn btn-success">Simpan Perubahan</button>

        </form>

      </div>
    </div>
  </div>
</div>

</div>
</main>

<?php include __DIR__ . "/../includes/layout_bottom.php"; ?>
