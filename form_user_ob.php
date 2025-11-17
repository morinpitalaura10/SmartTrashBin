<?php
$pesan = $_GET['pesan'] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah User OB</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-dark text-light">

<div class="container py-4">

    <h2 class="mb-3">Form Tambah User OB</h2>

    <?php if ($pesan == "sukses") : ?>
        <div class="alert alert-success">Data User OB berhasil ditambahkan!</div>
    <?php endif; ?>

    <form action="proses_user_ob.php" method="POST" class="bg-secondary p-4 rounded">

        <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
                <option value="OB">OB</option>
                <option value="Admin">Admin</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="dashboard_admin.php" class="btn btn-light">Kembali</a>
    </form>

</div>

</body>
</html>
