<?php
// 1. WAJIB DI ATAS SENDIRI: Logic cek sesi dan BroadcastChannel
include "cek_sesi.php";
include "koneksi.php";

/* ================= STATISTIK ================= */
// Bagian ini baru dijalankan setelah cek_sesi.php diproses
$jml_barang = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM barang"));
$jml_pinjam = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM peminjaman WHERE status='Dipinjam'"));
$jml_user   = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users"));

// Ambil data session sesuai key di proses_login.php (PAKAI ISSET BIAR GAK WARNING)
$nama_user = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'User';
$role_user = isset($_SESSION['role']) ? $_SESSION['role'] : '';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard | Sistem Peminjaman Inventaris RT04</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f6f8fb;
            font-family: system-ui, -apple-system, Segoe UI, Roboto;
        }

        .navbar-main {
            background: #1F3C88;
        }

        .main-wrapper {
            max-width: 1200px;
            margin: auto;
            padding: 30px 20px;
        }

        .menu-card {
            background: #fff;
            border: none;
            border-radius: 14px;
            height: 190px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
            transition: all .2s ease;
        }

        .menu-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, .12);
        }

        .menu-icon {
            font-size: 34px;
            margin-bottom: 10px;
        }

        .stat-card {
            background: #fff;
            border: none;
            border-radius: 12px;
            padding: 18px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
        }

        .stat-number {
            font-weight: 700;
            font-size: 26px;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark navbar-main shadow-sm">
        <div class="container-fluid">
            <span class="navbar-brand fw-semibold">
                <i class="bi bi-houses"></i> Sistem Peminjaman Inventaris RT04
            </span>
            <div class="d-flex align-items-center gap-3">
                <a href="logout.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="main-wrapper">
        <h5 class="mb-4 text-secondary">
            Selamat datang!<br>
            <small>Sistem Peminjaman Inventaris Dk. Ngemplak Baru RT04 RW01 Gentan</small>
        </h5>

        <div class="row g-4">
            <div class="col-md-3">
                <a href="inventaris.php" class="text-decoration-none text-dark">
                    <div class="menu-card text-center">
                        <i class="bi bi-box-seam menu-icon text-primary"></i>
                        <h6 class="fw-bold">Data Barang</h6>
                        <small class="text-muted">Kelola inventaris RT</small>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="peminjaman.php" class="text-decoration-none text-dark">
                    <div class="menu-card text-center">
                        <i class="bi bi-arrow-left-right menu-icon text-success"></i>
                        <h6 class="fw-bold">Peminjaman</h6>
                        <small class="text-muted">Pinjam & kembalikan barang</small>
                    </div>
                </a>
            </div>

            <?php if ($role_user == 'admin'): ?>
                <div class="col-md-3">
                    <a href="data_user.php" class="text-decoration-none text-dark">
                        <div class="menu-card text-center">
                            <i class="bi bi-people-fill menu-icon text-warning"></i>
                            <h6 class="fw-bold">Manajemen User</h6>
                            <small class="text-muted">Kelola admin & petugas</small>
                        </div>
                    </a>
                </div>
            <?php endif; ?>

            <div class="col-md-3">
                <a href="cetak_pdf.php" target="_blank" class="text-decoration-none text-dark">
                    <div class="menu-card text-center">
                        <i class="bi bi-file-earmark-pdf-fill menu-icon text-danger"></i>
                        <h6 class="fw-bold">Laporan</h6>
                        <small class="text-muted">Cetak laporan PDF</small>
                    </div>
                </a>
            </div>
        </div>

        <div class="row mt-5 g-4 text-center">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-number text-primary"><?= $jml_barang ?></div>
                    <span class="text-muted">Total Barang</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-number text-success"><?= $jml_pinjam ?></div>
                    <span class="text-muted">Sedang Dipinjam</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-number text-warning"><?= $jml_user ?></div>
                    <span class="text-muted">Total User</span>
                </div>
            </div>
        </div>
    </div>
</body>

</html>