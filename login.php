<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login</title>
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

/* Modern peran card */
.role-section {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 20px;
}
.role-card {
    border: 1px solid #90caf9;
    border-radius: 10px;
    padding: 10px 20px;
    cursor: pointer;
    transition: 0.3s;
    user-select: none;
    text-align: center;
}
.role-card.active {
    background: #007bff;
    color: white;
    border-color: #007bff;
}
.role-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* Password toggle dengan input group */
.input-group .btn {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}
</style>
</head>
<body>

<div class="form-container">
    <img src="logono.jpeg" alt="logo">
    <h4 class="text-center mb-3">Notulen Tracker</h4>

    <form id="loginform">
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" id="email" placeholder="Masukkan email" required>
      </div>

      <!-- Password dengan input group Bootstrap -->
      <div class="mb-4">
        <label class="form-label">Kata Sandi</label>
        <div class="input-group">
          <input type="password" class="form-control" id="password" placeholder="Masukkan kata sandi" required>
          <button type="button" class="btn btn-outline-secondary d-flex align-items-center" onclick="togglePassword()">
            <i class="bi bi-eye" id="passwordIcon"></i>
          </button>
        </div>
      </div>

      <!-- Modern peran card -->
      <div class="role-section">
        <div class="role-card" data-role="notulis">Notulis</div>
        <div class="role-card" data-role="peserta">Peserta</div>
      </div>

      <button type="submit" class="btn btn-primary w-100">Masuk</button>
    </form>

    <a href="katasandi.php" class="text-decoration-none" style="font-size: 13px;">Lupa Kata Sandi?</a>
    <p class="text-center mt-3 mb-0" style="font-size:13px;">
      Belum punya akun? 
      <a href="daftar.php" class="text-decoration-none" style="color:#007bff;">Daftar</a>
      <a href="#" class="text-decoration-none" style="color:#000000;">|</a>
      <a href="welcome.php" class="text-decoration-none" style="color:#007bff;">Kembali</a>
    </p>
</div>

<script>
let selectedRole = null;

// Toggle password show/hide
function togglePassword() {
    const passInput = document.getElementById('password');
    const icon = document.getElementById('passwordIcon');
    if(passInput.type === "password") {
        passInput.type = "text";
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        passInput.type = "password";
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

// Role selection
document.querySelectorAll('.role-card').forEach(card => {
    card.addEventListener('click', () => {
        document.querySelectorAll('.role-card').forEach(c => c.classList.remove('active'));
        card.classList.add('active');
        selectedRole = card.getAttribute('data-role');
    });
});

// Login validation
document.getElementById('loginform').addEventListener('submit', function(e){
    e.preventDefault();
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    if(email === "" || password === "") {
        alert("Silakan isi semua kolom!");
        return;
    }
    if(!selectedRole) {
        alert("Silakan pilih peran terlebih dahulu!");
        return;
    }

    alert(`Berhasil login sebagai ${selectedRole}`);
    window.location.href = "dashboard.php";
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
