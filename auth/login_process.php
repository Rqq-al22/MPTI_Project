<?php
session_start();
require_once "../config/db.php";
require_once "../helpers/log_helper.php";

// DEBUG: aktifkan untuk merekam detail proses login lokal (Hapus/Matikan di produksi)
$DEBUG_LOGIN = true;
function login_debug_log($msg) {
    global $DEBUG_LOGIN;
    if (!$DEBUG_LOGIN) return;
    $dir = __DIR__ . '/../logs';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    $file = $dir . '/login_debug.log';
    $time = date('Y-m-d H:i:s');
    $entry = "[{$time}] " . $msg . PHP_EOL;
    file_put_contents($file, $entry, FILE_APPEND | LOCK_EX);
}

$role     = $_POST['role'] ?? '';
$identity = trim($_POST['identity'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($role === '' || $identity === '' || $password === '') {
    header("Location: login_form.php?error=empty");
    exit;
}

// --- Fast path: lookup by username first (accept username for any role) ---
$stmtU = $conn->prepare("SELECT id_user, username, password, id_role FROM users WHERE username = ? LIMIT 1");
if ($stmtU) {
    $stmtU->bind_param("s", $identity);
    $stmtU->execute();
    $urow = $stmtU->get_result()->fetch_assoc();
    if ($urow) {
        login_debug_log("username-first | identity={$identity} | found_user=1 | id_user={$urow['id_user']} | id_role={$urow['id_role']}");

        if (password_verify($password, $urow['password']) || $password === $urow['password']) {
            // password ok -> fill role-specific identifiers if needed
            if ($urow['id_role'] == 3) {
                $q = $conn->prepare("SELECT nim FROM mahasiswa WHERE id_user = ? LIMIT 1");
                if ($q) {
                    $q->bind_param("i", $urow['id_user']);
                    $q->execute();
                    $r = $q->get_result()->fetch_assoc();
                    $nim = $r['nim'] ?? null;
                }
                $_SESSION['nim'] = $nim ?? null;
                $_SESSION['id_role'] = 3;
                $_SESSION['user_id'] = $urow['id_user'];
                $_SESSION['username'] = $urow['username'];
                log_activity($conn, "Mahasiswa login ke sistem");
                header("Location: ../mahasiswa/dashboard_mahasiswa.php");
                exit;
            } elseif ($urow['id_role'] == 2) {
                $q = $conn->prepare("SELECT nidn FROM dosen WHERE id_user = ? LIMIT 1");
                if ($q) {
                    $q->bind_param("i", $urow['id_user']);
                    $q->execute();
                    $r = $q->get_result()->fetch_assoc();
                    $nidn = $r['nidn'] ?? null;
                }
                $_SESSION['nidn'] = $nidn ?? null;
                $_SESSION['id_role'] = 2;
                $_SESSION['user_id'] = $urow['id_user'];
                $_SESSION['username'] = $urow['username'];
                log_activity($conn, "Dosen login ke sistem");
                header("Location: ../dosen/dashboard_dosen.php");
                exit;
            } elseif ($urow['id_role'] == 1) {
                $_SESSION['id_role'] = 1;
                $_SESSION['user_id'] = $urow['id_user'];
                $_SESSION['username'] = $urow['username'];
                log_activity($conn, "Admin login ke sistem");
                header("Location: ../admin/dashboard_admin.php");
                exit;
            }
        }
        // username matched but password didn't
        login_debug_log("username-first | identity={$identity} | found_user=1 | password_mismatch");
        header("Location: login_form.php?error=invalid");
        exit;
    } else {
        login_debug_log("username-first | identity={$identity} | found_user=0");
    }
    $stmtU->close();
} else {
    login_debug_log("username-first | failed to prepare statement");
}


/* ======================================================
   LOGIN MAHASISWA (id_role = 3 | login pakai NIM atau Username)
   ====================================================== */
if ($role === 'mahasiswa') {

    $stmt = $conn->prepare("
        SELECT 
            u.id_user,
            u.username,
            u.password,
            u.id_role,
            m.nim
        FROM users u
        LEFT JOIN mahasiswa m ON u.id_user = m.id_user
        WHERE (m.nim = ? OR u.username = ?) AND u.id_role = 3
        LIMIT 1
    ");
    $stmt->bind_param("ss", $identity, $identity);

    if (!$stmt) {
        // prepare failed (e.g., SQL error or DB issue)
        header("Location: login_form.php?error=server");
        exit;
    }

    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    // Debug: log hasil query, bagaimana user ditemukan, dan pengecekan password
    if (isset($DEBUG_LOGIN) && $DEBUG_LOGIN) {
        if (!$user) {
            login_debug_log("mahasiswa | identity={$identity} | user_found=0");
        } else {
            $pw_hash = $user['password'] ?? '';
            $verify = password_verify($password, $pw_hash) ? '1' : '0';
            $plain_eq = ($password === $pw_hash) ? '1' : '0';
            $matched_by = 'unknown';
            if (isset($user['username']) && strcasecmp($user['username'], $identity) === 0) $matched_by = 'username';
            elseif (isset($user['nim']) && strcasecmp($user['nim'], $identity) === 0) $matched_by = 'nim';
            login_debug_log("mahasiswa | identity={$identity} | user_found=1 | matched_by={$matched_by} | pw_verify={$verify} | plain_eq={$plain_eq} | stored_pw=" . $pw_hash);
        }
    }

    if ($user && (password_verify($password, $user['password']) || $password === $user['password'])) {

        // Jika nim tidak tersedia dari LEFT JOIN, coba ambil berdasarkan id_user
        if (empty($user['nim'])) {
            $q = $conn->prepare("SELECT nim FROM mahasiswa WHERE id_user = ? LIMIT 1");
            if ($q) {
                $q->bind_param("i", $user['id_user']);
                $q->execute();
                $r = $q->get_result()->fetch_assoc();
                if ($r && !empty($r['nim'])) {
                    $user['nim'] = $r['nim'];
                    login_debug_log("mahasiswa | filled nim from mahasiswa table | id_user={$user['id_user']} | nim={$user['nim']}");
                } else {
                    login_debug_log("mahasiswa | no mahasiswa row for id_user={$user['id_user']}");
                }
                $q->close();
            } else {
                login_debug_log("mahasiswa | failed to prepare fallback nim query");
            }
        }

        $_SESSION['user_id']  = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['id_role']  = 3;
        $_SESSION['nim']      = $user['nim'] ?? null;

        log_activity($conn, "Mahasiswa login ke sistem");

        header("Location: ../mahasiswa/dashboard_mahasiswa.php");
        exit;
    }
}

/* ======================================================
   LOGIN DOSEN (id_role = 2 | login pakai NIDN)
   ====================================================== */
elseif ($role === 'dosen') {

    $stmt = $conn->prepare("
        SELECT 
            u.id_user,
            u.username,
            u.password,
            u.id_role,
            d.nidn
        FROM users u
        LEFT JOIN dosen d ON u.id_user = d.id_user
        WHERE (d.nidn = ? OR u.username = ?) AND u.id_role = 2
        LIMIT 1
    ");
    $stmt->bind_param("ss", $identity, $identity);

    if (!$stmt) {
        header("Location: login_form.php?error=server");
        exit;
    }

    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    // Debug: log hasil query, bagaimana user ditemukan, dan pengecekan password
    if (isset($DEBUG_LOGIN) && $DEBUG_LOGIN) {
        if (!$user) {
            login_debug_log("dosen | identity={$identity} | user_found=0");
        } else {
            $pw_hash = $user['password'] ?? '';
            $verify = password_verify($password, $pw_hash) ? '1' : '0';
            $plain_eq = ($password === $pw_hash) ? '1' : '0';
            $matched_by = 'unknown';
            if (isset($user['username']) && strcasecmp($user['username'], $identity) === 0) $matched_by = 'username';
            elseif (isset($user['nidn']) && strcasecmp($user['nidn'], $identity) === 0) $matched_by = 'nidn';
            login_debug_log("dosen | identity={$identity} | user_found=1 | matched_by={$matched_by} | pw_verify={$verify} | plain_eq={$plain_eq} | stored_pw=" . $pw_hash);
        }
    }

    if ($user && (password_verify($password, $user['password']) || $password === $user['password'])) {

        // Jika nidn tidak tersedia dari LEFT JOIN, coba ambil berdasarkan id_user
        if (empty($user['nidn'])) {
            $q = $conn->prepare("SELECT nidn FROM dosen WHERE id_user = ? LIMIT 1");
            if ($q) {
                $q->bind_param("i", $user['id_user']);
                $q->execute();
                $r = $q->get_result()->fetch_assoc();
                if ($r && !empty($r['nidn'])) {
                    $user['nidn'] = $r['nidn'];
                    login_debug_log("dosen | filled nidn from dosen table | id_user={$user['id_user']} | nidn={$user['nidn']}");
                } else {
                    login_debug_log("dosen | no dosen row for id_user={$user['id_user']}");
                }
                $q->close();
            } else {
                login_debug_log("dosen | failed to prepare fallback nidn query");
            }
        }

        $_SESSION['user_id']  = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['id_role']  = 2;
        $_SESSION['nidn']     = $user['nidn'] ?? null;

        log_activity($conn, "Dosen login ke sistem");

        header("Location: ../dosen/dashboard_dosen.php");
        exit;
    }
}

/* ======================================================
   LOGIN ADMIN (id_role = 1 | login pakai USERNAME)
   ====================================================== */
elseif ($role === 'admin') {

    $stmt = $conn->prepare("
        SELECT 
            u.id_user,
            u.username,
            u.password,
            u.id_role
        FROM users u
        LEFT JOIN admin a ON u.id_user = a.id_user
        WHERE u.username = ? AND u.id_role = 1
        LIMIT 1
    ");
    $stmt->bind_param("s", $identity);

    if (!$stmt) {
        header("Location: login_form.php?error=server");
        exit;
    }

    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    // Debug: log hasil query dan pengecekan password
    if (isset($DEBUG_LOGIN) && $DEBUG_LOGIN) {
        if (!$user) {
            login_debug_log("admin | identity={$identity} | user_found=0");
        } else {
            $pw_hash = $user['password'] ?? '';
            $verify = password_verify($password, $pw_hash) ? '1' : '0';
            $plain_eq = ($password === $pw_hash) ? '1' : '0';
            $matched_by = (isset($user['username']) && strcasecmp($user['username'], $identity) === 0) ? 'username' : 'unknown';
            login_debug_log("admin | identity={$identity} | user_found=1 | matched_by={$matched_by} | pw_verify={$verify} | plain_eq={$plain_eq} | stored_pw=" . $pw_hash);
        }
    }

    if ($user && (password_verify($password, $user['password']) || $password === $user['password'])) {


        $_SESSION['user_id']  = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['id_role']  = 1;

        log_activity($conn, "Admin login ke sistem");

        header("Location: ../admin/dashboard_admin.php");
        exit;
    }
}

/* ======================================================
   LOGIN GAGAL
   ====================================================== */
header("Location: login_form.php?error=invalid");
exit;
