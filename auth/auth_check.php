<?php
session_start();

/* ===============================
   CEK SUDAH LOGIN
   =============================== */
function require_login() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['id_role'])) {
        header("Location: ../auth/login_form.php");
        exit;
    }
}

/* ===============================
   CEK ROLE
   =============================== */
function require_role($role) {
    require_login();

    $map = [
        'admin'     => 1,
        'dosen'     => 2,
        'mahasiswa' => 3
    ];

    if (!isset($map[$role])) {
        die("Role tidak valid");
    }

    if ($_SESSION['id_role'] !== $map[$role]) {
        die("Akses ditolak: bukan $role");
    }
}
