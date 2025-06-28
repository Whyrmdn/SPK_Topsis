
-- Database SPK TOPSIS untuk Pengangkatan Karyawan Tetap
CREATE DATABASE IF NOT EXISTS spk_topsis;
USE spk_topsis;

-- Tabel Users untuk sistem login
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Kriteria untuk penilaian
CREATE TABLE kriteria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_kriteria VARCHAR(10) UNIQUE NOT NULL,
    nama_kriteria VARCHAR(100) NOT NULL,
    bobot DECIMAL(5,3) NOT NULL,
    tipe ENUM('Benefit','Cost') DEFAULT 'Benefit',
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Alternatif (Karyawan)
CREATE TABLE alternatif (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_alternatif VARCHAR(10) UNIQUE NOT NULL,
    nama_alternatif VARCHAR(100) NOT NULL,
    nip VARCHAR(20),
    jabatan VARCHAR(50),
    departemen VARCHAR(50),
    masa_kerja INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Nilai Alternatif
CREATE TABLE nilai_alternatif (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_alternatif INT NOT NULL,
    id_kriteria INT NOT NULL,
    nilai DECIMAL(5,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_alternatif) REFERENCES alternatif(id) ON DELETE CASCADE,
    FOREIGN KEY (id_kriteria) REFERENCES kriteria(id) ON DELETE CASCADE,
    UNIQUE KEY unique_nilai (id_alternatif, id_kriteria)
);

-- Tabel Hasil Perhitungan TOPSIS
CREATE TABLE hasil_topsis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_alternatif INT NOT NULL,
    nilai_preferensi DECIMAL(10,6) NOT NULL,
    ranking INT NOT NULL,
    tanggal_hitung TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_alternatif) REFERENCES alternatif(id) ON DELETE CASCADE
);

-- Insert data default admin (password: password)
INSERT INTO users (username, password, nama_lengkap, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@spktopsis.com', 'admin');

-- Insert kriteria default
INSERT INTO kriteria (kode_kriteria, nama_kriteria, bobot, tipe, deskripsi) VALUES
('C1', 'Disiplin Kerja', 0.250, 'Benefit', 'Penilaian kedisiplinan karyawan dalam bekerja'),
('C2', 'Kinerja', 0.250, 'Benefit', 'Penilaian hasil kerja dan produktivitas karyawan'),
('C3', 'Pengalaman Kerja', 0.200, 'Benefit', 'Penilaian pengalaman dan masa kerja karyawan'),
('C4', 'Perilaku', 0.150, 'Benefit', 'Penilaian sikap dan perilaku karyawan'),
('C5', 'Komunikasi', 0.150, 'Benefit', 'Penilaian kemampuan komunikasi karyawan');

-- Insert contoh data alternatif
INSERT INTO alternatif (kode_alternatif, nama_alternatif, nip, jabatan, departemen, masa_kerja) VALUES
('A1', 'Fajrul', '2021001', 'Staff IT', 'IT', 3),
('A2', 'Fadillah', '2021002', 'Staff Keuangan', 'Keuangan', 2),
('A3', 'Citra', '2020001', 'Staff Marketing', 'Marketing', 4);
