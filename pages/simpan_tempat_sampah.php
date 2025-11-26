<?php
session_start();
require 'includes/koneksi.php'; // sesuaikan path

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: form_tempat_sampah.php?pesan=Metode+tidak+valid');
    exit;
}

$lokasi = trim($_POST['lokasi'] ?? '');

if ($lokasi === '') {
    header('Location: form_tempat_sampah.php?pesan=Lokasi+wajib+diisi');
    exit;
}

// DEFAULT: kapasitas 0, status 'Aman'
$kapasitas = 0;
$status    = 'Aman';

$stmt = $conn->prepare(
    "INSERT INTO tempat_sampah (lokasi, kapasitas, status) 
     VALUES (?, ?, ?)"
);
$stmt->bind_param('sis', $lokasi, $kapasitas, $status);
// 's' string, 'i' int, 's' string

if ($stmt->execute()) {
    header('Location: dashboard_admin.php?pesan=Tempat+sampah+berhasil+ditambah');
} else {
    header('Location: form_tempat_sampah.php?pesan=Gagal+menyimpan+data');
}
exit;
