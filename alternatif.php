<?php
$page_title = "Manajemen Alternatif (Karyawan)";
include 'templates/header.php';

$message = '';
$error = '';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $kode_alternatif = mysqli_real_escape_string($koneksi, $_POST['kode_alternatif']);
                $nama_alternatif = mysqli_real_escape_string($koneksi, $_POST['nama_alternatif']);
                $nip = mysqli_real_escape_string($koneksi, $_POST['nip']);
                $jabatan = mysqli_real_escape_string($koneksi, $_POST['jabatan']);
                $departemen = mysqli_real_escape_string($koneksi, $_POST['departemen']);
                $masa_kerja = intval($_POST['masa_kerja']);

                $query = "INSERT INTO alternatif (kode_alternatif, nama_alternatif, nip, jabatan, departemen, masa_kerja) VALUES ('$kode_alternatif', '$nama_alternatif', '$nip', '$jabatan', '$departemen', $masa_kerja)";
                if (mysqli_query($koneksi, $query)) {
                    $message = "Karyawan berhasil ditambahkan!";
                } else {
                    $error = "Error: " . mysqli_error($koneksi);
                }
                break;

            case 'edit':
                $id = $_POST['id'];
                $kode_alternatif = mysqli_real_escape_string($koneksi, $_POST['kode_alternatif']);
                $nama_alternatif = mysqli_real_escape_string($koneksi, $_POST['nama_alternatif']);
                $nip = mysqli_real_escape_string($koneksi, $_POST['nip']);
                $jabatan = mysqli_real_escape_string($koneksi, $_POST['jabatan']);
                $departemen = mysqli_real_escape_string($koneksi, $_POST['departemen']);
                $masa_kerja = intval($_POST['masa_kerja']);

                $query = "UPDATE alternatif SET kode_alternatif='$kode_alternatif', nama_alternatif='$nama_alternatif', nip='$nip', jabatan='$jabatan', departemen='$departemen', masa_kerja=$masa_kerja WHERE id=$id";
                if (mysqli_query($koneksi, $query)) {
                    $message = "Data karyawan berhasil diupdate!";
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
    $query = "DELETE FROM alternatif WHERE id = $id";
    if (mysqli_query($koneksi, $query)) {
        $message = "Data karyawan berhasil dihapus!";
    } else {
        $error = "Error: " . mysqli_error($koneksi);
    }
}

// Ambil data alternatif
$query = "SELECT * FROM alternatif ORDER BY created_at DESC";
$result = mysqli_query($koneksi, $query);
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-tie me-2"></i>Manajemen Alternatif (Karyawan)
        </h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAlternatifModal">
            <i class="fas fa-plus me-2"></i>Tambah Karyawan
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

    <!-- DataTales -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Karyawan (Alternatif)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Karyawan</th>
                            <th>NIP</th>
                            <th>Jabatan</th>
                            <th>Departemen</th>
                            <th>Masa Kerja</th>
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
                            <td><span class="badge bg-primary"><?php echo $row['kode_alternatif']; ?></span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-3">
                                        <div class="avatar-initial bg-primary rounded-circle">
                                            <?php echo strtoupper(substr($row['nama_alternatif'], 0, 2)); ?>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0"><?php echo $row['nama_alternatif']; ?></h6>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo $row['nip']; ?></td>
                            <td><?php echo $row['jabatan']; ?></td>
                            <td><?php echo $row['departemen']; ?></td>
                            <td>
                                <span class="badge bg-info"><?php echo $row['masa_kerja']; ?> tahun</span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick="editAlternatif(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="confirmDelete('alternatif.php?delete=<?php echo $row['id']; ?>', 'Apakah Anda yakin ingin menghapus data karyawan ini?')">
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

<!-- Add Alternatif Modal -->
<div class="modal fade" id="addAlternatifModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2"></i>Tambah Karyawan Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="kode_alternatif" class="form-label">Kode Alternatif</label>
                        <input type="text" class="form-control" name="kode_alternatif" placeholder="Contoh: A1" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_alternatif" class="form-label">Nama Karyawan</label>
                        <input type="text" class="form-control" name="nama_alternatif" required>
                    </div>
                    <div class="mb-3">
                        <label for="nip" class="form-label">NIP</label>
                        <input type="text" class="form-control" name="nip" required>
                    </div>
                    <div class="mb-3">
                        <label for="jabatan" class="form-label">Jabatan</label>
                        <input type="text" class="form-control" name="jabatan" required>
                    </div>
                    <div class="mb-3">
                        <label for="departemen" class="form-label">Departemen</label>
                        <input type="text" class="form-control" name="departemen" required>
                    </div>
                    <div class="mb-3">
                        <label for="masa_kerja" class="form-label">Masa Kerja (tahun)</label>
                        <input type="number" class="form-control" name="masa_kerja" min="0" required>
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

<!-- Edit Alternatif Modal -->
<div class="modal fade" id="editAlternatifModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-edit me-2"></i>Edit Data Karyawan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label for="edit_kode_alternatif" class="form-label">Kode Alternatif</label>
                        <input type="text" class="form-control" name="kode_alternatif" id="edit_kode_alternatif" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nama_alternatif" class="form-label">Nama Karyawan</label>
                        <input type="text" class="form-control" name="nama_alternatif" id="edit_nama_alternatif" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nip" class="form-label">NIP</label>
                        <input type="text" class="form-control" name="nip" id="edit_nip" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_jabatan" class="form-label">Jabatan</label>
                        <input type="text" class="form-control" name="jabatan" id="edit_jabatan" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_departemen" class="form-label">Departemen</label>
                        <input type="text" class="form-control" name="departemen" id="edit_departemen" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_masa_kerja" class="form-label">Masa Kerja (tahun)</label>
                        <input type="number" class="form-control" name="masa_kerja" id="edit_masa_kerja" min="0" required>
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

<script>
function editAlternatif(alternatif) {
    document.getElementById('edit_id').value = alternatif.id;
    document.getElementById('edit_kode_alternatif').value = alternatif.kode_alternatif;
    document.getElementById('edit_nama_alternatif').value = alternatif.nama_alternatif;
    document.getElementById('edit_nip').value = alternatif.nip;
    document.getElementById('edit_jabatan').value = alternatif.jabatan;
    document.getElementById('edit_departemen').value = alternatif.departemen;
    document.getElementById('edit_masa_kerja').value = alternatif.masa_kerja;

    var editModal = new bootstrap.Modal(document.getElementById('editAlternatifModal'));
    editModal.show();
}
</script>

<?php include 'templates/footer.php'; ?>