<?php
session_start();
require_once "../config/db.php";
require_once "../helpers/log_helper.php";

/* =========================
   DEBUG LOGGER (opsional)
   ========================= */
$DEBUG_LOGIN = true;
function login_debug_log($msg) {
    global $DEBUG_LOGIN;
    if (!$DEBUG_LOGIN) return;

    $dir = __DIR__ . '/../logs';
    if (!is_dir($dir)) @mkdir($dir, 0755, true);

    $file = $dir . '/login_debug.log';
    $time = date('Y-m-d H:i:s');
    file_put_contents($file, "[{$time}] {$msg}\n", FILE_APPEND | LOCK_EX);
}

/* =========================
   INPUT
   ========================= */
$role     = $_POST['role'] ?? '';
$identity = trim($_POST['identity'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($role === '' || $identity === '' || $password === '') {
    header("Location: login_form.php?error=empty");
    exit;
}

if (!in_array($role, ['admin','dosen','mahasiswa'], true)) {
    header("Location: login_form.php?error=role");
    exit;
}

/* =========================
   HELPER VERIFY PASSWORD
   ========================= */
function verify_pw(string $input, string $stored): bool {
    // Jika stored adalah hash bcrypt/argon -> password_verify akan true/false
    if (password_verify($input, $stored)) return true;

    // fallback (kalau database masih simpan plaintext)
    return hash_equals($stored, $input);
}

/* =========================
   QUERY BY ROLE (WAJIB cocok id_role)
   ========================= */
$user = null;

if ($role === 'admin') {

    $stmt = $conn->prepare("
        SELECT u.id_user, u.username, u.password, u.id_role
        FROM users u
        WHERE u.username = ? AND u.id_role = 1
        LIMIT 1
    ");
    $stmt->bind_param("s", $identity);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    login_debug_log("admin | identity={$identity} | found=" . ($user ? 1 : 0));

} elseif ($role === 'dosen') {

    $stmt = $conn->prepare("
        SELECT u.id_user, u.username, u.password, u.id_role, d.nidn
        FROM users u
        LEFT JOIN dosen d ON d.id_user = u.id_user
        WHERE u.id_role = 2
          AND (u.username = ? OR d.nidn = ?)
        LIMIT 1
    ");
    $stmt->bind_param("ss", $identity, $identity);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    login_debug_log("dosen | identity={$identity} | found=" . ($user ? 1 : 0) . " | nidn=" . ($user['nidn'] ?? '-'));

} else { // mahasiswa

    $stmt = $conn->prepare("
        SELECT u.id_user, u.username, u.password, u.id_role, m.nim
        FROM users u
        LEFT JOIN mahasiswa m ON m.id_user = u.id_user
        WHERE u.id_role = 3
          AND (u.username = ? OR m.nim = ?)
        LIMIT 1
    ");
    $stmt->bind_param("ss", $identity, $identity);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    login_debug_log("mahasiswa | identity={$identity} | found=" . ($user ? 1 : 0) . " | nim=" . ($user['nim'] ?? '-'));
}

/* =========================
   VALIDASI PASSWORD
   ========================= */
if (!$user) {
    header("Location: login_form.php?error=invalid");
    exit;
}

$storedPw = $user['password'] ?? '';
$ok = verify_pw($password, $storedPw);

login_debug_log("role={$role} | id_user={$user['id_user']} | id_role={$user['id_role']} | pw_ok=" . ($ok ? 1 : 0));

if (!$ok) {
    header("Location: login_form.php?error=invalid");
    exit;
}

/* =========================
   SET SESSION (BERSIH + AMAN)
   ========================= */
session_regenerate_id(true);

// reset session penting agar tidak nyangkut dari login sebelumnya
unset($_SESSION['nim'], $_SESSION['nidn']);

$_SESSION['user_id']  = (int)$user['id_user'];
$_SESSION['username'] = $user['username'];
$_SESSION['id_role']  = (int)$user['id_role'];

/* =========================
   REDIRECT BY ROLE
   ========================= */
if ($_SESSION['id_role'] === 1) {
    log_activity($conn, "Admin login ke sistem");
    header("Location: ../admin/dashboard_admin.php");
    exit;
}

if ($_SESSION['id_role'] === 2) {
    $_SESSION['nidn'] = $user['nidn'] ?? null;
    log_activity($conn, "Dosen login ke sistem");
    header("Location: ../dosen/dashboard_dosen.php");
    exit;
}

if ($_SESSION['id_role'] === 3) {
    $_SESSION['nim'] = $user['nim'] ?? null;
    log_activity($conn, "Mahasiswa login ke sistem");
    header("Location: ../mahasiswa/dashboard_mahasiswa.php");
    exit;
}

// fallback (kalau ada role tak dikenal)
header("Location: login_form.php?error=role");
exit;
