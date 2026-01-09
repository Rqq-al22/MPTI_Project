<?php
require_once "../auth/auth_check.php";
require_role('mahasiswa');

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Method tidak diizinkan.']);
    exit;
}

// CSRF dari header
$csrf = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!is_string($csrf) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'message' => 'CSRF tidak valid.']);
    exit;
}

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'Payload tidak valid.']);
    exit;
}

$lat = $data['lat'] ?? null;
$lng = $data['lng'] ?? null;
$acc = $data['acc'] ?? null;

if (!is_numeric($lat) || !is_numeric($lng)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'Koordinat tidak valid.']);
    exit;
}

$lat = (float)$lat;
$lng = (float)$lng;
$acc = is_numeric($acc) ? (float)$acc : null;

if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'Koordinat di luar rentang.']);
    exit;
}

// Simpan lokasi ke session (akan dipakai saat submit presensi)
$_SESSION['presensi_geo'] = [
    'lat' => $lat,
    'lng' => $lng,
    'acc' => $acc,
    'ts'  => time()
];

echo json_encode(['ok' => true]);
