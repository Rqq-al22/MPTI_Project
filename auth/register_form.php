<?php
// auth/register_form.php
require_once __DIR__ . "/../config/db.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama      = trim($_POST['nama'] ?? '');
    $nim       = trim($_POST['nim'] ?? '');
    $jurusan   = trim($_POST['jurusan'] ?? '');
    $angkatan  = trim($_POST['angkatan'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $password  = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($nama === '' || $nim === '' || $jurusan === '' || $angkatan === '' || $username === '' || $password === '') {
        $error = "Semua field wajib diisi.";
    } elseif ($password !== $password2) {
        $error = "Konfirmasi password tidak cocok.";
    } elseif ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email tidak valid atau kosong.";
    } else {

        // cek username
        $cek = $conn->prepare("SELECT id_user FROM users WHERE username = ? LIMIT 1");
        $cek->bind_param("s", $username);
        $cek->execute();
        $cek->store_result();

        if ($cek->num_rows > 0) {
            $error = "Username sudah digunakan.";
        } else {

            $conn->begin_transaction();

            try {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $role = 3; // mahasiswa

                // insert users
                $stmt = $conn->prepare("
                    INSERT INTO users (username, password, id_role)
                    VALUES (?, ?, ?)
                ");
                $stmt->bind_param("ssi", $username, $hash, $role);
                $stmt->execute();

                $id_user = $stmt->insert_id;

                // insert mahasiswa (include email)
                $stmt2 = $conn->prepare("
                    INSERT INTO mahasiswa (id_user, nim, nama, jurusan, angkatan, email)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt2->bind_param("isssis", $id_user, $nim, $nama, $jurusan, $angkatan, $email);
                $stmt2->execute();

                $conn->commit();
                $success = "Pendaftaran berhasil. Silakan login.";

            } catch (Exception $e) {
                $conn->rollback();
                $error = "Terjadi kesalahan sistem.";
            }
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Daftar Mahasiswa | SI Rekap KP</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

  <!-- Bootstrap -->
  <link href="/MPTI_Project/assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="/MPTI_Project/assets/css/bootstrap-icons.css" rel="stylesheet">

  <style>
 body{
  font-family:'Inter',sans-serif;
  background: linear-gradient(135deg, #13698eff, #199bc6ff);
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
}
}

   .card{
  background:#ffffff;
  border:none;
  border-radius:20px;
  box-shadow:0 25px 50px rgba(15,23,42,.15);
}

    h3{
      font-family:'Montserrat',sans-serif;
      font-weight:800;
    }
  </style>
</head>

<body>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-6">

      <div class="card p-4">
        <div class="text-center mb-3">
          <h3>Daftar Mahasiswa</h3>
          <p class="text-muted mb-0">
            Sistem Informasi Rekap Kerja Praktik Mahasiswa
          </p>
        </div>

        <?php if ($error): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
          <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="post">

          <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">NIM</label>
            <input type="text" name="nim" class="form-control" required>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Jurusan</label>
              <input type="text" name="jurusan" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Angkatan</label>
              <input type="number" name="angkatan" class="form-control" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>

          <hr>

          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password" name="password2" class="form-control" required>
          </div>

          <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn btn-primary btn-lg">
              <i class="bi bi-person-plus"></i> Daftar
            </button>
            <a href="/MPTI_Project/auth/login_form.php" class="btn btn-outline-secondary">
              Sudah punya akun? Login
            </a>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>

<script src="/MPTI_Project/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
