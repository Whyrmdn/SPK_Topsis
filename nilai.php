<?php
$page_title = "Manajemen Nilai Alternatif";
include 'templates/header.php';

$message = '';
$error = '';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $id_alternatif = intval($_POST['id_alternatif']);
                $id_kriteria = intval($_POST['id_kriteria']);
                $nilai = floatval($_POST['nilai']);

                // Cek apakah sudah ada nilai untuk kombinasi alternatif-kriteria ini
                $check_query = "SELECT id FROM nilai_alternatif WHERE id_alternatif = $id_alternatif AND id_kriteria = $id_kriteria";
                $check_result = mysqli_query($koneksi, $check_query);

                if (mysqli_num_rows($check_result) > 0) {
                    $error = "Nilai untuk kombinasi karyawan dan kriteria ini sudah ada!";
                } else {
                    $query = "INSERT INTO nilai_alternatif (id_alternatif, id_kriteria, nilai) VALUES ($id_alternatif, $id_kriteria, $nilai)";
                    if (mysqli_query($koneksi, $query)) {
                        $message = "Nilai berhasil ditambahkan!";
                    } else {
                        $error = "Error: " . mysqli_error($koneksi);
                    }
                }
                break;

            case 'edit':
                $id = $_POST['id'];
                $nilai = floatval($_POST['nilai']);

                $query = "UPDATE nilai_alternatif SET nilai = $nilai WHERE id = $id";
                if (mysqli_query($koneksi, $query)) {
                    $message = "Nilai berhasil diupdate!";
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
    $query = "DELETE FROM nilai_alternatif WHERE id = $id";
    if (mysqli_query($koneksi, $query)) {
        $message = "Nilai berhasil dihapus!";
    } else {
        $error = "Error: " . mysqli_error($koneksi);
    }
}

// Ambil data nilai dengan join
$query = "SELECT na.*, a.nama_alternatif, a.kode_alternatif, k.nama_kriteria, k.kode_kriteria
          FROM nilai_alternatif na
          JOIN alternatif a ON na.id_alternatif = a.id
          JOIN kriteria k ON na.id_kriteria = k.id
          ORDER BY a.nama_alternatif, k.kode_kriteria";
$result = mysqli_query($koneksi, $query);

// Ambil data alternatif untuk dropdown
$query_alternatif = "SELECT * FROM alternatif ORDER BY nama_alternatif";
$result_alternatif = mysqli_query($koneksi, $query_alternatif);

// Ambil data kriteria untuk dropdown
$query_kriteria = "SELECT * FROM kriteria ORDER BY kode_kriteria";
$result_kriteria = mysqli_query($koneksi, $query_kriteria);

// Matrix view - ambil semua data untuk tampilan matrix
$query_matrix = "SELECT a.id as id_alternatif, a.nama_alternatif, a.kode_alternatif,
                        k.id as id_kriteria, k.nama_kriteria, k.kode_kriteria,
                        na.nilai, na.id as nilai_id
                 FROM alternatif a
                 CROSS JOIN kriteria k
                 LEFT JOIN nilai_alternatif na ON a.id = na.id_alternatif AND k.id = na.id_kriteria
                 ORDER BY a.nama_alternatif, k.kode_kriteria";
$result_matrix = mysqli_query($koneksi, $query_matrix);

