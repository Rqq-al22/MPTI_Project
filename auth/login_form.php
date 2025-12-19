<!doctype html>
<html lang="en">
<head>
    <title>Login | Sistem Informasi Rekap KP</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- FONT -->
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900&display=swap" rel="stylesheet">

    <!-- ICON -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- CSS LOGIN -->
    <link rel="stylesheet" href="../assets/css/login.css">
</head>

<body>

<div class="login-container">
<section class="ftco-section">
<div class="container">

    <!-- JUDUL -->
    <div class="row justify-content-center">
        <div class="col-md-6 text-center mb-5">
            <h2 class="heading-section">Sistem Informasi Rekap Kerja Praktik</h2>
            <p class="text-muted">Silakan login sesuai peran Anda</p>
        </div>
    </div>

    <div class="row justify-content-center">
    <div class="col-md-12 col-lg-10">
    <div class="wrap d-md-flex">

        <!-- BAGIAN KANAN -->
        <div class="text-wrap p-4 p-lg-5 text-center d-flex align-items-center order-md-last">
            <div class="text w-100">
                <h2>Welcome</h2>
                <p>
                    Mahasiswa login menggunakan <b>NIM</b><br>
                    Dosen login menggunakan <b>NIDN</b><br>
                    Admin login menggunakan <b>Username</b>
                </p>
            </div>
        </div>

        <!-- FORM LOGIN -->
        <div class="login-wrap p-4 p-lg-5">
            <div class="d-flex">
                <div class="w-100">
                    <h3 class="mb-4">Sign In</h3>
                </div>
            </div>

            <form action="../auth/login_process.php" method="POST" class="signin-form">

                <!-- PILIH ROLE -->
                <div class="form-group mb-3">
                    <label class="label">Login Sebagai</label>
                    <select name="role" class="form-control" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="dosen">Dosen</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <!-- IDENTITAS -->
                <div class="form-group mb-3">
                    <label class="label">NIM / NIDN / Username</label>
                    <input type="text"
                           name="identity"
                           class="form-control"
                           placeholder="Masukkan NIM / NIDN / Username"
                           required>
                </div>

                <!-- PASSWORD -->
                <div class="form-group mb-3">
                    <label class="label">Password</label>
                    <input type="password"
                           name="password"
                           class="form-control"
                           placeholder="Password"
                           required>
                </div>

                <!-- SUBMIT -->
                <div class="form-group">
                    <button type="submit" class="form-control btn btn-primary submit px-3">
                        <i class="fa fa-sign-in"></i> Log In
                    </button>
                </div>

            </form>

        </div>
    </div>
    </div>
    </div>

</div>
</section>
</div>

<!-- JS -->
<script src="/MPTI/assets/js/jquery.min.js"></script>
<script src="/MPTI/assets/js/popper.js"></script>
<script src="/MPTI/assets/js/bootstrap.min.js"></script>
<script src="/MPTI/assets/js/main.js"></script>

</body>
</html>
