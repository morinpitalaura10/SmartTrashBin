<?php
session_start();
require '../includes/cek_login.php';

// Hanya OB yang boleh akses halaman ini
if ($_SESSION['role'] !== 'ob') {
    header('Location: dashboard_admin.php');
    exit;
}

$id_ob = $_SESSION['id_user'] ?? 0;

require '../includes/koneksi.php';

// Ambil riwayat dari bin_history untuk OB ini saja, terbaru -> terlama
$sql = "
    SELECT h.id_history,
           h.waktu_tindakan,
           h.kapasitas_sebelum,
           h.status_sebelum,
           h.tindakan,
           t.lokasi
    FROM bin_history h
    JOIN tempat_sampah t ON h.id_tempat = t.id_bin
    WHERE h.id_ob = ?
    ORDER BY h.waktu_tindakan DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id_ob);
$stmt->execute();
$result = $stmt->get_result();

$riwayat = [];
while ($row = $result->fetch_assoc()) {
    $riwayat[] = $row;
}
$stmt->close();

// Helper: status -> class badge (merah / oranye / hijau)
function status_badge_class($status) {
    $s = strtolower($status);
    if (strpos($s, 'penuh') !== false && strpos($s, 'hampir') === false) {
        return 'badge-penuh';   // merah
    }
    if (strpos($s, 'hampir') !== false) {
        return 'badge-hampir';  // oranye
    }
    return 'badge-aman';        // hijau (normal/aman)
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Riwayat | Smart Trash Bin</title>

  <!-- Font Awesome -->
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Styles global (dari root, jadi ../) -->
  <link rel="stylesheet" href="../styles.css" />
</head>

<body>
  <div class="main-container">
    <!-- SIDEBAR (include) -->
    <div data-include="../components/sidebar_OB.html"></div>

    <!-- KONTEN -->
    <main class="content" id="content">
      <!-- HEADER KHUSUS RIWAYAT (mirip riwayat.html) -->
      <header class="dashboard-header show">
        <div class="header-text">
          <h1>Riwayat Pengosongan Tempat Sampah</h1>
          <p>Smart monitoring for cleaner environment</p>
        </div>
        <div class="notif">
          <i class="fa-solid fa-bell"></i>
        </div>
      </header>

      <!-- TABEL RIWAYAT -->
      <section class="table-card">
        <h5 class="section-title section-title-left">Daftar Riwayat</h5>

        <?php if (empty($riwayat)): ?>
          <p style="padding: 1rem; font-size: .95rem; opacity: .8;">
            Belum ada riwayat tindakan.
          </p>
        <?php else: ?>
          <table class="data-table">
            <thead>
              <tr>
                <th>No</th>
                <th>Waktu</th>
                <th>Lokasi Tempat Sampah</th>
                <th>Kapasitas Sebelum</th>
                <th>Status Sebelum</th>
                <th>Tindakan</th>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1; ?>
              <?php foreach ($riwayat as $row): ?>
                <?php
                  $statusAsli   = $row['status_sebelum'];
                  // Tampilkan "Normal" jika di DB status = "Aman"
                  $statusLabel  = (strtolower($statusAsli) === 'aman') ? 'Normal' : $statusAsli;
                  $badgeClass   = status_badge_class($statusAsli);
                ?>
                <tr>
                  <td><?= $no++; ?></td>
                  <td><?= htmlspecialchars($row['waktu_tindakan']); ?></td>
                  <td><?= htmlspecialchars($row['lokasi']); ?></td>
                  <td><?= (int)$row['kapasitas_sebelum']; ?>%</td>
                  <td>
                    <span class="badge <?= $badgeClass; ?>">
                      <?= htmlspecialchars($statusLabel); ?>
                    </span>
                  </td>
                  <td><?= htmlspecialchars($row['tindakan']); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </section>
    </main>
  </div>

  <!-- FOOTER (include) -->
  <div data-include="../components/footer.html"></div>

  <!-- Include loader + script global -->
  <script src="../include.js"></script>
  <script src="../script.js"></script>

  <!-- Sinkron tema + tandai menu aktif -->
  <script>
    document.addEventListener('componentsLoaded', () => {
      const dash = document.getElementById('link-dashboard');
      if (dash) dash.setAttribute('href', 'dashboard_ob.php');
      // Dark mode sinkron
      const toggle = document.getElementById('toggle-theme');
      const saved = localStorage.getItem('stb-theme');
      if (saved === 'dark') {
        document.body.classList.add('dark-mode');
        if (toggle) toggle.checked = true;
      }
      toggle?.addEventListener('change', () => {
        const dark = toggle.checked;
        document.body.classList.toggle('dark-mode', dark);
        localStorage.setItem('stb-theme', dark ? 'dark' : 'light');
      });

      // Pastikan menu "Riwayat" aktif
      document.querySelectorAll('.menu a').forEach(a => a.classList.remove('active'));
      document.getElementById('link-riwayat')?.classList.add('active');
    });
    
  </script>
</body>
</html>
