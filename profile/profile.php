<?php
require_once __DIR__ . "/../auth/auth_check.php";
require_login();
require_once __DIR__ . "/../config/db.php";

$current_page  = "profile.php";
$page_title    = "Profil Pengguna";
$asset_prefix  = "../";
$logout_prefix = "../";

/* =========================================================
   HELPER: cek kolom ada/tidak
   ========================================================= */
function column_exists(mysqli $conn, string $table, string $column): bool {
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS cnt
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME   = ?
          AND COLUMN_NAME  = ?
    ");
    $stmt->bind_param("ss", $table, $column);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return ((int)($row['cnt'] ?? 0) > 0);
}

/* =========================================================
   HELPER UPLOAD FOTO
   ========================================================= */
function upload_foto_local(array $file, string $targetDirAbs): ?string
{
    if (!isset($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) return null;

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
$user_id = (int)($_SESSION['user_id'] ?? 0);
$id_role = (int)($_SESSION['id_role'] ?? 0);

if ($user_id <= 0) die("Sesi tidak valid.");

$qUser = $conn->prepare("SELECT username, foto FROM users WHERE id_user=? LIMIT 1");
$qUser->bind_param("i", $user_id);
$qUser->execute();
$user = $qUser->get_result()->fetch_assoc();
$qUser->close();
if (!$user) die("User tidak ditemukan");

/* =========================================================
   CEK ADA EMAIL DI TABEL MASING-MASING
   ========================================================= */
$adminHasEmail    = column_exists($conn, "admin", "email");
$dosenHasEmail    = column_exists($conn, "dosen", "email");
$mahasiswaHasEmail= column_exists($conn, "mahasiswa", "email");

/* =========================================================
   DATA PROFIL BERDASARKAN ROLE
   ========================================================= */
$profil = [];

if ($id_role === 1) {
    // admin
    if ($adminHasEmail) {
        $q = $conn->prepare("SELECT nama_admin AS nama, email FROM admin WHERE id_user=? LIMIT 1");
    } else {
        $q = $conn->prepare("SELECT nama_admin AS nama FROM admin WHERE id_user=? LIMIT 1");
    }

} elseif ($id_role === 2) {
    // dosen (struktur Anda: nidn, nip, nama, jurusan, peminatan, id_user)
    if ($dosenHasEmail) {
        $q = $conn->prepare("SELECT nama, nidn, nip, jurusan, peminatan, email FROM dosen WHERE id_user=? LIMIT 1");
    } else {
        $q = $conn->prepare("SELECT nama, nidn, nip, jurusan, peminatan FROM dosen WHERE id_user=? LIMIT 1");
    }

} else {
    // mahasiswa (struktur Anda: nim, nama, jurusan, angkatan, id_user)
    if ($mahasiswaHasEmail) {
        $q = $conn->prepare("SELECT nama, nim, jurusan, angkatan, email FROM mahasiswa WHERE id_user=? LIMIT 1");
    } else {
        $q = $conn->prepare("SELECT nama, nim, jurusan, angkatan FROM mahasiswa WHERE id_user=? LIMIT 1");
    }
}

$q->bind_param("i", $user_id);
$q->execute();
$profil = $q->get_result()->fetch_assoc() ?: [];
$q->close();

/* =========================================================
   PROSES POST
   ========================================================= */
$success = null;
$error   = "";

/* ---------- A) UPLOAD FOTO ---------- */
if (isset($_POST['upload_foto'])) {

    $uploadDirAbs = __DIR__ . "/../uploads/profile";
    $fotoName = upload_foto_local($_FILES['foto'] ?? [], $uploadDirAbs);

    if ($fotoName) {
        $stmt = $conn->prepare("UPDATE users SET foto=? WHERE id_user=?");
        $stmt->bind_param("si", $fotoName, $user_id);
        $stmt->execute();
        $stmt->close();
        $success = 1;
        // refresh data foto agar langsung tampil
        $user['foto'] = $fotoName;
    } else {
        $success = 0;
        $error = "Upload foto gagal. Pastikan format JPG/PNG dan file dipilih.";
    }
}

/* ---------- B) UPDATE PROFIL ---------- */
if (isset($_POST['simpan_profil'])) {

    $nama = trim($_POST['nama'] ?? '');
    if ($nama === '') {
        $success = 0;
        $error = "Nama wajib diisi.";
    } else {

        if ($id_role === 1) {
            // admin
            if ($adminHasEmail) {
                $email = trim($_POST['email'] ?? '');
                $stmt = $conn->prepare("UPDATE admin SET nama_admin=?, email=? WHERE id_user=?");
                $stmt->bind_param("ssi", $nama, $email, $user_id);
            } else {
                $stmt = $conn->prepare("UPDATE admin SET nama_admin=? WHERE id_user=?");
                $stmt->bind_param("si", $nama, $user_id);
            }

        } elseif ($id_role === 2) {
            // dosen
            $jurusan   = trim($_POST['jurusan'] ?? '');
            $peminatan = trim($_POST['peminatan'] ?? '');

            if ($dosenHasEmail) {
                $email = trim($_POST['email'] ?? '');
                $stmt = $conn->prepare("UPDATE dosen SET nama=?, jurusan=?, peminatan=?, email=? WHERE id_user=?");
                $stmt->bind_param("ssssi", $nama, $jurusan, $peminatan, $email, $user_id);
            } else {
                $stmt = $conn->prepare("UPDATE dosen SET nama=?, jurusan=?, peminatan=? WHERE id_user=?");
                $stmt->bind_param("sssi", $nama, $jurusan, $peminatan, $user_id);
            }

        } else {
            // mahasiswa
            $jurusan   = trim($_POST['jurusan'] ?? '');
            $angkatan  = (int)($_POST['angkatan'] ?? 0);

            if ($mahasiswaHasEmail) {
                $email = trim($_POST['email'] ?? '');
                $stmt = $conn->prepare("UPDATE mahasiswa SET nama=?, jurusan=?, angkatan=?, email=? WHERE id_user=?");
                // benar: ssisi (nama:string, jurusan:string, angkatan:int, email:string, user_id:int)
                $stmt->bind_param("ssisi", $nama, $jurusan, $angkatan, $email, $user_id);
            } else {
                $stmt = $conn->prepare("UPDATE mahasiswa SET nama=?, jurusan=?, angkatan=? WHERE id_user=?");
                $stmt->bind_param("ssii", $nama, $jurusan, $angkatan, $user_id);
            }
        }

        $stmt->execute();
        $stmt->close();
        $success = 1;

        // refresh data profil setelah update
        header("Location: profile.php?saved=1");
        exit;
    }
}

/* =========================================================
   TAMPILAN
   ========================================================= */
$fotoRel = !empty($user['foto'])
    ? "../uploads/profile/" . $user['foto']
    : "../uploads/profile/default.png";

include __DIR__ . "/../includes/layout_top.php";
if ($id_role === 1) include __DIR__ . "/../includes/sidebar_admin.php";
elseif ($id_role === 2) include __DIR__ . "/../includes/sidebar_dosen.php";
else include __DIR__ . "/../includes/sidebar_mahasiswa.php";
?>

<main class="pc-container">
<?php include __DIR__ . "/../includes/header.php"; ?>

<div class="pc-content">

<h3 class="fw-bold mb-4">Profil Pengguna</h3>

<?php if (isset($_GET['saved']) && $_GET['saved'] == 1): ?>
  <div class="alert alert-success">Perubahan berhasil disimpan.</div>
<?php elseif ($success === 1): ?>
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
        <input type="file" name="foto" class="form-control mb-2" accept=".jpg,.jpeg,.png" required>
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

        <?php if ($id_role === 1): ?>
          <?php if ($adminHasEmail): ?>
            <div class="mb-2">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($profil['email'] ?? '') ?>">
            </div>
          <?php endif; ?>

        <?php elseif ($id_role === 2): ?>
          <div class="mb-2">
            <label class="form-label">NIDN</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($profil['nidn'] ?? '') ?>" disabled>
          </div>

          <div class="mb-2">
            <label class="form-label">NIP</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($profil['nip'] ?? '') ?>" disabled>
          </div>

          <?php if ($dosenHasEmail): ?>
            <div class="mb-2">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($profil['email'] ?? '') ?>">
            </div>
          <?php endif; ?>

          <div class="mb-2">
            <label class="form-label">Jurusan</label>
            <input type="text" name="jurusan" class="form-control" value="<?= htmlspecialchars($profil['jurusan'] ?? '') ?>">
          </div>

          <div class="mb-2">
            <label class="form-label">Peminatan</label>
            <input type="text" name="peminatan" class="form-control" value="<?= htmlspecialchars($profil['peminatan'] ?? '') ?>">
          </div>

        <?php else: ?>
          <div class="mb-2">
            <label class="form-label">NIM</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($profil['nim'] ?? '') ?>" disabled>
          </div>

          <?php if ($mahasiswaHasEmail): ?>
            <div class="mb-2">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($profil['email'] ?? '') ?>">
            </div>
          <?php endif; ?>

          <div class="mb-2">
            <label class="form-label">Jurusan</label>
            <input type="text" name="jurusan" class="form-control" value="<?= htmlspecialchars($profil['jurusan'] ?? '') ?>">
          </div>

          <div class="mb-2">
            <label class="form-label">Angkatan</label>
            <input type="number" name="angkatan" class="form-control" value="<?= htmlspecialchars($profil['angkatan'] ?? '') ?>">
          </div>
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
