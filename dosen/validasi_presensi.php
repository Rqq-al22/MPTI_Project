<?php
require_once "../auth/auth_check.php";
require_role('dosen');
require_once "../config/db.php";
log_activity($conn, "Memvalidasi presensi mahasiswa bimbingan");


$id_presensi = (int)($_GET['id'] ?? 0);
$aksi = $_GET['aksi'] ?? '';

if (!$id_presensi || !in_array($aksi, ['approve', 'reject'])) {
    die("Aksi tidak valid");
}

$status = ($aksi === 'approve') ? 'Approve' : 'Reject';

$stmt = $conn->prepare("
    UPDATE presensi
    SET validasi = ?
    WHERE id_presensi = ?
");
$stmt->bind_param("si", $status, $id_presensi);
$stmt->execute();

header("Location: presensi_mhs.php");
exit;
