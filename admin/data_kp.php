<?php
require_once "../auth/auth_check.php";
require_role('admin');
require_once "../config/db.php";

$current_page  = 'data_kp.php';
$page_title    = "Data Kerja Praktik";
$asset_prefix  = "../";
$logout_prefix = "../";

include "../includes/layout_top.php";
include "../includes/sidebar_admin.php";
?>

<main class="pc-container">
<?php include "../includes/header.php"; ?>

<div class="pc-content">

<h3 class="fw-bold mb-4">Data Kerja Praktik Mahasiswa</h3>

<div class="card shadow-sm">
<div class="card-body">

<div class="table-responsive">
<table class="table table-bordered table-hover align-middle">
<thead class="table-light text-center">
<tr>
  <th>NIM</th>
  <th>Nama</th>
  <th>Instansi</th>
  <th>Periode</th>
  <th>Status</th>
  <th>Dosen Pembimbing</th>
  <th width="110">Aksi</th>
</tr>
</thead>
<tbody>

<?php
$q = $conn->query("
  SELECT 
    k.id_kp,
    k.nim,
    m.nama,
    k.nama_instansi,
    k.tgl_mulai,
    k.tgl_selesai,
    k.status,
    d.nama AS dosen
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
<?php else:
while ($r = $q->fetch_assoc()):
?>
<tr>
<td><?= htmlspecialchars($r['nim']) ?></td>
<td><?= htmlspecialchars($r['nama']) ?></td>
<td><?= htmlspecialchars($r['nama_instansi']) ?></td>
<td class="text-center">
<?= date('d M Y', strtotime($r['tgl_mulai'])) ?>
 –
<?= date('d M Y', strtotime($r['tgl_selesai'])) ?>
</td>
<td class="text-center">
<span class="badge bg-secondary"><?= htmlspecialchars($r['status']) ?></span>
</td>
<td>
<?= $r['dosen'] 
  ? htmlspecialchars($r['dosen']) 
  : '<span class="text-danger fw-semibold">Belum ditetapkan</span>' ?>
</td>
<td class="text-center">
<a href="assign_bimbingan.php?id_kp=<?= (int)$r['id_kp'] ?>"
   class="btn btn-sm btn-primary">
Assign
</a>
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
