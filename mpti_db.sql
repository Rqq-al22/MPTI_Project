-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 19 Des 2025 pada 03.10
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mpti_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bimbingan`
--

CREATE TABLE `bimbingan` (
  `id_bimbingan` int(11) NOT NULL,
  `nim` varchar(15) DEFAULT NULL,
  `nidn` varchar(20) DEFAULT NULL,
  `topik` varchar(150) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `dokumen_akhir`
--

CREATE TABLE `dokumen_akhir` (
  `id_dokumen_akhir` int(11) NOT NULL,
  `id_kp` int(11) NOT NULL,
  `file_laporan_akhir` varchar(255) DEFAULT NULL,
  `file_ppt` varchar(255) DEFAULT NULL,
  `tanggal_upload` date DEFAULT NULL,
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `dosen`
--

CREATE TABLE `dosen` (
  `nidn` varchar(20) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `keahlian` varchar(100) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `instansi`
--

CREATE TABLE `instansi` (
  `id_instansi` int(11) NOT NULL,
  `nama_instansi` varchar(150) NOT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `kota` varchar(100) DEFAULT NULL,
  `provinsi` varchar(100) DEFAULT NULL,
  `kontak` varchar(100) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal_pengumuman`
--

CREATE TABLE `jadwal_pengumuman` (
  `id_jadwal` int(11) NOT NULL,
  `judul` varchar(150) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `tanggal` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kp`
--

CREATE TABLE `kp` (
  `id_kp` int(11) NOT NULL,
  `nim` varchar(15) NOT NULL,
  `id_instansi` int(11) DEFAULT NULL,
  `nidn` varchar(20) DEFAULT NULL,
  `posisi` varchar(120) DEFAULT NULL,
  `pembimbing_instansi` varchar(120) DEFAULT NULL,
  `tgl_mulai` date DEFAULT NULL,
  `tgl_selesai` date DEFAULT NULL,
  `status` enum('Pengajuan','Diterima','Ditolak','Berlangsung','Selesai') NOT NULL DEFAULT 'Pengajuan',
  `surat_diterima_file` varchar(255) DEFAULT NULL,
  `catatan_admin` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `laporan`
--

CREATE TABLE `laporan` (
  `id_laporan` int(11) NOT NULL,
  `nim` varchar(15) DEFAULT NULL,
  `judul` varchar(150) DEFAULT NULL,
  `file_laporan` varchar(255) DEFAULT NULL,
  `tanggal_upload` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `laporan_mingguan`
--

CREATE TABLE `laporan_mingguan` (
  `id_laporan_mingguan` int(11) NOT NULL,
  `id_kp` int(11) NOT NULL,
  `minggu_ke` int(11) NOT NULL,
  `judul` varchar(150) DEFAULT NULL,
  `ringkasan` text DEFAULT NULL,
  `file_laporan` varchar(255) DEFAULT NULL,
  `tanggal_upload` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `nim` varchar(15) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `jurusan` varchar(50) DEFAULT NULL,
  `angkatan` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `monitoring`
--

CREATE TABLE `monitoring` (
  `id_monitoring` int(11) NOT NULL,
  `aktivitas` varchar(150) DEFAULT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `monitoring`
--

INSERT INTO `monitoring` (`id_monitoring`, `aktivitas`, `waktu`, `id_user`) VALUES
(1, 'Login', '2025-12-13 11:51:33', 3),
(2, 'Login', '2025-12-13 11:52:50', 3),
(3, 'Login', '2025-12-13 11:52:57', 3),
(4, 'Login', '2025-12-13 12:06:09', 3),
(5, 'Login', '2025-12-13 12:19:10', 2),
(6, 'Login', '2025-12-13 12:27:48', 3),
(7, 'Login', '2025-12-13 12:30:21', 3),
(8, 'Login', '2025-12-13 12:30:37', 3),
(9, 'Login', '2025-12-13 12:49:35', 3),
(10, 'Login', '2025-12-13 12:50:10', 3),
(11, 'Login', '2025-12-13 12:52:43', 3),
(12, 'Login', '2025-12-13 13:15:35', 1),
(13, 'Login', '2025-12-13 13:23:34', 1),
(14, 'Login', '2025-12-13 13:24:27', 3),
(15, 'Login', '2025-12-14 03:08:19', 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `penilaian`
--

CREATE TABLE `penilaian` (
  `id_penilaian` int(11) NOT NULL,
  `id_laporan` int(11) DEFAULT NULL,
  `nidn` varchar(20) DEFAULT NULL,
  `nilai` int(11) DEFAULT NULL,
  `komentar` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `penilaian_akhir`
--

CREATE TABLE `penilaian_akhir` (
  `id_penilaian_akhir` int(11) NOT NULL,
  `id_kp` int(11) NOT NULL,
  `nidn` varchar(20) NOT NULL,
  `nilai_presensi` int(11) DEFAULT NULL,
  `nilai_laporan` int(11) DEFAULT NULL,
  `nilai_presentasi` int(11) DEFAULT NULL,
  `nilai_akhir` int(11) DEFAULT NULL,
  `komentar` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `penilaian_mingguan`
--

CREATE TABLE `penilaian_mingguan` (
  `id_penilaian_mingguan` int(11) NOT NULL,
  `id_laporan_mingguan` int(11) NOT NULL,
  `nidn` varchar(20) NOT NULL,
  `nilai` int(11) DEFAULT NULL,
  `komentar` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `presensi`
--

CREATE TABLE `presensi` (
  `id_presensi` int(11) NOT NULL,
  `nim` varchar(15) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `status` enum('Hadir','Izin','Alpha') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `presentasi`
--

CREATE TABLE `presentasi` (
  `id_presentasi` int(11) NOT NULL,
  `id_kp` int(11) NOT NULL,
  `jadwal` datetime DEFAULT NULL,
  `lokasi` varchar(150) DEFAULT NULL,
  `status` enum('Dijadwalkan','Selesai','Ditunda') NOT NULL DEFAULT 'Dijadwalkan',
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id_role` int(11) NOT NULL,
  `nama_role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id_role`, `nama_role`) VALUES
