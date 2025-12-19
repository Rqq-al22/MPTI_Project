<?php
require_once "../auth/auth_check.php";
require_role('admin');
require_once "../config/db.php";

/* =====================================================
   PROSES TAMBAH DOSEN
   ===================================================== */
if (isset($_POST['tambah_dosen'])) {

    $nidn     = trim($_POST['nidn']);
    $nama     = trim($_POST['nama']);
    $jurusan  = trim($_POST['jurusan']);
    $keahlian = trim($_POST['keahlian']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($nidn && $nama && $username && $password) {

        // 1. Tambah user (role DOSEN = 2)
        $stmt = $conn->prepare("
            INSERT INTO users (username, password, id_role)
            VALUES (?, ?, 2)
        ");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $id_user = $conn->insert_id;

        // 2. Tambah dosen
        $stmt = $conn->prepare("
            INSERT INTO dosen (nidn, nama, jurusan, keahlian, id_user)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssi", $nidn, $nama, $jurusan, $keahlian, $id_user);
        $stmt->execute();

        header("Location: data_dosen.php?status=added");
        exit;
    }
}

/* =====================================================
   PROSES EDIT DOSEN
   ===================================================== */
if (isset($_POST['edit_dosen'])) {

    $nidn     = $_POST['nidn'];
    $nama     = $_POST['nama'];
    $jurusan  = $_POST['jurusan'];
    $keahlian = $_POST['keahlian'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $id_user  = $_POST['id_user'];

    // Update tabel dosen
    $stmt = $conn->prepare("
        UPDATE dosen
        SET nama=?, jurusan=?, keahlian=?
        WHERE nidn=?
    ");
    $stmt->bind_param("ssss", $nama, $jurusan, $keahlian, $nidn);
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

    header("Location: data_dosen.php?status=updated");
    exit;
}

/* =====================================================
   SETUP HALAMAN
   ===================================================== */
$current_page  = 'data_dosen.php';
$page_title    = "Data Dosen";
$asset_prefix  = "../";
$logout_prefix = "../";

include "../includes/layout_top.php";
include "../includes/sidebar_admin.php";
?>

<main class="pc-container">
<?php include "../includes/header.php"; ?>

<div class="pc-content">

<h3 class="fw-bold mb-3">Data Dosen</h3>

<?php if (isset($_GET['status'])): ?>
<div class="alert alert-success">
    <?php
    if ($_GET['status'] === 'added')   echo "Dosen berhasil ditambahkan.";
    if ($_GET['status'] === 'updated') echo "Data dosen berhasil diperbarui.";
    ?>
</div>
<?php endif; ?>

<!-- =====================================================
     FORM TAMBAH DOSEN
     ===================================================== -->
<div class="card mb-4 shadow-sm">
<div class="card-body">
<h5 class="fw-bold mb-3">Tambah Dosen Baru</h5>

<form method="post">
<input type="hidden" name="tambah_dosen" value="1">

<div class="row g-3">

<div class="col-md-4">
<label class="form-label">NIDN</label>
<input type="text" name="nidn" class="form-control" required>
</div>

<div class="col-md-8">
<label class="form-label">Nama Dosen</label>
<input type="text" name="nama" class="form-control" required>
</div>

<div class="col-md-6">
<label class="form-label">Jurusan</label>
<input type="text" name="jurusan" class="form-control">
</div>

<div class="col-md-6">
<label class="form-label">Keahlian</label>
<input type="text" name="keahlian" class="form-control">
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
Simpan Dosen
</button>
</form>
</div>
</div>

<!-- =====================================================
     TABEL DATA DOSEN
     ===================================================== -->
<div class="card shadow-sm">
<div class="card-body">

<div class="table-responsive">
<table class="table table-bordered table-hover align-middle">
<thead class="table-primary text-center">
<tr>
<th>NIDN</th>
<th>Nama</th>
<th>Jurusan</th>
<th>Keahlian</th>
<th>Username</th>
<th width="12%">Aksi</th>
</tr>
</thead>
<tbody>

<?php
$q = $conn->query("
    SELECT d.*, u.username
    FROM dosen d
    JOIN users u ON d.id_user = u.id_user
    ORDER BY d.nama ASC
");

if ($q->num_rows === 0):
?>
<tr>
<td colspan="6" class="text-center text-muted">
Belum ada data dosen.
</td>
</tr>
<?php else:
while ($r = $q->fetch_assoc()):
?>
<tr>
<td><?= htmlspecialchars($r['nidn']) ?></td>
<td><?= htmlspecialchars($r['nama']) ?></td>
<td><?= htmlspecialchars($r['jurusan'] ?: '-') ?></td>
<td><?= htmlspecialchars($r['keahlian'] ?: '-') ?></td>
<td><?= htmlspecialchars($r['username']) ?></td>
<td class="text-center">
<button class="btn btn-sm btn-warning"
onclick="editDosen(
'<?= $r['nidn'] ?>',
'<?= addslashes($r['nama']) ?>',
'<?= addslashes($r['jurusan']) ?>',
'<?= addslashes($r['keahlian']) ?>',
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
     MODAL EDIT DOSEN
     ===================================================== -->
<div class="modal fade" id="modalEdit" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<form method="post">

<div class="modal-header">
<h5 class="modal-title">Edit Data Dosen</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<input type="hidden" name="edit_dosen" value="1">
<input type="hidden" name="nidn" id="e_nidn">
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
<label>Keahlian</label>
<input type="text" name="keahlian" id="e_keahlian" class="form-control">
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
function editDosen(nidn,nama,jurusan,keahlian,username,id_user){
document.getElementById('e_nidn').value = nidn;
document.getElementById('e_nama').value = nama;
document.getElementById('e_jurusan').value = jurusan;
document.getElementById('e_keahlian').value = keahlian;
document.getElementById('e_username').value = username;
document.getElementById('e_id_user').value = id_user;
new bootstrap.Modal(document.getElementById('modalEdit')).show();
}
</script>

<?php include "../includes/layout_bottom.php"; ?>
