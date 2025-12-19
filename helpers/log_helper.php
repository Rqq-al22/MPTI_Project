<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Mencatat aktivitas user ke tabel monitoring
 */
function log_activity(mysqli $conn, string $aktivitas)
{
    if (!isset($_SESSION['user_id'])) {
        return;
    }

    $id_user = $_SESSION['user_id'];

    $stmt = $conn->prepare("
        INSERT INTO monitoring (aktivitas, id_user)
        VALUES (?, ?)
    ");
    $stmt->bind_param("si", $aktivitas, $id_user);
    $stmt->execute();
}


/**
 * Helper upload foto profil
 */
function upload_foto($file, $folder = "../uploads/profile/")
{
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowed = ['jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        return null;
    }

    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $filename = uniqid('profile_') . '.' . $ext;
    $target = $folder . $filename;

    if (move_uploaded_file($file['tmp_name'], $target)) {
        return $filename;
    }

    return null;
}

?>