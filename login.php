<?php
session_start();
include 'koneksi.php';

// Jika user sudah dalam keadaan login, langsung alihkan ke dashboard utama
if (isset($_SESSION['username'])) {
    header("Location: index.php"); exit;
}

$error_msg = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); 

    // FIX LOGIKA: Mengubah nama tabel dari 'users' menjadi 'user' sesuai standar database kamu
    $query = "SELECT * FROM user WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Daftarkan session data user
        $_SESSION['username']     = $row['username'];
        $_SESSION['role']         = $row['role']; // 'ustadz' atau 'santri'
        $_SESSION['nama_lengkap']  = $row['nama_lengkap'];
        
        // Jika login sebagai santri, daftarkan santri_id untuk filter data personal
        if ($row['role'] == 'santri') {
            $_SESSION['santri_id'] = $row['santri_id'];
        }

        header("Location: index.php"); exit;
    } else {
        $error_msg = "Username atau password Anda salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Sistem - Monitoring Tahfizh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-navy: #111e38;
            --accent-gold: #c5a059;
            --dark-navy: #0a1120;
            --bg-light: #f4f6f9;
        }

        body {
            background: linear-gradient(135deg, var(--dark-navy) 0%, var(--primary-navy) 100%);
            font-family: 'Plus Jakarta Sans', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }

        .login-card {
            background: #ffffff;
            width: 100%;
            max-width: 420px;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
        }

        .login-card::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 5px;
            background: var(--accent-gold);
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }

        .brand-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            display: inline-block;
        }

        .login-title {
            color: var(--primary-navy);
            font-weight: 800;
            letter-spacing: -0.5px;
            font-size: 1.6rem;
        }

        .login-subtitle {
            color: #64748b;
            font-size: 0.88rem;
            font-weight: 500;
        }

        .form-label {
            font-weight: 600;
            color: #334155;
            font-size: 0.85rem;
            margin-bottom: 6px;
        }

        .form-control {
            padding: 11px 14px;
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            font-size: 0.9rem;
            font-weight: 500;
            color: #1e293b;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--accent-gold);
            box-shadow: 0 0 0 3px rgba(197, 160, 89, 0.15);
            outline: none;
        }

        .form-hint {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 4px;
            display: block;
            font-weight: 500;
        }

        .btn-login {
            background: var(--primary-navy);
            color: #ffffff;
            border: 1px solid var(--primary-navy);
            padding: 12px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.95rem;
            width: 100%;
            transition: all 0.2s ease;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: var(--dark-navy);
            border-color: var(--dark-navy);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(10, 17, 32, 0.25);
        }

        .alert-custom {
            font-size: 0.82rem;
            padding: 10px 14px;
            border-radius: 8px;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="login-card text-center">
    
    <div class="mb-2">
        <span class="brand-icon">🕌</span>
    </div>
    
    <h3 class="login-title mb-1">E-TAHFIZH ACADEMIC</h3>
    <p class="login-subtitle mb-4">Sistem Monitoring Perkembangan Hafalan Santri</p>

    <?php if($error_msg != ""): ?>
        <div class="alert alert-danger alert-custom mb-3 text-start" role="alert">
            ⚠️ <?php echo $error_msg; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" class="text-start">
        
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan nama pengguna" required value="ustadz1">
            <span class="form-hint">Contoh: ustadz1 / ahmad</span>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="••••••" required value="123456">
            <span class="form-hint">Petunjuk default: 123456</span>
        </div>

        <button type="submit" name="login" class="btn btn-login shadow-sm">Masuk</button>
        
    </form>
</div>

</body>
</html>