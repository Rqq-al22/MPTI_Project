<?php
require_once "../auth/auth_check.php";
require_role('dosen');
require_once "../config/db.php";

/* ===============================
   KONFIGURASI
================================ */
$current_page = 'rekap_penilaian.php';
$page_title   = 'Rekap Penilaian';
$asset_prefix = "../";

/* ===============================
   AMBIL NIDN DOSEN
================================ */
$user_id = $_SESSION['user_id'];

$q = $conn->prepare("SELECT nidn FROM dosen WHERE id_user = ? LIMIT 1");
$q->bind_param("i", $user_id);
$q->execute();
$dosen = $q->get_result()->fetch_assoc();

if (!$dosen) {
    die("Data dosen tidak ditemukan");
}
$nidn = $dosen['nidn'];

/* ===============================
   QUERY REKAP NILAI
================================ */
$stmt = $conn->prepare("
    SELECT
        m.nim,
        m.nama,
        k.id_kp,

        -- rata-rata nilai mingguan
        ROUND(AVG(pm.nilai), 2) AS rata_mingguan,

        -- nilai akhir
        pa.nilai_presensi,
        pa.nilai_laporan,
        pa.nilai_presentasi,
        pa.nilai_akhir

    FROM kp k
    JOIN mahasiswa m ON k.nim = m.nim

    LEFT JOIN laporan_mingguan lm 
        ON lm.id_kp = k.id_kp

    LEFT JOIN penilaian_mingguan pm 
        ON pm.id_laporan_mingguan = lm.id_laporan_mingguan

    LEFT JOIN penilaian_akhir pa 
        ON pa.id_kp = k.id_kp

    WHERE k.nidn = ?

    GROUP BY 
        k.id_kp,
        m.nim,
        m.nama,
        pa.nilai_presensi,
        pa.nilai_laporan,
        pa.nilai_presentasi,
        pa.nilai_akhir

    ORDER BY m.nama ASC
");
$stmt->bind_param("s", $nidn);
$stmt->execute();
$data = $stmt->get_result();

/* ===============================
   LAYOUT
================================ */
include "../includes/layout_top.php";
include "../includes/sidebar_dosen.php";
include "../includes/header.php";
?>

<main class="pc-container">
<div class="pc-content">

<h3 class="fw-bold mb-3">Rekap Penilaian Mahasiswa</h3>

<div class="card shadow-sm">
<div class="card-body table-responsive">

<table class="table table-bordered align-middle">
<thead class="table-light">
<tr>
    <th>NIM</th>
    <th>Nama</th>
    <th>Rata Nilai Mingguan</th>
    <th>Presensi</th>
    <th>Laporan</th>
    <th>Presentasi</th>
    <th>Nilai Akhir</th>
</tr>
</thead>
<tbody>

<?php if ($data->num_rows > 0): ?>
<?php while ($row = $data->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($row['nim']) ?></td>
    <td><?= htmlspecialchars($row['nama']) ?></td>

    <td class="text-center">
        <?= $row['rata_mingguan'] !== null ? $row['rata_mingguan'] : '-' ?>
    </td>

    <td class="text-center"><?= $row['nilai_presensi'] ?? '-' ?></td>
    <td class="text-center"><?= $row['nilai_laporan'] ?? '-' ?></td>
    <td class="text-center"><?= $row['nilai_presentasi'] ?? '-' ?></td>

    <td class="fw-bold text-center text-primary">
        <?= $row['nilai_akhir'] ?? '-' ?>
    </td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="7" class="text-center text-muted">
        Belum ada data penilaian
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
