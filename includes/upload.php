<?php
// Helper upload file yang aman-sederhana (untuk tugas kampus)
require_once __DIR__ . "/auth.php";

function upload_file(array $file, string $dest_dir, string $name_prefix, array $allowed_ext): array {
  if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
    return [false, null, 'Upload gagal. Pastikan memilih file dan ukuran tidak melebihi batas server.'];
  }

  $orig = (string)($file['name'] ?? 'file');
  $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
  if (!in_array($ext, $allowed_ext, true)) {
    return [false, null, 'Format file tidak diizinkan.'];
  }

  $safe = preg_replace('/[^a-zA-Z0-9._-]/', '_', $orig);
  $newname = $name_prefix . "_" . date('Ymd_His') . "_" . $safe;

  $dest_dir = rtrim($dest_dir, '/');
  if (!is_dir($dest_dir)) {
    @mkdir($dest_dir, 0775, true);
  }

  $dest_path = $dest_dir . "/" . $newname;
  if (!move_uploaded_file($file['tmp_name'], $dest_path)) {
    return [false, null, 'Gagal memindahkan file upload. Periksa izin folder uploads.'];
  }

  return [true, $newname, null];
}
?>
