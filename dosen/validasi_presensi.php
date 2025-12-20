<?php
require_once "../auth/auth_check.php";
require_role('dosen');
require_once "../config/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: presensi_mhs.php");
    exit;
}

$id_presensi = intval($_POST['id_presensi']);
$aksi = $_POST['aksi'];

$status = match ($aksi) {
    'approve' => 'Approve',
    'reject'  => 'Reject',
    default   => 'Pending'
};

$stmt = $conn->prepare("
    UPDATE presensi
    SET validasi = ?
    WHERE id_presensi = ?
");
$stmt->bind_param("si", $status, $id_presensi);
$stmt->execute();

header("Location: presensi_mhs.php");
exit;