// Organize matrix data
$matrix_data = [];
$kriteria_list = [];
while ($row = mysqli_fetch_assoc($result_matrix)) {
    $matrix_data[$row['id_alternatif']][$row['id_kriteria']] = $row;
    if (!in_array($row['kode_kriteria'], $kriteria_list)) {
        $kriteria_list[$row['id_kriteria']] = $row['kode_kriteria'] . ' - ' . $row['nama_kriteria'];
    }
}
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-star me-2"></i>Manajemen Nilai Alternatif
        </h1>
        <div>
            <button class="btn btn-success me-2" onclick="toggleView()">
                <i class="fas fa-table me-2"></i><span id="viewToggleText">Tampilan Matrix</span>
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNilaiModal">
                <i class="fas fa-plus me-2"></i>Tambah Nilai
            </button>
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

    <!-- List View -->
    <div id="listView" class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Nilai Alternatif</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Karyawan</th>
                            <th>Kriteria</th>
                            <th>Nilai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        mysqli_data_seek($result, 0); // Reset pointer
                        while ($row = mysqli_fetch_assoc($result)):
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-2"><?php echo $row['kode_alternatif']; ?></span>
                                    <?php echo $row['nama_alternatif']; ?>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-success me-2"><?php echo $row['kode_kriteria']; ?></span>
                                    <?php echo $row['nama_kriteria']; ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info fs-6"><?php echo number_format($row['nilai'], 2); ?></span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick="editNilai(<?php echo $row['id']; ?>, <?php echo $row['nilai']; ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="confirmDelete('nilai.php?delete=<?php echo $row['id']; ?>', 'Apakah Anda yakin ingin menghapus nilai ini?')">
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

    <!-- Matrix View -->
    <div id="matrixView" class="card shadow mb-4" style="display: none;">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Matrix Nilai Alternatif</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Alternatif</th>
                            <?php foreach ($kriteria_list as $kriteria): ?>
                                <th class="text-center"><?php echo $kriteria; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $alternatif_processed = [];
                        foreach ($matrix_data as $id_alternatif => $kriteria_values):
                            if (in_array($id_alternatif, $alternatif_processed)) continue;
                            $alternatif_processed[] = $id_alternatif;
                            $first_kriteria = reset($kriteria_values);
                        ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-2"><?php echo $first_kriteria['kode_alternatif']; ?></span>
                                    <strong><?php echo $first_kriteria['nama_alternatif']; ?></strong>
                                </div>
                            </td>
                            <?php foreach ($kriteria_list as $id_kriteria => $kriteria_name): ?>
                                <td class="text-center">
                                    <?php if (isset($kriteria_values[$id_kriteria]['nilai']) && $kriteria_values[$id_kriteria]['nilai'] !== null): ?>
                                        <span class="badge bg-success fs-6" onclick="editNilai(<?php echo $kriteria_values[$id_kriteria]['nilai_id']; ?>, <?php echo $kriteria_values[$id_kriteria]['nilai']; ?>)" style="cursor: pointer;">
                                            <?php echo number_format($kriteria_values[$id_kriteria]['nilai'], 2); ?>
                                        </span>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-primary" onclick="addNilaiQuick(<?php echo $id_alternatif; ?>, <?php echo $id_kriteria; ?>)">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Nilai Modal -->
<div class="modal fade" id="addNilaiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Tambah Nilai Alternatif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="id_alternatif" class="form-label">Karyawan</label>
                        <select class="form-control" name="id_alternatif" id="id_alternatif" required>
                            <option value="">Pilih Karyawan</option>
                            <?php
                            mysqli_data_seek($result_alternatif, 0);
                            while ($alt = mysqli_fetch_assoc($result_alternatif)):
                            ?>
                                <option value="<?php echo $alt['id']; ?>"><?php echo $alt['kode_alternatif']; ?> - <?php echo $alt['nama_alternatif']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="id_kriteria" class="form-label">Kriteria</label>
                        <select class="form-control" name="id_kriteria" id="id_kriteria" required>
                            <option value="">Pilih Kriteria</option>
                            <?php
                            mysqli_data_seek($result_kriteria, 0);
                            while ($krit = mysqli_fetch_assoc($result_kriteria)):
                            ?>
                                <option value="<?php echo $krit['id']; ?>"><?php echo $krit['kode_kriteria']; ?> - <?php echo $krit['nama_kriteria']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="nilai" class="form-label">Nilai (1-100)</label>
                        <input type="number" class="form-control" name="nilai" id="nilai" min="1" max="100" step="0.01" required>
                        <small class="text-muted">Masukkan nilai antara 1-100</small>
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

<!-- Edit Nilai Modal -->
<div class="modal fade" id="editNilaiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Edit Nilai
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_nilai_id">
                    <div class="mb-3">
                        <label for="edit_nilai" class="form-label">Nilai (1-100)</label>
                        <input type="number" class="form-control" name="nilai" id="edit_nilai" min="1" max="100" step="0.01" required>
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
let currentView = 'list';

function toggleView() {
    const listView = document.getElementById('listView');
    const matrixView = document.getElementById('matrixView');
    const toggleText = document.getElementById('viewToggleText');

    if (currentView === 'list') {
        listView.style.display = 'none';
        matrixView.style.display = 'block';
        toggleText.textContent = 'Tampilan List';
        currentView = 'matrix';
    } else {
        listView.style.display = 'block';
        matrixView.style.display = 'none';
        toggleText.textContent = 'Tampilan Matrix';
        currentView = 'list';
    }
}

function editNilai(id, nilai) {
    document.getElementById('edit_nilai_id').value = id;
    document.getElementById('edit_nilai').value = nilai;

    var editModal = new bootstrap.Modal(document.getElementById('editNilaiModal'));
    editModal.show();
}

function addNilaiQuick(id_alternatif, id_kriteria) {
    document.getElementById('id_alternatif').value = id_alternatif;
    document.getElementById('id_kriteria').value = id_kriteria;

    var addModal = new bootstrap.Modal(document.getElementById('addNilaiModal'));
    addModal.show();
}
</script>

<?php include 'templates/footer.php'; ?>