<?php
require_once "../auth/auth_check.php";
require_role('admin');
require_once "../config/db.php";

/* =====================================================
   PROSES TAMBAH MAHASISWA
   ===================================================== */
if (isset($_POST['tambah_mahasiswa'])) {

    $nim      = trim($_POST['nim']);
    $nama     = trim($_POST['nama']);
    $jurusan  = trim($_POST['jurusan']);
    $angkatan = trim($_POST['angkatan']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($nim && $nama && $username && $password) {

        // 1. Tambah user (role mahasiswa = 3)
        $stmt = $conn->prepare("
            INSERT INTO users (username, password, id_role)
            VALUES (?, ?, 3)
        ");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $id_user = $conn->insert_id;

        // 2. Tambah mahasiswa
        $stmt = $conn->prepare("
            INSERT INTO mahasiswa (nim, nama, jurusan, angkatan, id_user)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssii", $nim, $nama, $jurusan, $angkatan, $id_user);
        $stmt->execute();

        header("Location: data_mahasiswa.php?status=added");
        exit;
    }
}

/* =====================================================
   PROSES EDIT MAHASISWA
   ===================================================== */
if (isset($_POST['edit_mahasiswa'])) {

    $nim      = $_POST['nim'];
    $nama     = $_POST['nama'];
    $jurusan  = $_POST['jurusan'];
    $angkatan = $_POST['angkatan'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $id_user  = $_POST['id_user'];

    // Update tabel mahasiswa
    $stmt = $conn->prepare("
        UPDATE mahasiswa
        SET nama=?, jurusan=?, angkatan=?
        WHERE nim=?
    ");
    $stmt->bind_param("ssis", $nama, $jurusan, $angkatan, $nim);
    $stmt->execute();

    // Update tabel users
    if (!empty($password)) {
        $stmt = $conn->prepare("
            UPDATE users SET username=?, password=? WHERE id_user=?
        ");
        $stmt->bind_param("ssi", $username, $password, $id_user);
    } else {
        $stmt = $conn->prepare("
            UPDATE users SET username=? WHERE id_user=?
        ");
        $stmt->bind_param("si", $username, $id_user);
    }
    $stmt->execute();

    header("Location: data_mahasiswa.php?status=updated");
    exit;
}

/* =====================================================
   SETUP HALAMAN
   ===================================================== */
$current_page  = 'data_mahasiswa.php';
$page_title    = "Data Mahasiswa";
$asset_prefix  = "../";
$logout_prefix = "../";

include "../includes/layout_top.php";
include "../includes/sidebar_admin.php";
?>

<main class="pc-container">
<?php include "../includes/header.php"; ?>

<div class="pc-content">

<h3 class="fw-bold mb-3">Data Mahasiswa</h3>

<?php if (isset($_GET['status'])): ?>
<div class="alert alert-success">
    <?php
    if ($_GET['status'] === 'added')   echo "Mahasiswa berhasil ditambahkan.";
    if ($_GET['status'] === 'updated') echo "Data mahasiswa berhasil diperbarui.";
    ?>
</div>
<?php endif; ?>

<!-- =====================================================
     FORM TAMBAH MAHASISWA
     ===================================================== -->
<div class="card mb-4 shadow-sm">
<div class="card-body">
<h5 class="fw-bold mb-3">Tambah Mahasiswa Baru</h5>

<form method="post">
<input type="hidden" name="tambah_mahasiswa" value="1">

<div class="row g-3">

<div class="col-md-4">
<label class="form-label">NIM</label>
<input type="text" name="nim" class="form-control" required>
</div>

<div class="col-md-8">
<label class="form-label">Nama Mahasiswa</label>
<input type="text" name="nama" class="form-control" required>
</div>

<div class="col-md-6">
<label class="form-label">Jurusan</label>
<input type="text" name="jurusan" class="form-control">
</div>

<div class="col-md-6">
<label class="form-label">Angkatan</label>
<input type="number" name="angkatan" class="form-control">
</div>

<hr class="my-3">

<div class="col-md-6">
<label class="form-label">Username Login</label>
<input type="text" name="username" class="form-control" required>
</div>

<div class="col-md-6">
<label class="form-label">Password Login</label>
<input type="text" name="password" class="form-control" required>
</div>

</div>

<button class="btn btn-primary mt-3">
Simpan Mahasiswa
</button>
</form>
</div>
</div>

<!-- =====================================================
     TABEL DATA MAHASISWA
     ===================================================== -->
<div class="card shadow-sm">
<div class="card-body">

<div class="table-responsive">
<table class="table table-bordered table-hover align-middle">
<thead class="table-primary text-center">
<tr>
<th>NIM</th>
<th>Nama</th>
<th>Jurusan</th>
<th>Angkatan</th>
<th>Username</th>
<th width="12%">Aksi</th>
</tr>
</thead>
<tbody>

<?php
$q = $conn->query("
    SELECT m.*, u.username
    FROM mahasiswa m
    JOIN users u ON m.id_user = u.id_user
    ORDER BY m.nama ASC
");

if ($q->num_rows === 0):
?>
<tr>
<td colspan="6" class="text-center text-muted">
Belum ada data mahasiswa.
</td>
</tr>
<?php else:
while ($r = $q->fetch_assoc()):
?>
<tr>
<td><?= htmlspecialchars($r['nim']) ?></td>
<td><?= htmlspecialchars($r['nama']) ?></td>
<td><?= htmlspecialchars($r['jurusan'] ?: '-') ?></td>
<td><?= htmlspecialchars($r['angkatan'] ?: '-') ?></td>
<td><?= htmlspecialchars($r['username']) ?></td>
<td class="text-center">
<button class="btn btn-sm btn-warning"
onclick="editMahasiswa(
'<?= $r['nim'] ?>',
'<?= addslashes($r['nama']) ?>',
'<?= addslashes($r['jurusan']) ?>',
'<?= $r['angkatan'] ?>',
'<?= $r['username'] ?>',
'<?= $r['id_user'] ?>'
)">
Edit
</button>
</td>
</tr>
<?php endwhile; endif; ?>

</tbody>
</table>
</div>

</div>
</div>

</div>
</main>

<!-- =====================================================
     MODAL EDIT MAHASISWA
     ===================================================== -->
<div class="modal fade" id="modalEditMahasiswa" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<form method="post">

<div class="modal-header">
<h5 class="modal-title">Edit Data Mahasiswa</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<input type="hidden" name="edit_mahasiswa" value="1">
<input type="hidden" name="nim" id="e_nim">
<input type="hidden" name="id_user" id="e_id_user">

<div class="mb-2">
<label>Nama</label>
<input type="text" name="nama" id="e_nama" class="form-control" required>
</div>

<div class="mb-2">
<label>Jurusan</label>
<input type="text" name="jurusan" id="e_jurusan" class="form-control">
</div>

<div class="mb-2">
<label>Angkatan</label>
<input type="number" name="angkatan" id="e_angkatan" class="form-control">
</div>

<div class="mb-2">
<label>Username</label>
<input type="text" name="username" id="e_username" class="form-control" required>
</div>

<div class="mb-2">
<label>Password (kosongkan jika tidak diubah)</label>
<input type="text" name="password" class="form-control">
</div>

</div>

<div class="modal-footer">
<button class="btn btn-primary">Simpan Perubahan</button>
</div>

</form>
</div>
</div>
</div>

<script>
function editMahasiswa(nim,nama,jurusan,angkatan,username,id_user){
document.getElementById('e_nim').value = nim;
document.getElementById('e_nama').value = nama;
document.getElementById('e_jurusan').value = jurusan;
document.getElementById('e_angkatan').value = angkatan;
document.getElementById('e_username').value = username;
document.getElementById('e_id_user').value = id_user;
new bootstrap.Modal(document.getElementById('modalEditMahasiswa')).show();
}
</script>

<?php include "../includes/layout_bottom.php"; ?>
