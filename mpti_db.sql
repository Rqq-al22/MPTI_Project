-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 10 Jan 2026 pada 08.08
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
(3, 219, 'Admin Utama', NULL, '2026-01-10 10:01:05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `angkatan`
--

CREATE TABLE `angkatan` (
  `tahun` int(11) NOT NULL,
  `label` varchar(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `angkatan`
--

INSERT INTO `angkatan` (`tahun`, `label`, `created_at`) VALUES
(2024, 'Angkatan 2024', '2026-01-09 14:17:42'),
(2025, 'Angkatan 2025', '2026-01-09 14:17:42');

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
  `nip` varchar(30) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `jurusan` varchar(100) DEFAULT NULL,
  `peminatan` varchar(100) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `dosen`
--

INSERT INTO `dosen` (`nidn`, `nip`, `nama`, `jurusan`, `peminatan`, `id_user`) VALUES
('0006049104', '199104062019031021', 'Rizal Adi Saputra, ST., M.Kom.', 'Teknik Informatika', 'KCV', 228),
('0007078904', '0007078904', 'Ilham Julian Efendi, ST., MT.', 'Teknik Informatika', 'RPL', 229),
('0007118106', '198111072008122003', 'Statiswaty, ST., MMSi.', 'Teknik Informatika', 'RPL', 230),
('0008078610', '198607082019031013', 'La Surimi, S.Si., M.Cs.', 'Teknik Informatika', 'KBJ', 231),
('0009096503', '196502081988021001', 'Dr. Muh. Ihsan Sarita, M.Kom.', 'Teknik Informatika', 'KCV', 232),
('0014068304', '198306142010011017', 'Muhammad Yamin, ST., M.Eng', 'Teknik Informatika', 'KBJ', 233),
('0016018308', '198301162010122002', 'Ika Purwanti Ningrum, S.Kom., M.Cs', 'Teknik Informatika', 'KCV', 234),
('0017088402', '199408172022031014', 'Asa Hari Wibowo, ST., M.Eng.', 'Teknik Informatika', 'RPL', 235),
('0017117606', '197611172006122001', 'Isnawaty, S.Si., MT.', 'Teknik Informatika', 'KBJ', 236),
('0017127802', '197812172006012002', 'Hasmina Tari Mokui, S.ST., M.E., P.hD.', 'Teknik Informatika', 'RPL', 237),
('0020057902', '0020057902', 'Subardin, ST., MT.', 'Teknik Informatika', 'KBJ', 238),
('0022017304', '197301222001121002', 'Mustarum Musaruddin, S.T., M.IT., Ph.D.', 'Teknik Informatika', 'KBJ', 239),
('0022027607', '197602220101210001', 'Sutardi, S.Kom., MT.', 'Teknik Informatika', 'RPL', 240),
('0023068101', '198106332018031001', 'Adha Mashur Sajiah, ST., M.Eng.', 'Teknik Informatika', 'KCV', 241),
('0023078406', '198407222015041003', 'LM. Fid Aksara, S.Kom., M.Kom.', 'Teknik Informatika', 'KBJ', 242),
('0025047107', '197104252006011010', 'Bambang Pramono, S.Si., MT.', 'Teknik Informatika', 'RPL', 243),
('0028107501', '197802200501002', 'Dr. Laode Muhammad Golok Jaya, ST., MT.', 'Teknik Informatika', 'RPL', 244),
('0029128402', '198412282015041002', 'Natalis Ransi, S.Si., M.Cs.', 'Teknik Informatika', 'RPL', 245),
('0030048107', '0030048107', 'Laode Muhammad Tajidun, ST., MT.', 'Teknik Informatika', 'RPL', 246),
('0906028701', '198702062015041003', 'Jumadil Nangi, S.Kom., MT.', 'Teknik Informatika', 'RPL', 247),
('0929058902', '198809262019031011', 'LM Bahtiar Aksara, ST., MT.', 'Teknik Informatika', 'KCV', 248);

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
(10, 'E1E124007', 'CV Antam Kolaka', 'Kolaka', '082296644593', NULL, '0014068304', 'Web Developer', 'Agus S.T,.M.T', '2026-01-10', '2026-03-10', 'Berlangsung', 'surat_1768016988_817.jpg', NULL, '2026-01-10 03:49:48');

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
('E1E124001', 'A. MUFIDAH IDRIS', 'Teknik Informatika', 2024, 92),
('E1E124002', 'ABDUL RAHIM HUSEIN', 'Teknik Informatika', 2024, 93),
('E1E124003', 'AQNY DANU UTAMI', 'Teknik Informatika', 2024, 94),
('E1E124004', 'AWALIYAH FADHILATUN NISA', 'Teknik Informatika', 2024, 95),
('E1E124005', 'ELANG FIRMANSYAH', 'Teknik Informatika', 2024, 96),
('E1E124006', 'FARID KHADHRA RAMADHAN', 'Teknik Informatika', 2024, 97),
('E1E124007', 'LA ODE MUHAMAD DIRGA', 'Teknik Informatika', 2024, 98),
('E1E124008', 'LA ODE MUHAMMAD SULTHAN KOLOGOU', 'Teknik Informatika', 2024, 99),
('E1E124010', 'MUHAMMAD ROZZAAQ NUR RAMADHAN', 'Teknik Informatika', 2024, 100),
('E1E124012', 'NINDY ASMAWATY', 'Teknik Informatika', 2024, 101),
('E1E124013', 'PUTU EKA FEBRIANI', 'Teknik Informatika', 2024, 102),
('E1E124014', 'RESKY YANI', 'Teknik Informatika', 2024, 103),
('E1E124015', 'REZKI ALYA PASRUN', 'Teknik Informatika', 2024, 104),
('E1E124016', 'SHEERA ANNISA', 'Teknik Informatika', 2024, 105),
('E1E124017', 'SYAFIATUL ADAWIAH', 'Teknik Informatika', 2024, 106),
('E1E124018', 'SYAKILA MEILANA RUSLIN', 'Teknik Informatika', 2024, 107),
('E1E124019', 'THALIA DWI PUSPITA AYU', 'Teknik Informatika', 2024, 108),
('E1E124020', 'WA LIANDANI', 'Teknik Informatika', 2024, 109),
('E1E124021', 'WA ODE INDAH NURRAMADHANI', 'Teknik Informatika', 2024, 110),
('E1E124022', 'WA RAHMIYANTI', 'Teknik Informatika', 2024, 111),
('E1E124023', 'A. TENRY LIU MEY ASPAT COLLE', 'Teknik Informatika', 2024, 112),
('E1E124024', 'AGUS CASHFLOWER AKBAR', 'Teknik Informatika', 2024, 113),
('E1E124025', 'AGUS HARTONO', 'Teknik Informatika', 2024, 114),
('E1E124026', 'AIRUL ROFIQ RAMADHAN', 'Teknik Informatika', 2024, 115),
('E1E124027', 'ANANDA ADEN PUTRA', 'Teknik Informatika', 2024, 116),
('E1E124028', 'ANDER GIBRAN SIREGAR', 'Teknik Informatika', 2024, 117),
('E1E124029', 'ANISSA SALSABILA', 'Teknik Informatika', 2024, 118),
('E1E124030', 'ASRAF FALAJ BUNTALA', 'Teknik Informatika', 2024, 119),
('E1E124031', 'DIVA PRATIWI SARMUDIN', 'Teknik Informatika', 2024, 120),
('E1E124032', 'ELDA INDAH SANDA LANGI', 'Teknik Informatika', 2024, 121),
('E1E124033', 'FADHILAH FAJAR RAHMA HEDA', 'Teknik Informatika', 2024, 122),
('E1E124034', 'FAIRUZ NAILAL RAJWA PUTRI AMRAN', 'Teknik Informatika', 2024, 123),
('E1E124035', 'FATIH MUHAMMAD BINTANG POSSUMAH', 'Teknik Informatika', 2024, 124),
('E1E124036', 'FRISILIA FEBIOLA', 'Teknik Informatika', 2024, 125),
('E1E124037', 'GILANG SYAH FITRAH RAMADHAN', 'Teknik Informatika', 2024, 126),
('E1E124038', 'GITA PRANGESTI', 'Teknik Informatika', 2024, 127),
('E1E124039', 'LA ODE GUNTUR KAIMUDIN', 'Teknik Informatika', 2024, 128),
('E1E124040', 'LA ODE MUHAMAD INDRA RUKMANA', 'Teknik Informatika', 2024, 129),
('E1E124041', 'MIQDAD ASYRAF RIZQULLAH', 'Teknik Informatika', 2024, 130),
('E1E124042', 'MUH. FAHRIL', 'Teknik Informatika', 2024, 131),
('E1E124043', 'MUH. NUR ALAM SYAHRIR', 'Teknik Informatika', 2024, 132),
('E1E124044', 'MUH. YUSUF', 'Teknik Informatika', 2024, 133),
('E1E124045', 'MUHAMMAD RIZKY YAMIN', 'Teknik Informatika', 2024, 134),
('E1E124046', 'NEYSA RAZANA MAHNEERAH', 'Teknik Informatika', 2024, 135),
('E1E124047', 'NIKMAL ANAKORUO', 'Teknik Informatika', 2024, 136),
('E1E124048', 'RAHMI', 'Teknik Informatika', 2024, 137),
('E1E124049', 'SAFARIL ADAM', 'Teknik Informatika', 2024, 138),
('E1E124050', 'SASKYA MEYTRA ODE', 'Teknik Informatika', 2024, 139),
('E1E124051', 'SRI MAHARANI', 'Teknik Informatika', 2024, 140),
('E1E124052', 'SUCI WULANDARI', 'Teknik Informatika', 2024, 141),
('E1E124053', 'SYAFIQ DAWWAS', 'Teknik Informatika', 2024, 142),
('E1E124054', 'SYAWAL AHMAD RABIUL', 'Teknik Informatika', 2024, 143),
('E1E124055', 'ABDILLAH JUMAWAL KODA', 'Teknik Informatika', 2024, 144),
('E1E124056', 'ALISCHA PUTRI WULAN AZ-ZAHWA', 'Teknik Informatika', 2024, 145),
('E1E124057', 'ANNISA NURUL FAIZAH', 'Teknik Informatika', 2024, 146),
('E1E124058', 'ARRIYA RAHALDI AL KANDARI', 'Teknik Informatika', 2024, 147),
('E1E124059', 'CINTA WARDANA', 'Teknik Informatika', 2024, 148),
('E1E124060', 'FAJRINA AULIA AMLAN', 'Teknik Informatika', 2024, 149),
('E1E124061', 'KAHAR MUNAJAT', 'Teknik Informatika', 2024, 150),
('E1E124062', 'LA ODE AFDAL MUNIFA', 'Teknik Informatika', 2024, 151),
('E1E124063', 'LA ODE MUAMMAR RAIHAN SALADIN', 'Teknik Informatika', 2024, 152),
('E1E124064', 'LA ODE MUHAMAD AHLUL BAIT', 'Teknik Informatika', 2024, 153),
('E1E124065', 'LA ODE MUHAMMAD FAUZILAZHIM', 'Teknik Informatika', 2024, 154),
('E1E124066', 'LA ODE NAUVAL AQIILAH TSAQIF', 'Teknik Informatika', 2024, 155),
('E1E124067', 'MAYA AGUSTIN', 'Teknik Informatika', 2024, 156),
('E1E124068', 'MIKHAEL ABRAHAM WIDIANTO', 'Teknik Informatika', 2024, 157),
('E1E124069', 'MUH FILDAN PRATAMA', 'Teknik Informatika', 2024, 158),
('E1E124071', 'MUH.ALBYANSYAH QAISHAR POROSI', 'Teknik Informatika', 2024, 159),
('E1E124072', 'MUH.RABILDZAN', 'Teknik Informatika', 2024, 160),
('E1E124073', 'MUHAMAD ASWAAD SATRIA PRATAMA', 'Teknik Informatika', 2024, 161),
('E1E124074', 'MUHAMMAD DZAKY FIRDAUS', 'Teknik Informatika', 2024, 162),
('E1E124076', 'PUTRI FADHILAH ZUHAIRAH', 'Teknik Informatika', 2024, 163),
('E1E124077', 'RIZKMAH LAILATUL RAMADHANI', 'Teknik Informatika', 2024, 164),
('E1E124078', 'TOBING ADYA YAKOP', 'Teknik Informatika', 2024, 165),
('E1E124079', 'VYOLA CECILIA POTTO', 'Teknik Informatika', 2024, 166),
('E1E124080', 'WA ODE YURISMAWATI', 'Teknik Informatika', 2024, 167),
('E1E124081', 'WAHAB RAHMAN SAPUTRA', 'Teknik Informatika', 2024, 168),
('E1E124082', 'ZULFAN NURCAHAYDI', 'Teknik Informatika', 2024, 169),
('E1E125001', 'ABD. RAZAK', 'Teknik Informatika', 2025, 259),
('E1E125002', 'ABDURRAHMAN AL-ARAFAH', 'Teknik Informatika', 2025, 260),
('E1E125003', 'AFRIANI', 'Teknik Informatika', 2025, 261),
('E1E125004', 'ANDI MAIVA ISMAR', 'Teknik Informatika', 2025, 262),
('E1E125005', 'ANNISA FITRIA ZAHRA AMIR', 'Teknik Informatika', 2025, 263),
('E1E125006', 'DENYAWAN', 'Teknik Informatika', 2025, 264),
('E1E125007', 'DINO FAHRI', 'Teknik Informatika', 2025, 265),
('E1E125008', 'FAINAL', 'Teknik Informatika', 2025, 266),
('E1E125009', 'FITRA NURUL FADYA', 'Teknik Informatika', 2025, 267),
('E1E125010', 'GEDE SUDIATMIKA', 'Teknik Informatika', 2025, 268),
('E1E125011', 'HUSAIN MUBARAK', 'Teknik Informatika', 2025, 294),
('E1E125012', 'I MADE MERTA SANTIKA', 'Teknik Informatika', 2025, 295),
('E1E125013', 'IDAYANA. S', 'Teknik Informatika', 2025, 296),
('E1E125014', 'LAODE SYAHRUL MAGHFIRO', 'Teknik Informatika', 2025, 297),
('E1E125015', 'LEON CHRISTIAN', 'Teknik Informatika', 2025, 298),
('E1E125016', 'LUTFI RAHMAN AL - FAYED', 'Teknik Informatika', 2025, 299),
('E1E125017', 'MAHARANI PRITA ANANSARI', 'Teknik Informatika', 2025, 300),
('E1E125018', 'MOCH. RAYZHAN ABSAR ALWANIE', 'Teknik Informatika', 2025, 301),
('E1E125019', 'MUH. ARSYAF NAWIR', 'Teknik Informatika', 2025, 329),
('E1E125020', 'MUHAMMAD ASSJUL SUBHI', 'Teknik Informatika', 2025, 330),
('E1E125021', 'MUHAMMAD FISHABILLAH', 'Teknik Informatika', 2025, 331),
('E1E125022', 'NABILA NAWA\'AD RAMADHANI', 'Teknik Informatika', 2025, 332),
('E1E125023', 'NAILAH NUR SALSABILA SALIM', 'Teknik Informatika', 2025, 302),
('E1E125024', 'NAYLA RIZQY DERMAWAN', 'Teknik Informatika', 2025, 303),
('E1E125025', 'NUR RAHMA REZKI', 'Teknik Informatika', 2025, 304),
('E1E125026', 'SALSABILA', 'Teknik Informatika', 2025, 333),
('E1E125027', 'SRI MULANDARI', 'Teknik Informatika', 2025, 334),
('E1E125028', 'VICKYA HERIANA SAPUTRI', 'Teknik Informatika', 2025, 335),
('E1E125029', 'YULIAH RIFKA', 'Teknik Informatika', 2025, 336),
('E1E125030', 'YUSRIL MUHAMMAD', 'Teknik Informatika', 2025, 337),
('E1E125031', 'ADITYA RIZKY RAMADHAN', 'Teknik Informatika', 2025, 269),
('E1E125032', 'AHMAT LANDFRAN', 'Teknik Informatika', 2025, 270),
('E1E125033', 'ANDI MUH. ARFAN SAID', 'Teknik Informatika', 2025, 271),
('E1E125034', 'ANNISA ALIMMUNIRAH', 'Teknik Informatika', 2025, 272),
('E1E125035', 'AYU RATIH SUPUTRI', 'Teknik Informatika', 2025, 273),
('E1E125036', 'AZKA ARIA PUTRA', 'Teknik Informatika', 2025, 274),
('E1E125037', 'BADRAN NAWWAAR RUMUDALE', 'Teknik Informatika', 2025, 275),
('E1E125038', 'BERLAND TONGAPA', 'Teknik Informatika', 2025, 276),
('E1E125039', 'DWI AZIZAH NUR\'ABIDAH', 'Teknik Informatika', 2025, 277),
('E1E125040', 'ENDRICO GLENO DELO', 'Teknik Informatika', 2025, 278),
('E1E125041', 'FILSA SALSABILAH', 'Teknik Informatika', 2025, 279),
('E1E125042', 'HIKMAT HIDAYAT', 'Teknik Informatika', 2025, 280),
('E1E125043', 'HILBRAN SAFAR DEYASKA', 'Teknik Informatika', 2025, 338),
('E1E125044', 'ISKANDAR ZACFARON', 'Teknik Informatika', 2025, 305),
('E1E125045', 'ISMA AYU', 'Teknik Informatika', 2025, 306),
('E1E125046', 'KHASANUL FAJRI', 'Teknik Informatika', 2025, 307),
('E1E125047', 'LA ODE ABDUL KADIR RAMBEGA', 'Teknik Informatika', 2025, 308),
('E1E125048', 'LA ODE AGUS SYAIFULLAH', 'Teknik Informatika', 2025, 309),
('E1E125049', 'LA ODE ERLAN AL AZHAM ARE', 'Teknik Informatika', 2025, 310),
('E1E125050', 'LA ODE MUHAMMAD FAATHIR ASSHADIQ', 'Teknik Informatika', 2025, 311),
('E1E125051', 'LANGID GILANG RAMADHAN ODE', 'Teknik Informatika', 2025, 312),
('E1E125052', 'LAODE MUHAMMAD SYAHRIL', 'Teknik Informatika', 2025, 313),
('E1E125053', 'MARIO CHRISTIAN CHOUKROSIMON', 'Teknik Informatika', 2025, 314),
('E1E125054', 'MUH NABIL', 'Teknik Informatika', 2025, 315),
('E1E125055', 'MUH. ASHIF AL BANNA', 'Teknik Informatika', 2025, 339),
('E1E125056', 'MUH. FAHRI FAIRUZ RAMADHAN', 'Teknik Informatika', 2025, 340),
('E1E125057', 'MUH. FARREL PRASETYA RASIT', 'Teknik Informatika', 2025, 341),
('E1E125058', 'MUH. FATHAN RAMADHAN', 'Teknik Informatika', 2025, 342),
('E1E125059', 'MUHAMMAD HILAL ANDRIAN', 'Teknik Informatika', 2025, 343),
('E1E125060', 'MUNABILA', 'Teknik Informatika', 2025, 344),
('E1E125061', 'NUR AISYAH', 'Teknik Informatika', 2025, 345),
('E1E125062', 'OSKAR DWIYANA', 'Teknik Informatika', 2025, 346),
('E1E125063', 'PURWANTI RETHOB RUMLEAN', 'Teknik Informatika', 2025, 347),
('E1E125064', 'RAMLAN AHMADDAUN', 'Teknik Informatika', 2025, 348),
('E1E125065', 'REVA AUREL AMANDA', 'Teknik Informatika', 2025, 349),
('E1E125066', 'ROIHAN FAJRUR RAMADHAN', 'Teknik Informatika', 2025, 350),
('E1E125067', 'SASKIA MASITA', 'Teknik Informatika', 2025, 351),
('E1E125068', 'TEGAR ADIYATMA NUGRAHA SANTOSO', 'Teknik Informatika', 2025, 316),
('E1E125069', 'WA ODE KHAIRATUN', 'Teknik Informatika', 2025, 281),
('E1E125070', 'ADINDA RIZKY WULANDARI', 'Teknik Informatika', 2025, 317),
('E1E125071', 'AHMAD FAREL AL HUSEN', 'Teknik Informatika', 2025, 318),
('E1E125072', 'ARCHADIUS LERIAN PEDOR', 'Teknik Informatika', 2025, 352),
('E1E125073', 'ARIEF DWI YANUAR', 'Teknik Informatika', 2025, 353),
('E1E125074', 'FADIL TRI AUGUSTA', 'Teknik Informatika', 2025, 319),
('E1E125075', 'FARDAN', 'Teknik Informatika', 2025, 320),
('E1E125076', 'IBNUZABILSUDIRO', 'Teknik Informatika', 2025, 354),
('E1E125077', 'IRDINA ZAAFARANI', 'Teknik Informatika', 2025, 321),
('E1E125078', 'KEISYA ZALWA AZZAHRA', 'Teknik Informatika', 2025, 322),
('E1E125079', 'LAODE USMAN RASSYA RIANTA', 'Teknik Informatika', 2025, 323),
('E1E125080', 'MAISY PUTRI SYAHRANI SARANANI', 'Teknik Informatika', 2025, 324),
('E1E125081', 'MAWAR AGUSTINA', 'Teknik Informatika', 2025, 325),
('E1E125082', 'MUH. ALFARIL ANUGRAH LATORUMO', 'Teknik Informatika', 2025, 282),
('E1E125083', 'MUH.ZAYYAN', 'Teknik Informatika', 2025, 283),
('E1E125084', 'MUHAMMAD ADE SAPUTRA RIZAL', 'Teknik Informatika', 2025, 284),
('E1E125085', 'MUHAMMAD AKBAR AL BADAWI', 'Teknik Informatika', 2025, 285),
('E1E125086', 'MUHAMMAD ALFIN', 'Teknik Informatika', 2025, 286),
('E1E125087', 'MUHAMMAD ALI SHANJAYA', 'Teknik Informatika', 2025, 287),
('E1E125088', 'MUHAMMAD RIFKY SAFAAT SIDARIMA', 'Teknik Informatika', 2025, 355),
('E1E125089', 'MUHAMMAD SULTAN MAFTUH RAMADHAN', 'Teknik Informatika', 2025, 356),
('E1E125090', 'NANDIRA KANZA ALIKA RAMDANI', 'Teknik Informatika', 2025, 357),
('E1E125091', 'NILSA KIRANIYA NURMI ZALUNA LATUANDA', 'Teknik Informatika', 2025, 288),
('E1E125092', 'NINDI DWIANI', 'Teknik Informatika', 2025, 289),
('E1E125093', 'NURUL AULIA RAMADHANI', 'Teknik Informatika', 2025, 290),
('E1E125094', 'NURUL CAHYANISA PUTRI', 'Teknik Informatika', 2025, 291),
('E1E125095', 'NURUL MULYA AZAHRA', 'Teknik Informatika', 2025, 326),
('E1E125096', 'OLIVIA ODE MUPAHIR', 'Teknik Informatika', 2025, 327),
('E1E125097', 'PUTRI AISYIAH WAHYUNI', 'Teknik Informatika', 2025, 328),
('E1E125098', 'RADITYA FARDHAN', 'Teknik Informatika', 2025, 358),
('E1E125099', 'RUSTANG', 'Teknik Informatika', 2025, 359),
('E1E125100', 'SARA NABILA PUTRI SAHLIN', 'Teknik Informatika', 2025, 360),
('E1E125101', 'SHEYLA RAHMANAIR', 'Teknik Informatika', 2025, 361),
('E1E125102', 'SUCI AGISTA RAMADANI LUBIS', 'Teknik Informatika', 2025, 362),
('E1E125103', 'TISYA AMIRAH RAISYA', 'Teknik Informatika', 2025, 363),
('E1E125104', 'YASNI REZKY FITRI AZHARA', 'Teknik Informatika', 2025, 292),
('E1E125105', 'ZAHRA KIRANA', 'Teknik Informatika', 2025, 293);

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
(262, 'Mahasiswa login ke sistem', '2026-01-10 01:57:20', 98),
(263, 'Logout dari sistem', '2026-01-10 01:57:28', 98),
(264, 'Admin login ke sistem', '2026-01-10 02:01:34', 219),
(265, 'Logout dari sistem', '2026-01-10 03:48:46', 219),
(266, 'Admin login ke sistem', '2026-01-10 03:48:51', 219),
(267, 'Logout dari sistem', '2026-01-10 03:48:57', 219),
(268, 'Mahasiswa login ke sistem', '2026-01-10 03:49:02', 98),
(269, 'Logout dari sistem', '2026-01-10 03:49:51', 98),
(270, 'Admin login ke sistem', '2026-01-10 03:49:57', 219),
(271, 'Menetapkan dosen pembimbing untuk mahasiswa NIM ', '2026-01-10 03:50:01', 219),
(272, 'Menetapkan dosen pembimbing untuk mahasiswa NIM ', '2026-01-10 03:50:11', 219),
(273, 'Logout dari sistem', '2026-01-10 03:50:19', 219),
(274, 'Mahasiswa login ke sistem', '2026-01-10 03:50:27', 98),
(275, 'Logout dari sistem', '2026-01-10 03:50:33', 98),
(276, 'Dosen login ke sistem', '2026-01-10 03:51:19', 233),
(277, 'Logout dari sistem', '2026-01-10 03:51:33', 233),
(278, 'Mahasiswa login ke sistem', '2026-01-10 03:51:38', 98),
(279, 'Logout dari sistem', '2026-01-10 04:06:59', 98),
(280, 'Dosen login ke sistem', '2026-01-10 04:07:12', 233),
(281, 'Logout dari sistem', '2026-01-10 04:52:44', 233),
(282, 'Mahasiswa login ke sistem', '2026-01-10 04:52:49', 98),
(283, 'Logout dari sistem', '2026-01-10 04:53:10', 98),
(284, 'Admin login ke sistem', '2026-01-10 04:53:15', 219),
(285, 'Admin login ke sistem', '2026-01-10 05:40:32', 219),
(286, 'Logout dari sistem', '2026-01-10 05:40:35', 219),
(287, 'Admin login ke sistem', '2026-01-10 05:40:41', 219),
(288, 'Admin login ke sistem', '2026-01-10 05:40:49', 219),
(289, 'Admin login ke sistem', '2026-01-10 05:40:56', 219),
(290, 'Logout dari sistem', '2026-01-10 05:40:59', 219),
(291, 'Mahasiswa login ke sistem', '2026-01-10 05:41:14', 98),
(292, 'Admin login ke sistem', '2026-01-10 05:42:05', 219),
(293, 'Logout dari sistem', '2026-01-10 05:42:32', 219),
(294, 'Admin login ke sistem', '2026-01-10 05:42:43', 219),
(295, 'Admin login ke sistem', '2026-01-10 05:48:52', 219),
(296, 'Logout dari sistem', '2026-01-10 05:53:23', 219),
(297, 'Admin login ke sistem', '2026-01-10 06:37:31', 219),
(298, 'Logout dari sistem', '2026-01-10 07:07:52', 219);

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
(10, 10, 0, 0, 'Belum', 'Berlangsung', '2026-01-10 03:51:23');

-- --------------------------------------------------------

--
-- Struktur dari tabel `peminatan`
--

CREATE TABLE `peminatan` (
  `id_peminatan` int(11) NOT NULL,
  `kode` varchar(10) NOT NULL,
  `nama` varchar(120) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `peminatan`
--

INSERT INTO `peminatan` (`id_peminatan`, `kode`, `nama`, `created_at`) VALUES
(1, 'RPL', 'Rekayasa Perangkat Lunak', '2026-01-10 03:03:59'),
(2, 'KBJ', 'Komputasi Berbasis Jaringan', '2026-01-10 03:03:59'),
(3, 'KCV', 'Komputasi Cerdas dan Visualisasi', '2026-01-10 03:03:59');

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
(8, 'E1E124007', '2026-01-10', 'Hadir', 'presensi_E1E124007_1768017120_a35eb57d.jpg', -4.00821330, 122.52453420, 'Pending', NULL);

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
(92, 'E1E124001', 'idris01', 3, '2026-01-10 00:46:38', NULL),
(93, 'E1E124002', 'husein02', 3, '2026-01-10 00:46:38', NULL),
(94, 'E1E124003', 'utami03', 3, '2026-01-10 00:46:38', NULL),
(95, 'E1E124004', 'nisa04', 3, '2026-01-10 00:46:38', NULL),
(96, 'E1E124005', 'firmansyah05', 3, '2026-01-10 00:46:38', NULL),
(97, 'E1E124006', 'ramadhan06', 3, '2026-01-10 00:46:38', NULL),
(98, 'E1E124007', 'dirga07', 3, '2026-01-10 00:46:38', NULL),
(99, 'E1E124008', 'kologou08', 3, '2026-01-10 00:46:38', NULL),
(100, 'E1E124010', 'ramadhan10', 3, '2026-01-10 00:46:38', NULL),
(101, 'E1E124012', 'asmawaty12', 3, '2026-01-10 00:46:38', NULL),
(102, 'E1E124013', 'febriani13', 3, '2026-01-10 00:46:38', NULL),
(103, 'E1E124014', 'yani14', 3, '2026-01-10 00:46:38', NULL),
(104, 'E1E124015', 'pasrun15', 3, '2026-01-10 00:46:38', NULL),
(105, 'E1E124016', 'annisa16', 3, '2026-01-10 00:46:38', NULL),
(106, 'E1E124017', 'adawiah17', 3, '2026-01-10 00:46:38', NULL),
(107, 'E1E124018', 'ruslin18', 3, '2026-01-10 00:46:38', NULL),
(108, 'E1E124019', 'ayu19', 3, '2026-01-10 00:46:38', NULL),
(109, 'E1E124020', 'liandani20', 3, '2026-01-10 00:46:38', NULL),
(110, 'E1E124021', 'nurramadhani21', 3, '2026-01-10 00:46:38', NULL),
(111, 'E1E124022', 'rahmiyanti22', 3, '2026-01-10 00:46:38', NULL),
(112, 'E1E124023', 'liu23', 3, '2026-01-10 00:46:38', NULL),
(113, 'E1E124024', 'akbar24', 3, '2026-01-10 00:46:38', NULL),
(114, 'E1E124025', 'hartono25', 3, '2026-01-10 00:46:38', NULL),
(115, 'E1E124026', 'ramadhan26', 3, '2026-01-10 00:46:38', NULL),
(116, 'E1E124027', 'putra27', 3, '2026-01-10 00:46:38', NULL),
(117, 'E1E124028', 'siregar28', 3, '2026-01-10 00:46:38', NULL),
(118, 'E1E124029', 'salsabila29', 3, '2026-01-10 00:46:38', NULL),
(119, 'E1E124030', 'buntala30', 3, '2026-01-10 00:46:38', NULL),
(120, 'E1E124031', 'sarmudin31', 3, '2026-01-10 00:46:38', NULL),
(121, 'E1E124032', 'langi32', 3, '2026-01-10 00:46:38', NULL),
(122, 'E1E124033', 'heda33', 3, '2026-01-10 00:46:38', NULL),
(123, 'E1E124034', 'amran34', 3, '2026-01-10 00:46:38', NULL),
(124, 'E1E124035', 'possumah35', 3, '2026-01-10 00:46:38', NULL),
(125, 'E1E124036', 'febiola36', 3, '2026-01-10 00:46:38', NULL),
(126, 'E1E124037', 'ramadhan37', 3, '2026-01-10 00:46:38', NULL),
(127, 'E1E124038', 'prangesti38', 3, '2026-01-10 00:46:38', NULL),
(128, 'E1E124039', 'kaimudin39', 3, '2026-01-10 00:46:38', NULL),
(129, 'E1E124040', 'rukmana40', 3, '2026-01-10 00:46:38', NULL),
(130, 'E1E124041', 'rizqullah41', 3, '2026-01-10 00:46:38', NULL),
(131, 'E1E124042', 'fahril42', 3, '2026-01-10 00:46:38', NULL),
(132, 'E1E124043', 'syahrir43', 3, '2026-01-10 00:46:38', NULL),
(133, 'E1E124044', 'yusuf44', 3, '2026-01-10 00:46:38', NULL),
(134, 'E1E124045', 'yamin45', 3, '2026-01-10 00:46:38', NULL),
(135, 'E1E124046', 'mahneerah46', 3, '2026-01-10 00:46:38', NULL),
(136, 'E1E124047', 'anakoruo47', 3, '2026-01-10 00:46:38', NULL),
(137, 'E1E124048', 'rahmi48', 3, '2026-01-10 00:46:38', NULL),
(138, 'E1E124049', 'adam49', 3, '2026-01-10 00:46:38', NULL),
(139, 'E1E124050', 'ode50', 3, '2026-01-10 00:46:38', NULL),
(140, 'E1E124051', 'maharani51', 3, '2026-01-10 00:46:38', NULL),
(141, 'E1E124052', 'wulandari52', 3, '2026-01-10 00:46:38', NULL),
(142, 'E1E124053', 'dawwas53', 3, '2026-01-10 00:46:38', NULL),
(143, 'E1E124054', 'rabiul54', 3, '2026-01-10 00:46:38', NULL),
(144, 'E1E124055', 'koda55', 3, '2026-01-10 00:46:38', NULL),
(145, 'E1E124056', 'azzahwa56', 3, '2026-01-10 00:46:38', NULL),
(146, 'E1E124057', 'faizah57', 3, '2026-01-10 00:46:38', NULL),
(147, 'E1E124058', 'kandari58', 3, '2026-01-10 00:46:38', NULL),
(148, 'E1E124059', 'wardana59', 3, '2026-01-10 00:46:38', NULL),
(149, 'E1E124060', 'amlan60', 3, '2026-01-10 00:46:38', NULL),
(150, 'E1E124061', 'munajat61', 3, '2026-01-10 00:46:38', NULL),
(151, 'E1E124062', 'munifa62', 3, '2026-01-10 00:46:38', NULL),
(152, 'E1E124063', 'saladin63', 3, '2026-01-10 00:46:38', NULL),
(153, 'E1E124064', 'bait64', 3, '2026-01-10 00:46:38', NULL),
(154, 'E1E124065', 'fauzilazhim65', 3, '2026-01-10 00:46:38', NULL),
(155, 'E1E124066', 'tsaqif66', 3, '2026-01-10 00:46:38', NULL),
(156, 'E1E124067', 'agustin67', 3, '2026-01-10 00:46:38', NULL),
(157, 'E1E124068', 'widianto68', 3, '2026-01-10 00:46:38', NULL),
(158, 'E1E124069', 'pratama69', 3, '2026-01-10 00:46:38', NULL),
(159, 'E1E124071', 'porosi71', 3, '2026-01-10 00:46:38', NULL),
(160, 'E1E124072', 'rabildzan72', 3, '2026-01-10 00:46:38', NULL),
(161, 'E1E124073', 'pratama73', 3, '2026-01-10 00:46:38', NULL),
(162, 'E1E124074', 'firdaus74', 3, '2026-01-10 00:46:38', NULL),
(163, 'E1E124076', 'zuhairah76', 3, '2026-01-10 00:46:38', NULL),
(164, 'E1E124077', 'ramadhani77', 3, '2026-01-10 00:46:38', NULL),
(165, 'E1E124078', 'yakop78', 3, '2026-01-10 00:46:38', NULL),
(166, 'E1E124079', 'potto79', 3, '2026-01-10 00:46:38', NULL),
(167, 'E1E124080', 'yurismawati80', 3, '2026-01-10 00:46:38', NULL),
(168, 'E1E124081', 'saputra81', 3, '2026-01-10 00:46:38', NULL),
(169, 'E1E124082', 'nurcahaydi82', 3, '2026-01-10 00:46:38', NULL),
(219, 'admin', 'admin123', 1, '2026-01-10 02:01:05', NULL),
(220, 'L.M.Dirga', 'syahrir2443', 2, '2026-01-10 02:10:39', NULL),
(228, 'saputra04', '0006049104', 2, '2026-01-10 03:04:00', NULL),
(229, 'efendi04', '0007078904', 2, '2026-01-10 03:04:00', NULL),
(230, 'statiswaty06', '0007118106', 2, '2026-01-10 03:04:00', NULL),
(231, 'surimi10', '0008078610', 2, '2026-01-10 03:04:00', NULL),
(232, 'sarita03', '0009096503', 2, '2026-01-10 03:04:00', NULL),
(233, 'yamin04', '0014068304', 2, '2026-01-10 03:04:00', NULL),
(234, 'ningrum08', '0016018308', 2, '2026-01-10 03:04:00', NULL),
(235, 'wibowo02', '0017088402', 2, '2026-01-10 03:04:00', NULL),
(236, 'isnawaty06', '0017117606', 2, '2026-01-10 03:04:00', NULL),
(237, 'mokui02', '0017127802', 2, '2026-01-10 03:04:00', NULL),
(238, 'subardin02', '0020057902', 2, '2026-01-10 03:04:00', NULL),
(239, 'musaruddin04', '0022017304', 2, '2026-01-10 03:04:00', NULL),
(240, 'sutardi07', '0022027607', 2, '2026-01-10 03:04:00', NULL),
(241, 'sajiah01', '0023068101', 2, '2026-01-10 03:04:00', NULL),
(242, 'aksara06', '0023078406', 2, '2026-01-10 03:04:00', NULL),
(243, 'pramono07', '0025047107', 2, '2026-01-10 03:04:00', NULL),
(244, 'jaya01', '0028107501', 2, '2026-01-10 03:04:00', NULL),
(245, 'ransi02', '0029128402', 2, '2026-01-10 03:04:00', NULL),
(246, 'tajidun07', '0030048107', 2, '2026-01-10 03:04:00', NULL),
(247, 'nangi01', '0906028701', 2, '2026-01-10 03:04:00', NULL),
(248, 'aksara02', '0929058902', 2, '2026-01-10 03:04:00', NULL),
(259, 'E1E125001', 'razak01', 3, '2026-01-10 06:31:16', NULL),
(260, 'E1E125002', 'alarafah02', 3, '2026-01-10 06:31:16', NULL),
(261, 'E1E125003', 'afriani03', 3, '2026-01-10 06:31:16', NULL),
(262, 'E1E125004', 'ismar04', 3, '2026-01-10 06:31:16', NULL),
(263, 'E1E125005', 'amir05', 3, '2026-01-10 06:31:16', NULL),
(264, 'E1E125006', 'denyawan06', 3, '2026-01-10 06:31:16', NULL),
(265, 'E1E125007', 'fahri07', 3, '2026-01-10 06:31:16', NULL),
(266, 'E1E125008', 'fainal08', 3, '2026-01-10 06:31:16', NULL),
(267, 'E1E125009', 'fadya09', 3, '2026-01-10 06:31:16', NULL),
(268, 'E1E125010', 'sudiatmika10', 3, '2026-01-10 06:31:16', NULL),
(269, 'E1E125031', 'ramadhan31', 3, '2026-01-10 06:31:16', NULL),
(270, 'E1E125032', 'landfran32', 3, '2026-01-10 06:31:17', NULL),
(271, 'E1E125033', 'said33', 3, '2026-01-10 06:31:17', NULL),
(272, 'E1E125034', 'alimmunirah34', 3, '2026-01-10 06:31:17', NULL),
(273, 'E1E125035', 'suputri35', 3, '2026-01-10 06:31:17', NULL),
(274, 'E1E125036', 'putra36', 3, '2026-01-10 06:31:17', NULL),
(275, 'E1E125037', 'rumudale37', 3, '2026-01-10 06:31:17', NULL),
(276, 'E1E125038', 'tongapa38', 3, '2026-01-10 06:31:17', NULL),
(277, 'E1E125039', 'nurabidah39', 3, '2026-01-10 06:31:17', NULL),
(278, 'E1E125040', 'delo40', 3, '2026-01-10 06:31:17', NULL),
(279, 'E1E125041', 'salsabilah41', 3, '2026-01-10 06:31:17', NULL),
(280, 'E1E125042', 'hidayat42', 3, '2026-01-10 06:31:17', NULL),
(281, 'E1E125069', 'khairatun69', 3, '2026-01-10 06:31:17', NULL),
(282, 'E1E125082', 'latorumo82', 3, '2026-01-10 06:31:17', NULL),
(283, 'E1E125083', 'muhzayyan83', 3, '2026-01-10 06:31:17', NULL),
(284, 'E1E125084', 'rizal84', 3, '2026-01-10 06:31:17', NULL),
(285, 'E1E125085', 'badawi85', 3, '2026-01-10 06:31:17', NULL),
(286, 'E1E125086', 'alfin86', 3, '2026-01-10 06:31:17', NULL),
(287, 'E1E125087', 'shanjaya87', 3, '2026-01-10 06:31:17', NULL),
(288, 'E1E125091', 'latuanda91', 3, '2026-01-10 06:31:17', NULL),
(289, 'E1E125092', 'dwiani92', 3, '2026-01-10 06:31:17', NULL),
(290, 'E1E125093', 'ramadhani93', 3, '2026-01-10 06:31:17', NULL),
(291, 'E1E125094', 'putri94', 3, '2026-01-10 06:31:17', NULL),
(292, 'E1E125104', 'azhara04', 3, '2026-01-10 06:31:17', NULL),
(293, 'E1E125105', 'kirana05', 3, '2026-01-10 06:31:17', NULL),
(294, 'E1E125011', 'mubarak11', 3, '2026-01-10 06:31:17', NULL),
(295, 'E1E125012', 'santika12', 3, '2026-01-10 06:31:17', NULL),
(296, 'E1E125013', 's13', 3, '2026-01-10 06:31:17', NULL),
(297, 'E1E125014', 'maghfiro14', 3, '2026-01-10 06:31:17', NULL),
(298, 'E1E125015', 'christian15', 3, '2026-01-10 06:31:17', NULL),
(299, 'E1E125016', 'fayed16', 3, '2026-01-10 06:31:17', NULL),
(300, 'E1E125017', 'anansari17', 3, '2026-01-10 06:31:17', NULL),
(301, 'E1E125018', 'alwanie18', 3, '2026-01-10 06:31:17', NULL),
(302, 'E1E125023', 'salim23', 3, '2026-01-10 06:31:17', NULL),
(303, 'E1E125024', 'dermawan24', 3, '2026-01-10 06:31:17', NULL),
(304, 'E1E125025', 'rezki25', 3, '2026-01-10 06:31:17', NULL),
(305, 'E1E125044', 'zacfaron44', 3, '2026-01-10 06:31:17', NULL),
(306, 'E1E125045', 'ayu45', 3, '2026-01-10 06:31:17', NULL),
(307, 'E1E125046', 'fajri46', 3, '2026-01-10 06:31:17', NULL),
(308, 'E1E125047', 'rambega47', 3, '2026-01-10 06:31:17', NULL),
(309, 'E1E125048', 'syaifullah48', 3, '2026-01-10 06:31:17', NULL),
(310, 'E1E125049', 'are49', 3, '2026-01-10 06:31:17', NULL),
(311, 'E1E125050', 'asshadiq50', 3, '2026-01-10 06:31:17', NULL),
(312, 'E1E125051', 'ode51', 3, '2026-01-10 06:31:17', NULL),
(313, 'E1E125052', 'syahril52', 3, '2026-01-10 06:31:17', NULL),
(314, 'E1E125053', 'choukrosimon53', 3, '2026-01-10 06:31:17', NULL),
(315, 'E1E125054', 'nabil54', 3, '2026-01-10 06:31:17', NULL),
(316, 'E1E125068', 'santoso68', 3, '2026-01-10 06:31:17', NULL),
(317, 'E1E125070', 'wulandari70', 3, '2026-01-10 06:31:17', NULL),
(318, 'E1E125071', 'husen71', 3, '2026-01-10 06:31:17', NULL),
(319, 'E1E125074', 'augusta74', 3, '2026-01-10 06:31:17', NULL),
(320, 'E1E125075', 'fardan75', 3, '2026-01-10 06:31:17', NULL),
(321, 'E1E125077', 'zaafarani77', 3, '2026-01-10 06:31:17', NULL),
(322, 'E1E125078', 'azzahra78', 3, '2026-01-10 06:31:17', NULL),
(323, 'E1E125079', 'rianta79', 3, '2026-01-10 06:31:17', NULL),
(324, 'E1E125080', 'saranani80', 3, '2026-01-10 06:31:17', NULL),
(325, 'E1E125081', 'agustina81', 3, '2026-01-10 06:31:17', NULL),
(326, 'E1E125095', 'azahra95', 3, '2026-01-10 06:31:17', NULL),
(327, 'E1E125096', 'mupahir96', 3, '2026-01-10 06:31:17', NULL),
(328, 'E1E125097', 'wahyuni97', 3, '2026-01-10 06:31:17', NULL),
(329, 'E1E125019', 'nawir19', 3, '2026-01-10 06:31:17', NULL),
(330, 'E1E125020', 'subhi20', 3, '2026-01-10 06:31:17', NULL),
(331, 'E1E125021', 'fishabillah21', 3, '2026-01-10 06:31:17', NULL),
(332, 'E1E125022', 'ramadhani22', 3, '2026-01-10 06:31:17', NULL),
(333, 'E1E125026', 'salsabila26', 3, '2026-01-10 06:31:17', NULL),
(334, 'E1E125027', 'mulandari27', 3, '2026-01-10 06:31:17', NULL),
(335, 'E1E125028', 'saputri28', 3, '2026-01-10 06:31:17', NULL),
(336, 'E1E125029', 'rifka29', 3, '2026-01-10 06:31:17', NULL),
(337, 'E1E125030', 'muhammad30', 3, '2026-01-10 06:31:17', NULL),
(338, 'E1E125043', 'deyaska43', 3, '2026-01-10 06:31:17', NULL),
(339, 'E1E125055', 'banna55', 3, '2026-01-10 06:31:17', NULL),
(340, 'E1E125056', 'ramadhan56', 3, '2026-01-10 06:31:17', NULL),
(341, 'E1E125057', 'rasit57', 3, '2026-01-10 06:31:17', NULL),
(342, 'E1E125058', 'ramadhan58', 3, '2026-01-10 06:31:17', NULL),
(343, 'E1E125059', 'andrian59', 3, '2026-01-10 06:31:17', NULL),
(344, 'E1E125060', 'munabila60', 3, '2026-01-10 06:31:17', NULL),
(345, 'E1E125061', 'aisyah61', 3, '2026-01-10 06:31:17', NULL),
(346, 'E1E125062', 'dwiyana62', 3, '2026-01-10 06:31:17', NULL),
(347, 'E1E125063', 'rumlean63', 3, '2026-01-10 06:31:17', NULL),
(348, 'E1E125064', 'ahmaddaun64', 3, '2026-01-10 06:31:17', NULL),
(349, 'E1E125065', 'amanda65', 3, '2026-01-10 06:31:17', NULL),
(350, 'E1E125066', 'ramadhan66', 3, '2026-01-10 06:31:17', NULL),
(351, 'E1E125067', 'masita67', 3, '2026-01-10 06:31:17', NULL),
(352, 'E1E125072', 'pedor72', 3, '2026-01-10 06:31:17', NULL),
(353, 'E1E125073', 'yanuar73', 3, '2026-01-10 06:31:17', NULL),
(354, 'E1E125076', 'ibnuzabilsudiro76', 3, '2026-01-10 06:31:17', NULL),
(355, 'E1E125088', 'sidarima88', 3, '2026-01-10 06:31:17', NULL),
(356, 'E1E125089', 'ramadhan89', 3, '2026-01-10 06:31:17', NULL),
(357, 'E1E125090', 'ramdani90', 3, '2026-01-10 06:31:17', NULL),
(358, 'E1E125098', 'fardhan98', 3, '2026-01-10 06:31:17', NULL),
(359, 'E1E125099', 'rustang99', 3, '2026-01-10 06:31:17', NULL),
(360, 'E1E125100', 'sahlin00', 3, '2026-01-10 06:31:17', NULL),
(361, 'E1E125101', 'rahmanair01', 3, '2026-01-10 06:31:17', NULL),
(362, 'E1E125102', 'lubis02', 3, '2026-01-10 06:31:17', NULL),
(363, 'E1E125103', 'raisya03', 3, '2026-01-10 06:31:17', NULL);

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
-- Indeks untuk tabel `angkatan`
--
ALTER TABLE `angkatan`
  ADD PRIMARY KEY (`tahun`);

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
-- Indeks untuk tabel `peminatan`
--
ALTER TABLE `peminatan`
  ADD PRIMARY KEY (`id_peminatan`),
  ADD UNIQUE KEY `kode` (`kode`);

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
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `bimbingan`
--
ALTER TABLE `bimbingan`
  MODIFY `id_bimbingan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `dokumen_akhir`
--
ALTER TABLE `dokumen_akhir`
  MODIFY `id_dokumen_akhir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id_kp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id_laporan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `laporan_mingguan`
--
ALTER TABLE `laporan_mingguan`
  MODIFY `id_laporan_mingguan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `monitoring`
--
ALTER TABLE `monitoring`
  MODIFY `id_monitoring` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=299;

--
-- AUTO_INCREMENT untuk tabel `monitoring_kp`
--
ALTER TABLE `monitoring_kp`
  MODIFY `id_monitoring` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `peminatan`
--
ALTER TABLE `peminatan`
  MODIFY `id_peminatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `id_pengumuman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `penilaian`
--
ALTER TABLE `penilaian`
  MODIFY `id_penilaian` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `penilaian_akhir`
--
ALTER TABLE `penilaian_akhir`
  MODIFY `id_penilaian_akhir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `penilaian_mingguan`
--
ALTER TABLE `penilaian_mingguan`
  MODIFY `id_penilaian_mingguan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `presensi`
--
ALTER TABLE `presensi`
  MODIFY `id_presensi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=364;

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
