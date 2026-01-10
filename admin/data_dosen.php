<?php
require_once "../auth/auth_check.php";
require_role('admin');
require_once "../config/db.php";

/* =====================================================
   DETEKSI: dosen pakai id_peminatan atau peminatan (varchar)
   ===================================================== */
$useIdPeminatan = false;
$cek = $conn->prepare("
    SELECT COUNT(*) AS cnt
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'dosen'
      AND COLUMN_NAME  = 'id_peminatan'
");
$cek->execute();
$row = $cek->get_result()->fetch_assoc();
$cek->close();
$useIdPeminatan = ((int)($row['cnt'] ?? 0) > 0);

/* =====================================================
   AMBIL MASTER PEMINATAN UNTUK DROPDOWN
   ===================================================== */
$peminatanList = []; // [kode => nama]
$res = $conn->query("SELECT kode, nama FROM peminatan ORDER BY kode ASC");
while ($r = $res->fetch_assoc()) {
    $kode = trim($r['kode']);
    $peminatanList[$kode] = $r['nama'];
}

/* Helper: validasi kode peminatan */
function validPeminatan(string $kode, array $list): bool {
    return isset($list[$kode]);
}

/* Helper: ambil id_peminatan dari kode (jika pakai relasi) */
function getIdPeminatan(mysqli $conn, string $kode): ?int {
    $stmt = $conn->prepare("SELECT id_peminatan FROM peminatan WHERE kode = ? LIMIT 1");
    $stmt->bind_param("s", $kode);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$row) return null;
    return (int)$row['id_peminatan'];
}

$error = "";

/* =====================================================
   PROSES TAMBAH DOSEN
   ===================================================== */
