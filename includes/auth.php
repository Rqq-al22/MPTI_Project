<?php
// Helper autentikasi & otorisasi sederhana
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login() {
    if (!isset($_SESSION['id_user'])) {
        header("Location: ../index.php");
        exit;
    }
}

function require_role($role) {
    require_login();
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        header("Location: ../index.php");
        exit;
    }
}

function esc($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}
?>