(1, 'admin'),
(2, 'dosen'),
(3, 'mahasiswa');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_role` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `id_role`, `created_at`) VALUES
(1, 'admin', 'admin123', 1, '2025-12-13 11:31:10'),
(2, 'dosen', 'dosen123', 2, '2025-12-13 11:31:10'),
(3, 'mhs', 'mhs123', 3, '2025-12-13 11:31:10');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bimbingan`
--
ALTER TABLE `bimbingan`
  ADD PRIMARY KEY (`id_bimbingan`),
  ADD KEY `nim` (`nim`),
  ADD KEY `nidn` (`nidn`);

--
-- Indeks untuk tabel `dokumen_akhir`
--
ALTER TABLE `dokumen_akhir`
  ADD PRIMARY KEY (`id_dokumen_akhir`),
  ADD UNIQUE KEY `uq_docakhir` (`id_kp`);

--
-- Indeks untuk tabel `dosen`
--
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`nidn`),
  ADD UNIQUE KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `instansi`
--
ALTER TABLE `instansi`
  ADD PRIMARY KEY (`id_instansi`);

--
-- Indeks untuk tabel `jadwal_pengumuman`
--
ALTER TABLE `jadwal_pengumuman`
  ADD PRIMARY KEY (`id_jadwal`);

--
-- Indeks untuk tabel `kp`
--
ALTER TABLE `kp`
  ADD PRIMARY KEY (`id_kp`),
  ADD KEY `idx_kp_nim` (`nim`),
  ADD KEY `idx_kp_nidn` (`nidn`),
  ADD KEY `idx_kp_instansi` (`id_instansi`);

--
-- Indeks untuk tabel `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id_laporan`),
  ADD KEY `nim` (`nim`);

--
-- Indeks untuk tabel `laporan_mingguan`
--
ALTER TABLE `laporan_mingguan`
  ADD PRIMARY KEY (`id_laporan_mingguan`),
  ADD UNIQUE KEY `uq_lap_minggu` (`id_kp`,`minggu_ke`),
  ADD KEY `idx_lapkp` (`id_kp`);

--
-- Indeks untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`nim`),
  ADD UNIQUE KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `monitoring`
