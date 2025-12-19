<?php
require_once "../auth/auth_check.php";
require_role('dosen');
require_once "../config/db.php";

$id_kp = $_POST['id_kp'] ?? null;
$aksi  = $_POST['aksi'] ?? null;

if (!$id_kp || !$aksi) {
    die("Aksi tidak valid");
}

if ($aksi === 'setujui') {
    $status = 'Berlangsung';
} elseif ($aksi === 'tolak') {
    $status = 'Ditolak';
} else {
    die("Aksi tidak dikenali");
}

$stmt = $conn->prepare("UPDATE kp SET status = ? WHERE id_kp = ?");
$stmt->bind_param("si", $status, $id_kp);
$stmt->execute();

header("Location: bimbingan.php");
exit;

