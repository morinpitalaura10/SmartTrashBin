// script.js — hanya SPA di index.html; grafik.html pakai halaman terpisah
document.addEventListener('componentsLoaded', () => {
  // ===== Dark mode di semua halaman =====
  const themeToggle = document.getElementById('toggle-theme');
  const saved = localStorage.getItem('stb-theme');
  if (saved === 'dark') {
    document.body.classList.add('dark-mode');
    if (themeToggle) themeToggle.checked = true;
  }
  themeToggle?.addEventListener('change', () => {
    const dark = themeToggle.checked;
    document.body.classList.toggle('dark-mode', dark);
    localStorage.setItem('stb-theme', dark ? 'dark' : 'light');
  });

  // ===== Deteksi halaman saat ini =====
  const current = (location.pathname.split('/').pop() || 'index.html').toLowerCase();

  // Set active menu berdasarkan halaman
  function markActiveByPage() {
    document.querySelectorAll('.menu a').forEach(a => a.classList.remove('active'));
    if (current.includes('index') || current === '') {
      document.getElementById('link-dashboard')?.classList.add('active');
    } else if (current.includes('riwayat')) {
      document.getElementById('link-riwayat')?.classList.add('active');
    } else if (current.includes('grafik')) {
      document.getElementById('link-grafik')?.classList.add('active');
    }
  }
  markActiveByPage();

  // ===== SPA hanya untuk index.html =====
  if (!(current.includes('index') || current === '')) {
    // Bukan index.html → selesai (biarkan grafik.html/riwayat.html jalan sendiri)
    return;
  }

  // --- Index.html: siapkan container konten dinamis
  const content = document.getElementById('content');
  let page = document.getElementById('page');
  if (!page) {
    page = document.createElement('div');
    page.id = 'page';
    content.appendChild(page);
  }

  // ===== Render Dashboard =====
  function renderDashboard() {
    page.innerHTML = `
      <div class="cards">
        <div class="card card-total">
          <img src="assets/icon-total.png" alt="Total" class="card-icon">
          <div>
            <div class="title">Total Tempat Sampah</div>
            <div class="value">5</div>
          </div>
        </div>
        <div class="card card-penuh">
          <img src="assets/icon-penuh.png" alt="Penuh" class="card-icon">
          <div>
            <div class="title">Tempat Sampah Penuh</div>
            <div class="value">2</div>
          </div>
        </div>
        <div class="card card-kosong">
          <img src="assets/icon-kosong.png" alt="Kosong" class="card-icon">
          <div>
            <div class="title">Tempat Sampah Kosong</div>
            <div class="value">3</div>
          </div>
        </div>
      </div>

      <h2 class="section-title">Data Tempat Sampah</h2>

      <section class="table-card">
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th><th>Nama</th><th>Lantai</th><th>Lokasi</th>
              <th>Kapasitas</th><th>Status</th><th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>B001</td><td>Bin Utama</td><td>Lantai 1</td><td>Dekat Pintu Masuk</td>
              <td class="kapasitas">
                <div class="progress"><div class="progress-bar" style="width:85%"></div></div>
                <span class="progress-text">85%</span>
              </td>
              <td><span class="badge badge-hampir">Hampir Penuh</span></td>
              <td><button class="btn-kosongkan">Kosongkan</button></td>
            </tr>
            <tr><td>B002</td><td>Bin Koridor</td><td>Lantai 2</td><td>Ujung Koridor Utara</td>
              <td class="kapasitas">
                <div class="progress"><div class="progress-bar" style="width:15%"></div></div>
                <span class="progress-text">15%</span>
              </td>
              <td><span class="badge badge-aman">Aman</span></td>
              <td><button class="btn-kosongkan">Kosongkan</button></td>
            </tr>
            <tr><td>B003</td><td>Bin Dapur</td><td>Lantai 1</td><td>Area Dapur Karyawan</td>
              <td class="kapasitas">
                <div class="progress"><div class="progress-bar" style="width:8%"></div></div>
                <span class="progress-text">8%</span>
              </td>
              <td><span class="badge badge-aman">Aman</span></td>
              <td><button class="btn-kosongkan">Kosongkan</button></td>
            </tr>
            <tr><td>B004</td><td>Bin Ruang Rapat</td><td>Lantai 3</td><td>Dekat Ruang Anggrek</td>
              <td class="kapasitas">
                <div class="progress"><div class="progress-bar" style="width:90%"></div></div>
                <span class="progress-text">90%</span>
              </td>
              <td><span class="badge badge-penuh">Penuh</span></td>
              <td><button class="btn-kosongkan">Kosongkan</button></td>
            </tr>
            <tr><td>B005</td><td>Bin Lobby</td><td>Lantai Dasar</td><td>Samping Lift Utama</td>
              <td class="kapasitas">
                <div class="progress"><div class="progress-bar" style="width:17%"></div></div>
                <span class="progress-text">17%</span>
              </td>
              <td><span class="badge badge-aman">Aman</span></td>
              <td><button class="btn-kosongkan">Kosongkan</button></td>
            </tr>
          </tbody>
        </table>
      </section>
    `;
  }

  // ===== Ganti menu aktif (khusus index) =====
  function setActiveLink(linkId) {
    document.querySelectorAll('.menu a').forEach(a => a.classList.remove('active'));
    document.getElementById(linkId)?.classList.add('active');
  }

  // ===== Event menu di index =====
  // Dashboard
  document.getElementById('link-dashboard')?.addEventListener('click', (e) => {
    e.preventDefault();
    const header = document.querySelector('.dashboard-header');
    header?.classList.remove('hidden');
    header?.querySelector('h1') && (header.querySelector('h1').textContent = 'SMART TRASH BIN DASHBOARD');
    header?.querySelector('p') && (header.querySelector('p').textContent = 'Smart monitoring for cleaner environment');
    header?.querySelector('.notif-badge') && (header.querySelector('.notif-badge').textContent = '');

    renderDashboard();
    setActiveLink('link-dashboard');
  });

  // Riwayat
  document.getElementById('link-riwayat')?.addEventListener('click', (e) => {
    e.preventDefault();

    const header = document.querySelector('.dashboard-header');
    header?.classList.remove('hidden');
    header?.querySelector('h1') && (header.querySelector('h1').textContent = 'Riwayat Pengosongan Tempat Sampah');
    header?.querySelector('p') && (header.querySelector('p').textContent = 'Smart monitoring for cleaner environment');
    header?.querySelector('.notif-badge') && (header.querySelector('.notif-badge').textContent = '');

    page.innerHTML = `
      <section class="table-card">
        <h5 class="section-title section-title-left">Daftar Riwayat</h5>
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th><th>Nama</th><th>Lantai</th><th>Waktu</th><th>Petugas</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>TS-1001</td><td>Tempat Sampah C</td><td>Lantai 1</td><td>05 November 2025, 00:52</td><td>Wildan</td></tr>
            <tr><td>TS-1002</td><td>Tempat Sampah E</td><td>Lantai 2</td><td>04 November 2025, 05:05</td><td>Dika</td></tr>
            <tr><td>TS-1003</td><td>Tempat Sampah C</td><td>Lantai 1</td><td>03 November 2025, 02:12</td><td>Dicky</td></tr>
            <tr><td>TS-1004</td><td>Tempat Sampah D</td><td>Lantai 3</td><td>02 November 2025, 22:13</td><td>Cevi</td></tr>
            <tr><td>TS-1005</td><td>Tempat Sampah A</td><td>Lantai 2</td><td>01 November 2025, 05:08</td><td>Dhani</td></tr>
            <tr><td>TS-1006</td><td>Tempat Sampah B</td><td>Lantai 3</td><td>31 Oktober 2025, 19:01</td><td>Morin</td></tr>
          </tbody>
        </table>
      </section>
    `;
    setActiveLink('link-riwayat');
  });

  // Penting: JANGAN preventDefault link-grafik — biarkan ke grafik.html
  // document.getElementById('link-grafik')...  <-- dihapus

  // Render awal dashboard di index
  renderDashboard();
});
