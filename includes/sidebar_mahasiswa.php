<?php
$current_page = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'] ?? '';
?>

<nav class="pc-sidebar">
  <div class="navbar-wrapper">
    <div class="m-header">
      <a href="dashboard_mahasiswa.php" class="b-brand text-primary">
        <img src="<?= $asset_prefix ?>assets/images/logo-white.svg"
             class="img-fluid logo-lg" alt="logo" />
      </a>
    </div>

    <div class="navbar-content">
      <ul class="pc-navbar">

        <!-- DASHBOARD (SEMUA ROLE) -->
        <li class="pc-item <?= $current_page=='dashboard.php'?'active':'' ?>">
          <a href="dashboard.php" class="pc-link">
            <span class="pc-micon"><i class="ph ph-house-line"></i></span>
            <span class="pc-mtext">Dashboard</span>
          </a>
        </li>

        <!-- ========================================================= -->
        <!-- ===================== MAHASISWA ========================= -->
        <!-- ========================================================= -->
        <?php if ($role === 'mahasiswa'): ?>

        <li class="pc-item pc-caption"><label>KERJA PRAKTIK</label></li>

        <li class="pc-item <?= $current_page=='data_kp.php'?'active':'' ?>">
          <a href="data_kp.php" class="pc-link">
            <i class="ph ph-briefcase pc-micon"></i>
            <span class="pc-mtext">Data KP</span>
          </a>
        </li>

        <li class="pc-item <?= $current_page=='presensi.php'?'active':'' ?>">
          <a href="presensi.php" class="pc-link">
            <i class="ph ph-fingerprint pc-micon"></i>
            <span class="pc-mtext">Presensi</span>
          </a>
        </li>

        <li class="pc-item <?= $current_page=='laporan_mingguan.php'?'active':'' ?>">
          <a href="laporan_mingguan.php" class="pc-link">
            <i class="ph ph-file-text pc-micon"></i>
            <span class="pc-mtext">Laporan Mingguan</span>
          </a>
        </li>

        <li class="pc-item <?= $current_page=='nilai.php'?'active':'' ?>">
          <a href="nilai.php" class="pc-link">
            <i class="ph ph-star pc-micon"></i>
            <span class="pc-mtext">Nilai & Evaluasi</span>
          </a>
        </li>

        <li class="pc-item <?= $current_page=='dokumen_akhir.php'?'active':'' ?>">
          <a href="dokumen_akhir.php" class="pc-link">
            <i class="ph ph-upload pc-micon"></i>
            <span class="pc-mtext">Dokumen Akhir</span>
          </a>
        </li>

        <li class="pc-item pc-caption"><label>INFORMASI</label></li>

        <li class="pc-item <?= $current_page=='pengumuman.php'?'active':'' ?>">
          <a href="pengumuman.php" class="pc-link">
            <i class="ph ph-megaphone pc-micon"></i>
            <span class="pc-mtext">Pengumuman</span>
          </a>
        </li>

        <li class="pc-item pc-caption"><label>AKUN</label></li>

        <li class="pc-item <?= $current_page=='profil.php'?'active':'' ?>">
          <a href="profil.php" class="pc-link">
            <i class="ph ph-user pc-micon"></i>
            <span class="pc-mtext">Profil</span>
          </a>
        </li>

        <?php endif; ?>

        <!-- ========================================================= -->
<!-- ======================== DOSEN ========================== -->
<!-- ========================================================= -->
<?php if ($role === 'dosen'): ?>

<li class="pc-item pc-caption"><label>DASHBOARD</label></li>

<li class="pc-item <?= $current_page=='dashboard.php'?'active':'' ?>">
  <a href="dashboard.php" class="pc-link">
    <span class="pc-micon"><i class="ph ph-house-line"></i></span>
    <span class="pc-mtext">Dashboard Dosen</span>
  </a>
</li>

<li class="pc-item pc-caption"><label>BIMBINGAN KP</label></li>

<li class="pc-item <?= $current_page=='bimbingan.php'?'active':'' ?>">
  <a href="bimbingan.php" class="pc-link">
    <span class="pc-micon"><i class="ph ph-users"></i></span>
    <span class="pc-mtext">Mahasiswa Bimbingan</span>
  </a>
</li>

<li class="pc-item <?= $current_page=='monitoring.php'?'active':'' ?>">
  <a href="monitoring.php" class="pc-link">
    <span class="pc-micon"><i class="ph ph-chart-line"></i></span>
    <span class="pc-mtext">Monitoring KP</span>
  </a>