if (isset($_POST['tambah_dosen'])) {

    $nidn      = trim($_POST['nidn'] ?? '');
    $nip       = trim($_POST['nip'] ?? '');
    $nama      = trim($_POST['nama'] ?? '');
    $jurusan   = trim($_POST['jurusan'] ?? '');
    $pemKode   = trim($_POST['peminatan'] ?? ''); // simpan kode (RPL/KBJ/KCV)
    $username  = trim($_POST['username'] ?? '');
    $password  = trim($_POST['password'] ?? '');

    if ($nidn === '' || $nama === '' || $username === '' || $password === '') {
        $error = "NIDN, Nama, Username, dan Password wajib diisi.";
    } elseif (!validPeminatan($pemKode, $peminatanList)) {
        $error = "Peminatan tidak valid. Pilih dari daftar.";
    }

    if ($error === "") {
        $conn->begin_transaction();

        try {
            // (1) Insert user DOSEN (role dosen = 2)
            $stmt = $conn->prepare("
                INSERT INTO users (username, password, id_role)
                VALUES (?, ?, 2)
            ");
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $stmt->close();
            $id_user = (int)$conn->insert_id;

            // (2) Insert dosen
            if ($useIdPeminatan) {
                $idPem = getIdPeminatan($conn, $pemKode);
                if ($idPem === null) {
                    throw new Exception("Kode peminatan tidak ditemukan di tabel peminatan.");
                }
                $stmt = $conn->prepare("
                    INSERT INTO dosen (nidn, nip, nama, jurusan, id_peminatan, id_user)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->bind_param("ssssii", $nidn, $nip, $nama, $jurusan, $idPem, $id_user);
            } else {
                // simpan kode peminatan ke kolom dosen.peminatan (varchar)
                $stmt = $conn->prepare("
                    INSERT INTO dosen (nidn, nip, nama, jurusan, peminatan, id_user)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->bind_param("sssssi", $nidn, $nip, $nama, $jurusan, $pemKode, $id_user);
            }

            $stmt->execute();
            $stmt->close();

            $conn->commit();
            header("Location: data_dosen.php?status=added");
            exit;

        } catch (Throwable $e) {
            $conn->rollback();
            $error = "Gagal menambahkan dosen: " . $e->getMessage();
        }
    }
}

/* =====================================================
   PROSES EDIT DOSEN
   ===================================================== */
if (isset($_POST['edit_dosen'])) {

    $nidn      = trim($_POST['nidn'] ?? '');
    $nip       = trim($_POST['nip'] ?? '');
    $nama      = trim($_POST['nama'] ?? '');
    $jurusan   = trim($_POST['jurusan'] ?? '');
    $pemKode   = trim($_POST['peminatan'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $password  = trim($_POST['password'] ?? '');
    $id_user   = (int)($_POST['id_user'] ?? 0);

    if ($nidn === '' || $nama === '' || $username === '' || $id_user <= 0) {
        $error = "Data edit tidak lengkap.";
    } elseif (!validPeminatan($pemKode, $peminatanList)) {
        $error = "Peminatan tidak valid. Pilih dari daftar.";
    }

    if ($error === "") {
        $conn->begin_transaction();

        try {
            // Update dosen
            if ($useIdPeminatan) {
                $idPem = getIdPeminatan($conn, $pemKode);
                if ($idPem === null) {
                    throw new Exception("Kode peminatan tidak ditemukan di tabel peminatan.");
                }
                $stmt = $conn->prepare("
                    UPDATE dosen
                    SET nip=?, nama=?, jurusan=?, id_peminatan=?
                    WHERE nidn=?
                ");
                $stmt->bind_param("sssis", $nip, $nama, $jurusan, $idPem, $nidn);
            } else {
                $stmt = $conn->prepare("
                    UPDATE dosen
                    SET nip=?, nama=?, jurusan=?, peminatan=?
                    WHERE nidn=?
                ");
                $stmt->bind_param("sssss", $nip, $nama, $jurusan, $pemKode, $nidn);
            }
            $stmt->execute();
            $stmt->close();

            // Update users
            if ($password !== '') {
                $stmt = $conn->prepare("UPDATE users SET username=?, password=? WHERE id_user=?");
                $stmt->bind_param("ssi", $username, $password, $id_user);
            } else {
                $stmt = $conn->prepare("UPDATE users SET username=? WHERE id_user=?");
                $stmt->bind_param("si", $username, $id_user);
            }
            $stmt->execute();
            $stmt->close();

            $conn->commit();
            header("Location: data_dosen.php?status=updated");
            exit;

        } catch (Throwable $e) {
            $conn->rollback();
            $error = "Gagal memperbarui dosen: " . $e->getMessage();
        }
    }
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

<?php if ($error): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

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

  <div class="col-md-4">
    <label class="form-label">NIP</label>
    <input type="text" name="nip" class="form-control" placeholder="Opsional">
  </div>

  <div class="col-md-8">
    <label class="form-label">Nama Dosen</label>
    <input type="text" name="nama" class="form-control" required>
  </div>

  <div class="col-md-6">
    <label class="form-label">Jurusan</label>
    <input type="text" name="jurusan" class="form-control" value="Teknik Informatika">
  </div>

  <div class="col-md-6">
    <label class="form-label">Peminatan</label>
    <select name="peminatan" class="form-select" required>
      <option value="">-- Pilih Peminatan --</option>
      <?php foreach ($peminatanList as $kode => $nama): ?>
        <option value="<?= htmlspecialchars($kode) ?>">
          <?= htmlspecialchars($kode . " — " . $nama) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <hr class="my-3">

  <div class="col-md-6">
    <label class="form-label">Username Login</label>
    <input type="text" name="username" class="form-control" required>
    <small class="text-muted">Saran: gunakan NIDN sebagai username.</small>
  </div>

  <div class="col-md-6">
    <label class="form-label">Password Login</label>
    <input type="text" name="password" class="form-control" required>
  </div>

</div>

<button class="btn btn-primary mt-3">Simpan Dosen</button>
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
  <th>NIP</th>
  <th>Nama</th>
  <th>Jurusan</th>
  <th>Peminatan</th>
  <th>Username</th>
  <th width="12%">Aksi</th>
</tr>
</thead>
<tbody>

<?php
if ($useIdPeminatan) {
    $q = $conn->query("
        SELECT d.*, u.username,
               p.kode AS pem_kode, p.nama AS pem_nama
        FROM dosen d
        JOIN users u ON d.id_user = u.id_user
        LEFT JOIN peminatan p ON d.id_peminatan = p.id_peminatan
        ORDER BY d.nama ASC
    ");
} else {
    // d.peminatan berisi kode (RPL/KBJ/KCV). Tampilkan nama dari tabel peminatan.
    $q = $conn->query("
        SELECT d.*, u.username,
               p.kode AS pem_kode, p.nama AS pem_nama
        FROM dosen d
        JOIN users u ON d.id_user = u.id_user
        LEFT JOIN peminatan p ON p.kode = d.peminatan
        ORDER BY d.nama ASC
    ");
}

if ($q->num_rows === 0):
?>
<tr>
  <td colspan="7" class="text-center text-muted">Belum ada data dosen.</td>
</tr>
<?php else:
while ($r = $q->fetch_assoc()):
  $pemKode = $r['pem_kode'] ?: ($r['peminatan'] ?? '');
  $pemNama = $r['pem_nama'] ?: '';
  $pemLabel = trim($pemKode . ($pemNama ? " — " . $pemNama : ""));
?>
<tr>
  <td><?= htmlspecialchars($r['nidn']) ?></td>
  <td><?= htmlspecialchars($r['nip'] ?: '-') ?></td>
  <td><?= htmlspecialchars($r['nama']) ?></td>
  <td><?= htmlspecialchars($r['jurusan'] ?: '-') ?></td>
  <td><?= htmlspecialchars($pemLabel ?: '-') ?></td>
  <td><?= htmlspecialchars($r['username']) ?></td>
  <td class="text-center">
    <button class="btn btn-sm btn-warning"
      onclick="editDosen(
        '<?= htmlspecialchars($r['nidn'], ENT_QUOTES) ?>',
        '<?= htmlspecialchars($r['nip'] ?? '', ENT_QUOTES) ?>',
        '<?= htmlspecialchars($r['nama'], ENT_QUOTES) ?>',
        '<?= htmlspecialchars($r['jurusan'] ?? '', ENT_QUOTES) ?>',
        '<?= htmlspecialchars($pemKode ?? '', ENT_QUOTES) ?>',
        '<?= htmlspecialchars($r['username'], ENT_QUOTES) ?>',
        '<?= (int)$r['id_user'] ?>'
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
    <label>NIP</label>
    <input type="text" name="nip" id="e_nip" class="form-control" placeholder="Opsional">
  </div>

  <div class="mb-2">
    <label>Nama</label>
    <input type="text" name="nama" id="e_nama" class="form-control" required>
  </div>

  <div class="mb-2">
    <label>Jurusan</label>
    <input type="text" name="jurusan" id="e_jurusan" class="form-control">
  </div>

  <div class="mb-2">
    <label>Peminatan</label>
    <select name="peminatan" id="e_peminatan" class="form-select" required>
      <option value="">-- Pilih Peminatan --</option>
      <?php foreach ($peminatanList as $kode => $nama): ?>
        <option value="<?= htmlspecialchars($kode) ?>">
          <?= htmlspecialchars($kode . " — " . $nama) ?>
        </option>
      <?php endforeach; ?>
    </select>
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
function editDosen(nidn, nip, nama, jurusan, peminatanKode, username, id_user){
  document.getElementById('e_nidn').value = nidn;
  document.getElementById('e_nip').value = nip || '';
  document.getElementById('e_nama').value = nama;
  document.getElementById('e_jurusan').value = jurusan || '';
  document.getElementById('e_username').value = username;
  document.getElementById('e_id_user').value = id_user;

  // set select peminatan berdasarkan kode
  const sel = document.getElementById('e_peminatan');
  sel.value = peminatanKode || '';

  new bootstrap.Modal(document.getElementById('modalEdit')).show();
}
</script>

<?php include "../includes/layout_bottom.php"; ?>
