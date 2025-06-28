<?php
$page_title = "Dashboard";
include 'templates/header.php';

// Ambil statistik
$query_users = "SELECT COUNT(*) as total FROM users";
$result_users = mysqli_query($koneksi, $query_users);
$total_users = mysqli_fetch_assoc($result_users)['total'];

$query_kriteria = "SELECT COUNT(*) as total FROM kriteria";
$result_kriteria = mysqli_query($koneksi, $query_kriteria);
$total_kriteria = mysqli_fetch_assoc($result_kriteria)['total'];

$query_alternatif = "SELECT COUNT(*) as total FROM alternatif";
$result_alternatif = mysqli_query($koneksi, $query_alternatif);
$total_alternatif = mysqli_fetch_assoc($result_alternatif)['total'];

$query_nilai = "SELECT COUNT(*) as total FROM nilai_alternatif";
$result_nilai = mysqli_query($koneksi, $query_nilai);
$total_nilai = mysqli_fetch_assoc($result_nilai)['total'];

// Ambil data terbaru
$query_recent_alternatif = "SELECT * FROM alternatif ORDER BY created_at DESC LIMIT 5";
$result_recent_alternatif = mysqli_query($koneksi, $query_recent_alternatif);

$query_recent_hasil = "SELECT a.nama_alternatif, h.nilai_preferensi, h.ranking, h.tanggal_hitung
                      FROM hasil_topsis h
                      JOIN alternatif a ON h.id_alternatif = a.id
                      ORDER BY h.tanggal_hitung DESC LIMIT 5";
$result_recent_hasil = mysqli_query($koneksi, $query_recent_hasil);
?>

<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="mb-1">Selamat Datang, <?php echo $_SESSION['nama_lengkap']; ?>!</h2>
                            <p class="mb-0">Sistem Pendukung Keputusan Pengangkatan Karyawan Tetap menggunakan Metode TOPSIS</p>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_users; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Kriteria Penilaian
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_kriteria; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Alternatif (Karyawan)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_alternatif; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Penilaian
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_nilai; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Alternatif Terbaru -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-plus me-2"></i>Karyawan Terbaru
                    </h6>
                    <a href="alternatif.php" class="btn btn-sm btn-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($result_recent_alternatif) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php while ($row = mysqli_fetch_assoc($result_recent_alternatif)): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?php echo $row['nama_alternatif']; ?></h6>
                                        <small class="text-muted">
                                            <?php echo $row['jabatan']; ?> - <?php echo $row['departemen']; ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-primary rounded-pill"><?php echo $row['kode_alternatif']; ?></span>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>Belum ada data karyawan</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Hasil Ranking Terbaru -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-trophy me-2"></i>Hasil Ranking Terbaru
                    </h6>
                    <a href="hasil.php" class="btn btn-sm btn-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($result_recent_hasil) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php while ($row = mysqli_fetch_assoc($result_recent_hasil)): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?php echo $row['nama_alternatif']; ?></h6>
                                        <small class="text-muted">
                                            Nilai: <?php echo number_format($row['nilai_preferensi'], 4); ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-<?php echo $row['ranking'] <= 3 ? 'success' : 'secondary'; ?> rounded-pill">
                                        Rank #<?php echo $row['ranking']; ?>
                                    </span>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted">
                            <i class="fas fa-calculator fa-3x mb-3"></i>
                            <p>Belum ada hasil perhitungan</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>Aksi Cepat
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="alternatif.php" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-user-plus fa-2x mb-2"></i><br>
                                Tambah Karyawan
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="nilai.php" class="btn btn-outline-success w-100 py-3">
                                <i class="fas fa-star fa-2x mb-2"></i><br>
                                Input Penilaian
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="hasil.php" class="btn btn-outline-info w-100 py-3">
                                <i class="fas fa-calculator fa-2x mb-2"></i><br>
                                Hitung TOPSIS
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="hasil.php" class="btn btn-outline-warning w-100 py-3">
                                <i class="fas fa-trophy fa-2x mb-2"></i><br>
                                Lihat Ranking
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.bg-gradient-primary {
    background: var(--primary-gradient) !important;
}
</style>

<?php include 'templates/footer.php'; ?>