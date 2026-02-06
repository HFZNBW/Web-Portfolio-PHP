<?php
include 'cek_sesi.php';
include 'koneksi.php';


/* ===============================
   PROSES TAMBAH DATA
=============================== */
if (isset($_POST['simpan'])) {

    $nama = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $jml  = (int) $_POST['jumlah'];
    $knd  = mysqli_real_escape_string($conn, $_POST['kondisi']);

    $insert = mysqli_query(
        $conn,
        "INSERT INTO barang (nama_barang, jumlah, kondisi)
         VALUES ('$nama','$jml','$knd')"
    );

    if ($insert) {
        echo "<script>alert('Data berhasil ditambah!'); window.location='inventaris.php';</script>";
        exit();
    }
}


/* ===============================
   PROSES HAPUS
=============================== */
if (isset($_GET['hapus'])) {

    $id = (int) $_GET['hapus'];

    mysqli_query($conn, "DELETE FROM barang WHERE id_barang='$id'");
    header("Location: inventaris.php");
    exit();
}


/* ===============================
   PENCARIAN
=============================== */
$cari = $_GET['cari'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Inventaris Aula - RT04</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f4f7f6;
        }

        .navbar {
            background: #1f3c88;
        }

        .card {
            border-radius: 12px;
            border: none;
        }
    </style>

    <script>
        /* ===============================
           ANTI MULTI TAB SESSION
        =============================== */

        const authChannel = new BroadcastChannel('auth_system');

        authChannel.onmessage = (event) => {
            if (event.data === 'new_login_detected') {
                alert('Sesi Anda berakhir! Login di perangkat lain.');
                window.location.href = 'login.php?pesan=illegal_access';
            }

            if (event.data === 'logout_everyone') {
                window.location.href = 'login.php?pesan=logout';
            }
        };

        function sendLogoutSignal() {
            authChannel.postMessage('logout_everyone');
        }
    </script>
</head>



<body>

    <!-- ================= NAVBAR ================= -->
    <nav class="navbar navbar-dark shadow-sm mb-4">
        <div class="container-fluid">
            <span class="navbar-brand fw-bold">
                <i class="bi bi-archive-fill me-2"></i> Data Barang Aula
            </span>

            <a href="dashboard.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
        </div>
    </nav>



    <div class="container">

        <!-- HEADER + BUTTON -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="text-secondary fw-bold mb-0">Daftar Barang</h5>

            <button class="btn btn-primary btn-sm"
                data-bs-toggle="modal"
                data-bs-target="#modalTambah">
                <i class="bi bi-plus-circle"></i> Tambah Barang
            </button>
        </div>


        <!-- SEARCH -->
        <form method="GET" class="mb-3">
            <div class="input-group shadow-sm">
                <input type="text"
                    name="cari"
                    class="form-control"
                    placeholder="Cari nama barang..."
                    value="<?= $cari ?>">

                <button class="btn btn-white border">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>



        <!-- ================= TABEL ================= -->
        <div class="card shadow-sm">
            <div class="card-body p-0">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light text-center">
                        <tr>
                            <th width="60">No</th>
                            <th class="text-start">Nama Barang</th>
                            <th width="120">Jumlah</th>
                            <th width="150">Kondisi</th>
                            <th width="140">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php
                        $no = 1;

                        $query = mysqli_query(
                            $conn,
                            "SELECT *
                             FROM barang
                             WHERE nama_barang LIKE '%$cari%'
                             ORDER BY id_barang DESC"
                        );

                        if (mysqli_num_rows($query) == 0) {
                            echo "<tr>
                                    <td colspan='5' class='text-center text-muted p-4'>
                                        Data tidak ditemukan
                                    </td>
                                  </tr>";
                        }

                        while ($data = mysqli_fetch_assoc($query)) :

                            /* ===============================
                               BADGE KONDISI MAPPING
                            =============================== */
                            $map = [
                                'baik' => 'success',
                                'sedang' => 'warning',
                                'rusak' => 'danger',
                                'buruk' => 'danger',
                                'maintenance' => 'secondary',
                                'hilang' => 'dark'
                            ];

                            $badge = $map[strtolower($data['kondisi'])] ?? 'secondary';
                        ?>

                            <tr class="text-center">

                                <td><?= $no++ ?></td>

                                <td class="text-start fw-bold">
                                    <?= $data['nama_barang'] ?>
                                </td>

                                <td>
                                    <span class="badge bg-info text-dark">
                                        <?= $data['jumlah'] ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-<?= $badge ?>">
                                        <?= strtoupper($data['kondisi']) ?>
                                    </span>
                                </td>

                                <td>
                                    <a href="edit_data.php?type=barang&id=<?= $data['id_barang'] ?>"
                                        class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <a href="?hapus=<?= $data['id_barang'] ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Hapus data ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>

                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>
            </div>
        </div>

    </div>



    <!-- ================= MODAL TAMBAH ================= -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">

            <form method="POST" class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Barang Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="jumlah" class="form-control" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kondisi</label>
                        <select name="kondisi" class="form-select">
                            <option value="Baik">Baik</option>
                            <option value="Sedang">Sedang</option>
                            <option value="Rusak">Rusak</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Hilang">Hilang</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" name="simpan" class="btn btn-primary">
                        Simpan Barang
                    </button>
                </div>

            </form>

        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>