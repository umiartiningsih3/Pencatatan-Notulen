<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Notulen Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #003366, #007bff);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            margin: 0;
        }
        .form-container {
            background: #e3f2fd;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            padding: 40px 35px;
            width: 100%;
            max-width: 400px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .form-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
        }
        .form-container img {
            display: block;
            margin: 0 auto 15px auto;
            border-radius: 50%;
            width: 90px;
            height: 90px;
            object-fit: cover;
            border: 3px solid #fff;
        }
        h4 {
            font-weight: 700;
            font-size: 26px;
            color: #003366;
            margin-bottom: 25px;
        }
        .form-label {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        .form-control {
            font-size: 14px;
            border: 1px solid #90caf9;
            border-radius: 10px;
            padding: 12px;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.25 red rgba(0, 123, 255, 0.25);
        }
        .btn-primary {
            background: #007bff;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            padding: 12px 0;
            margin-top: 10px;
            transition: background 0.3s ease;
        }
        .btn-primary:hover {
            background: #003366;
        }
        .forgot-link {
            font-size: 13px;
            color: #007bff;
            display: inline-block;
            margin-top: 15px;
            transition: color 0.2s;
        }
        .forgot-link:hover {
            color: #003366;
            text-decoration: underline !important;
        }
        /* Alert Styling */
        .alert {
            font-size: 13px;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <img src="logono.jpeg" alt="Logo Notulen Tracker">
    <h4 class="text-center">Notulen Tracker</h4>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>
                <?php 
                    if($_GET['error'] == 'wrong') echo "NIM atau Password salah!";
                    else if($_GET['error'] == 'empty') echo "Harap isi semua kolom!";
                ?>
            </div>
        </div>
    <?php endif; ?>

    <form id="loginform" action="proses_login.php" method="POST">
        <div class="mb-3">
            <label class="form-label">Nomor Induk Mahasiswa (NIM)</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0" style="border-radius: 10px 0 0 10px; border-color: #90caf9;">
                    <i class="bi bi-person text-muted"></i>
                </span>
                <input type="text" class="form-control border-start-0" 
                       name="nim" 
                       placeholder="Masukkan NIM Anda" 
                       style="border-radius: 0 10px 10px 0;"
                       required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Kata Sandi</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0" style="border-radius: 10px 0 0 10px; border-color: #90caf9;">
                    <i class="bi bi-lock text-muted"></i>
                </span>
                <input type="password" class="form-control border-start-0" 
                       name="password" 
                       placeholder="Masukkan kata sandi" 
                       style="border-radius: 0 10px 10px 0;"
                       required>
            </div>
            <div class="d-flex justify-content-between">
                <a href="katasandi.php" class="text-decoration-none forgot-link">Lupa Kata Sandi?</a>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
        </button>
    </form>
    
    <div class="text-center mt-4">
        <a href="index.php" class="text-decoration-none" style="color:#6c757d; font-size: 13px;">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Beranda
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>