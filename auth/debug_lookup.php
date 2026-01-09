<?php
require_once __DIR__ . '/../config/db.php';

$identity = $_GET['identity'] ?? '';
if (!$identity) {
    echo "Pass ?identity=... to inspect lookups.";
    exit;
}

$identity_raw = $identity;
$identity = trim($identity);

header('Content-Type: text/plain; charset=utf-8');

echo "Debug lookup for identity: '{$identity_raw}'\n\n";

// 1) Find users with exact username
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $identity);
$stmt->execute();
$res = $stmt->get_result();
echo "Users with username = '{$identity}': " . $res->num_rows . "\n";
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
$stmt->close();

echo "\n";

// 2) Find users with username LIKE (case-insensitive)
$stmt = $conn->prepare("SELECT * FROM users WHERE username LIKE ?");
$like = $identity;
$stmt->bind_param("s", $like);
$stmt->execute();
$res = $stmt->get_result();
echo "Users with username LIKE '{$identity}': " . $res->num_rows . "\n";
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
$stmt->close();

echo "\n";

// 3) Find mahasiswa rows by nim or by username mapping
$stmt = $conn->prepare("SELECT m.*, u.username FROM mahasiswa m JOIN users u ON u.id_user = m.id_user WHERE m.nim = ? OR u.username = ?");
$stmt->bind_param("ss", $identity, $identity);
$stmt->execute();
$res = $stmt->get_result();
echo "Mahasiswa matching nim or username: " . $res->num_rows . "\n";
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
$stmt->close();

echo "\n";

// 4) Find dosen rows by nidn or username mapping
$stmt = $conn->prepare("SELECT d.*, u.username FROM dosen d JOIN users u ON u.id_user = d.id_user WHERE d.nidn = ? OR u.username = ?");
$stmt->bind_param("ss", $identity, $identity);
$stmt->execute();
$res = $stmt->get_result();
echo "Dosen matching nidn or username: " . $res->num_rows . "\n";
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
$stmt->close();

echo "\n";

// 5) Find admin rows by username
$stmt = $conn->prepare("SELECT a.*, u.username FROM admin a JOIN users u ON u.id_user = a.id_user WHERE u.username = ?");
$stmt->bind_param("s", $identity);
$stmt->execute();
$res = $stmt->get_result();
echo "Admin matching username: " . $res->num_rows . "\n";
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
$stmt->close();

echo "\n--- End ---\n";

?>
