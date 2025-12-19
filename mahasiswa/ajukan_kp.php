<?php
require_once "../auth/auth_check.php";
require_role('mahasiswa');
require_once "../config/db.php";

$current_page  = 'ajukan_kp.php';
$page_title    = "Ajukan Kerja Praktik";
$asset_prefix  = "../";
$logout_prefix = "../";

include "../includes/layout_top.php";
include "../includes/sidebar.php";
include "../includes/header.php";

$nim = $_SESSION['nim'] ?? null;
$error = '';
$sukses = '';

if (!$nim) {
    echo "<div class='alert alert-danger'>NIM tidak ditemukan dalam sesi login.</div>";
    include "../includes/layout_bottom.php";
    exit;
}

/* ===============================
   PROSES SUBMIT
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil & rapikan input
    $nama_instansi   = trim($_POST['nama_instansi'] ?? '');
    $alamat_instansi = trim($_POST['alamat_instansi'] ?? '');
    $kontak_instansi = trim($_POST['kontak_instansi'] ?? '');
    $posisi          = trim($_POST['posisi'] ?? '');
    $pembimbing      = trim($_POST['pembimbing_instansi'] ?? '');
    $tgl_mulai       = $_POST['tgl_mulai'] ?? '';
    $tgl_selesai     = $_POST['tgl_selesai'] ?? '';

    // Validasi wajib
    $fields = [
        'Nama Instansi'   => $nama_instansi,
        'Alamat Instansi' => $alamat_instansi,
        'Kontak Instansi' => $kontak_instansi,
        'Posisi KP'       => $posisi,
        'Tanggal Mulai'   => $tgl_mulai,
        'Tanggal Selesai' => $tgl_selesai
    ];

    foreach ($fields as $label => $value) {
        if ($value === '') {
            $error = "$label wajib diisi.";
            break;
        }
    }

    if ($error === '' && $tgl_mulai > $tgl_selesai) {
        $error = "Tanggal mulai tidak boleh lebih besar dari tanggal selesai.";
    }

    /* ===============================
       UPLOAD SURAT (OPSIONAL)
    ================================ */
    $surat_file = null;
    if ($error === '' && !empty($_FILES['surat_diterima_file']['name'])) {

        $dir = "../uploads/surat/";
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $ext = pathinfo($_FILES['surat_diterima_file']['name'], PATHINFO_EXTENSION);
        $surat_file = "surat_{$nim}_" . time() . "." . $ext;

        move_uploaded_file(
            $_FILES['surat_diterima_file']['tmp_name'],
            $dir . $surat_file
        );
    }

    /* ===============================
       SIMPAN DATABASE
    ================================ */
    if ($error === '') {

        $stmt = $conn->prepare(
            "INSERT INTO kp
            (nim, nama_instansi, alamat_instansi, kontak_instansi, posisi, pembimbing_instansi,
             tgl_mulai, tgl_selesai, status, surat_diterima_file)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pengajuan', ?)"
        );

        $stmt->bind_param(
            "sssssssss",
            $nim,
            $nama_instansi,
            $alamat_instansi,
            $kontak_instansi,
            $posisi,
            $pembimbing,
            $tgl_mulai,
            $tgl_selesai,
            $surat_file
        );

        if ($stmt->execute()) {
            $sukses = "Pengajuan Kerja Praktik berhasil dikirim.";
        } else {
            $error = "Gagal menyimpan data KP.";
        }
    }
}
?>

<main class="pc-container">
<div class="pc-content">

<h3 class="mb-4">Ajukan Kerja Praktik</h3>

<?php if ($error): ?>
<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($sukses): ?>
<div class="alert alert-success"><?= $sukses ?></div>
<a href="data_kp.php" class="btn btn-primary mt-3">Lihat Data KP</a>

<?php else: ?>

<form method="POST" enctype="multipart/form-data" class="card p-4">

<div class="mb-3">
    <label>Nama Instansi</label>
    <input type="text" name="nama_instansi" class="form-control" required>
</div>

<div class="mb-3">
    <label>Alamat Instansi</label>
    <textarea name="alamat_instansi" class="form-control" rows="2" required></textarea>
</div>

<div class="mb-3">
    <label>Kontak Instansi (HP / Email)</label>
    <input type="text" name="kontak_instansi" class="form-control" required>
</div>

<div class="mb-3">
    <label>Posisi Kerja Praktik</label>
    <input type="text" name="posisi" class="form-control" required>
</div>

<div class="mb-3">
    <label>Pembimbing Instansi</label>
    <input type="text" name="pembimbing_instansi" class="form-control">
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label>Tanggal Mulai</label>
        <input type="date" name="tgl_mulai" class="form-control" required>
    </div>
    <div class="col-md-6 mb-3">
        <label>Tanggal Selesai</label>
        <input type="date" name="tgl_selesai" class="form-control" required>
    </div>
</div>

<div class="mb-3">
    <label>Surat Diterima (PDF/JPG)</label>
    <input type="file" name="surat_diterima_file" class="form-control">
</div>

<button class="btn btn-primary">Kirim Pengajuan</button>

</form>

<?php endif; ?>

</div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
