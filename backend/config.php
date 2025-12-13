<?php
// KONFIGURASI DATABASE
$host     = "localhost";
$username = "root";
$password = "";
$database = "mpti_db";

// Kasih konek

$db = mysqli_connect($host, $username, $password, $database);

// Cek
if (!$db) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
