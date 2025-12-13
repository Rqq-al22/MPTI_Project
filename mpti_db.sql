-- =====================================
-- DATABASE SIAKAD MINI
-- =====================================

CREATE DATABASE siakad_mini;
USE siakad_mini;

-- =====================================
-- 1. TABEL ROLE
-- =====================================
CREATE TABLE roles (
    id_role INT AUTO_INCREMENT PRIMARY KEY,
    nama_role VARCHAR(20) NOT NULL
);

INSERT INTO roles (nama_role) VALUES
('admin'),
('dosen'),
('mahasiswa');

-- =====================================
-- 2. TABEL USER (LOGIN)
-- =====================================
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    id_role INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_role) REFERENCES roles(id_role)
);

-- =====================================
-- 3. TABEL MAHASISWA
-- =====================================
CREATE TABLE mahasiswa (
    nim VARCHAR(15) PRIMARY KEY,
    nama VARCHAR(100),
    jurusan VARCHAR(50),
    angkatan INT,
    id_user INT UNIQUE,
    FOREIGN KEY (id_user) REFERENCES users(id_user)
);

-- =====================================
-- 4. TABEL DOSEN
-- =====================================
CREATE TABLE dosen (
    nidn VARCHAR(20) PRIMARY KEY,
    nama VARCHAR(100),
    keahlian VARCHAR(100),
    id_user INT UNIQUE,
    FOREIGN KEY (id_user) REFERENCES users(id_user)
);

-- =====================================
-- 5. TABEL PRESENSI MAHASISWA
-- =====================================
CREATE TABLE presensi (
    id_presensi INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(15),
    tanggal DATE,
    status ENUM('Hadir','Izin','Alpha'),
    FOREIGN KEY (nim) REFERENCES mahasiswa(nim)
);

-- =====================================
-- 6. TABEL LAPORAN MAHASISWA
-- =====================================
CREATE TABLE laporan (
    id_laporan INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(15),
    judul VARCHAR(150),
    file_laporan VARCHAR(255),
    tanggal_upload DATE,
    FOREIGN KEY (nim) REFERENCES mahasiswa(nim)
);

-- =====================================
-- 7. TABEL PENILAIAN LAPORAN (DOSEN)
-- =====================================
CREATE TABLE penilaian (
    id_penilaian INT AUTO_INCREMENT PRIMARY KEY,
    id_laporan INT,
    nidn VARCHAR(20),
    nilai INT,
    komentar TEXT,
    FOREIGN KEY (id_laporan) REFERENCES laporan(id_laporan),
    FOREIGN KEY (nidn) REFERENCES dosen(nidn)
);

-- =====================================
-- 8. TABEL JADWAL / PENGUMUMAN (ADMIN)
-- =====================================
CREATE TABLE jadwal_pengumuman (
    id_jadwal INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(150),
    deskripsi TEXT,
    tanggal DATE
);

-- =====================================
-- 9. TABEL BIMBINGAN
-- =====================================
CREATE TABLE bimbingan (
    id_bimbingan INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(15),
    nidn VARCHAR(20),
    topik VARCHAR(150),
    tanggal DATE,
    catatan TEXT,
    FOREIGN KEY (nim) REFERENCES mahasiswa(nim),
    FOREIGN KEY (nidn) REFERENCES dosen(nidn)
);

-- =====================================
-- 10. TABEL MONITORING SISTEM
-- =====================================
CREATE TABLE monitoring (
    id_monitoring INT AUTO_INCREMENT PRIMARY KEY,
    aktivitas VARCHAR(150),
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_user INT,
    FOREIGN KEY (id_user) REFERENCES users(id_user)
);

-- tambah role dan user dkk
INSERT INTO users (username, password, id_role)
VALUES ('E1E124', 'oke', 1);

