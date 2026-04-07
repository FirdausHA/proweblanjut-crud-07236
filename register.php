<?php
include 'koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Konfirmasi password tidak sesuai!";
    } else {
        $check_user = mysqli_query($koneksi, "SELECT id FROM users WHERE username = '$username'");
        if (mysqli_num_rows($check_user) > 0) {
            $error = "Username sudah terdaftar!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (nama_lengkap, username, password) VALUES ('$nama_lengkap', '$username', '$hashed_password')";
            if (mysqli_query($koneksi, $query)) {
                $_SESSION['pesan'] = "Registrasi berhasil! Silakan login.";
                $_SESSION['tipe'] = "success";
                header("Location: login.php");
                exit();
            } else {
                $error = "Terjadi kesalahan: " . mysqli_error($koneksi);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | SIGUDHANG</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="CSS/style.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f5f7f9;
        }
        .login-card {
            width: 100%;
            max-width: 450px;
            padding: 40px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-header .logo {
            font-size: 32px;
            color: var(--primary);
            margin-bottom: 16px;
            justify-content: center;
        }
        .login-header h2 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-dark);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }
        .error-msg {
            background: #fff5f5;
            color: #fa5252;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            border: 1px solid #ffe3e3;
        }
        .login-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: var(--text-muted);
        }
        .login-footer a {
            color: var(--primary);
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="logo">
                <i class="fas fa-boxes"></i>
            </div>
            <h2>Daftar SIGUDHANG</h2>
            <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">Buat akun inventaris gudang Anda</p>
        </div>

        <?php if ($error): ?>
            <div class="error-msg">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control-custom" placeholder="Nama Lengkap Anda" required>
            </div>
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control-custom" placeholder="Pilih username" required>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control-custom" placeholder="Minimal 6 karakter" required>
            </div>
            <div class="form-group">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="confirm_password" class="form-control-custom" placeholder="Ulangi password" required>
            </div>
            <button type="submit" class="btn-simpan" style="width: 100%; margin-top: 10px;">Daftar Sekarang</button>
        </form>

        <div class="login-footer">
            Sudah punya akun? <a href="login.php">Masuk di sini</a>
        </div>
    </div>
</body>
</html>
