<?php
require_once "../auth/auth_check.php";
require_role('mahasiswa');
require_once "../config/db.php";

/* ===============================
   KONFIGURASI HALAMAN
   =============================== */
$current_page  = 'pengumuman.php';
$page_title    = "Pengumuman";
$asset_prefix  = "../";
$logout_prefix = "../";

/* ===============================
   AMBIL DATA PENGUMUMAN
   - Ditampilkan ke mahasiswa
   - Dibuat oleh ADMIN atau DOSEN
   =============================== */
$stmt = $conn->prepare("
    SELECT 
        p.id_pengumuman,
        p.judul,
        p.isi,
        p.created_at,
        u.username,
        r.nama_role
    FROM pengumuman p
    LEFT JOIN users u ON p.dibuat_oleh = u.id_user
    LEFT JOIN roles r ON u.id_role = r.id_role
    WHERE r.nama_role IN ('admin','dosen')
    ORDER BY p.created_at DESC
");
$stmt->execute();
$pengumuman = $stmt->get_result();

/* ===============================
   LAYOUT
   =============================== */
include "../includes/layout_top.php";
include "../includes/sidebar_mahasiswa.php";
include "../includes/header.php";
?>

<main class="pc-container">
<div class="pc-content">

<h3 class="fw-bold mb-1">Pengumuman</h3>
<p class="text-muted mb-4">
  Informasi dan pengumuman terkait Kerja Praktik dari dosen dan admin.
</p>

<?php if ($pengumuman->num_rows === 0): ?>
  <div class="alert alert-info">
    Belum ada pengumuman.
  </div>
<?php else: ?>

<div class="row">
<?php while ($row = $pengumuman->fetch_assoc()): ?>
  <div class="col-md-12 mb-3">
    <div class="card shadow-sm border-0">
      <div class="card-body">

        <h5 class="fw-bold mb-1">
          <?= htmlspecialchars($row['judul']) ?>
        </h5>

        <div class="text-muted small mb-2">
          Diposting oleh 
          <strong><?= ucfirst($row['nama_role'] ?? 'Unknown') ?></strong>
          · <?= date('d M Y H:i', strtotime($row['created_at'])) ?>
        </div>

        <p class="mb-0">
          <?= nl2br(htmlspecialchars($row['isi'])) ?>
        </p>

      </div>
    </div>
  </div>
<?php endwhile; ?>
</div>

<?php endif; ?>

</div>
</main>

<?php include "../includes/layout_bottom.php"; ?>
