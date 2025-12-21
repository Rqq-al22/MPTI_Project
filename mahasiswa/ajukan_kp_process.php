<?php
require_once "../auth/auth_check.php";
require_role('mahasiswa');
require_once "../config/db.php";

$nim  = $_SESSION['nim'] ?? null;
$mode = $_POST['mode'] ?? 'create';

if (!$nim) {
    die("Session mahasiswa tidak valid.");
}

/* ===============================
   AMBIL INPUT
   =============================== */
$nama_instansi       = $_POST['nama_instansi'];
$alamat_instansi     = $_POST['alamat_instansi'];
$kontak_instansi     = $_POST['kontak_instansi'];
$posisi              = $_POST['posisi'];
$pembimbing_instansi = $_POST['pembimbing_instansi'];
$tgl_mulai           = $_POST['tgl_mulai'];
$tgl_selesai         = $_POST['tgl_selesai'];

if ($tgl_mulai > $tgl_selesai) {
    die("Tanggal mulai tidak boleh melebihi tanggal selesai.");
}

/* ===============================
   UPLOAD SURAT PENERIMAAN
   =============================== */
$surat_file = null;

if (!empty($_FILES['surat_diterima']['name'])) {

    $allowed = ['pdf','jpg','jpeg','png'];
    $ext = strtolower(pathinfo($_FILES['surat_diterima']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        die("Format surat tidak diizinkan.");
    }

    if ($_FILES['surat_diterima']['size'] > 2 * 1024 * 1024) {
        die("Ukuran file maksimal 2MB.");
    }

    $dir = "../uploads/surat/";
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $surat_file = 'surat_' . time() . '_' . rand(100,999) . '.' . $ext;
    move_uploaded_file($_FILES['surat_diterima']['tmp_name'], $dir . $surat_file);
}

/* ===============================
   INSERT / UPDATE KP
   =============================== */
if ($mode === 'create') {

    $stmt = $conn->prepare("
        INSERT INTO kp
        (nim, nama_instansi, alamat_instansi, kontak_instansi,
         posisi, pembimbing_instansi, tgl_mulai, tgl_selesai,
         status, surat_diterima_file)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pengajuan', ?)
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

} else {

    $id_kp = $_POST['id_kp'];

    $stmt = $conn->prepare("
        UPDATE kp SET
            nama_instansi = ?,
            alamat_instansi = ?,
            kontak_instansi = ?,
            posisi = ?,
            pembimbing_instansi = ?,
            tgl_mulai = ?,
            tgl_selesai = ?,
            surat_diterima_file = COALESCE(?, surat_diterima_file)
        WHERE id_kp = ? AND nim = ?
    ");
    $stmt->bind_param(
        "ssssssssss",
        $nama_instansi,
        $alamat_instansi,
        $kontak_instansi,
        $posisi,
        $pembimbing_instansi,
        $tgl_mulai,
        $tgl_selesai,
        $surat_file,
        $id_kp,
        $nim
    );
}

$stmt->execute();
$stmt->close();

header("Location: data_kp.php");
exit;
