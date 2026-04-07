<?php
include 'koneksi.php';


if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    $query = "SELECT * FROM users WHERE remember_token = '$token' LIMIT 1";
    $result = mysqli_query($koneksi, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['nama_lengkap'] = $row['nama_lengkap'];
        header("Location: index.php");
        exit();
    }
}

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    $query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($koneksi, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['nama_lengkap'] = $row['nama_lengkap'];

            if ($remember) {
                $token = bin2hex(random_bytes(32));
                mysqli_query($koneksi, "UPDATE users SET remember_token = '$token' WHERE id = " . $row['id']);
                setcookie('remember_token', $token, time() + (86400 * 30), "/");
            }

            header("Location: index.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak terdaftar!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | SIGUDHANG</title>
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
            max-width: 400px;
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
        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 20px;
            cursor: pointer;
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
        .success-msg {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            border: 1px solid #c8e6c9;
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
            <h2>Login SIGUDHANG</h2>
            <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">Kelola stok gudang dengan mudah</p>
        </div>

        <?php if (isset($_SESSION['pesan'])): ?>
            <div class="success-msg">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['pesan']; unset($_SESSION['pesan']); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error-msg">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control-custom" placeholder="Masukkan username" required>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control-custom" placeholder="Masukkan password" required>
            </div>
            <label class="remember-me">
                <input type="checkbox" name="remember"> Ingat Saya
            </label>
            <button type="submit" class="btn-simpan" style="width: 100%;">Masuk Sekarang</button>
        </form>

        <div class="login-footer">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
        </div>
    </div>
</body>
</html>
