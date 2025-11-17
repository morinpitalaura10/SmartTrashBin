<?php
session_start();
// Contoh nama user dari session
$nama = $_SESSION['nama'] ?? "Admin";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-dark text-light">

<div class="container py-4">

    <!-- Pesan selamat datang -->
    <div class="alert alert-success">
        <h4 class="m-0">Selamat Datang, <?= $nama ?> 👋</h4>
    </div>

    <h2 class="mb-4">Dashboard Admin</h2>

    <!-- Menu -->
    <a href="form_user_ob.php" class="btn btn-primary mb-2">Tambah User OB</a>
    <a href="form_tempat_sampah.php" class="btn btn-warning mb-2">Tambah Tempat Sampah</a>

    <!-- Contoh konten -->
    <div class="card bg-secondary text-light mt-4">
        <div class="card-body">
            <h5>Informasi Sistem</h5>
            <p>Smart Trash Bin Monitoring</p>
        </div>
    </div>

</div>

</body>
</html>
