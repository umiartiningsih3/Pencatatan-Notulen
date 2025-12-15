<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Form Pendaftaran</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <style>
    body {
    min-height: 100vh;
    display: flex;
    justify-content: center;     /* center horizontal */
    align-items: center;         /* center vertical */
    background: linear-gradient(135deg, #003366, #007bff);
    padding-top: 20px;           /* jaga kalau form panjang */
    padding-bottom: 20px;        /* supaya tetap bisa scroll */
  }
    .form-container {
      background: #e3f2fd;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.50);
      padding: 20px 35px;
      width: 100%;
      max-width: 420px;
      margin: auto;
      transition: all 0.3s ease;
    }
    .form-container:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.40);
    }
    .form-control {
      font-size: 14px;
      border: 1px solid #90caf9;
      padding: 9px 10px;
      border-radius: 10px;
    }
    .btn-primary {
      background: #007bff;
      border: none;
      border-radius: 10px;
      font-size: 15px;
      font-weight: 500;
      padding: 10px 0;
      transition: 0.3s;
    }
    .btn-primary:hover {
      background: #003366;
    }
    h4 {
      font-weight: bold;
      font-size: 30px;
      color: #007bff;
    }
  </style>
</head>

<body>

  <div class="form-container">
    <h4 class="text-center mb-3">Daftar Akun Baru</h4>

    <form action="proses_daftar.php" method="POST" onsubmit="return validasiPassword()">
      
      <div class="mb-3">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" class="form-control" name="nama" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" name="email" required>
      </div>

      <!-- PASSWORD DENGAN IKON MODERN -->
      <div class="mb-3">
        <label class="form-label">Kata Sandi</label>
        <div class="input-group">
          <input type="password" class="form-control" name="password" id="password" required>
          <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password', this)">
            <i class="bi bi-eye"></i>
          </button>
        </div>
        <small class="text-danger">Minimal 8 karakter</small>
      </div>

      <div class="mb-4">
        <label class="form-label">Ulangi Kata Sandi</label>
        <div class="input-group">
          <input type="password" class="form-control" name="password2" id="konfirmasi" required>
          <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('konfirmasi', this)">
            <i class="bi bi-eye"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-100">Daftar Sekarang</button>
    </form>

    <p class="text-center mt-3 mb-0" style="font-size:13px;">
      Sudah punya akun? <a href="login.php" class="text-decoration-none" style="color:#007bff;">Masuk</a>
    </p>
  </div>



  <!-- SCRIPT -->
  <script>
    // === Fitur tampilkan/sembunyikan password dengan icon modern ===
    function togglePassword(id, btn) {
      const input = document.getElementById(id);
      const icon = btn.querySelector("i");

      if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
      } else {
        input.type = "password";
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
      }
    }

    // === Validasi password minimal 8 karakter + cocok ===
    function validasiPassword() {
      const pass = document.getElementById('password').value;
      const konfirmasi = document.getElementById('konfirmasi').value;
      const peran = document.getElementById('peran').value;

      if (pass.length < 8) {
        alert('Kata sandi minimal 8 karakter!');
        return false;
      }

      if (pass !== konfirmasi) {
        alert('Kata sandi dan konfirmasi tidak sama!');
        return false;
      }
      return true;
    }
  </script>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
