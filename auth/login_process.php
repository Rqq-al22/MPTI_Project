<?php
session_start();
require_once "../config/db.php";
require_once "../helpers/log_helper.php";

$role     = $_POST['role'] ?? '';
$identity = trim($_POST['identity'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($role === '' || $identity === '' || $password === '') {
    header("Location: login_form.php?error=empty");
    exit;
}

/* ======================================================
   LOGIN MAHASISWA (id_role = 3 | login pakai NIM)
   ====================================================== */
if ($role === 'mahasiswa') {

    $stmt = $conn->prepare("
        SELECT 
            u.id_user,
            u.username,
            u.password,
            u.id_role,
            m.nim
        FROM users u
        JOIN mahasiswa m ON u.id_user = m.id_user
        WHERE m.nim = ? AND u.id_role = 3
        LIMIT 1
    ");
    $stmt->bind_param("s", $identity);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password']) || $password === $user['password']) {

        $_SESSION['user_id']  = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['id_role']  = 3;
        $_SESSION['nim']      = $user['nim'];

        log_activity($conn, "Mahasiswa login ke sistem");

        header("Location: ../mahasiswa/dashboard_mahasiswa.php");
        exit;
    }
}

/* ======================================================
   LOGIN DOSEN (id_role = 2 | login pakai NIDN)
   ====================================================== */
elseif ($role === 'dosen') {

    $stmt = $conn->prepare("
        SELECT 
            u.id_user,
            u.username,
            u.password,
            u.id_role,
            d.nidn
        FROM users u
        JOIN dosen d ON u.id_user = d.id_user
        WHERE d.nidn = ? AND u.id_role = 2
        LIMIT 1
    ");
    $stmt->bind_param("s", $identity);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password']) || $password === $user['password']) {


        $_SESSION['user_id']  = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['id_role']  = 2;
        $_SESSION['nidn']     = $user['nidn'];

        log_activity($conn, "Dosen login ke sistem");

        header("Location: ../dosen/dashboard_dosen.php");
        exit;
    }
}

/* ======================================================
   LOGIN ADMIN (id_role = 1 | login pakai USERNAME)
   ====================================================== */
elseif ($role === 'admin') {

    $stmt = $conn->prepare("
        SELECT 
            u.id_user,
            u.username,
            u.password,
            u.id_role
        FROM users u
        JOIN admin a ON u.id_user = a.id_user
        WHERE u.username = ? AND u.id_role = 1
        LIMIT 1
    ");
    $stmt->bind_param("s", $identity);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password']) || $password === $user['password']) {


        $_SESSION['user_id']  = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['id_role']  = 1;

        log_activity($conn, "Admin login ke sistem");

        header("Location: ../admin/dashboard_admin.php");
        exit;
    }
}

/* ======================================================
   LOGIN GAGAL
   ====================================================== */
header("Location: login_form.php?error=invalid");
exit;