--
ALTER TABLE `monitoring`
  ADD PRIMARY KEY (`id_monitoring`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `penilaian`
--
ALTER TABLE `penilaian`
  ADD PRIMARY KEY (`id_penilaian`),
  ADD KEY `id_laporan` (`id_laporan`),
  ADD KEY `nidn` (`nidn`);

--
-- Indeks untuk tabel `penilaian_akhir`
--
ALTER TABLE `penilaian_akhir`
  ADD PRIMARY KEY (`id_penilaian_akhir`),
  ADD UNIQUE KEY `uq_penakhir` (`id_kp`,`nidn`),
  ADD KEY `fk_penakhir_dsn` (`nidn`);

--
-- Indeks untuk tabel `penilaian_mingguan`
--
ALTER TABLE `penilaian_mingguan`
  ADD PRIMARY KEY (`id_penilaian_mingguan`),
  ADD UNIQUE KEY `uq_pen_minggu` (`id_laporan_mingguan`,`nidn`),
  ADD KEY `idx_pen_nidn` (`nidn`);

--
-- Indeks untuk tabel `presensi`
--
ALTER TABLE `presensi`
  ADD PRIMARY KEY (`id_presensi`),
  ADD KEY `nim` (`nim`);

--
-- Indeks untuk tabel `presentasi`
--
ALTER TABLE `presentasi`
  ADD PRIMARY KEY (`id_presentasi`),
  ADD UNIQUE KEY `uq_presentasi` (`id_kp`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_role`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `id_role` (`id_role`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bimbingan`
--
ALTER TABLE `bimbingan`
  MODIFY `id_bimbingan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `dokumen_akhir`
--
ALTER TABLE `dokumen_akhir`
  MODIFY `id_dokumen_akhir` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `instansi`
--
ALTER TABLE `instansi`
  MODIFY `id_instansi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jadwal_pengumuman`
--
ALTER TABLE `jadwal_pengumuman`
  MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kp`
--
ALTER TABLE `kp`
  MODIFY `id_kp` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id_laporan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `laporan_mingguan`
--
ALTER TABLE `laporan_mingguan`
  MODIFY `id_laporan_mingguan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `monitoring`
--
ALTER TABLE `monitoring`
  MODIFY `id_monitoring` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `penilaian`
--
ALTER TABLE `penilaian`
  MODIFY `id_penilaian` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `penilaian_akhir`
--
ALTER TABLE `penilaian_akhir`
  MODIFY `id_penilaian_akhir` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `penilaian_mingguan`
--
ALTER TABLE `penilaian_mingguan`
  MODIFY `id_penilaian_mingguan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `presensi`
--
ALTER TABLE `presensi`
  MODIFY `id_presensi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `presentasi`
--
ALTER TABLE `presentasi`
  MODIFY `id_presentasi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `bimbingan`
--
ALTER TABLE `bimbingan`
  ADD CONSTRAINT `bimbingan_ibfk_1` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`),
  ADD CONSTRAINT `bimbingan_ibfk_2` FOREIGN KEY (`nidn`) REFERENCES `dosen` (`nidn`);

--
-- Ketidakleluasaan untuk tabel `dokumen_akhir`
--
ALTER TABLE `dokumen_akhir`
  ADD CONSTRAINT `fk_docakhir_kp` FOREIGN KEY (`id_kp`) REFERENCES `kp` (`id_kp`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `dosen`
--
ALTER TABLE `dosen`
  ADD CONSTRAINT `dosen_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Ketidakleluasaan untuk tabel `kp`
--
ALTER TABLE `kp`
  ADD CONSTRAINT `fk_kp_dsn` FOREIGN KEY (`nidn`) REFERENCES `dosen` (`nidn`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_kp_instansi` FOREIGN KEY (`id_instansi`) REFERENCES `instansi` (`id_instansi`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_kp_mhs` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `laporan`
--
ALTER TABLE `laporan`
  ADD CONSTRAINT `laporan_ibfk_1` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`);

--
-- Ketidakleluasaan untuk tabel `laporan_mingguan`
--
ALTER TABLE `laporan_mingguan`
  ADD CONSTRAINT `fk_lapkp` FOREIGN KEY (`id_kp`) REFERENCES `kp` (`id_kp`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD CONSTRAINT `mahasiswa_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Ketidakleluasaan untuk tabel `monitoring`
--
ALTER TABLE `monitoring`
  ADD CONSTRAINT `monitoring_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Ketidakleluasaan untuk tabel `penilaian`
--
ALTER TABLE `penilaian`
  ADD CONSTRAINT `penilaian_ibfk_1` FOREIGN KEY (`id_laporan`) REFERENCES `laporan` (`id_laporan`),
  ADD CONSTRAINT `penilaian_ibfk_2` FOREIGN KEY (`nidn`) REFERENCES `dosen` (`nidn`);

--
-- Ketidakleluasaan untuk tabel `penilaian_akhir`
--
ALTER TABLE `penilaian_akhir`
  ADD CONSTRAINT `fk_penakhir_dsn` FOREIGN KEY (`nidn`) REFERENCES `dosen` (`nidn`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_penakhir_kp` FOREIGN KEY (`id_kp`) REFERENCES `kp` (`id_kp`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `penilaian_mingguan`
--
ALTER TABLE `penilaian_mingguan`
  ADD CONSTRAINT `fk_pen_dsn` FOREIGN KEY (`nidn`) REFERENCES `dosen` (`nidn`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pen_minggu` FOREIGN KEY (`id_laporan_mingguan`) REFERENCES `laporan_mingguan` (`id_laporan_mingguan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `presensi`
--
ALTER TABLE `presensi`
  ADD CONSTRAINT `presensi_ibfk_1` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`);

--
-- Ketidakleluasaan untuk tabel `presentasi`
--
ALTER TABLE `presentasi`
  ADD CONSTRAINT `fk_presentasi_kp` FOREIGN KEY (`id_kp`) REFERENCES `kp` (`id_kp`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
