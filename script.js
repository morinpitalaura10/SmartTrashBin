document.addEventListener('DOMContentLoaded', () => {
    const content = document.getElementById('content');

    // ===== Fungsi render Dashboard =====
    function renderDashboard() {
        content.innerHTML = `
            <div class="dashboard-header">
                <h1>SMART TRASH BIN DASHBOARD</h1>
                <p>Smart monitoring for cleaner environment</p>
            </div>

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

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th><th>Nama</th><th>Lantai</th><th>Lokasi</th>
                            <th>Kapasitas</th><th>Status</th><th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>B001</td><td>Bin Utama</td><td>Lantai 1</td><td>Dekat Pintu Masuk</td><td>85%</td><td><span class="status-hampir">Hampir Penuh</span></td><td><button class="btn-kosongkan">Kosongkan</button></td></tr>
                        <tr><td>B002</td><td>Bin Koridor</td><td>Lantai 2</td><td>Ujung Koridor Utara</td><td>15%</td><td><span class="status-aman">Aman</span></td><td><button class="btn-kosongkan">Kosongkan</button></td></tr>
                        <tr><td>B003</td><td>Bin Dapur</td><td>Lantai 1</td><td>Area Dapur Karyawan</td><td>8%</td><td><span class="status-aman">Aman</span></td><td><button class="btn-kosongkan">Kosongkan</button></td></tr>
                        <tr><td>B004</td><td>Bin Ruang Rapat</td><td>Lantai 3</td><td>Dekat Ruang Anggrek</td><td>90%</td><td><span class="status-penuh">Penuh</span></td><td><button class="btn-kosongkan">Kosongkan</button></td></tr>
                        <tr><td>B005</td><td>Bin Lobby</td><td>Lantai Dasar</td><td>Samping Lift Utama</td><td>17%</td><td><span class="status-aman">Aman</span></td><td><button class="btn-kosongkan">Kosongkan</button></td></tr>
                        <tr><td>B006</td><td>Bin Kantin</td><td>Lantai Dasar</td><td>Pojok Kantin</td><td>100%</td><td><span class="status-penuh">Penuh</span></td><td><button class="btn-kosongkan">Kosongkan</button></td></tr>
                    </tbody>
                </table>
            </div>
        `;
    }

    // ===== Ganti menu aktif =====
    function setActiveLink(linkId) {
        document.querySelectorAll('.menu a').forEach(a => a.classList.remove('active'));
        const link = document.getElementById(linkId);
        if (link) link.classList.add('active');
    }

    // ===== Event untuk setiap menu =====
    document.getElementById('link-dashboard').addEventListener('click', (e) => {
        e.preventDefault();
        renderDashboard();
        setActiveLink('link-dashboard');
    });

    document.getElementById('link-riwayat').addEventListener('click', (e) => {
        e.preventDefault();
        content.innerHTML = '<h1>Riwayat</h1><p>Data riwayat akan ditampilkan di sini.</p>';
        setActiveLink('link-riwayat');
    });

    document.getElementById('link-grafik').addEventListener('click', (e) => {
        e.preventDefault();
        content.innerHTML = '<h1>Grafik</h1><p>Grafik akan ditampilkan di sini.</p>';
        setActiveLink('link-grafik');
    });

    // ===== Toggle Dark Mode =====
    const toggle = document.getElementById('toggle-theme');
    toggle.addEventListener('change', () => {
        document.body.classList.toggle('dark-mode', toggle.checked);
    });

    // ===== Load Dashboard pertama kali =====
    renderDashboard();

    // ===== Load Footer (opsional) =====
    fetch("components/footer.html")
        .then(res => res.text())
        .then(data => {
            const footer = document.getElementById("footer");
            if (footer) footer.innerHTML = data;
        });
});