</li>

<li class="pc-item pc-caption"><label>AKTIVITAS MAHASISWA</label></li>

<li class="pc-item <?= $current_page=='presensi_mhs.php'?'active':'' ?>">
  <a href="presensi_mhs.php" class="pc-link">
    <span class="pc-micon"><i class="ph ph-fingerprint"></i></span>
    <span class="pc-mtext">Presensi Mahasiswa</span>
  </a>
</li>

<li class="pc-item <?= $current_page=='laporan_mhs.php'?'active':'' ?>">
  <a href="laporan_mhs.php" class="pc-link">
    <span class="pc-micon"><i class="ph ph-file-text"></i></span>
    <span class="pc-mtext">Laporan Mingguan</span>
  </a>
</li>

<li class="pc-item pc-caption"><label>PENILAIAN</label></li>

<li class="pc-item <?= $current_page=='penilaian_mingguan.php'?'active':'' ?>">
  <a href="penilaian_mingguan.php" class="pc-link">
    <span class="pc-micon"><i class="ph ph-star"></i></span>
    <span class="pc-mtext">Penilaian Mingguan</span>
  </a>
</li>

<li class="pc-item <?= $current_page=='penilaian_akhir.php'?'active':'' ?>">
  <a href="penilaian_akhir.php" class="pc-link">
    <span class="pc-micon"><i class="ph ph-medal"></i></span>
    <span class="pc-mtext">Penilaian Akhir</span>
  </a>
</li>

<li class="pc-item pc-caption"><label>DOKUMEN</label></li>

<li class="pc-item <?= $current_page=='dokumen_akhir_mhs.php'?'active':'' ?>">
  <a href="dokumen_akhir_mhs.php" class="pc-link">
    <span class="pc-micon"><i class="ph ph-upload"></i></span>
    <span class="pc-mtext">Dokumen Akhir</span>
  </a>
</li>

<li class="pc-item pc-caption"><label>INFORMASI</label></li>

<li class="pc-item <?= $current_page=='pengumuman.php'?'active':'' ?>">
  <a href="pengumuman.php" class="pc-link">
    <span class="pc-micon"><i class="ph ph-megaphone"></i></span>
    <span class="pc-mtext">Pengumuman</span>
  </a>
</li>

<li class="pc-item pc-caption"><label>AKUN</label></li>

<li class="pc-item">
  <a href="../profile/profile.php" class="pc-link">
    <span class="pc-micon">
      <i class="bi bi-person-circle"></i>
    </span>
    <span class="pc-mtext">Profil</span>
  </a>
</li>


<?php endif; ?>



        <!-- ========================================================= -->
        <!-- ========================= ADMIN ========================= -->
        <!-- ========================================================= -->
        <?php if ($role === 'admin'): ?>

        <li class="pc-item pc-caption"><label>MASTER DATA</label></li>

        <li class="pc-item <?= $current_page=='mahasiswa.php'?'active':'' ?>">
          <a href="mahasiswa.php" class="pc-link">
            <i class="ph ph-student pc-micon"></i>
            <span class="pc-mtext">Mahasiswa</span>
          </a>
        </li>

        <li class="pc-item <?= $current_page=='dosen.php'?'active':'' ?>">
          <a href="dosen.php" class="pc-link">
            <i class="ph ph-chalkboard-teacher pc-micon"></i>
            <span class="pc-mtext">Dosen</span>
          </a>
        </li>

        <li class="pc-item <?= $current_page=='instansi.php'?'active':'' ?>">
          <a href="instansi.php" class="pc-link">
            <i class="ph ph-building pc-micon"></i>
            <span class="pc-mtext">Instansi</span>
          </a>
        </li>

        <li class="pc-item pc-caption"><label>SISTEM</label></li>

        <li class="pc-item <?= $current_page=='pengumuman.php'?'active':'' ?>">
          <a href="pengumuman.php" class="pc-link">
            <i class="ph ph-megaphone pc-micon"></i>
            <span class="pc-mtext">Pengumuman</span>
          </a>
        </li>

        <?php endif; ?>

        <!-- LOGOUT (SEMUA ROLE) -->
        <li class="pc-item">
          <a href="<?= $logout_prefix ?>logout.php" class="pc-link text-danger">
            <i class="ph ph-sign-out pc-micon"></i>
            <span class="pc-mtext">Logout</span>
          </a>
        </li>

      </ul>
    </div>
  </div>
</nav>
