<?php
require_once "../auth/auth_check.php";
require_role('mahasiswa');

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) session_start();

$raw  = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(["ok" => false, "message" => "Payload tidak valid"]);
    exit;
}

$lat = $data['lat'] ?? null;
$lng = $data['lng'] ?? null;
$acc = $data['accuracy'] ?? null;

if (!is_numeric($lat) || !is_numeric($lng) || !is_numeric($acc)) {
    http_response_code(400);
    echo json_encode(["ok" => false, "message" => "Koordinat/akurasi tidak valid"]);
    exit;
}

$lat = (float)$lat;
$lng = (float)$lng;
$acc = (float)$acc;

if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
    http_response_code(400);
    echo json_encode(["ok" => false, "message" => "Rentang koordinat tidak valid"]);
    exit;
}

if ($acc <= 0 || $acc > 5000) {
    http_response_code(400);
    echo json_encode(["ok" => false, "message" => "Akurasi tidak wajar"]);
    exit;
}

$_SESSION['geo_lock'] = [
    "lat" => $lat,
    "lng" => $lng,
    "accuracy" => $acc,
    "captured_at" => date("Y-m-d H:i:s"),
];

echo json_encode(["ok" => true, "message" => "Lokasi tersimpan"]);
