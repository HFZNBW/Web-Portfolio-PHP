<?php
include 'cek_sesi.php';
include 'koneksi.php';

$type = $_GET['type'] ?? '';
$id   = $_GET['id'] ?? '';


// ================= UPDATE PROSES =================
if (isset($_POST['update'])) {

    $type = $_POST['type'];
    $id   = $_POST['id'];

    // ===== EDIT BARANG =====
    if ($type == 'barang') {

        $nama   = mysqli_real_escape_string($conn, $_POST['nama_barang']);
        $jumlah = $_POST['jumlah'];

        mysqli_query($conn, "
            UPDATE barang 
            SET nama_barang='$nama', jumlah='$jumlah'
            WHERE id_barang='$id'
        ");

        header("location:inventaris.php?pesan=update_sukses");
        exit();
    }


    // ===== EDIT PEMINJAMAN =====
    if ($type == 'peminjaman') {

        $nama        = mysqli_real_escape_string($conn, $_POST['nama']);
        $jumlah      = $_POST['jumlah'];
        $tgl_pinjam  = $_POST['tgl_pinjam'];
        $tgl_kembali = $_POST['tgl_kembali'];

        // ================= VALIDASI LOGIKA =================
        if (!empty($tgl_kembali) && $tgl_kembali < $tgl_pinjam) {
            echo "<script>
                    alert('Tanggal kembali tidak boleh sebelum tanggal pinjam!');
                    history.back();
                  </script>";
            exit();
        }

        // ================= AUTO STATUS =================
        if (empty($tgl_kembali)) {
            $status = "Dipinjam";
            $tgl_kembali_sql = "NULL";
        } else {
            $status = "Dikembalikan";
            $tgl_kembali_sql = "'$tgl_kembali'";
        }

        mysqli_query($conn, "
            UPDATE peminjaman 
            SET 
                nama='$nama',
                jumlah='$jumlah',
                tgl_pinjam='$tgl_pinjam',
                tgl_kembali=$tgl_kembali_sql,
                status='$status'
            WHERE id_peminjaman='$id'
        ");

        header("location:peminjaman.php?pesan=update_sukses");
        exit();
    }
}


// ================= AMBIL DATA =================
if ($type == 'barang') {
    $data = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT * FROM barang WHERE id_barang='$id'"
    ));
}

if ($type == 'peminjaman') {
    $data = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT * FROM peminjaman WHERE id_peminjaman='$id'"
    ));
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-5">

        <div class="card shadow-sm p-4">

            <h5 class="fw-bold mb-4">Edit Data</h5>

            <form method="POST">

                <input type="hidden" name="type" value="<?= $type ?>">
                <input type="hidden" name="id" value="<?= $id ?>">


                <!-- ================= BARANG ================= -->
                <?php if ($type == 'barang'): ?>

                    <div class="mb-3">
                        <label>Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control"
                            value="<?= $data['nama_barang'] ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>Jumlah</label>
                        <input type="number" name="jumlah" class="form-control"
                            value="<?= $data['jumlah'] ?>" required>
                    </div>

                <?php endif; ?>


                <!-- ================= PEMINJAMAN ================= -->
                <?php if ($type == 'peminjaman'): ?>

                    <div class="mb-3">
                        <label>Nama Peminjam</label>
                        <input type="text" name="nama" class="form-control"
                            value="<?= $data['nama'] ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>Jumlah</label>
                        <input type="number" name="jumlah" class="form-control"
                            value="<?= $data['jumlah'] ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>Tanggal Pinjam</label>
                        <input type="date" name="tgl_pinjam" class="form-control"
                            value="<?= $data['tgl_pinjam'] ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>Tanggal Kembali</label>
                        <input
                            type="date"
                            name="tgl_kembali"
                            class="form-control"
                            value="<?= $data['tgl_kembali'] ?>"
                            min="<?= $data['tgl_pinjam'] ?>">

                        <small class="text-muted">
                            Kosongkan jika masih dipinjam
                        </small>
                    </div>

                    <div class="mb-3">
                        <label>Status (otomatis)</label>
                        <input type="text" class="form-control"
                            value="<?= $data['status'] ?>" disabled>
                    </div>

                <?php endif; ?>


                <div class="d-flex gap-2">
                    <button type="submit" name="update" class="btn btn-primary">
                        Simpan Perubahan
                    </button>

                    <a href="javascript:history.back()" class="btn btn-secondary">
                        Batal
                    </a>
                </div>

            </form>

        </div>
    </div>

    <script>
        const pinjam = document.querySelector('input[name="tgl_pinjam"]');
        const kembali = document.querySelector('input[name="tgl_kembali"]');

        kembali.addEventListener('change', function() {

            if (!this.value) return;

            const tPinjam = pinjam.value;
            const tKembali = this.value;

            if (tKembali < tPinjam) {
                alert("Tanggal kembali tidak boleh sebelum tanggal pinjam");
                this.value = "";
            }
        });
    </script>



</body>

</html>