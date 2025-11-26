<?php
session_start();
require '../includes/koneksi.php'; // pastikan koneksi bikin $conn (mysqli)

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
$sql  = "SELECT id_user, nama, username, password, role 
         FROM users 
         WHERE username = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    $_SESSION['login_error'] = 'Terjadi kesalahan pada server.';
    header('Location: login.php');
    exit;
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user   = $result->fetch_assoc();
$stmt->close();

// Cek user ada atau nggak
if (!$user) {
    $_SESSION['login_error'] = 'Username atau password salah.';
    header('Location: login.php');
    exit;
}

// Cek password
$stored = $user['password'];
$valid  = false;

// Kalau di DB sudah disimpan dalam bentuk hash (password_hash)
if (password_verify($password, $stored)) {
    $valid = true;
}

// OPTIONAL: fallback kalau dulu pernah simpan plain text
if ($password === $stored) {
    $valid = true;
}

if (!$valid) {
    $_SESSION['login_error'] = 'Username atau password salah.';
    header('Location: login.php');
    exit;
}

// ====== LOGIN SUKSES ======
$_SESSION['logged_in'] = true;
$_SESSION['id_user']   = $user['id_user'];
$_SESSION['nama']      = $user['nama'];
$_SESSION['role']      = $user['role']; // 'admin' atau 'ob'

// Arahkan sesuai role
if ($user['role'] === 'admin') {
    header('Location: dashboard_admin.php');
    exit;
} elseif ($user['role'] === 'ob') {
    header('Location: dashboard_ob.php');
    exit;
} else {
    // Kalau ada role lain, sementara balikin ke login
    $_SESSION['login_error'] = 'Role user tidak dikenali.';
    header('Location: login.php');
    exit;
}
