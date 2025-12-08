<?php
session_start();
require '../includes/cek_login.php';

// Hanya OB yang boleh akses
if ($_SESSION['role'] !== 'ob') {
    header('Location: dashboard_admin.php');
    exit;
}

$id_ob = $_SESSION['id_user'] ?? 0;
$nama  = $_SESSION['nama'] ?? 'Petugas OB';

require '../includes/koneksi.php';

// =============== MODE DATA (AJAX JSON) ===============
if (isset($_GET['mode']) && $_GET['mode'] === 'data') {

    // 1) Riwayat kapasitas (kalau nanti mau dipakai grafik lain)
    $sqlKap = "
        SELECT waktu_tindakan, kapasitas_sebelum, status_sebelum
        FROM bin_history
        WHERE id_ob = ?
        ORDER BY waktu_tindakan ASC
    ";
    $stmtKap = $conn->prepare($sqlKap);
    $stmtKap->bind_param('i', $id_ob);
    $stmtKap->execute();
    $resKap = $stmtKap->get_result();

    $kapasitas = [];
    while ($row = $resKap->fetch_assoc()) {
        $kapasitas[] = [
            'waktu'     => $row['waktu_tindakan'],
            'kapasitas' => (int)$row['kapasitas_sebelum'],
            'status'    => $row['status_sebelum'],
        ];
    }
    $stmtKap->close();

    // 2) Hitung berapa kali status Penuh / Hampir Penuh / Aman dari bin_history
    $sqlStat = "
        SELECT status_sebelum AS status, COUNT(*) AS total
        FROM bin_history
        WHERE id_ob = ?
        GROUP BY status_sebelum
    ";
    $stmtStat = $conn->prepare($sqlStat);
    $stmtStat->bind_param('i', $id_ob);
    $stmtStat->execute();
    $resStat = $stmtStat->get_result();

    $statusCounts = [];
    $summary = [
        'penuh'  => 0,
        'hampir' => 0,
        'aman'   => 0,
        'total'  => 0,
    ];

    while ($row = $resStat->fetch_assoc()) {
        $status = strtolower($row['status']);
        $total  = (int)$row['total'];

        $statusCounts[] = [
            'status' => $row['status'],
            'total'  => $total,
        ];

        if (strpos($status, 'penuh') !== false && strpos($status, 'hampir') === false) {
            $summary['penuh'] += $total;
        } elseif (strpos($status, 'hampir') !== false) {
            $summary['hampir'] += $total;
        } else {
            // anggap selain itu = Aman / Normal
            $summary['aman'] += $total;
        }
        $summary['total'] += $total;
    }
    $stmtStat->close();

    // 3) Frekuensi pengosongan dari riwayat_kosongkan (per hari)
    //    Sesuaikan nama kolom timestamp jika beda (di sini diasumsikan waktu_kosongkan)
    $sqlFreq = "
        SELECT DATE(waktu_kosongkan) AS tanggal, COUNT(*) AS total
        FROM riwayat_kosongkan
        WHERE id_user = ?
        GROUP BY DATE(waktu_kosongkan)
        ORDER BY tanggal ASC
    ";
    $stmtFreq = $conn->prepare($sqlFreq);
    $stmtFreq->bind_param('i', $id_ob);
    $stmtFreq->execute();
    $resFreq = $stmtFreq->get_result();

    $frekuensi = [];
    while ($row = $resFreq->fetch_assoc()) {
        $frekuensi[] = [
            'tanggal' => $row['tanggal'],
            'total'   => (int)$row['total'],
        ];
    }
    $stmtFreq->close();

    $hasData = ($summary['total'] > 0);

    header('Content-Type: application/json');
    echo json_encode([
        'success'      => true,
        'hasData'      => $hasData,
        'summary'      => $summary,
        'statusCounts' => $statusCounts,
        'kapasitas'    => $kapasitas,
        'frekuensi'    => $frekuensi,
    ]);
    exit;
}

// =============== MODE HALAMAN (HTML) ===============
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Grafik | Smart Trash Bin</title>

  <!-- Font Awesome -->
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Styles global (dari root, jadi ../) -->
  <link rel="stylesheet" href="../styles.css" />

  <!-- Chart.js + Datalabels -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
