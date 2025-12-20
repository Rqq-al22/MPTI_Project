<?php
require_once __DIR__ . "/../auth/auth_check.php";
require_login();
require_once __DIR__ . "/../config/db.php";

$current_page  = "profile.php";
$page_title    = "Profil Pengguna";
$asset_prefix  = "../";
$logout_prefix = "../";

/* =========================================================
   HELPER UPLOAD FOTO
   ========================================================= */
function upload_foto_local(array $file, string $targetDirAbs): ?string
{
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return null;

    $allowedExt = ['jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt)) return null;

    if (!is_dir($targetDirAbs)) mkdir($targetDirAbs, 0777, true);

    $filename = uniqid("profile_", true) . "." . $ext;
    $destAbs  = rtrim($targetDirAbs, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

    return move_uploaded_file($file['tmp_name'], $destAbs) ? $filename : null;
}

/* =========================================================
   DATA USER
   ========================================================= */
$user_id = $_SESSION['user_id'];
$id_role = $_SESSION['id_role'] ?? null;

$qUser = $conn->prepare("SELECT username, foto FROM users WHERE id_user=? LIMIT 1");
$qUser->bind_param("i", $user_id);
$qUser->execute();
$user = $qUser->get_result()->fetch_assoc();
if (!$user) die("User tidak ditemukan");

/* =========================================================
   DATA PROFIL BERDASARKAN ROLE
   ========================================================= */
if ($id_role == 1) {
    $q = $conn->prepare("SELECT nama_admin AS nama, email FROM admin WHERE id_user=?");
} elseif ($id_role == 2) {
    $q = $conn->prepare("SELECT nama, nidn, jurusan, keahlian FROM dosen WHERE id_user=?");
} else {
    $q = $conn->prepare("SELECT nama, nim, jurusan, angkatan FROM mahasiswa WHERE id_user=?");
}
$q->bind_param("i", $user_id);
$q->execute();
$profil = $q->get_result()->fetch_assoc();

/* =========================================================
   PROSES POST
   ========================================================= */
$success = null;
$error   = "";

/* ---------- A) UPLOAD FOTO ---------- */
if (isset($_POST['upload_foto'])) {

    $uploadDirAbs = __DIR__ . "/../uploads/profile";
    $fotoName = upload_foto_local($_FILES['foto'], $uploadDirAbs);

    if ($fotoName) {
        $stmt = $conn->prepare("UPDATE users SET foto=? WHERE id_user=?");
        $stmt->bind_param("si", $fotoName, $user_id);
        $stmt->execute();
        $success = 1;
    } else {
        $success = 0;
        $error = "Upload foto gagal. Pastikan JPG/PNG.";
    }
}

/* ---------- B) UPDATE PROFIL ---------- */
if (isset($_POST['simpan_profil'])) {

    $nama = trim($_POST['nama'] ?? '');
    if ($nama === '') {
        $success = 0;
        $error = "Nama wajib diisi.";
    } else {

        if ($id_role == 1) {
            $email = trim($_POST['email'] ?? '');
            $stmt = $conn->prepare("UPDATE admin SET nama_admin=?, email=? WHERE id_user=?");
            $stmt->bind_param("ssi", $nama, $email, $user_id);

        } elseif ($id_role == 2) {
            $jurusan = trim($_POST['jurusan'] ?? '');
            $keahlian = trim($_POST['keahlian'] ?? '');
            $stmt = $conn->prepare("UPDATE dosen SET nama=?, jurusan=?, keahlian=? WHERE id_user=?");
            $stmt->bind_param("sssi", $nama, $jurusan, $keahlian, $user_id);

        } else {
            $jurusan = trim($_POST['jurusan'] ?? '');
            $angkatan = (int)($_POST['angkatan'] ?? 0);
            $stmt = $conn->prepare("UPDATE mahasiswa SET nama=?, jurusan=?, angkatan=? WHERE id_user=?");
            $stmt->bind_param("ssii", $nama, $jurusan, $angkatan, $user_id);
        }

        $stmt->execute();
        $success = 1;
    }
}

/* =========================================================
   TAMPILAN
   ========================================================= */
$fotoRel = !empty($user['foto'])
    ? "../uploads/profile/" . $user['foto']
    : "../uploads/profile/default.png";

include __DIR__ . "/../includes/layout_top.php";
if ($id_role == 1) include __DIR__ . "/../includes/sidebar_admin.php";
elseif ($id_role == 2) include __DIR__ . "/../includes/sidebar_dosen.php";
else include __DIR__ . "/../includes/sidebar_mahasiswa.php";
?>

<main class="pc-container">
<?php include __DIR__ . "/../includes/header.php"; ?>

<div class="pc-content">

<h3 class="fw-bold mb-4">Profil Pengguna</h3>

<?php if ($success === 1): ?>
  <div class="alert alert-success">Perubahan berhasil disimpan.</div>
<?php elseif ($success === 0): ?>
  <div class="alert alert-warning"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="row">

<!-- FOTO -->
<div class="col-md-4">
<div class="card text-center">
<div class="card-body">
<img src="<?= htmlspecialchars($fotoRel) ?>" class="img-thumbnail mb-3" style="width:180px;height:180px;object-fit:cover;">

<form method="POST" enctype="multipart/form-data">
  <input type="file" name="foto" class="form-control mb-2" accept=".jpg,.jpeg,.png">
  <button name="upload_foto" class="btn btn-primary btn-sm w-100">Upload Foto</button>
</form>
</div>
</div>
</div>

<!-- PROFIL -->
<div class="col-md-8">
<div class="card">
<div class="card-body">

<form method="POST">

<div class="mb-3">
<label class="form-label">Nama</label>
<input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($profil['nama'] ?? '') ?>" required>
</div>

<?php if ($id_role == 1): ?>
<input type="email" name="email" class="form-control" value="<?= htmlspecialchars($profil['email'] ?? '') ?>">

<?php elseif ($id_role == 2): ?>
<input type="text" class="form-control mb-2" value="<?= htmlspecialchars($profil['nidn'] ?? '') ?>" disabled>
<input type="text" name="jurusan" class="form-control mb-2" value="<?= htmlspecialchars($profil['jurusan'] ?? '') ?>">
<input type="text" name="keahlian" class="form-control" value="<?= htmlspecialchars($profil['keahlian'] ?? '') ?>">

<?php else: ?>
<input type="text" class="form-control mb-2" value="<?= htmlspecialchars($profil['nim'] ?? '') ?>" disabled>
<input type="text" name="jurusan" class="form-control mb-2" value="<?= htmlspecialchars($profil['jurusan'] ?? '') ?>">
<input type="number" name="angkatan" class="form-control" value="<?= htmlspecialchars($profil['angkatan'] ?? '') ?>">

<?php endif; ?>

<button name="simpan_profil" class="btn btn-success mt-3">Simpan Perubahan</button>
</form>

</div>
</div>
</div>

</div>
</div>
</main>

<?php include __DIR__ . "/../includes/layout_bottom.php"; ?>
