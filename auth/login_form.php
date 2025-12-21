<!doctype html>
<html lang="id">
<head>
  <title>Login | Sistem Informasi Rekap KP</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">

  <!-- Bootstrap -->
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/bootstrap-icons.css" rel="stylesheet">

  <style>
    :root{
      --tosca:#14b8a6;
      --tosca-dark:#0f766e;
      --bg:#f3f4f6;
      --text-dark:#1f2937;
      --text-muted:#6b7280;
      --border:#e5e7eb;
    }

    body{
      font-family:'Open Sans',sans-serif;
      min-height:100vh;
      background:linear-gradient(135deg,#eeeeee,#f5f5f5);
      display:flex;
      align-items:center;
      justify-content:center;
    }

    /* ===============================
       TOMBOL KEMBALI KE BERANDA
       =============================== */
    .btn-back-home{
      position:fixed;
      top:24px;
      left:24px;
      display:flex;
      align-items:center;
      gap:10px;
      padding:10px 18px;
      border-radius:999px;
      text-decoration:none;
      font-weight:600;
      font-size:.95rem;

      color:var(--tosca-dark);
      background:rgba(255,255,255,.85);
      backdrop-filter:blur(8px);

      border:1px solid rgba(20,184,166,.25);
      box-shadow:
        0 10px 30px rgba(0,0,0,.12),
        inset 0 0 0 1px rgba(255,255,255,.4);

      transition:all .25s ease;
      z-index:999;
    }

    .btn-back-home i{
      font-size:1.1rem;
    }

    .btn-back-home:hover{
      background:linear-gradient(135deg,var(--tosca),var(--tosca-dark));
      color:#fff;
      transform:translateY(-2px);
      box-shadow:0 14px 35px rgba(20,184,166,.45);
    }

    /* ===============================
       LOGIN BOX
       =============================== */
    .login-box{
      max-width:960px;
      width:100%;
      background:#fff;
      border-radius:22px;
      overflow:hidden;
      box-shadow:0 30px 70px rgba(0,0,0,.25);
    }

    .left-panel{
      background:linear-gradient(135deg,var(--tosca),var(--tosca-dark));
      color:#fff;
      padding:45px;
      display:flex;
      align-items:center;
      justify-content:center;
      text-align:center;
    }

    .left-panel h2{
      font-family:'Montserrat',sans-serif;
      font-weight:800;
      margin-bottom:14px;
    }

    .role-list{
      margin-top:22px;
      text-align:left;
      font-size:.95rem;
    }

    .role-list div{
      margin-bottom:10px;
    }

    .right-panel{
      padding:50px;
      background:#fff;
    }

    .right-panel h3{
      font-family:'Montserrat',sans-serif;
      font-weight:800;
      text-align:center;
      margin-bottom:6px;
    }

    .right-panel .sub{
      text-align:center;
      color:var(--text-muted);
      margin-bottom:32px;
    }

    .form-label{
      font-weight:600;
      color:var(--text-dark);
    }

    .form-control{
      border-radius:14px;
      padding:12px 16px;
      border:1px solid var(--border);
    }

    .form-control:focus{
      border-color:var(--tosca);
      box-shadow:0 0 0 .2rem rgba(20,184,166,.15);
    }

    .btn-login{
      background:linear-gradient(135deg,var(--tosca),var(--tosca-dark));
      border:none;
      color:#fff;
      font-weight:700;
      border-radius:14px;
      padding:13px;
      box-shadow:0 14px 35px rgba(20,184,166,.45);
    }

    .btn-login:hover{
      filter:brightness(.95);
      color:#fff;
    }

    .note{
      margin-top:18px;
      font-size:.9rem;
      color:var(--text-muted);
      text-align:center;
    }

    @media(max-width:768px){
      .left-panel{ display:none; }
      .right-panel{ padding:36px; }
    }
  </style>
</head>

<body>

<!-- TOMBOL KEMBALI -->
<a href="../index.php" class="btn-back-home">
  <i class="bi bi-arrow-left"></i>
  <span>Kembali ke Beranda</span>
</a>

<div class="login-box">
  <div class="row g-0">

    <!-- PANEL KIRI -->
    <div class="col-md-5 left-panel">
      <div>
        <h2>Sistem Informasi<br>Rekap KP</h2>
        <p class="mb-3">Login sesuai peran Anda</p>

        <div class="role-list">
          <div><i class="bi bi-mortarboard me-2"></i>Mahasiswa → <b>NIM</b></div>
          <div><i class="bi bi-person-badge me-2"></i>Dosen → <b>NIDN</b></div>
          <div><i class="bi bi-gear me-2"></i>Admin → <b>Username</b></div>
        </div>
      </div>
    </div>

    <!-- PANEL KANAN -->
    <div class="col-md-7 right-panel">
      <h3>Login</h3>
      <div class="sub">Silakan masuk ke sistem</div>

      <form action="../auth/login_process.php" method="POST">

        <div class="mb-3">
          <label class="form-label">Login Sebagai</label>
          <select name="role" class="form-control" required>
            <option value="">-- Pilih Role --</option>
            <option value="mahasiswa">Mahasiswa</option>
            <option value="dosen">Dosen</option>
            <option value="admin">Admin</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">NIM / NIDN / Username</label>
          <input type="text" name="identity" class="form-control" required>
        </div>

        <div class="mb-4">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>

        <div class="d-grid">
          <button type="submit" class="btn btn-login">
            <i class="bi bi-box-arrow-in-right me-1"></i> Login
          </button>
        </div>

        <div class="note">
          Belum punya akun? Hubungi admin prodi.
        </div>

      </form>
    </div>

  </div>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
