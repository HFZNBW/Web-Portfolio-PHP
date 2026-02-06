<?php
session_start();
// Jika sudah login, langsung lempar ke dashboard
if (isset($_SESSION['status']) && $_SESSION['status'] == "login") {
    header("location:dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventaris RT04 Dk. Ngemplak Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 15px;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .card-header {
            background: transparent;
            border: none;
            padding-top: 30px;
        }

        .btn-primary {
            background: #764ba2;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background: #667eea;
            transform: translateY(-2px);
        }

        .form-control {
            border-radius: 10px;
            padding: 12px;
            background: #f8f9fa;
        }

        .alert {
            border-radius: 10px;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>

    <div class="login-container">
        <?php
        if (isset($_GET['pesan'])) {
            if ($_GET['pesan'] == "gagal") {
                echo "<div class='alert alert-danger text-center shadow-sm'>Username atau password salah!</div>";
            } else if ($_GET['pesan'] == "belum_login") {
                echo "<div class='alert alert-warning text-center shadow-sm'>Silakan login terlebih dahulu.</div>";
            } else if ($_GET['pesan'] == "sesi_berakhir") {
                echo "<div class='alert alert-danger text-center shadow-sm'><strong>Sesi Berakhir!</strong> Login terdeteksi di tab lain.</div>";
            } else if ($_GET['pesan'] == "logout") {
                echo "<div class='alert alert-success text-center shadow-sm'>Anda telah berhasil keluar.</div>";
            }
        }
        ?>

        <div class="card shadow">
            <div class="card-header text-center">
                <h3 class="fw-bold text-dark mb-0">Login System</h3>
                <p class="text-muted small">Inventaris RT04 Dk. Ngemplak Baru</p>
            </div>
            <div class="card-body px-4 pb-4">
                <form action="proses_login.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autocomplete="off">
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 shadow">Masuk Ke Dashboard</button>
                </form>
            </div>
            <div class="card-footer text-center bg-white border-0 pb-4">
                <small class="text-muted">&copy; 2026 RT04 Gentan</small>
            </div>
        </div>
    </div>

    <script>
        // BroadcastChannel untuk memantau tab baru
        const authChannel = new BroadcastChannel('rt_ngemplak_auth');

        // Jika ada tab baru yang dibuka, kirim pesan ke tab lain
        authChannel.postMessage({
            action: 'check_session'
        });

        authChannel.onmessage = (event) => {
            if (event.data.action === 'check_session') {
                // Jika tab lama menerima pesan ini, artinya ada tab baru yang mengakses link login/dashboard
                // Kita bisa memaksa logout atau sekadar alert
                console.log("Tab baru terdeteksi.");
            }
        };
    </script>
</body>

</html>