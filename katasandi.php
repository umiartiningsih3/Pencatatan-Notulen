<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Lupa Kata Sandi</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #003366, #007bff);
    font-family: 'Segoe UI', sans-serif;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px 0;
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
.form-container:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.40);
}
.form-control {
    font-size: 14px;
    border: 1px solid #90caf9;
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
    font-size: 28px;
    color: #007bff;
}
.input-group .btn {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}
</style>
</head>
<body>

<div class="form-container">
    <h4 class="text-center mb-3">Lupa Kata Sandi</h4>

    <form id="forgotForm">
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" id="email" placeholder="Masukkan email Anda" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Kata Sandi Baru</label>
        <div class="input-group">
          <input type="password" class="form-control" id="newPassword" placeholder="Kata sandi baru" required>
          <button type="button" class="btn btn-outline-secondary d-flex align-items-center" onclick="togglePassword('newPassword', 'newPasswordIcon')">
            <i class="bi bi-eye" id="newPasswordIcon"></i>
          </button>
        </div>
        <small class="text-danger">Minimal 8 karakter</small>
      </div>

      <div class="mb-4">
        <label class="form-label">Ulangi Kata Sandi Baru</label>
        <div class="input-group">
          <input type="password" class="form-control" id="confirmPassword" placeholder="Ulangi kata sandi" required>
          <button type="button" class="btn btn-outline-secondary d-flex align-items-center" onclick="togglePassword('confirmPassword', 'confirmPasswordIcon')">
            <i class="bi bi-eye" id="confirmPasswordIcon"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-100">Reset Kata Sandi</button>
    </form>

    <p class="text-center mt-3 mb-0" style="font-size:13px;">
      Kembali ke <a href="login.php" class="text-decoration-none" style="color:#007bff;">Login</a>
    </p>
</div>

<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if(input.type === "password") {
        input.type = "text";
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = "password";
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

document.getElementById('forgotForm').addEventListener('submit', function(e){
    e.preventDefault();
    const email = document.getElementById('email').value;
    const newPass = document.getElementById('newPassword').value;
    const confirmPass = document.getElementById('confirmPassword').value;

    if(email === "" || newPass === "" || confirmPass === "") {
        alert("Silakan isi semua kolom!");
        return;
    }

    if(newPass.length < 8) {
        alert("Kata sandi minimal 8 karakter!");
        return;
    }

    if(newPass !== confirmPass) {
        alert("Kata sandi baru dan konfirmasi tidak sama!");
        return;
    }

    alert("Kata sandi berhasil diubah!");
    // window.location.href = "login.php"; // uncomment jika pakai PHP
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
