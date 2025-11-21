<?php
session_start();
require '../includes/koneksi.php'; // pastikan file ini bikin variabel $conn (mysqli)

// Kalau bukan request POST, balikin ke form login
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// Ambil data dari form
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

// Validasi awal
if ($username === '' || $password === '') {
    $_SESSION['login_error'] = 'Username dan password wajib diisi.';
    header('Location: login.php');
    exit;
}

// Ambil user dari database berdasarkan username
$sql  = "SELECT id_user, nama, username, password, role FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user   = $result->fetch_assoc();

// Cek user ada atau nggak
if (!$user) {
    $_SESSION['login_error'] = 'Username atau password salah.';
    header('Location: login.php');
    exit;
}

$stored = $user['password'];
$valid  = false;

// Jika password di DB dalam bentuk hash (pakai password_hash)
if (password_verify($password, $stored)) {
    $valid = true;
}

// Kalau ternyata di DB masih plain text (misal: admin123 langsung)
if ($password === $stored) {
    $valid = true;
}

// Kalau password tetap tidak valid
if (!$valid) {
    $_SESSION['login_error'] = 'Username atau password salah.';
    header('Location: login.php');
    exit;
}

// ====== LOGIN SUKSES ======
$_SESSION['logged_in'] = true;
$_SESSION['id_user']   = $user['id_user'];
$_SESSION['nama']      = $user['nama'];
$_SESSION['role']      = $user['role'];

// Arahkan sesuai role
$role = $user['role'];

if ($role === 'admin') {
    // admin -> dashboard admin
    header('Location: dashboard_admin.php');
    exit;
} elseif ($role === 'ob') {
    // OB -> dashboard OB
    header('Location: dashboard_ob.php');
    exit;
} else {
    // role lain (kalau ada) -> dashboard umum
    header('Location: dashboard.php');
    exit;
}
