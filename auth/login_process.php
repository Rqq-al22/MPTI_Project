<?php
session_start();
require_once "../config/db.php";

$role     = $_POST['role'] ?? '';
$identity = trim($_POST['identity'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($role === '' || $identity === '' || $password === '') {
    header("Location: login_form.php?error=empty");
    exit;
}

/* =========================
   LOGIN MAHASISWA
   ========================= */
elseif ($role === 'admin') {

    $stmt = $conn->prepare("
        SELECT u.id_user, u.username, u.password, u.id_role
        FROM users u
        JOIN admin a ON u.id_user = a.id_user
        WHERE u.username = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $identity);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && $user['id_role'] == 1 && $password === $user['password']) {

        $_SESSION['user_id']  = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['id_role']  = $user['id_role'];

        header("Location: ../admin/dashboard_admin.php");
        exit;
    }
}


/* =========================
   LOGIN DOSEN (FIX UTAMA)
   ========================= */
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
        WHERE d.nidn = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $identity);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    // ===== DEBUG SEMENTARA =====
    /*
    echo "<pre>";
    var_dump($user);
    exit;
    */
    // ==========================

    if ($user && $user['id_role'] == 2 && $password === $user['password']) {

        $_SESSION['user_id']  = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['id_role']  = $user['id_role'];

        header("Location: ../dosen/dashboard_dosen.php");
        exit;
    }
}

/* =========================
   LOGIN ADMIN
   ========================= */
elseif ($role === 'admin') {

    $stmt = $conn->prepare("
        SELECT u.id_user, u.username, u.password, u.id_role
        FROM users u
        JOIN admin a ON u.id_user = a.id_user
        WHERE u.username = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $identity);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && $user['id_role'] == 1 && $password === $user['password']) {

        $_SESSION['user_id']  = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['id_role']  = $user['id_role'];

        header("Location: ../admin/dashboard_admin.php");
        exit;
    }
}

/* =========================
   LOGIN GAGAL
   ========================= */
header("Location: login_form.php?error=invalid");
exit;
