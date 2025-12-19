<?php
/**
 * -------------------------------------------------
 * KONFIGURASI DATABASE
 * Sistem Informasi Rekap Kerja Praktik (KP)
 * -------------------------------------------------
 */

$host     = "localhost";
$user     = "root";
$password = "";
$database = "mpti_db";

/* Membuat koneksi */
$conn = mysqli_connect($host, $user, $password, $database);

/* Cek koneksi */
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

/* Set charset agar aman dari masalah encoding */
mysqli_set_charset($conn, "utf8");
