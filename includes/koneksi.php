<?php
$host = "localhost";
$user = "root";          // default XAMPP
$pass = "";              // default XAMPP kosong
$db   = "smarttrash_db"; // nama database yang akan kita buat

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
?>
