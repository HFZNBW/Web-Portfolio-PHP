<?php
include 'cek_sesi.php';
include 'koneksi.php'; // Pastikan di sini variabelnya adalah $conn

// --- 1. PROSES SIMPAN PEMINJAMAN ---
if (isset($_POST['simpan'])) {
    $nama       = mysqli_real_escape_string($conn, $_POST['nama']);
    $id_barang  = $_POST['id_barang'];
    $jumlah     = $_POST['jumlah'];
    $tgl_pinjam = $_POST['tgl_pinjam'];

    // Simpan data peminjaman
    $query_pinjam = "INSERT INTO peminjaman (nama, id_barang, jumlah, tgl_pinjam, status) 
                     VALUES ('$nama', '$id_barang', '$jumlah', '$tgl_pinjam', 'Dipinjam')";

    if (mysqli_query($conn, $query_pinjam)) {
        // Otomatis kurangi stok barang
        mysqli_query($conn, "UPDATE barang SET jumlah = jumlah - '$jumlah' WHERE id_barang = '$id_barang'");
        echo "<script>alert('Peminjaman Berhasil!'); window.location='peminjaman.php';</script>";
    }
}

// --- 2. PROSES PENGEMBALIAN BARANG ---
if (isset($_GET['kembali'])) {
    $id = $_GET['kembali'];

    // Ambil data peminjaman dulu untuk tahu jumlah yang harus dikembalikan ke stok
    $data_p = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM peminjaman WHERE id_peminjaman='$id'"));
    $jml_pinjam = $data_p['jumlah'];
    $id_b       = $data_p['id_barang'];

    // Update status peminjaman
    mysqli_query($conn, "UPDATE peminjaman SET status='Dikembalikan', tgl_kembali=NOW() WHERE id_peminjaman='$id'");

    // Kembalikan stok barang
    mysqli_query($conn, "UPDATE barang SET jumlah = jumlah + '$jml_pinjam' WHERE id_barang = '$id_b'");

    header("location:peminjaman.php");
    exit();
}

// --- 3. PROSES HAPUS DATA ---
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM peminjaman WHERE id_peminjaman='$id'");
    header("location:peminjaman.php");
    exit();
}

// Fitur Cari
$cari = $_GET['cari'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Peminjaman Barang Aula</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7f6;
        }

        .navbar-custom {
            background-color: #1f3c88;
        }

        .card {
            border: none;
            border-radius: 12px;
        }

        .table thead {
            background-color: #f8f9fa;
        }
    </style>
    <script>
        // Anti Multi-Tab / BroadcastChannel Sesi
        // Jangan pasang di cetak_pdf.php

        const authChannel = new BroadcastChannel('auth_system');

        // Dengarkan pesan dari tab lain
        authChannel.onmessage = (event) => {
            if (event.data === 'new_login_detected') {
                alert('Sesi Anda berakhir! Akun ini baru saja login di tab/perangkat lain.');
                window.location.href = 'login.php?pesan=illegal_access';
            }

            if (event.data === 'logout_everyone') {
                window.location.href = 'login.php?pesan=logout';
            }
        };

        // Fungsi opsional untuk broadcast logout manual
        function sendLogoutSignal() {
            authChannel.postMessage('logout_everyone');
        }
    </script>

</head>

<body>

    <nav class="navbar navbar-dark navbar-custom shadow-sm mb-4">
        <div class="container-fluid">
            <span class="navbar-brand fw-bold"><i class="bi bi-arrow-left-right"></i> Peminjaman Barang Aula</span>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm"><i class="bi bi-house"></i> Dashboard</a>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-secondary mb-0">Daftar Peminjaman</h5>
            <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-circle"></i> Tambah Peminjaman
            </button>
        </div>

        <form method="GET" class="mb-3">
            <div class="input-group shadow-sm">
                <input type="text" name="cari" class="form-control" placeholder="Cari nama peminjam..." value="<?= $cari ?>">
                <button class="btn btn-white border" type="submit"><i class="bi bi-search"></i></button>
            </div>
        </form>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="text-center small fw-bold text-muted">
                            <tr>
                                <th>No</th>
                                <th class="text-start">Peminjam</th>
                                <th>Barang</th>
                                <th>Jumlah</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $query = mysqli_query($conn, "
                        SELECT p.*, b.nama_barang 
                        FROM peminjaman p
                        JOIN barang b ON p.id_barang = b.id_barang
                        WHERE p.nama LIKE '%$cari%'
                        ORDER BY p.id_peminjaman DESC
                    ");

                            while ($d = mysqli_fetch_assoc($query)) {
                            ?>
                                <tr class="text-center">
                                    <td><?= $no++; ?></td>
                                    <td class="text-start fw-bold"><?= $d['nama']; ?></td>
                                    <td><?= $d['nama_barang']; ?></td>

                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <?= $d['jumlah']; ?>
                                        </span>
                                    </td>

                                    <td class="small">
                                        <?= date('d-m-Y', strtotime($d['tgl_pinjam'])); ?>
                                    </td>

                                    <!-- TANGGAL KEMBALI -->
                                    <td class="small">
                                        <?= $d['tgl_kembali']
                                            ? date('d-m-Y', strtotime($d['tgl_kembali']))
                                            : '-' ?>
                                    </td>

                                    <!-- STATUS -->
                                    <td>
                                        <?php if ($d['status'] == 'Dipinjam'): ?>
                                            <span class="badge bg-warning text-dark px-3">DIPINJAM</span>
                                        <?php else: ?>
                                            <span class="badge bg-success px-3">DIKEMBALIKAN</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- AKSI -->
                                    <td>
                                        <div class="btn-group">

                                            <?php if ($d['status'] == 'Dipinjam'): ?>
                                                <a href="?kembali=<?= $d['id_peminjaman']; ?>"
                                                    class="btn btn-sm btn-outline-success"
                                                    onclick="return confirm('Sudah dikembalikan?')">
                                                    <i class="bi bi-check-circle"></i>
                                                </a>
                                            <?php endif; ?>

                                            <a href="edit_data.php?type=peminjaman&id=<?= $d['id_peminjaman'] ?>"
                                                class="btn btn-sm btn-outline-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            <a href="?hapus=<?= $d['id_peminjaman']; ?>"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Hapus data ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>

                                        </div>
                                    </td>
                                </tr>

                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">Input Peminjaman Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Peminjam</label>
                            <input type="text" name="nama" class="form-control" required placeholder="Nama lengkap warga...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Barang</label>
                            <select name="id_barang" class="form-select" required>
                                <option value="">-- Pilih Barang --</option>
                                <?php
                                $barang = mysqli_query($conn, "SELECT * FROM barang WHERE jumlah > 0");
                                while ($b = mysqli_fetch_assoc($barang)) {
                                    echo "<option value='{$b['id_barang']}'>{$b['nama_barang']} (Stok: {$b['jumlah']})</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold">Jumlah Pinjam</label>
                                <input type="number" name="jumlah" class="form-control" required min="1" value="1">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold">Tanggal Pinjam</label>
                                <input type="date" name="tgl_pinjam" class="form-control" required value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" name="simpan" class="btn btn-primary px-4">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>