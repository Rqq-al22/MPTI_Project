-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 20 Des 2025 pada 07.38
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
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_admin` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `id_user`, `nama_admin`, `email`, `created_at`) VALUES
(1, 7, 'Administrator Kampus', 'admin@kampus.ac.id', '2025-12-19 19:52:05');

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

--
-- Trigger `dokumen_akhir`
--
DELIMITER $$
CREATE TRIGGER `trg_dokumen_akhir_ai` AFTER INSERT ON `dokumen_akhir` FOR EACH ROW BEGIN
    UPDATE monitoring_kp
    SET dokumen_akhir = 'Sudah'
    WHERE id_kp = NEW.id_kp;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `dosen`
--

CREATE TABLE `dosen` (
  `nidn` varchar(20) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `jurusan` varchar(100) DEFAULT NULL,
  `keahlian` varchar(100) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `dosen`
--

INSERT INTO `dosen` (`nidn`, `nama`, `jurusan`, `keahlian`, `id_user`) VALUES
('0981234567', 'Hasman S.kom', 'Teknik Informatika', 'Web Devolper', 8),
('1987654321', 'Andi Pratama, S.T., M.Kom', 'Teknik Informatika', 'Teknik Informatika', 5);

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
  `kontak_instansi` varchar(100) DEFAULT NULL,
  `pembimbing_instansi` varchar(150) DEFAULT NULL,
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
  `nama_instansi` varchar(150) DEFAULT NULL,
  `alamat_instansi` varchar(255) DEFAULT NULL,
  `kontak_instansi` varchar(120) DEFAULT NULL,
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

--
-- Dumping data untuk tabel `kp`
--

INSERT INTO `kp` (`id_kp`, `nim`, `nama_instansi`, `alamat_instansi`, `kontak_instansi`, `id_instansi`, `nidn`, `posisi`, `pembimbing_instansi`, `tgl_mulai`, `tgl_selesai`, `status`, `surat_diterima_file`, `catatan_admin`, `created_at`) VALUES
(1, '202201001', 'PT Telkom Indonesia Witel Sulawesi Tenggara', 'Jl. Jenderal Ahmad Yani No. 15, Kelurahan Wawowanggu, Kendari\r\n', NULL, NULL, NULL, 'Web Developer', 'Bapak Andi Saputra, S.Kom', '2025-12-19', '2026-02-28', 'Pengajuan', 'surat_202201001_1766137967.jpg', NULL, '2025-12-19 09:52:47'),
(2, '202201001', 'PT Telkom Indonesia Witel Sulawesi Tenggara', 'jln.Ahmad Yani', '082215545654', NULL, '1987654321', 'Web Developer', 'Bapak Andi Saputra, S.Kom', '2025-12-19', '2026-02-19', 'Berlangsung', 'surat_202201001_1766141354.jpg', NULL, '2025-12-19 10:49:14'),
(4, '202201001', NULL, NULL, NULL, NULL, '1987654321', NULL, NULL, NULL, NULL, 'Ditolak', NULL, NULL, '2025-12-20 00:57:01'),
(5, 'E1E124015', 'PT Telkom Indonesia Witel Sulawesi Tenggara', 'kkalal', '192919010', NULL, NULL, 'Backend', 'jjsjs', NULL, NULL, 'Pengajuan', NULL, NULL, '2025-12-20 02:28:38');

--
-- Trigger `kp`
--
DELIMITER $$
CREATE TRIGGER `trg_insert_monitoring_kp` AFTER INSERT ON `kp` FOR EACH ROW BEGIN
    INSERT INTO monitoring_kp
    (
        id_kp,
        total_laporan,
        total_presensi,
        dokumen_akhir,
        status_kp
    )
    VALUES
    (
        NEW.id_kp,
        0,
        0,
        'Belum',
        NEW.status
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_kp_au` AFTER UPDATE ON `kp` FOR EACH ROW BEGIN
    IF OLD.status <> NEW.status THEN
        UPDATE monitoring_kp
        SET status_kp = NEW.status
        WHERE id_kp = NEW.id_kp;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_kp_prevent_multi_active_insert` BEFORE INSERT ON `kp` FOR EACH ROW BEGIN
  IF NEW.status IN ('Pengajuan','Diterima','Berlangsung') THEN
    IF (SELECT COUNT(*) FROM kp 
        WHERE nim = NEW.nim
          AND status IN ('Pengajuan','Diterima','Berlangsung')) > 0 THEN
      SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Mahasiswa masih memiliki KP aktif. Tidak boleh membuat KP aktif baru.';
    END IF;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_kp_prevent_multi_active_update` BEFORE UPDATE ON `kp` FOR EACH ROW BEGIN
  IF NEW.status IN ('Pengajuan','Diterima','Berlangsung') THEN
    IF (SELECT COUNT(*) FROM kp
        WHERE nim = NEW.nim
          AND status IN ('Pengajuan','Diterima','Berlangsung')
          AND id_kp <> OLD.id_kp) > 0 THEN
      SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Update ditolak: sudah ada KP aktif lain untuk NIM ini.';
    END IF;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_update_status_kp` AFTER UPDATE ON `kp` FOR EACH ROW BEGIN
    IF NEW.status <> OLD.status THEN
        UPDATE monitoring_kp
        SET status_kp = NEW.status
        WHERE id_kp = NEW.id_kp;
    END IF;
END
$$
DELIMITER ;

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
  `status` enum('Menunggu','Disetujui','Ditolak') NOT NULL DEFAULT 'Menunggu',
  `tanggal_upload` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `laporan_mingguan`
--

INSERT INTO `laporan_mingguan` (`id_laporan_mingguan`, `id_kp`, `minggu_ke`, `judul`, `ringkasan`, `file_laporan`, `status`, `tanggal_upload`) VALUES
(1, 2, 1, 'haloo', 'cm,zc zmc', 'laporan_202201001_minggu1_1766188064.docx', 'Menunggu', '2025-12-20');

--
-- Trigger `laporan_mingguan`
--
DELIMITER $$
CREATE TRIGGER `trg_laporan_mingguan_ai` AFTER INSERT ON `laporan_mingguan` FOR EACH ROW BEGIN
    UPDATE monitoring_kp
    SET total_laporan = (
        SELECT COUNT(*)
        FROM laporan_mingguan
        WHERE id_kp = NEW.id_kp
    )
    WHERE id_kp = NEW.id_kp;
END
$$
DELIMITER ;

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

--
-- Dumping data untuk tabel `mahasiswa`
--

INSERT INTO `mahasiswa` (`nim`, `nama`, `jurusan`, `angkatan`, `id_user`) VALUES
('202201001', 'La Ode Muhamad Dirga', 'Teknik Informatika', 2024, 4),
('E1E124015', 'Rezky Alya', 'Teknik Informatika', 2024, 9),
('E1E124043', 'Muhammmad Nur Alam Syahrir', 'Teknik Informatika', 2024, 10),
('E1E124057', 'Annisa Nurul Faizah', 'Teknik Informatika', 2024, 11),
('E1E124069', 'Muhammad Fildan Pratama', 'Teknik Informatika', 2024, 12);

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
(15, 'Login', '2025-12-14 03:08:19', 3),
(16, 'Login', '2025-12-19 05:25:00', 3),
(17, 'Login', '2025-12-19 05:27:40', 3),
(18, 'Login', '2025-12-19 05:43:03', 3),
(19, 'Login', '2025-12-19 05:46:40', 3),
(20, 'Login', '2025-12-19 05:51:02', 3),
(21, 'Logout dari sistem', '2025-12-19 16:51:50', 7),
(22, 'Menetapkan dosen pembimbing untuk mahasiswa NIM ', '2025-12-19 16:59:41', 7),
(23, 'Logout dari sistem', '2025-12-19 17:09:05', 7),
(24, 'Logout dari sistem', '2025-12-19 17:21:06', 7),
(25, 'Mahasiswa login ke sistem', '2025-12-19 17:26:09', 4),
(26, 'Dosen login ke sistem', '2025-12-19 17:34:30', 5),
(27, 'Mahasiswa login ke sistem', '2025-12-19 17:37:46', 4),
(28, 'Mahasiswa login ke sistem', '2025-12-19 17:45:38', 4),
(29, 'Mahasiswa login ke sistem', '2025-12-19 17:48:05', 4),
(30, 'Mahasiswa login ke sistem', '2025-12-19 17:49:35', 4),
(31, 'Logout dari sistem', '2025-12-19 17:57:15', 4),
(32, 'Dosen login ke sistem', '2025-12-19 17:58:37', 5),
(33, 'Logout dari sistem', '2025-12-19 17:59:01', 5),
(34, 'Mahasiswa login ke sistem', '2025-12-19 17:59:41', 4),
(35, 'Mahasiswa login ke sistem', '2025-12-19 18:02:02', 4),
(36, 'Logout dari sistem', '2025-12-19 18:13:11', 4),
(37, 'Admin login ke sistem', '2025-12-19 18:15:25', 7),
(38, 'Logout dari sistem', '2025-12-19 18:15:35', 7),
(39, 'Dosen login ke sistem', '2025-12-19 18:15:42', 5),
(40, 'Admin login ke sistem', '2025-12-19 18:16:23', 7),
(41, 'Menetapkan dosen pembimbing untuk mahasiswa NIM ', '2025-12-19 18:16:34', 7),
(42, 'Menetapkan dosen pembimbing untuk mahasiswa NIM ', '2025-12-19 18:16:48', 7),
(43, 'Logout dari sistem', '2025-12-19 18:16:57', 7),
(44, 'Dosen login ke sistem', '2025-12-19 18:17:09', 5),
(45, 'Logout dari sistem', '2025-12-19 18:22:25', 5),
(46, 'Mahasiswa login ke sistem', '2025-12-19 18:24:10', 4),
(47, 'Dosen login ke sistem', '2025-12-19 18:24:22', 5),
(48, 'Mahasiswa login ke sistem', '2025-12-19 18:29:50', 4),
(49, 'Logout dari sistem', '2025-12-19 18:29:57', 4),
(50, 'Dosen login ke sistem', '2025-12-19 18:30:04', 5),
(51, 'Logout dari sistem', '2025-12-19 18:30:33', 5),
(52, 'Admin login ke sistem', '2025-12-19 18:30:44', 7),
(53, 'Logout dari sistem', '2025-12-19 18:31:04', 7),
(54, 'Mahasiswa login ke sistem', '2025-12-19 23:03:55', 4),
(55, 'Dosen login ke sistem', '2025-12-19 23:07:01', 5),
(56, 'Mahasiswa login ke sistem', '2025-12-19 23:46:52', 4),
(57, 'Dosen login ke sistem', '2025-12-19 23:48:10', 5),
(58, 'Mahasiswa login ke sistem', '2025-12-19 23:52:25', 4),
(59, 'Dosen login ke sistem', '2025-12-19 23:52:59', 5),
(60, 'Logout dari sistem', '2025-12-20 00:58:33', 5),
(61, 'Mahasiswa login ke sistem', '2025-12-20 00:58:38', 4),
(62, 'Logout dari sistem', '2025-12-20 02:06:07', 4),
(63, 'Dosen login ke sistem', '2025-12-20 02:06:14', 5),
(64, 'Logout dari sistem', '2025-12-20 02:15:18', 5),
(65, 'Mahasiswa login ke sistem', '2025-12-20 02:15:29', 4),
(66, 'Logout dari sistem', '2025-12-20 02:15:39', 4),
(67, 'Admin login ke sistem', '2025-12-20 02:15:53', 7),
(68, 'Menetapkan dosen pembimbing untuk mahasiswa NIM ', '2025-12-20 02:19:23', 7),
(69, 'Logout dari sistem', '2025-12-20 02:20:02', 7),
(70, 'Admin login ke sistem', '2025-12-20 02:20:29', 7),
(71, 'Logout dari sistem', '2025-12-20 02:23:15', 7),
(72, 'Mahasiswa login ke sistem', '2025-12-20 02:23:57', 9),
(73, 'Logout dari sistem', '2025-12-20 02:27:21', 9),
(74, 'Mahasiswa login ke sistem', '2025-12-20 02:27:27', 4),
(75, 'Logout dari sistem', '2025-12-20 02:27:38', 4),
(76, 'Mahasiswa login ke sistem', '2025-12-20 02:27:47', 9),
(77, 'Logout dari sistem', '2025-12-20 02:35:59', 9),
(78, 'Mahasiswa login ke sistem', '2025-12-20 02:36:05', 4),
(79, 'Logout dari sistem', '2025-12-20 02:41:31', 4),
(80, 'Mahasiswa login ke sistem', '2025-12-20 02:41:37', 9),
(81, 'Logout dari sistem', '2025-12-20 02:42:01', 9),
(82, 'Admin login ke sistem', '2025-12-20 02:42:13', 7),
(83, 'Logout dari sistem', '2025-12-20 02:46:04', 7),
(84, 'Admin login ke sistem', '2025-12-20 02:46:08', 7),
(85, 'Logout dari sistem', '2025-12-20 02:46:12', 7),
(86, 'Mahasiswa login ke sistem', '2025-12-20 02:46:17', 9),
(87, 'Admin login ke sistem', '2025-12-20 02:47:22', 7),
(88, 'Logout dari sistem', '2025-12-20 03:15:50', 7),
(89, 'Admin login ke sistem', '2025-12-20 04:51:04', 7),
(90, 'Logout dari sistem', '2025-12-20 04:51:16', 7),
(91, 'Admin login ke sistem', '2025-12-20 06:23:48', 7),
(92, 'Logout dari sistem', '2025-12-20 06:23:59', 7),
(93, 'Admin login ke sistem', '2025-12-20 06:25:24', 7);

-- --------------------------------------------------------

--
-- Struktur dari tabel `monitoring_kp`
--

CREATE TABLE `monitoring_kp` (
  `id_monitoring` int(11) NOT NULL,
  `id_kp` int(11) NOT NULL,
  `total_laporan` int(11) DEFAULT 0,
  `total_presensi` int(11) DEFAULT 0,
  `dokumen_akhir` enum('Belum','Sudah') DEFAULT 'Belum',
  `status_kp` enum('Pengajuan','Diterima','Berlangsung','Selesai') NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `monitoring_kp`
--

INSERT INTO `monitoring_kp` (`id_monitoring`, `id_kp`, `total_laporan`, `total_presensi`, `dokumen_akhir`, `status_kp`, `last_update`) VALUES
(1, 1, 0, 0, 'Belum', 'Pengajuan', '2025-12-20 00:50:41'),
(2, 2, 1, 0, 'Belum', 'Berlangsung', '2025-12-20 00:51:11'),
(4, 4, 0, 0, 'Belum', '', '2025-12-20 02:02:31'),
(5, 5, 0, 0, 'Belum', 'Pengajuan', '2025-12-20 02:28:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengumuman`
--

