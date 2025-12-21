<?php
require_once __DIR__ . "/../config/db.php";

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama      = trim($_POST['nama'] ?? '');
    $nim       = trim($_POST['nim'] ?? '');
    $jurusan   = trim($_POST['jurusan'] ?? '');
    $angkatan  = trim($_POST['angkatan'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $password  = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($nama === '' || $nim === '' || $jurusan === '' || $angkatan === '' || $username === '' || $password === '') {
        $error = "Semua field wajib diisi.";
    } elseif ($password !== $password2) {
        $error = "Konfirmasi password tidak cocok.";
    } else {

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
                $role = 3;

                $stmt = $conn->prepare("
                    INSERT INTO users (username, password, id_role)
                    VALUES (?, ?, ?)
                ");
                $stmt->bind_param("ssi", $username, $hash, $role);
                $stmt->execute();

                $id_user = $stmt->insert_id;

                $stmt2 = $conn->prepare("
                    INSERT INTO mahasiswa (id_user, nim, nama, jurusan, angkatan)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt2->bind_param("isssi", $id_user, $nim, $nama, $jurusan, $angkatan);
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

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">

<link href="/MPTI_Project/assets/css/bootstrap.min.css" rel="stylesheet">
<link href="/MPTI_Project/assets/css/bootstrap-icons.css" rel="stylesheet">

<style>
:root{
  --tosca:#14b8a6;
  --tosca-dark:#0f766e;
  --tosca-soft:#e6fffb;
  --text:#1f2937;
  --muted:#6b7280;
  --border:#e5e7eb;
}
/* ===============================
   TOMBOL KEMBALI KE BERANDA
   =============================== */
.btn-back-home{
  position: fixed;
  top: 28px;
  left: 28px;
  z-index: 999;

  display: inline-flex;
  align-items: center;
  gap: 10px;

  padding: 10px 18px;
  border-radius: 999px;

  font-weight: 600;
  font-size: .95rem;
  text-decoration: none;

  color: var(--tosca-dark);
  background: #ffffff;

  border: 1.5px solid rgba(20,184,166,.35);
  box-shadow: 0 10px 30px rgba(20,184,166,.25);

  transition: all .25s ease;
}

.btn-back-home i{
  font-size: 1.1rem;
}

.btn-back-home:hover{
  background: linear-gradient(135deg,var(--tosca),var(--tosca-dark));
  color: #fff;
  transform: translateX(-3px);
  box-shadow: 0 14px 35px rgba(20,184,166,.45);
}

/* MOBILE */
@media (max-width: 576px){
  .btn-back-home{
    top: 16px;
    left: 16px;
    padding: 8px 14px;
    font-size: .9rem;
  }
}


body{
  font-family:'Open Sans',sans-serif;
  min-height:100vh;
  background:linear-gradient(135deg,#f3f4f6,#e5e7eb);
  display:flex;
  align-items:center;
  justify-content:center;
}

.register-box{
  max-width:980px;
  width:100%;
  background:#fff;
  border-radius:24px;
  overflow:hidden;
  box-shadow:0 30px 70px rgba(0,0,0,.25);
  position:relative;
}

/* === TOMBOL KEMBALI === */
.btn-back{
  position:absolute;
  top:20px;
  left:20px;
  padding:8px 14px;
  border-radius:12px;
  font-weight:600;
  text-decoration:none;
  color:var(--tosca-dark);
  background:#fff;
  border:1px solid rgba(20,184,166,.45);
  display:inline-flex;
  align-items:center;
  gap:6px;
  transition:.25s;
  box-shadow:0 6px 18px rgba(20,184,166,.18);
}
.btn-back:hover{
  background:linear-gradient(135deg,var(--tosca),var(--tosca-dark));
  color:#fff;
  transform:translateX(-2px);
}

/* LEFT */
.left-panel{
  background:linear-gradient(135deg,var(--tosca),var(--tosca-dark));
  color:#fff;
  padding:50px;
  display:flex;
  align-items:center;
}
.left-panel h2{
  font-family:'Montserrat',sans-serif;
  font-weight:800;
}
.left-panel ul{list-style:none;padding-left:0;margin-top:20px;}
.left-panel li{margin-bottom:10px;}

/* RIGHT */
.right-panel{padding:50px;}
.right-panel h3{
  font-family:'Montserrat',sans-serif;
  font-weight:800;
  text-align:center;
}
.sub{text-align:center;color:var(--muted);margin-bottom:30px;}

.form-control{
  border-radius:14px;
  padding:12px 14px;
  border:1px solid var(--border);
}
.form-control:focus{
  border-color:var(--tosca);
  box-shadow:0 0 0 .2rem rgba(20,184,166,.18);
}

.btn-brand{
  background:linear-gradient(135deg,var(--tosca),var(--tosca-dark));
  border:none;
  color:#fff;
  font-weight:700;
  border-radius:14px;
  padding:12px;
  box-shadow:0 14px 35px rgba(20,184,166,.45);
}
.btn-brand:hover{filter:brightness(.95);}

@media(max-width:768px){
  .left-panel{display:none;}
}
</style>
</head>

<body>

<div class="register-box">
<!-- TOMBOL KEMBALI KE BERANDA -->
<a href="../index.php" class="btn-back-home">
  <i class="bi bi-arrow-left"></i>
  Kembali ke Beranda
</a>


  <div class="row g-0">
    <div class="col-md-5 left-panel">
      <div>
        <h2>Sistem Informasi<br>Rekap KP</h2>
        <p>Form pendaftaran mahasiswa Kerja Praktik</p>
        <ul>
          <li><i class="bi bi-check-circle me-2"></i>Aman & Terverifikasi</li>
          <li><i class="bi bi-check-circle me-2"></i>Terintegrasi dosen</li>
          <li><i class="bi bi-check-circle me-2"></i>Paperless</li>
        </ul>
      </div>
    </div>

    <div class="col-md-7 right-panel">
      <h3>Daftar Mahasiswa</h3>
      <div class="sub">Lengkapi data dengan benar</div>

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

        <hr>

        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-4">
          <label class="form-label">Konfirmasi Password</label>
          <input type="password" name="password2" class="form-control" required>
        </div>

        <div class="d-grid gap-2">
          <button class="btn btn-brand">
            <i class="bi bi-person-plus me-1"></i> Daftar
          </button>
          <a href="/MPTI_Project/auth/login_form.php" class="btn btn-outline-secondary">
            Sudah punya akun? Login
          </a>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="/MPTI_Project/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
