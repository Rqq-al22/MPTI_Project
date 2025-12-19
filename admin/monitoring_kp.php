<?php
require_once "../auth/auth_check.php";
require_role('admin');
require_once "../config/db.php";

/* ===============================
   KONFIGURASI HALAMAN
   =============================== */
$current_page  = 'monitoring_kp.php';
$page_title    = "Monitoring KP";
$asset_prefix  = "../";
$logout_prefix = "../";

include "../includes/layout_top.php";
include "../includes/sidebar_admin.php";
?>

<main class="pc-container">
<?php include "../includes/header.php"; ?>

<div class="pc-content">

<h3 class="fw-bold mb-4">Monitoring Kerja Praktik</h3>

<div class="card shadow-sm">
<div class="card-body">

<div class="table-responsive">
<table class="table table-bordered table-hover align-middle">
<thead class="table-light text-center">
<tr>
  <th width="5%">#</th>
  <th>NIM</th>
  <th>Nama</th>
  <th>Instansi</th>
  <th>Periode</th>
  <th>Status</th>
  <th>Dosen Pembimbing</th>
</tr>
</thead>
<tbody>

<?php
$q = $conn->query("
  SELECT 
    k.id_kp,
    k.nim,
    m.nama AS nama_mhs,
    k.nama_instansi,
    k.tgl_mulai,
    k.tgl_selesai,
    k.status,
    d.nama AS nama_dosen
  FROM kp k
  JOIN mahasiswa m ON k.nim = m.nim
  LEFT JOIN dosen d ON k.nidn = d.nidn
  ORDER BY k.created_at DESC
");

if ($q->num_rows === 0):
?>
<tr>
<td colspan="7" class="text-center text-muted">
Belum ada data Kerja Praktik.
</td>
</tr>
<?php
else:
$no = 1;
while ($r = $q->fetch_assoc()):
  $status = $r['status'] ?? '-';

  // badge status
  $badge = "bg-secondary";
  if (strtolower($status) === 'disetujui') $badge = "bg-success";
  if (strtolower($status) === 'pengajuan') $badge = "bg-warning";
  if (strtolower($status) === 'ditolak')   $badge = "bg-danger";
?>
<tr>
<td class="text-center"><?= $no++ ?></td>
<td><?= htmlspecialchars($r['nim']) ?></td>
<td><?= htmlspecialchars($r['nama_mhs']) ?></td>
<td><?= htmlspecialchars($r['nama_instansi']) ?></td>
<td class="text-center">
  <?= date('d M Y', strtotime($r['tgl_mulai'])) ?> –
  <?= date('d M Y', strtotime($r['tgl_selesai'])) ?>
</td>
<td class="text-center">
  <span class="badge <?= $badge ?>"><?= htmlspecialchars($status) ?></span>
</td>
<td>
  <?= $r['nama_dosen'] ? htmlspecialchars($r['nama_dosen']) : '<span class="text-danger fw-semibold">Belum ditetapkan</span>' ?>
</td>
</tr>
<?php endwhile; endif; ?>

</tbody>
</table>
</div>

</div>
</div>

</div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
