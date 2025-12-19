<?php
require_once "../auth/auth_check.php";
require_role('dosen');
require_once "../config/db.php";

$id = $_POST['id_laporan_mingguan'] ?? null;
$aksi = $_POST['aksi'] ?? null;

if (!$id || !$aksi) {
    die("Aksi tidak valid");
}

$status = ($aksi === 'setujui') ? 'Disetujui' : 'Ditolak';

$stmt = $conn->prepare("
    UPDATE laporan_mingguan
    SET status = ?
    WHERE id_laporan_mingguan = ?
");
$stmt->bind_param("si", $status, $id);
$stmt->execute();

header("Location: laporan_mhs.php");
exit;
