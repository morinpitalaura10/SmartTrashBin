<?php
session_start();
require 'includes/koneksi.php'; // sesuaikan path

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: form_user_ob.php?pesan=Metode+tidak+valid');
    exit;
}

$nama     = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$roleRaw  = $_POST['role'] ?? ''; // 'OB' atau 'Admin' dari form

if ($nama === '' || $username === '' || $password === '' || $roleRaw === '') {
    header('Location: form_user_ob.php?pesan=Semua+field+wajib+diisi');
    exit;
}

// NORMALISASI ROLE -> 'admin' / 'ob' (sesuai enum DB)
$role = strtolower($roleRaw); // 'OB' -> 'ob', 'Admin' -> 'admin'

// HASH PASSWORD
$hash = password_hash($password, PASSWORD_DEFAULT);

// INSERT USER
$stmt = $conn->prepare(
    "INSERT INTO users (nama, username, password, role) 
     VALUES (?, ?, ?, ?)"
);
$stmt->bind_param('ssss', $nama, $username, $hash, $role);

if ($stmt->execute()) {
    header('Location: dashboard_admin.php?pesan=User+berhasil+ditambah');
} else {
    header('Location: form_user_ob.php?pesan=Gagal+menyimpan+user');
}
exit;
