<?php
$page_title = "Manajemen Kriteria";
include 'templates/header.php';

$message = '';
$error = '';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $kode_kriteria = mysqli_real_escape_string($koneksi, $_POST['kode_kriteria']);
                $nama_kriteria = mysqli_real_escape_string($koneksi, $_POST['nama_kriteria']);
                $bobot = floatval($_POST['bobot']);
                $tipe = mysqli_real_escape_string($koneksi, $_POST['tipe']);
                $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

                $query = "INSERT INTO kriteria (kode_kriteria, nama_kriteria, bobot, tipe, deskripsi) VALUES ('$kode_kriteria', '$nama_kriteria', $bobot, '$tipe', '$deskripsi')";
                if (mysqli_query($koneksi, $query)) {
                    $message = "Kriteria berhasil ditambahkan!";
                } else {
                    $error = "Error: " . mysqli_error($koneksi);
                }
                break;

            case 'edit':
                $id = $_POST['id'];
                $kode_kriteria = mysqli_real_escape_string($koneksi, $_POST['kode_kriteria']);
                $nama_kriteria = mysqli_real_escape_string($koneksi, $_POST['nama_kriteria']);
                $bobot = floatval($_POST['bobot']);
                $tipe = mysqli_real_escape_string($koneksi, $_POST['tipe']);
                $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

                $query = "UPDATE kriteria SET kode_kriteria='$kode_kriteria', nama_kriteria='$nama_kriteria', bobot=$bobot, tipe='$tipe', deskripsi='$deskripsi' WHERE id=$id";
                if (mysqli_query($koneksi, $query)) {
                    $message = "Kriteria berhasil diupdate!";
                } else {
                    $error = "Error: " . mysqli_error($koneksi);
                }
                break;
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM kriteria WHERE id = $id";
    if (mysqli_query($koneksi, $query)) {
        $message = "Kriteria berhasil dihapus!";
    } else {
        $error = "Error: " . mysqli_error($koneksi);
    }
}

// Ambil data kriteria
$query = "SELECT * FROM kriteria ORDER BY kode_kriteria";
$result = mysqli_query($koneksi, $query);

// Hitung total bobot
$query_total = "SELECT SUM(bobot) as total_bobot FROM kriteria";
$result_total = mysqli_query($koneksi, $query_total);
$total_bobot_result = mysqli_fetch_assoc($result_total)['total_bobot'];
$total_bobot = $total_bobot_result !== null ? floatval($total_bobot_result) : 0;
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-list-check me-2"></i>Manajemen Kriteria
        </h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addKriteriaModal">
            <i class="fas fa-plus me-2"></i>Tambah Kriteria
        </button>
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

    <!-- Info Total Bobot -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-<?php echo $total_bobot == 1 ? 'success' : 'warning'; ?> shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-<?php echo $total_bobot == 1 ? 'success' : 'warning'; ?> text-uppercase mb-1">
                                Total Bobot Kriteria
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($total_bobot, 3); ?></div>
                            <?php if ($total_bobot != 1): ?>
                                <small class="text-warning">⚠️ Total bobot harus = 1.000</small>
                            <?php else: ?>
                                <small class="text-success">✅ Total bobot sudah sesuai</small>
                            <?php endif; ?>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTales -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Kriteria Penilaian</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Kriteria</th>
                            <th>Bobot</th>
                            <th>Tipe</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)):
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><span class="badge bg-primary"><?php echo $row['kode_kriteria']; ?></span></td>
                            <td><?php echo $row['nama_kriteria']; ?></td>
                            <td>
                                <span class="badge bg-info"><?php echo number_format(floatval($row['bobot']), 3); ?></span>
                                <small class="text-muted">(<?php echo number_format(floatval($row['bobot']) * 100, 1); ?>%)</small>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $row['tipe'] == 'Benefit' ? 'success' : 'danger'; ?>">
                                    <?php echo $row['tipe']; ?>
                                </span>
                            </td>
                            <td><?php echo $row['deskripsi']; ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick="editKriteria(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="confirmDelete('kriteria.php?delete=<?php echo $row['id']; ?>', 'Apakah Anda yakin ingin menghapus kriteria ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Kriteria Modal -->
<div class="modal fade" id="addKriteriaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Tambah Kriteria Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="kode_kriteria" class="form-label">Kode Kriteria</label>
                        <input type="text" class="form-control" name="kode_kriteria" placeholder="Contoh: C1" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_kriteria" class="form-label">Nama Kriteria</label>
                        <input type="text" class="form-control" name="nama_kriteria" required>
                    </div>
                    <div class="mb-3">
                        <label for="bobot" class="form-label">Bobot (0-1)</label>
                        <input type="number" class="form-control" name="bobot" step="0.001" min="0" max="1" required>
                        <small class="text-muted">Contoh: 0.250 untuk 25%</small>
                    </div>
                    <div class="mb-3">
                        <label for="tipe" class="form-label">Tipe Kriteria</label>
                        <select class="form-control" name="tipe" required>
                            <option value="Benefit">Benefit (Semakin tinggi semakin baik)</option>
                            <option value="Cost">Cost (Semakin rendah semakin baik)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Kriteria Modal -->
<div class="modal fade" id="editKriteriaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Edit Kriteria
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label for="edit_kode_kriteria" class="form-label">Kode Kriteria</label>
                        <input type="text" class="form-control" name="kode_kriteria" id="edit_kode_kriteria" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nama_kriteria" class="form-label">Nama Kriteria</label>
                        <input type="text" class="form-control" name="nama_kriteria" id="edit_nama_kriteria" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_bobot" class="form-label">Bobot (0-1)</label>
                        <input type="number" class="form-control" name="bobot" id="edit_bobot" step="0.001" min="0" max="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_tipe" class="form-label">Tipe Kriteria</label>
                        <select class="form-control" name="tipe" id="edit_tipe" required>
                            <option value="Benefit">Benefit (Semakin tinggi semakin baik)</option>
                            <option value="Cost">Cost (Semakin rendah semakin baik)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" id="edit_deskripsi" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editKriteria(kriteria) {
    document.getElementById('edit_id').value = kriteria.id;
    document.getElementById('edit_kode_kriteria').value = kriteria.kode_kriteria;
    document.getElementById('edit_nama_kriteria').value = kriteria.nama_kriteria;
    document.getElementById('edit_bobot').value = kriteria.bobot;
    document.getElementById('edit_tipe').value = kriteria.tipe;
    document.getElementById('edit_deskripsi').value = kriteria.deskripsi;

    var editModal = new bootstrap.Modal(document.getElementById('editKriteriaModal'));
    editModal.show();
}
</script>

<?php include 'templates/footer.php'; ?>