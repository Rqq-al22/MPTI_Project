<?php
require_once "../auth/auth_check.php";
require_role('mahasiswa');
require_once "../config/db.php";

$nim = $_SESSION['nim'];
$edit = $_POST['edit'] ?? 0;

$nama_instansi = $_POST['nama_instansi'];
$alamat_instansi = $_POST['alamat_instansi'];
$kontak_instansi = $_POST['kontak_instansi'];
$posisi = $_POST['posisi'];
$pembimbing_instansi = $_POST['pembimbing_instansi'];
$tgl_mulai = $_POST['tgl_mulai'];
$tgl_selesai = $_POST['tgl_selesai'];

$surat_file = null;

if (!empty($_FILES['surat']['name'])) {
    $dir = "../uploads/surat/";
    if (!is_dir($dir)) mkdir($dir, 0777, true);

    $ext = pathinfo($_FILES['surat']['name'], PATHINFO_EXTENSION);
    $surat_file = "surat_{$nim}_" . time() . ".$ext";
    move_uploaded_file($_FILES['surat']['tmp_name'], $dir . $surat_file);
}

if ($edit) {
    $stmt = $conn->prepare("
        UPDATE kp SET
        nama_instansi=?,
        alamat_instansi=?,
        kontak_instansi=?,
        posisi=?,
        pembimbing_instansi=?,
        tgl_mulai=?,
        tgl_selesai=?,
        status='Pengajuan'
        WHERE nim=?
    ");
    $stmt->bind_param(
        "ssssssss",
        $nama_instansi,
        $alamat_instansi,
        $kontak_instansi,
        $posisi,
        $pembimbing_instansi,
        $tgl_mulai,
        $tgl_selesai,
        $nim
    );
} else {
    $stmt = $conn->prepare("
        INSERT INTO kp
        (nim, nama_instansi, alamat_instansi, kontak_instansi, posisi, pembimbing_instansi, tgl_mulai, tgl_selesai, status, surat_diterima_file)
        VALUES (?,?,?,?,?,?,?,?, 'Pengajuan',?)
    ");
    $stmt->bind_param(
        "sssssssss",
        $nim,
        $nama_instansi,
        $alamat_instansi,
        $kontak_instansi,
        $posisi,
        $pembimbing_instansi,
        $tgl_mulai,
        $tgl_selesai,
        $surat_file
    );
}

$stmt->execute();

header("Location: data_kp.php");
exit;
