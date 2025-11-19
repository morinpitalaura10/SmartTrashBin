<?php
session_start();
require '../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    $_SESSION['login_error'] = 'Username dan password wajib diisi.';
    header('Location: login.php');
    exit;
}

// --- DEBUG RINGAN: cek apakah POST masuk ---
// hapus komentar di bawah kalau mau cek
// echo "<pre>"; var_dump($_POST); exit;

$sql  = "SELECT id_user, nama, username, password, role FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user   = $result->fetch_assoc();

// --- DEBUG: cek user yang ketemu ---
// echo "<pre>"; var_dump($user); exit;

if (!$user) {
    $_SESSION['login_error'] = 'Username atau password salah.';
    header('Location: login.php');
    exit;
}

$stored = $user['password'];
$valid  = false;

// jika password disimpan hash (pakai password_hash)
if (password_verify($password, $stored)) {
    $valid = true;
}

// kalau ternyata di DB masih plain text (admin123 langsung)
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
$_SESSION['role']      = $user['role'];

// --- DEBUG: cek role ---
// echo "ROLE: " . $_SESSION['role']; exit;

if ($user['role'] === 'admin') {
    header('Location: dashboard_admin.php');
} else {
    header('Location: dashboard_ob.php');
}
exit;