</head>
<body>
  <div class="main-container">
    <!-- SIDEBAR (include) -->
    <div data-include="../components/sidebar.html"></div>

    <!-- KONTEN -->
    <main class="content" id="content">
        <header class="dashboard-header show">
            <div class="header-text">
                <h1>Grafik Tempat Sampah</h1>
                <p>Smart monitoring for cleaner environment</p>
                <!-- atau mau personal -->
                <!-- <p>Halo, <?= htmlspecialchars($nama); ?> — smart monitoring for cleaner environment</p> -->
                </div>
            <div class="notif"><i class="fa-solid fa-bell"></i></div>
        </header>

      <!-- KARTU CHART -->
        <section class="chart-card" style="margin-bottom:16px;">
            <h3 class="section-title" style="text-align:left;margin:0 0 12px 4px;font-size:18px;font-weight:700;">
              Distribusi Status Tempat Sampah
            </h3>

            <!-- Loader & pesan kosong -->
            <div id="grafik-loader" style="padding:0.5rem 0 0.75rem 4px; font-size:.9rem;">
                Memuat data grafik...
            </div>
            <div id="grafik-empty" style="padding:0.5rem 0 0.75rem 4px; font-size:.9rem; display:none; opacity:.8;">
                Belum ada data grafik.
            </div>

            <div class="chart-wrap" id="chart-wrap" style="display:none;">
                <canvas id="statusChart"></canvas>
            </div>
            <div id="statusLegend" class="chart-legend"></div>
        </section>

      <!-- KARTU RINGKASAN (otomatis dari data) -->
        <section class="summary-card">
            <h3 class="summary-title">Ringkasan Status Bin</h3>
            <div class="summary-row"><span>Total Bin</span>        <span id="sum-total" class="val">-</span></div>
            <div class="summary-row"><span>Penuh</span>            <span id="sum-penuh" class="val red">-</span></div>
            <div class="summary-row"><span>Hampir Penuh</span>     <span id="sum-hampir" class="val yellow">-</span></div>
            <div class="summary-row"><span>Aman</span>             <span id="sum-aman" class="val green">-</span></div>
        </section>
    </main>
  </div>

  <!-- FOOTER (include) -->
  <div data-include="../components/footer.html"></div>

  <!-- Loader komponen + script umum -->
  <script src="../include.js"></script>
  <script src="../script.js"></script>

  <script>
  // register plugin datalabels
  if (window.Chart && window.ChartDataLabels) {
    Chart.register(ChartDataLabels);
  }

  let statusChart = null;

  function updateSummary(summary) {
    document.getElementById('sum-total').textContent  = summary.total;
    document.getElementById('sum-penuh').textContent  = summary.penuh;
    document.getElementById('sum-hampir').textContent = summary.hampir;
    document.getElementById('sum-aman').textContent   = summary.aman;
  }

  function buildOrUpdateStatusChart(summary) {
  const canvas = document.getElementById('statusChart');
  if (!canvas) return;

  const ctx = canvas.getContext('2d');

  // ====== SAMAKAN CARA HITUNG DENGAN chart.js (FE) ======
  const totalBin = summary.total || 1;
  const counts   = [summary.aman, summary.hampir, summary.penuh];
  const labels   = ['Aman', 'Hampir Penuh', 'Penuh'];
  const percents = counts.map(v => Math.round((v / totalBin) * 100));

  // ====== LOGIKA niceMax PERSIS SEPERTI chart.js ======
  const maxPercent = Math.max(...percents, 100);
  let niceMax = 100;
  if (maxPercent <= 25) niceMax = 25;
  else if (maxPercent <= 50) niceMax = 50;
  else if (maxPercent <= 75) niceMax = 75;

  // kalau chart sudah ada, destroy dulu biar config-nya ke-reset
  if (statusChart) {
    statusChart.destroy();
  }

  statusChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Persentase bin',
        data: percents,
        backgroundColor: ['#2ecc71', '#f1c40f', '#e74c3c'], // hijau, kuning, merah
        borderRadius: 8,
        categoryPercentage: 0.7,
        barPercentage: 0.8
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      layout: { padding: { top: 8, right: 8, left: 8, bottom: 0 } },
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: ctx => {
              const i = ctx.dataIndex;
              return ` ${counts[i]} bin (${percents[i]}%)`;
            }
          }
        },
        datalabels: {
          anchor: 'end',
          align: 'end',
          formatter: (_, ctx) => `${percents[ctx.dataIndex]}%`,
          color: '#111',
          font: { weight: '700', size: 12 }
        }
      },
      scales: {
        x: {
          grid: { display: false, offset: true },
          ticks: { padding: 4 }
        },
        y: {
          beginAtZero: true,
          min: 0,
          max: niceMax,
          ticks: {
            stepSize: niceMax / 5,
            callback: v => v + '%',
            padding: 4
          },
          grid: { color: 'rgba(0,0,0,0.06)', drawBorder: false }
        }
      }
    }
  });

  // ====== LEGEND DI BAWAH CHART — PERSIS SEPERTI chart.js ======
  const legendEl = document.getElementById('statusLegend');
  if (legendEl) {
    const legendLabels = ['Aman/Kosong', 'Hampir Penuh', 'Penuh'];
    const legendColors = ['#2ecc71', '#f1c40f', '#e74c3c'];

    legendEl.innerHTML = legendLabels.map((text, i) => `
      <span class="item">
        <span class="dot" style="background:${legendColors[i]}"></span>${text}
      </span>
    `).join('');
  }
}



  function loadGrafikData() {
    const loader = document.getElementById('grafik-loader');
    const empty  = document.getElementById('grafik-empty');
    const wrap   = document.getElementById('chart-wrap');

    loader.style.display = 'block';
    empty.style.display  = 'none';
    wrap.style.display   = 'none';

    fetch('grafik_ob.php?mode=data', { cache: 'no-store' })
      .then(res => res.json())
      .then(res => {
        loader.style.display = 'none';

        if (!res.success || !res.hasData) {
          empty.style.display = 'block';
          return;
        }

        wrap.style.display  = 'block';
        empty.style.display = 'none';

        updateSummary(res.summary);
        buildOrUpdateStatusChart(res.summary);
      })
      .catch(err => {
        console.error(err);
        loader.textContent = 'Gagal memuat data grafik.';
      });
  }

  document.addEventListener('componentsLoaded', () => {
    // menu aktif
    document.querySelectorAll('.menu a').forEach(a => a.classList.remove('active'));
    document.getElementById('link-grafik')?.classList.add('active');

    // Dashboard harus balik ke dashboard_ob.php
    const dash = document.getElementById('link-dashboard');
    if (dash) dash.setAttribute('href', 'dashboard_ob.php');

    // load pertama + auto refresh
    loadGrafikData();
    setInterval(loadGrafikData, 5000);
  });
</script>

</body>
</html>
