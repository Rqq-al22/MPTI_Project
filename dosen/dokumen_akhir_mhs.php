<?php
require_once "../auth/auth_check.php";
require_role('dosen');
require_once "../config/db.php";

$current_page = 'dokumen_akhir_mhs.php';
$page_title   = 'Dokumen Akhir KP';
$asset_prefix = "../";

/* ===============================
   AMBIL NIDN DOSEN
================================ */
$user_id = $_SESSION['user_id'];
$q = $conn->prepare("SELECT nidn FROM dosen WHERE id_user = ? LIMIT 1");
$q->bind_param("i", $user_id);
$q->execute();
$dosen = $q->get_result()->fetch_assoc();
if (!$dosen) die("Data dosen tidak ditemukan");
$nidn = $dosen['nidn'];

/* ===============================
   DATA DOKUMEN AKHIR
================================ */
$stmt = $conn->prepare("
    SELECT
        m.nim,
        m.nama,
        k.nama_instansi,
        da.file_laporan_akhir,
        da.file_ppt,
        da.tanggal_upload,
        da.catatan,
        k.status
    FROM dokumen_akhir da
    JOIN kp k ON da.id_kp = k.id_kp
    JOIN mahasiswa m ON k.nim = m.nim
    WHERE k.nidn = ?
    ORDER BY da.tanggal_upload DESC
");
$stmt->bind_param("s", $nidn);
$stmt->execute();
$data = $stmt->get_result();

include "../includes/layout_top.php";
include "../includes/sidebar_dosen.php";
include "../includes/header.php";
?>

<main class="pc-container">
<div class="pc-content">

<h3 class="fw-bold mb-3">Dokumen Akhir Kerja Praktik</h3>

<div class="card shadow-sm">
<div class="card-body table-responsive">

<table class="table table-bordered align-middle">
<thead class="table-light">
<tr>
    <th>NIM</th>
    <th>Mahasiswa</th>
    <th>Instansi</th>
    <th>Laporan Akhir</th>
    <th>PPT</th>
    <th>Tanggal Upload</th>
    <th>Status KP</th>
</tr>
</thead>
<tbody>

<?php if ($data->num_rows > 0): ?>
<?php while ($row = $data->fetch_assoc()): ?>
<tr>
    <td><?= $row['nim'] ?></td>
    <td><?= htmlspecialchars($row['nama']) ?></td>
    <td><?= htmlspecialchars($row['nama_instansi']) ?></td>

    <td>
        <a href="../uploads/dokumen_akhir/<?= $row['file_laporan_akhir'] ?>"
           target="_blank" class="btn btn-sm btn-outline-primary">
           Lihat
        </a>
    </td>

    <td>
        <a href="../uploads/dokumen_akhir/<?= $row['file_ppt'] ?>"
           target="_blank" class="btn btn-sm btn-outline-secondary">
           Lihat
        </a>
    </td>

    <td><?= date('d M Y', strtotime($row['tanggal_upload'])) ?></td>

    <td>
        <span class="badge bg-<?= $row['status']=='Selesai'?'success':'warning' ?>">
            <?= $row['status'] ?>
        </span>
    </td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="7" class="text-center text-muted">
        Belum ada dokumen akhir.
    </td>
</tr>
<?php endif; ?>

</tbody>
</table>

</div>
</div>

</div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
