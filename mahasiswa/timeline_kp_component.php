<?php
// file ini dipanggil dari dashboard_mahasiswa.php
if (!isset($kp) || !$kp) return;

$status = $kp['status'];

$steps = [
  'Pengajuan'   => ['label' => 'Pengajuan',   'desc' => 'Menunggu verifikasi/penerimaan.'],
  'Diterima'    => ['label' => 'Diterima',    'desc' => 'KP diterima, siap mulai pelaksanaan.'],
  'Berlangsung' => ['label' => 'Berlangsung', 'desc' => 'Sedang melaksanakan KP dan mengisi laporan/presensi.'],
  'Selesai'     => ['label' => 'Selesai',     'desc' => 'KP selesai. Upload dokumen akhir dan tunggu nilai akhir.'],
];

$order = array_keys($steps);

// tentukan posisi status sekarang
$currentIndex = array_search($status, $order, true);
if ($currentIndex === false) $currentIndex = 0;

function stepBadge($i, $currentIndex){
  if ($i < $currentIndex) return 'success';
  if ($i === $currentIndex) return 'primary';
  return 'secondary';
}
?>

<div class="card shadow-sm border-0 mt-3">
  <div class="card-body">
    <h5 class="mb-1">Timeline KP</h5>
    <p class="text-muted mb-3">Alur status Kerja Praktik Anda.</p>

    <div class="row g-3">
      <?php foreach ($order as $i => $key): ?>
        <div class="col-md-3">
          <div class="border rounded p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
              <strong><?= htmlspecialchars($steps[$key]['label']) ?></strong>
              <span class="badge bg-<?= stepBadge($i, $currentIndex) ?>">
                <?= ($i < $currentIndex) ? 'Selesai' : (($i === $currentIndex) ? 'Sekarang' : 'Menunggu') ?>
              </span>
            </div>
            <div class="text-muted mt-2" style="font-size: 0.9rem;">
              <?= htmlspecialchars($steps[$key]['desc']) ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

  </div>
</div>
