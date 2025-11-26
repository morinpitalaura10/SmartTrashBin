document.addEventListener('componentsLoaded', () => {
  // ========== THEME ==========
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

  // Tandai menu aktif = Grafik
  document.querySelectorAll('.menu a').forEach(a => a.classList.remove('active'));
  document.getElementById('link-grafik')?.classList.add('active');

  // ========== DATA DUMMY (bisa diganti dari DB/API) ==========
  const bins = [
    { id: 'B001', name: 'Bin Utama',       floor: 'Lantai 1',        capacity: 85 },
    { id: 'B002', name: 'Bin Koridor',     floor: 'Lantai 2',        capacity: 58 },
    { id: 'B003', name: 'Bin Dapur',       floor: 'Lantai 1',        capacity:  8 },
    { id: 'B004', name: 'Bin Ruang Rapat', floor: 'Lantai 3',        capacity: 90 },
    { id: 'B005', name: 'Bin Lobby',       floor: 'Lantai Dasar',    capacity: 17 }
  ];

  // aturan klasifikasi status
  const classify = pct =>
    pct >= 75 ? 'Penuh' : (pct >= 50 ? 'Hampir Penuh' : 'Aman');

  let penuh = 0, hampir = 0, aman = 0;
  bins.forEach(b => {
    const s = classify(b.capacity);
    if (s === 'Penuh') penuh++;
    else if (s === 'Hampir Penuh') hampir++;
    else aman++;
  });

  const totalBin = bins.length || 1; // biar aman kalau 0
  const labels   = ['Aman', 'Hampir Penuh', 'Penuh'];
  const counts   = [aman, hampir, penuh];
  const percents = counts.map(v => Math.round((v / totalBin) * 100));

  // ========== UPDATE RINGKASAN DI KARTU ==========
  document.getElementById('sum-total').textContent  = bins.length;
  document.getElementById('sum-penuh').textContent  = penuh;
  document.getElementById('sum-hampir').textContent = hampir;
  document.getElementById('sum-aman').textContent   = aman;

  // ========== RENDER CHART ==========
  const canvas = document.getElementById('statusChart');
  if (!canvas) return;

  const ctx = canvas.getContext('2d');

  // register plugin datalabels (Chart.js v4)
  Chart.register(ChartDataLabels);

  const maxPercent = Math.max(...percents, 100);
  let niceMax = 100;
  if (maxPercent <= 25) niceMax = 25;
  else if (maxPercent <= 50) niceMax = 50;
  else if (maxPercent <= 75) niceMax = 75;

  new Chart(ctx, {
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

  // ========== LEGEND DI BAWAH CHART ==========
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
});
