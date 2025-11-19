<?php
session_start();
$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login | Smart Trash Bin</title>

  <!-- Font Awesome (ikon tombol Masuk) -->
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Styles Global -->
  <!-- PENTING: dari /pages ke root, jadi pakai ../ -->
  <link rel="stylesheet" href="../styles.css">
  <link rel="stylesheet" href="../css/variables.css">

  <!-- kalau script.js memang dipakai untuk animasi lain -->
  <script src="../script.js"></script>
</head>

<body class="auth-body with-photo">
  <div class="auth-wrap">
    <div class="auth-card" id="card">
      <div class="auth-brand">
        <!-- path logo dari /pages ke /assets -->
        <img src="../assets/logo.png" alt="Smart Trash Bin" class="auth-logo" />
      </div>

      <h1 class="auth-title">Masuk ke Smart Trash Bin</h1>
      <p class="auth-sub">Pantau dan kelola tempat sampah secara real-time</p>

      <!-- error dari backend (kalau login salah) -->
      <?php if ($error): ?>
        <div class="auth-error" style="margin-bottom:10px; text-align:center;">
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <!-- FORM LOGIN: pakai desain asli, tapi sekarang kirim ke login_process.php -->
      <form id="loginForm" method="POST" action="login_process.php" novalidate>

        <!-- USERNAME -->
        <div class="auth-field">
          <label for="username">Username</label>
          <input id="username" name="username" type="text" class="auth-input"
                 placeholder="••••••••" autocomplete="username" required />
          <div id="userErr" class="auth-error" style="display:none"></div>
        </div>

        <!-- PASSWORD -->
        <div class="auth-field">
          <label for="password">Password</label>
          <input id="password" name="password" type="password" class="auth-input"
                 placeholder="••••••••" autocomplete="current-password" required />
          <div id="passErr" class="auth-error" style="display:none"></div>
        </div>

        <div class="auth-actions">
          <label class="auth-check">
            <input type="checkbox" id="showPass" /> <span>Tampilkan Password</span>
          </label>
          <label class="auth-check">
            <input type="checkbox" id="remember" /> <span>Ingat saya</span>
          </label>
        </div>

        <button class="auth-btn" type="submit">
          <i class="fa-solid fa-right-to-bracket"></i> Masuk
        </button>
      </form>

      <!-- Loading overlay -->
      <div class="auth-loading" id="loading">
        <div class="auth-spinner"></div>
        <span>Memeriksa kredensial...</span>
      </div>
    </div>
  </div>

  <script>
    // ==== Toggle tampilkan password ====
    const passEl = document.getElementById('password');
    const showPass = document.getElementById('showPass');
    if (showPass) {
      showPass.addEventListener('change', () => {
        passEl.type = showPass.checked ? 'text' : 'password';
      });
    }

    const form     = document.getElementById('loginForm');
    const loading  = document.getElementById('loading');
    const userErr  = document.getElementById('userErr');
    const passErr  = document.getElementById('passErr');

    // VALIDASI KOSONG DI FRONTEND (tampilan sama, tapi sekarang form tetap kirim ke PHP)
    form.addEventListener('submit', (e) => {
      userErr.style.display = 'none';
      passErr.style.display = 'none';
      userErr.textContent = '';
      passErr.textContent = '';

      const username = document.getElementById('username').value.trim();
      const password = passEl.value.trim();

      let hasError = false;

      if (!username) {
        userErr.textContent = 'Username tidak boleh kosong.';
        userErr.style.display = 'block';
        hasError = true;
      }
      if (!password) {
        passErr.textContent = 'Password tidak boleh kosong.';
        passErr.style.display = 'block';
        hasError = true;
      }

      if (hasError) {
        e.preventDefault();
        return;
      }

      // kalau valid, tampilkan loading dan biarkan form submit ke login_process.php
      loading.classList.add('active');
    });
  </script>
</body>
</html>
