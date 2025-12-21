<?php
require_once __DIR__ . '/../config/db.php';

// Simple database connection test
if ($conn) {
    echo "OK: Connected to database '" . htmlspecialchars($database ?? '') . "' as user '" . htmlspecialchars($user ?? '') . "'.";
} else {
    echo "ERROR: Gagal koneksi: " . mysqli_connect_error();
}

// Optional: show mysqli server version
echo "<br>Server info: " . mysqli_get_server_info($conn);

?>
