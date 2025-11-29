<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #003366, #007bff);
    font-family: 'Segoe UI', sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}
.form-container {
    background: #e3f2fd;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.50);
    padding: 30px 35px;
    width: 100%;
    max-width: 400px;
    transition: all 0.3s ease;
}
.form-container img {
    display: block;
    margin: 0 auto 20px auto;
    border-radius: 60%;
    width: 100px;
    height: 100px;
    object-fit: contain;
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
    background: #1a237e;
}
h4 {
    font-weight: bold;
    font-size: 30px;
    color: #007bff;
}
/* 🔹 Tambahan CSS untuk center bagian "Masuk sebagai" */
.role-section {
    text-align: center;
}
.role-section .form-check {
    display: inline-block;
    margin: 0 10px;
}
</style>
</head>
<body>
  <div class="form-container">
    <img src="logono.jpeg" alt="logo">
    <h4 class="text-center mb-3">Notulen Tracker</h4>

    <form id="loginform" onsubmit="return cekLogin()">
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" id="email" placeholder="Masukkan email" required>
      </div>

      <div class="mb-4">
        <label class="form-label">Kata Sandi</label>
        <input type="password" class="form-control" id="password" placeholder="Masukkan kata sandi" required>
      </div>

      <!-- 🔹 Bagian "Masuk sebagai" sudah dibuat center -->
      <div class="mb-3 role-section">
        <label class="form-label d-block mb-1 fw-semibold">Masuk sebagai:</label>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" id="notulis" name="aktor" value="notulis">
          <label class="form-check-label" for="notulis">Notulis</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" id="peserta" name="aktor" value="peserta">
          <label class="form-check-label" for="peserta">Peserta</label>
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-100">Masuk</button>
    </form>

    <a href="katasandi.html" class="text-decoration-none" style="font-size: 13px;">Lupa Kata Sandi?</a>
    <p class="text-center mt-3 mb-0" style="font-size:13px;">
      Belum punya akun? 
      <a href="daftar.html" class="text-decoration-none" style="color:#007bff;">Daftar</a>
      <a href="#" class="text-decoration-none" style="color:#000000;">|</a>
      <a href="welcome.html" class="text-decoration-none" style="color:#007bff;">Kembali</a>
    </p>
  </div>
  
<script>
  function cekLogin() {
    const email = document.getElementById('email').value;
    const pass = document.getElementById('password').value;
    const notulis = document.getElementById('notulis').checked;
    const peserta = document.getElementById('peserta').checked;
      
    if (email.trim() === "" || pass.trim() === "") {
      alert("Silakan isi semua kolom!");
      return false;
    }

    if (!notulis && !peserta) {
      alert("Silakan pilih peran login terlebih dahulu!");
      return false;
    }

    if (notulis && peserta) {
      alert("Hanya boleh memilih salah satu peran!");
      return false;
    }

    let peran = notulis ? "Notulis" : "Peserta";
    alert("Berhasil masuk sebagai " + peran);
    return false;
  }

  const form = document.getElementById('loginform');
  form.addEventListener('submit', function(event) {
    event.preventDefault();
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const notulis = document.getElementById('notulis').checked;
    const peserta = document.getElementById('peserta').checked;

    if (email === '' || password === '' ) {
      alert('Email dan password harus diisi!');
    } else if (!notulis && !peserta) {
      alert('Pilih peran login terlebih dahulu!');
    } else if (notulis && peserta) {
      alert('Hanya boleh memilih satu peran!');
    } else {
      if (notulis) {
        window.location.href = 'dashboard.php';
      } else {
        window.location.href = 'dashboard.php';
      }
    }
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
