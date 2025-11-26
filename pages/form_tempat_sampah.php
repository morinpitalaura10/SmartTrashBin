<?php
$pesan = $_GET['pesan'] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Tempat Sampah</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-dark text-light">

<div class="container py-4">

    <h2 class="mb-3">Form Tambah Tempat Sampah</h2>

    <?php if ($pesan == "sukses") : ?>
        <div class="alert alert-success">Data tempat sampah berhasil ditambahkan!</div>
    <?php endif; ?>

    <form method="POST" method="proses_tempat_sampah.php" class="bg-secondary p-4 rounded">

        <div class="mb-3">
            <label class="form-label">ID Tempat Sampah</label>
            <input type="text" name="id" class="form-control" placeholder="Contoh: B001" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Tempat</label>
            <input type="text" name="nama" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Lantai</label>
            <input type="text" name="lantai" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Lokasi</label>
            <input type="text" name="lokasi" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-warning">Simpan</button>
        <a href="dashboard_admin.php" class="btn btn-light">Kembali</a>
    </form>

</div>

</body>
</html>