CREATE TABLE `pengumuman` (
  `id_pengumuman` int(11) NOT NULL,
  `judul` varchar(150) NOT NULL,
  `isi` text NOT NULL,
  `dibuat_oleh` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengumuman`
--

INSERT INTO `pengumuman` (`id_pengumuman`, `judul`, `isi`, `dibuat_oleh`, `created_at`) VALUES
(1, 'Haloo', 'kobvjjvv', 5, '2025-12-20 02:12:07');

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
  `status` enum('Hadir','Izin','Alpha') DEFAULT NULL,
  `bukti_foto` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `validasi` enum('Pending','Approve','Reject') DEFAULT 'Pending',
  `catatan_dosen` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `presensi`
--

INSERT INTO `presensi` (`id_presensi`, `nim`, `tanggal`, `status`, `bukti_foto`, `latitude`, `longitude`, `validasi`, `catatan_dosen`) VALUES
(2, NULL, '2025-12-19', 'Hadir', 'presensi__1766133365.jpg', -5.13802240, 119.43936000, 'Pending', NULL),
(3, '202201001', '2025-12-19', 'Hadir', 'presensi_202201001_1766134851.jpg', -5.13802240, 119.43936000, 'Pending', NULL);

--
-- Trigger `presensi`
--
DELIMITER $$
CREATE TRIGGER `trg_presensi_au` AFTER UPDATE ON `presensi` FOR EACH ROW BEGIN
    IF OLD.validasi <> NEW.validasi THEN
        UPDATE monitoring_kp
        SET total_presensi = (
            SELECT COUNT(*)
            FROM presensi p
            JOIN kp k ON p.nim = k.nim
            WHERE k.id_kp = monitoring_kp.id_kp
              AND p.validasi = 'Approve'
        );
    END IF;
END
$$
DELIMITER ;

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `id_role`, `created_at`, `foto`) VALUES
(1, 'admin', 'admin123', 1, '2025-12-13 11:31:10', NULL),
(2, 'dosen', 'dosen123', 2, '2025-12-13 11:31:10', NULL),
(3, 'mhs', 'mhs123', 3, '2025-12-13 11:31:10', NULL),
(4, 'mhs_dirga', '12345', 3, '2025-12-19 08:52:46', NULL),
(5, 'dosen_andi', 'dosen123', 2, '2025-12-19 11:43:54', NULL),
(7, 'admin_kp', 'admin123', 1, '2025-12-19 11:50:48', 'profile_69460db5d9a6a3.48702613.jpeg'),
(8, 'hasman_it', 'hasman123', 2, '2025-12-19 15:48:55', NULL),
(9, 'alya_15', 'alya2415', 3, '2025-12-20 02:23:00', NULL),
(10, 'syahrir_43', 'syahrir2443', 3, '2025-12-20 02:50:41', NULL),
(11, 'annisa_57', 'annisa2457', 3, '2025-12-20 02:52:33', NULL),
(12, 'fildan_69', 'fildan2469', 3, '2025-12-20 02:55:15', NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD KEY `fk_admin_user` (`id_user`);

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
-- Indeks untuk tabel `monitoring_kp`
--
ALTER TABLE `monitoring_kp`
  ADD PRIMARY KEY (`id_monitoring`),
  ADD KEY `fk_monitoring_kp` (`id_kp`);

--
-- Indeks untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD PRIMARY KEY (`id_pengumuman`);

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
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `kp`
--
ALTER TABLE `kp`
  MODIFY `id_kp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id_laporan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `laporan_mingguan`
--
ALTER TABLE `laporan_mingguan`
  MODIFY `id_laporan_mingguan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `monitoring`
--
ALTER TABLE `monitoring`
  MODIFY `id_monitoring` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT untuk tabel `monitoring_kp`
--
ALTER TABLE `monitoring_kp`
  MODIFY `id_monitoring` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `id_pengumuman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id_presensi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `fk_admin_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

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
-- Ketidakleluasaan untuk tabel `monitoring_kp`
--
ALTER TABLE `monitoring_kp`
  ADD CONSTRAINT `fk_monitoring_kp` FOREIGN KEY (`id_kp`) REFERENCES `kp` (`id_kp`) ON DELETE CASCADE;

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
