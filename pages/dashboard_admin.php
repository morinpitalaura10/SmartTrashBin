<?php
session_start();
require '../includes/cek_login.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: dashboard_ob.php');
    exit;
}

$nama = $_SESSION['nama'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Smart Trash Bin Dashboard - Admin</title>

  <!-- Font Awesome -->
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Styles -->
  <!-- PENTING: dari /pages ke root, jadi pakai ../ -->
  <link rel="stylesheet" href="../styles.css" />
  <link rel="stylesheet" href="../css/variables.css" />
</head>
<body>
  <!-- ====== COPY ISI BODY DARI index.html DI SINI ====== -->

  <div class="main-container">
    <!-- SIDEBAR -->
    <div data-include="../components/sidebar.html"></div>

    <!-- KONTEN -->
    <main class="content" id="content">
      <!-- HEADER -->
      <div data-include="../components/header.html"></div>

      <!-- Tempat render halaman -->
      <div id="page"></div>
    </main>
  </div>

  <!-- FOOTER -->
  <div data-include="../components/footer.html"></div>

  <!-- SCRIPT -->
  <script src="../include.js"></script>
  <script src="../script.js"></script>

  <script>
    document.addEventListener("componentsLoaded", () => {
      document.querySelector(".dashboard-header")?.classList.add("show");

      const toggle = document.getElementById("toggle-theme");
      const saved = localStorage.getItem("stb-theme");
      if (saved === "dark") {
        document.body.classList.add("dark-mode");
        if (toggle) toggle.checked = true;
      }
      toggle?.addEventListener("change", () => {
        const dark = toggle.checked;
        document.body.classList.toggle("dark-mode", dark);
        localStorage.setItem("stb-theme", dark ? "dark" : "light");
      });
    });
  </script>
</body>
</html>
