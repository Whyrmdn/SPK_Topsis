# SPK TOPSIS - Sistem Pendukung Keputusan Pengangkatan Karyawan Tetap

Aplikasi web berbasis PHP untuk membantu proses pengambilan keputusan pengangkatan karyawan tetap menggunakan metode TOPSIS.

## Fitur Utama

- **Manajemen Users:** Admin dapat menambah, mengedit, dan menghapus user dengan role admin/user.
- **Manajemen Kriteria:** Tambah, edit, dan hapus kriteria penilaian beserta bobot dan tipe (Benefit/Cost).
- **Manajemen Alternatif (Karyawan):** Kelola data karyawan yang akan dinilai.
- **Input Nilai Alternatif:** Input nilai setiap karyawan untuk setiap kriteria, tersedia tampilan list dan matrix.
- **Perhitungan TOPSIS:** Hitung ranking karyawan secara otomatis dengan metode TOPSIS.
- **Lihat Hasil & Ranking:** Tampilkan hasil ranking, top 3 karyawan terbaik, dan detail perhitungan.
- **Responsive UI:** Menggunakan Bootstrap 5, DataTables, dan SweetAlert2.

## Struktur Folder

- **config:** Berisi file konfigurasi database.
- **templates:** Berisi file header dan footer untuk tampilan.
- **assets:** Berisi file CSS, JS, dan gambar.
- **proses.php:** Berisi fungsi-fungsi untuk perhitungan TOPSIS.
- **index.php:** Halaman login.
- **dashboard.php:** Halaman dashboard.
- **users.php:** Halaman manajemen users.
- **kriteria.php:** Halaman manajemen kriteria.
- **alternatif.php:** Halaman manajemen alternatif (karyawan).
- **nilai.php:** Halaman input nilai alternatif.
- **hasil.php:** Halaman lihat hasil dan ranking.
- **ranking.php:** Halaman tampilkan ranking karyawan.

## Instalasi

1. **Clone repository ini** ke folder web server Anda (misal: `htdocs` untuk XAMPP).
2. **Import database**
   - Buka phpMyAdmin.
   - Buat database baru dengan nama `spk_topsis`.
   - Import file [`spk_topsis.sql`](spk_topsis.sql).
3. **Konfigurasi koneksi database**
   - Pastikan file [`config/db.php`](config/db.php) sudah sesuai dengan konfigurasi MySQL Anda.
4. **Akses aplikasi**
   - Buka browser dan akses `http://localhost/SPK_Topsis/index.php`.

## Akun Default

- **Username:** admin
- **Password:** password

## Teknologi

- PHP (Procedural)
- MySQL
- Bootstrap 5
- DataTables
- SweetAlert2

## Lisensi

Aplikasi ini dibuat untuk keperluan pembelajaran dan tugas akhir.

---

**Database Name:** spk_topsis
