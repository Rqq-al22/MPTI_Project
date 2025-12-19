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
