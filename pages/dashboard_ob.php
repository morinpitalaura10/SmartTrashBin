<?php
session_start();
require '../includes/cek_login.php';

// Hanya boleh diakses role OB
if ($_SESSION['role'] !== 'ob') {
    header('Location: dashboard_admin.php');
    exit;
}

$nama = $_SESSION['nama'] ?? 'Petugas OB';

// ================== DATA DUMMY TEMPAT SAMPAH ==================
// Anggap kita punya 3 tempat sampah dengan kapasitas berbeda (dalam %)
$bins = [
    [
        'id_bin'     => 1,
        'lokasi'     => 'Lantai 1 - Koridor',
        'kapasitas'  => 30,                    // 30% terisi
        'updated_at' => '2025-11-28 09:10:00',
    ],
    [
        'id_bin'     => 2,
        'lokasi'     => 'Lantai 2 - Lobby',
        'kapasitas'  => 65,                    // 65% terisi
        'updated_at' => '2025-11-28 09:15:00',
    ],
    [
        'id_bin'     => 3,
        'lokasi'     => 'Lantai 3 - Kantin',
        'kapasitas'  => 85,                    // 85% terisi
        'updated_at' => '2025-11-28 09:20:00',
    ],
];

// ================== HITUNG STATUS BERDASARKAN PERSENTASE ==================
foreach ($bins as &$bin) {
    $p = (int)$bin['kapasitas'];

    // < 50%  -> aman
    // 50–74% -> hampir penuh
    // >=75%  -> penuh
    if ($p < 50) {
        $bin['status'] = 'aman';
        $bin['warna']  = 'green';    // nanti bisa dipetakan ke class CSS
    } elseif ($p < 75) {
        $bin['status'] = 'hampir penuh';
        $bin['warna']  = 'orange';
    } else {
        $bin['status'] = 'penuh';
        $bin['warna']  = 'red';
    }
}
unset($bin);

// Encode ke JSON untuk dikirim ke frontend (JS)
$binsJson = json_encode($bins);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Smart Trash Bin Dashboard - ob</title>

  <!-- Font Awesome -->
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Styles (dari root, jadi pakai ../) -->
  <link rel="stylesheet" href="../styles.css" />
  <link rel="stylesheet" href="../css/variables.css" />
</head>
<body>
  <div class="main-container">
    <!-- SIDEBAR -->
    <div data-include="../components/sidebar_OB.html"></div>

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

  <!-- SCRIPT INCLUDE KOMPONEN + LOGIKA FRONTEND -->
  <script src="../include.js"></script>
  <script src="../script.js"></script>

  <!-- DATA DUMMY DARI BACKEND UNTUK DASHBOARD OB -->
  <script>
    // Data dummy dari PHP (backend) -> JavaScript
    const DUMMY_BINS = <?php echo $binsJson; ?>;
    console.log("Data dummy bin dari backend (OB):", DUMMY_BINS);

    // ====== Contoh pemakaian (sementara) ======
    // Di sini kamu BELUM wajib ubah tampilan.
    // Yang penting: data sudah tersedia di JS.
    //
    // Nanti script.js bisa dipakai untuk:
    // - nampilin kartu status per bin
    // - ganti warna indikator berdasarkan field 'warna'
    // - bikin grafik / riwayat dari data ini (sementara dummy)
  </script>

  <script>
    // Tema dark/light mode (sama kayak index.html)
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
