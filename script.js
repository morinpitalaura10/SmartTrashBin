// script.js — SPA hanya untuk index.html; grafik.html & riwayat.html adalah halaman terpisah
document.addEventListener('componentsLoaded', () => {
  // ====== Dark mode di semua halaman ======
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

    // ====== Tandai menu aktif berdasarkan halaman ======
  const current = (location.pathname.split('/').pop() || 'index.html').toLowerCase();

  function markActiveByPage() {
    document.querySelectorAll('.menu a').forEach(a => a.classList.remove('active'));

    // index.html, dashboard_ob.php, dashboard_admin.php -> dianggap halaman Dashboard
    if (
      current.includes('index') ||
      current === '' ||
      current.includes('dashboard_ob') ||
      current.includes('dashboard_admin')
    ) {
      document.getElementById('link-dashboard')?.classList.add('active');
    } else if (current.includes('riwayat')) {
      document.getElementById('link-riwayat')?.classList.add('active');
    } else if (current.includes('grafik')) {
      document.getElementById('link-grafik')?.classList.add('active');
    }
  }
  markActiveByPage();

  // ====== Halaman mana yang pakai dashboard SPA? ======
  const isDashboardPage =
    current.includes('index') ||
    current === '' ||
    current.includes('dashboard_ob') ||
    current.includes('dashboard_admin');

  // Kalau bukan halaman dashboard, jangan lanjut ke logika SPA
  if (!isDashboardPage) return;

  // =========================
  // ====== Dashboard SPA ====
  // =========================


  const content = document.getElementById('content');
  let page = document.getElementById('page');
  if (!page) {
    page = document.createElement('div');
    page.id = 'page';
    content.appendChild(page);
  }
  const basePath = location.pathname.toLowerCase().includes('/pages/')
    ? '../'
    : '';
  // ---- Aturan status sesuai threshold:
  // < 50%  => Aman
  // 50–74% => Hampir Penuh
  // ≥ 75%  => Penuh
  function getStatusFromPercent(p) {
    if (p >= 75) return { label: 'Penuh', cls: 'badge-penuh' };
    if (p >= 50) return { label: 'Hampir Penuh', cls: 'badge-hampir' };
    return { label: 'Aman', cls: 'badge-aman' };
  }

  // ---- Render Dashboard
  function renderDashboard() {
    page.innerHTML = `
      <div class="cards">
        <div class="card card-total">
          <img src="${basePath}assets/icon-total.png" alt="Total" class="card-icon">
          <div>
            <div class="title">Total Tempat Sampah</div>
            <div class="value">5</div>
          </div>
        </div>
        <div class="card card-penuh">
          <img src="${basePath}assets/icon-penuh.png" alt="Penuh" class="card-icon">
          <div>
            <div class="title">Tempat Sampah Penuh</div>
            <div class="value">2</div>
          </div>
        </div>
        <div class="card card-kosong">
          <img src="${basePath}assets/icon-kosong.png" alt="Kosong" class="card-icon">
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
              <tr data-id-bin="1">
                  <td>B001</td><td>Bin Utama</td><td>Lantai 1</td><td>Dekat Pintu Masuk</td>
                  <td class="kapasitas">
                      <div class="progress"><div class="progress-bar" style="width:85%"></div></div>
                      <span class="progress-text">85%</span>
                  </td>
                  <td></td>
                  <td><button class="btn-kosongkan">Kosongkan</button></td>
              </tr>

              <tr data-id-bin="2">
                  <td>B002</td><td>Bin Koridor</td><td>Lantai 2</td><td>Ujung Koridor Utara</td>
                  <td class="kapasitas">
                      <div class="progress"><div class="progress-bar" style="width:58%"></div></div>
                      <span class="progress-text">58%</span>
                  </td>
                  <td></td>
                  <td><button class="btn-kosongkan">Kosongkan</button></td>
              </tr>

              <tr data-id-bin="3">
                  <td>B003</td><td>Bin Dapur</td><td>Lantai 1</td><td>Area Dapur Karyawan</td>
                  <td class="kapasitas">
                      <div class="progress"><div class="progress-bar" style="width:8%"></div></div>
                      <span class="progress-text">8%</span>
                  </td>
                  <td></td>
                  <td><button class="btn-kosongkan">Kosongkan</button></td>
              </tr>

              <tr data-id-bin="4">
                  <td>B004</td><td>Bin Ruang Rapat</td><td>Lantai 3</td><td>Dekat Ruang Anggrek</td>
                  <td class="kapasitas">
                      <div class="progress"><div class="progress-bar" style="width:90%"></div></div>
                      <span class="progress-text">90%</span>
                  </td>
                  <td></td>
                  <td><button class="btn-kosongkan">Kosongkan</button></td>
              </tr>

              <tr data-id-bin="5">
                  <td>B005</td><td>Bin Lobby</td><td>Lantai Dasar</td><td>Samping Lift Utama</td>
                  <td class="kapasitas">
                      <div class="progress"><div class="progress-bar" style="width:17%"></div></div>
                      <span class="progress-text">17%</span>
                  </td>
                  <td></td>
                  <td><button class="btn-kosongkan">Kosongkan</button></td>
              </tr>
          </tbody>
        </table>
      </section>
    `;

    // Setelah render, sinkronkan status & kartu ringkasan
    refreshStatuses();
    recomputeCards();
  }

  // ---- Baca persentase dari baris
  function getPercentFromRow(tr) {
    const pctText = tr.querySelector('.progress-text')?.textContent || '0%';
    return parseInt(pctText, 10) || 0;
  }

  // ---- Tulis status per-baris sesuai persentase
  function refreshStatuses() {
    document.querySelectorAll('.data-table tbody tr').forEach(tr => {
      const p = getPercentFromRow(tr);
      const s = getStatusFromPercent(p);
      const statusCell = tr.children[5]; // kolom "Status"
      if (statusCell) {
        statusCell.innerHTML = `<span class="badge ${s.cls}">${s.label}</span>`;
      }
    });
  }

  // ---- Hitung ulang kartu ringkasan (total/penuh/kosong)
  function recomputeCards() {
    const rows = Array.from(document.querySelectorAll('.data-table tbody tr'));
    const total = rows.length;
    let penuh = 0;
    let kosong = 0;

    rows.forEach(tr => {
      const p = getPercentFromRow(tr);
      if (p >= 75) penuh += 1;
      if (p === 0)  kosong += 1;
    });

    const totalEl  = document.querySelector('.card-total .value');
    const penuhEl  = document.querySelector('.card-penuh .value');
    const kosongEl = document.querySelector('.card-kosong .value');
    if (totalEl)  totalEl.textContent  = total;
    if (penuhEl)  penuhEl.textContent  = penuh;
    if (kosongEl) kosongEl.textContent = kosong;
  }

  // ---- Delegasi klik untuk tombol "Kosongkan"
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-kosongkan');
    if (!btn) return;

    const tr = btn.closest('tr');
    const idBin = tr.getAttribute('data-id-bin'); // pastikan <tr> punya data-id-bin

    if (!idBin) {
      alert('ID tempat sampah tidak ditemukan.');
      return;
    }

    if (!confirm('Yakin ingin mengosongkan tempat sampah ini?')) return;
    const apiBase = location.pathname.toLowerCase().includes('/pages/')
      ? ''        // lagi di /pages → /pages/kosongkan.php
      : 'pages/';

    fetch(apiBase + 'kosongkan.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: 'id_bin=' + encodeURIComponent(idBin)
    })
      .then(res => res.json())
      .then(res => {
        if (!res.success) {
          alert(res.message || 'Gagal mengosongkan tempat sampah.');
          return;
        }

        // Update UI jadi 0% (bisa pakai data dari res.data)
        const bar = tr.querySelector('.progress-bar');
        const txt = tr.querySelector('.progress-text');

        if (bar) bar.style.width = '0%';
        if (txt) txt.textContent = '0%';

        // (opsional) ubah status warna di tabel
        // lalu hitung ulang kartu ringkasan
        refreshStatuses();
        recomputeCards();

        alert('Tempat sampah berhasil dikosongkan.');
      })
      .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan pada server.');
      });
  });


  // ===== Event menu (khusus index) =====
  // Dashboard
  document.getElementById('link-dashboard')?.addEventListener('click', (e) => {
    e.preventDefault();
    const header = document.querySelector('.dashboard-header');
    header?.classList.remove('hidden');
    header?.querySelector('h1') && (header.querySelector('h1').textContent = 'SMART TRASH BIN DASHBOARD');
    header?.querySelector('p') && (header.querySelector('p').textContent = 'Smart monitoring for cleaner environment');
    header?.querySelector('.notif-badge') && (header.querySelector('.notif-badge').textContent = '');

    renderDashboard();
    document.querySelectorAll('.menu a').forEach(a => a.classList.remove('active'));
    document.getElementById('link-dashboard')?.classList.add('active');
  });

  // Riwayat (SPA section sederhana—kalau kamu pakai riwayat.html terpisah, boleh di-skip)
    // Riwayat
  document.getElementById('link-riwayat')?.addEventListener('click', (e) => {
    const isPages = location.pathname.toLowerCase().includes('/pages/');

    // Kalau lagi di halaman PHP (/pages/...), pakai backend riwayat_ob.php
    if (isPages) {
      // BIAR pindah halaman, jadi JANGAN preventDefault
      window.location.href = 'riwayat_ob.php';
      return;
    }

    // Kalau di index.html (root), tetap pakai versi SPA dummy
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

    document.querySelectorAll('.menu a').forEach(a => a.classList.remove('active'));
    document.getElementById('link-riwayat')?.classList.add('active');
  });


  // Penting: link Grafik biarkan pindah halaman (grafik.html)
  // document.getElementById('link-grafik') ... (JANGAN preventDefault)

  // ---- Render awal dashboard
  renderDashboard();
});
// ============================
// LOGIN PAGE ONLY
// ============================
(function () {
  const page = (location.pathname.split('/').pop() || '').toLowerCase();
  if (page !== 'login.html') return;

  // Dummy cred
  const VALID_EMAIL = 'admin@stb.com';
  const VALID_PASS  = 'admin123';

  const form    = document.getElementById('loginForm');
  const email   = document.getElementById('email');
  const pass    = document.getElementById('password');
  const show    = document.getElementById('showPass');
  const remember= document.getElementById('remember');
  const err     = document.getElementById('err');
  const loading = document.getElementById('loading');
  const card    = document.getElementById('card');

  // restore remembered email
  const remembered = localStorage.getItem('stb-remember-email');
  if (remembered) { email.value = remembered; remember.checked = true; }

  // toggle show password
  show?.addEventListener('change', () => {
    pass.type = show.checked ? 'text' : 'password';
  });

  function showError(msg){
    err.style.display = 'block';
    err.textContent = msg;
    card.style.animation = 'shake .5s';
    setTimeout(() => card.style.animation = '', 500);
  }

  form?.addEventListener('submit', (e) => {
    e.preventDefault();
    err.style.display = 'none';

    const em = (email.value || '').trim();
    const pw = (pass.value || '').trim();

    if (!em || !pw) {
      showError('Email / kata sandi belum diisi.');
      return;
    }
    // very simple email check
    const okEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em);
    if (!okEmail) {
      showError('Format email tidak valid.');
      return;
    }
    // validate dummy cred
    if (em !== VALID_EMAIL || pw !== VALID_PASS) {
      showError('Email atau kata sandi salah.');
      return;
    }

    // remember
    if (remember.checked) localStorage.setItem('stb-remember-email', em);
    else localStorage.removeItem('stb-remember-email');

    // auth flag
    localStorage.setItem('stb-auth', '1');

    // show loading then go to dashboard
    loading.classList.add('active');
    setTimeout(() => location.href = 'index.html', 900);
  });
})();
