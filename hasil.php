<?php
// Handle hapus hasil HARUS di paling atas sebelum ada output
require_once 'config/db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['hapus']) && $_GET['hapus'] == '1') {
    $delete_query = "DELETE FROM hasil_topsis";
    if (mysqli_query($koneksi, $delete_query)) {
        $_SESSION['success_message'] = "Semua hasil perangkingan berhasil dihapus!";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus hasil perangkingan: " . mysqli_error($koneksi);
    }
    header("Location: hasil.php");
    exit();
}

$page_title = "Ranking & Hasil TOPSIS";
include 'templates/header.php';
require_once 'proses.php';

$message = '';
$error = '';

// Cek pesan dari session
if (isset($_SESSION['success_message'])) {
    $message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    $error = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Ambil hasil ranking dari database
$query_hasil = "SELECT h.*,
                       COALESCE(a.nama_alternatif, '') as nama_alternatif,
                       COALESCE(a.kode_alternatif, '') as kode_alternatif,
                       COALESCE(a.nip, '') as nip,
                       COALESCE(a.jabatan, '') as jabatan,
                       COALESCE(a.departemen, '') as departemen
                FROM hasil_topsis h
                LEFT JOIN alternatif a ON h.id_alternatif = a.id
                ORDER BY h.ranking";
$result_hasil = mysqli_query($koneksi, $query_hasil);

// Cek apakah query berhasil
if (!$result_hasil) {
    $error = "Error dalam mengambil data: " . mysqli_error($koneksi);
    $has_results = false;
} else {
    // Cek apakah ada data hasil
    $has_results = mysqli_num_rows($result_hasil) > 0;
}

// Jika diminta detail perhitungan
$show_detail = isset($_GET['detail']) && $_GET['detail'] == '1';
$detail_data = null;

if ($show_detail && $has_results) {
    $detail_data = hitungTOPSIS();
}
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-trophy me-2"></i>Ranking & Hasil TOPSIS
        </h1>
        <div>
            <?php if ($has_results): ?>
                <a href="hasil.php?detail=1" class="btn btn-info me-2">
                    <i class="fas fa-calculator me-2"></i>Detail Perhitungan
                </a>
                <a href="hasil.php?hapus=1" class="btn btn-danger me-2" onclick="return confirm('Apakah Anda yakin ingin menghapus semua hasil perangkingan? Data tidak dapat dikembalikan.')">
                    <i class="fas fa-trash me-2"></i>Hapus Hasil
                </a>
            <?php endif; ?>
            <a href="proses.php?hitung=1" class="btn btn-primary" onclick="return confirm('Apakah Anda yakin ingin menghitung ulang? Data hasil sebelumnya akan dihapus.')">
                <i class="fas fa-play me-2"></i>Hitung TOPSIS
            </a>
        </div>
    </div>

    <!-- Alerts -->
    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($has_results): ?>
        <!-- Hasil Ranking -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-medal me-2"></i>Hasil Ranking Karyawan
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">Ranking</th>
                                <th>Kode</th>
                                <th>Nama Karyawan</th>
                                <th>NIP</th>
                                <th>Jabatan</th>
                                <th>Departemen</th>
                                <th class="text-center">Nilai Preferensi</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($has_results && $result_hasil): ?>
                            <?php while ($row = mysqli_fetch_assoc($result_hasil)): ?>
                            <tr class="<?php echo (isset($row['ranking']) && $row['ranking'] <= 3) ? 'table-success' : ''; ?>">
                                <td class="text-center">
                                    <?php if (isset($row['ranking']) && $row['ranking'] == 1): ?>
                                        <span class="badge bg-warning fs-5">🥇 #<?php echo $row['ranking']; ?></span>
                                    <?php elseif (isset($row['ranking']) && $row['ranking'] == 2): ?>
                                        <span class="badge bg-secondary fs-5">🥈 #<?php echo $row['ranking']; ?></span>
                                    <?php elseif (isset($row['ranking']) && $row['ranking'] == 3): ?>
                                        <span class="badge bg-warning fs-5">🥉 #<?php echo $row['ranking']; ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-primary fs-5">#<?php echo isset($row['ranking']) ? $row['ranking'] : '-'; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-primary"><?php echo isset($row['kode_alternatif']) ? $row['kode_alternatif'] : '-'; ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-3">
                                            <div class="avatar-initial bg-primary rounded-circle">
                                                <?php echo isset($row['nama_alternatif']) ? strtoupper(substr($row['nama_alternatif'], 0, 2)) : 'NA'; ?>
                                            </div>
                                        </div>
                                        <strong><?php echo isset($row['nama_alternatif']) ? $row['nama_alternatif'] : '-'; ?></strong>
                                    </div>
                                </td>
                                <td><?php echo isset($row['nip']) ? $row['nip'] : '-'; ?></td>
                                <td><?php echo isset($row['jabatan']) ? $row['jabatan'] : '-'; ?></td>
                                <td><?php echo isset($row['departemen']) ? $row['departemen'] : '-'; ?></td>
                                <td class="text-center">
                                    <span class="badge bg-info fs-6"><?php echo isset($row['nilai_preferensi']) ? number_format($row['nilai_preferensi'], 6) : '0.000000'; ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if (isset($row['ranking']) && $row['ranking'] <= 2): ?>
                                        <span class="badge bg-success">Direkomendasikan</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Tidak Direkomendasikan</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top 3 Karyawan -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-star me-2"></i>Top 3 Karyawan Terbaik
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php
                            // Query terpisah untuk Top 3 agar tidak konflik dengan query utama
                            if ($has_results) {
                                $query_top3 = "SELECT h.*,
                                                      a.nama_alternatif,
                                                      a.kode_alternatif,
                                                      a.nip,
                                                      a.jabatan,
                                                      a.departemen
                                               FROM hasil_topsis h
                                               JOIN alternatif a ON h.id_alternatif = a.id
                                               ORDER BY h.ranking
                                               LIMIT 3";
                                $result_top3 = mysqli_query($koneksi, $query_top3);

                                if ($result_top3 && mysqli_num_rows($result_top3) > 0) {
                                    $top_count = 0;
                                    while ($row = mysqli_fetch_assoc($result_top3)) {
                                        $top_count++;
                            ?>
                            <div class="col-md-4 mb-3">
                                <div class="card border-<?php echo $top_count == 1 ? 'warning' : ($top_count == 2 ? 'secondary' : 'success'); ?> h-100">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <?php if ($top_count == 1): ?>
                                                <i class="fas fa-crown fa-3x text-warning"></i>
                                            <?php elseif ($top_count == 2): ?>
                                                <i class="fas fa-medal fa-3x text-secondary"></i>
                                            <?php else: ?>
                                                <i class="fas fa-award fa-3x text-success"></i>
                                            <?php endif; ?>
                                        </div>
                                        <h5 class="card-title"><?php echo $row['nama_alternatif']; ?></h5>
                                        <p class="card-text">
                                            <strong>NIP:</strong> <?php echo $row['nip']; ?><br>
                                            <strong>Jabatan:</strong> <?php echo $row['jabatan']; ?><br>
                                            <strong>Departemen:</strong> <?php echo $row['departemen']; ?>
                                        </p>
                                        <div class="mt-3">
                                            <span class="badge bg-primary fs-6">Ranking #<?php echo $row['ranking']; ?></span><br>
                                            <span class="badge bg-info fs-6 mt-2">Nilai: <?php echo number_format($row['nilai_preferensi'], 4); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                                    }
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- Belum ada hasil -->
        <div class="card shadow mb-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-calculator fa-5x text-muted mb-4"></i>
                <h4 class="text-muted">Belum Ada Hasil Perhitungan</h4>
                <p class="text-muted mb-4">Silakan lakukan perhitungan TOPSIS terlebih dahulu untuk melihat ranking karyawan.</p>
                <a href="proses.php?hitung=1" class="btn btn-primary btn-lg">
                    <i class="fas fa-play me-2"></i>Mulai Perhitungan TOPSIS
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.avatar {
    width: 40px;
    height: 40px;
}
.avatar-initial {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
    font-size: 14px;
}
</style>

<?php include 'templates/footer.php'; ?>