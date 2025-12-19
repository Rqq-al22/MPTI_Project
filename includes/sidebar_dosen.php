<?php
// Proteksi sidebar
if (!isset($_SESSION['id_role']) || $_SESSION['id_role'] != 2) {
    exit('Akses ditolak');
}
?>

<nav class="pc-sidebar">
  <div class="navbar-wrapper">
    <div class="navbar-content">

      <ul class="pc-navbar">

        <!-- DASHBOARD -->
        <li class="pc-item <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
          <a href="dashboard.php" class="pc-link">
            <span class="pc-micon"><i class="ti ti-home"></i></span>
            <span class="pc-mtext">Dashboard</span>
          </a>
        </li>

        <!-- KERJA PRAKTIK -->
        <li class="pc-item pc-caption">
          <label>Kerja Praktik</label>
        </li>

        <li class="pc-item <?= ($current_page == 'bimbingan.php') ? 'active' : '' ?>">
          <a href="bimbingan.php" class="pc-link">
            <span class="pc-micon"><i class="ti ti-users"></i></span>
            <span class="pc-mtext">Mahasiswa Bimbingan</span>
          </a>
        </li>

        <li class="pc-item <?= ($current_page == 'laporan_mhs.php') ? 'active' : '' ?>">
          <a href="laporan_mhs.php" class="pc-link">
            <span class="pc-micon"><i class="ti ti-file-text"></i></span>
            <span class="pc-mtext">Laporan Mingguan</span>
          </a>
        </li>

        <li class="pc-item <?= ($current_page == 'presensi_mhs.php') ? 'active' : '' ?>">
          <a href="presensi_mhs.php" class="pc-link">
            <span class="pc-micon"><i class="ti ti-calendar-check"></i></span>
            <span class="pc-mtext">Presensi Mahasiswa</span>
          </a>
        </li>

        <li class="pc-item <?= ($current_page == 'validasi_presensi.php') ? 'active' : '' ?>">
          <a href="validasi_presensi.php" class="pc-link">
            <span class="pc-micon"><i class="ti ti-checklist"></i></span>
            <span class="pc-mtext">Validasi Presensi</span>
          </a>
        </li>

        <li class="pc-item pc-caption">
          <label>Penilaian</label>
        </li>

        <li class="pc-item <?= ($current_page == 'penilaian_mingguan.php') ? 'active' : '' ?>">
          <a href="penilaian_mingguan.php" class="pc-link">
            <span class="pc-micon"><i class="ti ti-star"></i></span>
            <span class="pc-mtext">Penilaian Mingguan</span>
          </a>
        </li>

        <li class="pc-item <?= ($current_page == 'penilaian_akhir.php') ? 'active' : '' ?>">
          <a href="penilaian_akhir.php" class="pc-link">
            <span class="pc-micon"><i class="ti ti-award"></i></span>
            <span class="pc-mtext">Penilaian Akhir</span>
          </a>
        </li>

        <li class="pc-item <?= ($current_page == 'penilaian.php') ? 'active' : '' ?>">
          <a href="penilaian.php" class="pc-link">
            <span class="pc-micon"><i class="ti ti-clipboard-list"></i></span>
            <span class="pc-mtext">Rekap Penilaian</span>
          </a>
        </li>

        <li class="pc-item pc-caption">
          <label>Dokumen & Monitoring</label>
        </li>

        <li class="pc-item <?= ($current_page == 'dokumen_akhir_mhs.php') ? 'active' : '' ?>">
          <a href="dokumen_akhir_mhs.php" class="pc-link">
            <span class="pc-micon"><i class="ti ti-upload"></i></span>
            <span class="pc-mtext">Dokumen Akhir</span>
          </a>
        </li>

        <li class="pc-item <?= ($current_page == 'monitoring.php') ? 'active' : '' ?>">
          <a href="monitoring.php" class="pc-link">
            <span class="pc-micon"><i class="ti ti-activity"></i></span>
            <span class="pc-mtext">Monitoring KP</span>
          </a>
        </li>

        <!-- INFORMASI -->
        <li class="pc-item pc-caption">
          <label>Informasi</label>
        </li>

        <li class="pc-item <?= ($current_page == 'pengumuman.php') ? 'active' : '' ?>">
          <a href="pengumuman.php" class="pc-link">
            <span class="pc-micon"><i class="ti ti-bell"></i></span>
            <span class="pc-mtext">Pengumuman</span>
          </a>
        </li>

        <!-- AKUN -->
        <li class="pc-item pc-caption">
          <label>Akun</label>
        </li>

        <li class="pc-item">
  <a href="../profile/profile.php" class="pc-link">
    <span class="pc-micon">
      <i class="bi bi-person-circle"></i>
    </span>
    <span class="pc-mtext">Profil</span>
  </a>
</li>


        <li class="pc-item">
          <a href="../auth/logout.php" class="pc-link text-danger">
            <span class="pc-micon"><i class="ti ti-logout"></i></span>
            <span class="pc-mtext">Logout</span>
          </a>
        </li>

      </ul>

    </div>
  </div>
</nav>
