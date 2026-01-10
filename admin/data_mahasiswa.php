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
    $angkatan = (int)($_POST['angkatan'] ?? 0);
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
        $id_user = (int)$conn->insert_id;

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
    $angkatan = (int)($_POST['angkatan'] ?? 0);
    $username = $_POST['username'];
    $password = $_POST['password'];
    $id_user  = (int)$_POST['id_user'];

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

/* =====================================================
   FILTER / SEARCH (GET)
   - q        : cari NIM atau Nama
   - angkatan : filter angkatan (exact)
   ===================================================== */
$searchQ   = trim($_GET['q'] ?? '');
$angkatanF = trim($_GET['angkatan'] ?? '');
$angkatanFInt = ($angkatanF !== '' ? (int)$angkatanF : null);
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
<input type="text" name="jurusan" class="form-control" placeholder="contoh: Teknik Informatika">
</div>

<div class="col-md-6">
<label class="form-label">Angkatan</label>
<input type="number" name="angkatan" class="form-control" placeholder="contoh: 2024">
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
     TABEL DATA MAHASISWA + SEARCH
     ===================================================== -->
<div class="card shadow-sm">
<div class="card-body">

<!-- FORM CARI / FILTER -->
<form method="get" class="row g-2 align-items-end mb-3">
  <div class="col-md-6">
    <label class="form-label mb-1">Cari (NIM / Nama)</label>
    <input type="text" name="q" class="form-control"
           value="<?= htmlspecialchars($searchQ) ?>"
           placeholder="contoh: E1E1240 atau Dirga">
  </div>

  <div class="col-md-3">
    <label class="form-label mb-1">Angkatan</label>
    <input type="number" name="angkatan" class="form-control"
           value="<?= htmlspecialchars($angkatanF) ?>"
           placeholder="contoh: 2024">
  </div>

  <div class="col-md-3 d-flex gap-2">
    <button class="btn btn-outline-primary w-100" type="submit">Cari</button>
    <a class="btn btn-outline-secondary w-100" href="data_mahasiswa.php">Reset</a>
  </div>
</form>

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
// --- QUERY DENGAN FILTER (AMAN) ---
$sql = "
  SELECT m.*, u.username
  FROM mahasiswa m
  JOIN users u ON m.id_user = u.id_user
  WHERE 1=1
";

$types = "";
$params = [];

if ($searchQ !== "") {
    $sql .= " AND (m.nim LIKE ? OR m.nama LIKE ?)";
    $like = "%{$searchQ}%";
    $types .= "ss";
    $params[] = $like;
    $params[] = $like;
}

if ($angkatanFInt !== null && $angkatanF !== '') {
    $sql .= " AND m.angkatan = ?";
    $types .= "i";
    $params[] = $angkatanFInt;
}

$sql .= " ORDER BY m.nama ASC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo '<tr><td colspan="6" class="text-center text-danger">Query error: ' . htmlspecialchars($conn->error) . '</td></tr>';
} else {
    if ($types !== "") {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $q = $stmt->get_result();

    if ($q->num_rows === 0):
?>
<tr>
<td colspan="6" class="text-center text-muted">
Tidak ada data yang cocok dengan pencarian.
</td>
</tr>
<?php
    else:
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
'<?= (int)$r['angkatan'] ?>',
'<?= addslashes($r['username']) ?>',
'<?= (int)$r['id_user'] ?>'
)">
Edit
</button>
</td>
</tr>
<?php
        endwhile;
    endif;

    $stmt->close();
}
?>

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
