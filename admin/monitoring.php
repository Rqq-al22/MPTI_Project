<?php
require_once "../auth/auth_check.php";
require_role('admin');
require_once "../config/db.php";

$current_page  = 'monitoring.php';
$page_title    = "Monitoring Aktivitas Sistem";
$asset_prefix  = "../";
$logout_prefix = "../";

include "../includes/layout_top.php";
include "../includes/sidebar_admin.php";
?>

<main class="pc-container">
<?php include "../includes/header.php"; ?>

<div class="pc-content">

<h3 class="fw-bold mb-3">Monitoring Aktivitas Sistem</h3>
<p class="text-muted">
Memantau seluruh aktivitas pengguna (Admin, Dosen, Mahasiswa)
</p>

<div class="card shadow-sm">
<div class="card-body">

<div class="table-responsive">
<table class="table table-hover align-middle">
<thead class="table-light text-center">
<tr>
  <th>#</th>
  <th>Waktu</th>
  <th>User</th>
  <th>Role</th>
  <th>Aktivitas</th>
</tr>
</thead>
<tbody>

<?php
$q = $conn->query("
  SELECT 
    m.waktu,
    m.aktivitas,
    u.username,
    u.id_role
  FROM monitoring m
  LEFT JOIN users u ON m.id_user = u.id_user
  ORDER BY m.waktu DESC
");

if ($q->num_rows === 0):
?>
<tr>
<td colspan="5" class="text-center text-muted">
Belum ada aktivitas sistem.
</td>
</tr>
<?php
else:
$no = 1;
while ($r = $q->fetch_assoc()):
  switch ($r['id_role']) {
    case 1:
      $role = 'Admin';
      $badge = 'bg-danger';
      $icon = '👑';
      break;
    case 2:
      $role = 'Dosen';
      $badge = 'bg-primary';
      $icon = '🎓';
      break;
    case 3:
      $role = 'Mahasiswa';
      $badge = 'bg-success';
      $icon = '🧑‍🎓';
      break;
    default:
      $role = '-';
      $badge = 'bg-secondary';
      $icon = '❓';
  }
?>
<tr>
<td class="text-center"><?= $no++ ?></td>
<td><?= date('d M Y H:i:s', strtotime($r['waktu'])) ?></td>
<td><?= htmlspecialchars($r['username'] ?? '-') ?></td>
<td class="text-center">
<span class="badge <?= $badge ?>">
<?= $icon ?> <?= $role ?>
</span>
</td>
<td><?= htmlspecialchars($r['aktivitas']) ?></td>
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
