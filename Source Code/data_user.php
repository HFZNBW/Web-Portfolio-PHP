<?php
include_once 'koneksi.php';
include_once 'cek_sesi.php';

if ($_SESSION['role'] != 'admin') {
    header("location:dashboard.php");
    exit();
}


/* =================================================
   PROSES TAMBAH USER
================================================= */
if (isset($_POST['tambah_user'])) {

    $username  = mysqli_real_escape_string($conn, $_POST['username']);
    $password  = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama_user = mysqli_real_escape_string($conn, $_POST['nama_user']);
    $role      = $_POST['role'];

    mysqli_query($conn, "
        INSERT INTO users(username,password,nama_user,role)
        VALUES('$username','$password','$nama_user','$role')
    ");

    header("location:data_user.php");
    exit();
}


/* =================================================
   PROSES UPDATE USER (USERNAME + NAMA + PASSWORD + ROLE)
================================================= */
if (isset($_POST['update_user'])) {

    $id       = $_POST['id_user'];
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nama     = mysqli_real_escape_string($conn, $_POST['nama_user']);
    $role     = $_POST['role'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        mysqli_query($conn, "
            UPDATE users 
            SET username='$username',
                nama_user='$nama',
                password='$hash',
                role='$role'
            WHERE id_user='$id'
        ");
    } else {
        mysqli_query($conn, "
            UPDATE users 
            SET username='$username',
                nama_user='$nama',
                role='$role'
            WHERE id_user='$id'
        ");
    }

    header("location:data_user.php");
    exit();
}


/* =================================================
   HAPUS USER
================================================= */
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {

    mysqli_query($conn, "DELETE FROM users WHERE id_user='$_GET[id]'");

    header("location:data_user.php");
    exit();
}


$query = mysqli_query($conn, "SELECT * FROM users ORDER BY id_user DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Manajemen User</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f6f8fb;
        }

        .navbar-main {
            background: #1F3C88;
        }

        .card {
            border: none;
            border-radius: 12px;
        }

        .table-header {
            background: #5f676e;
            color: white;
            font-weight: 600;
        }
    </style>

    <!-- ================= BroadcastChannel Anti Multi Tab ================= -->
    <script>
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
    </script>
</head>


<body>

    <!-- ================= NAVBAR ================= -->
    <nav class="navbar navbar-dark navbar-main shadow-sm">
        <div class="container-fluid">
            <span class="navbar-brand fw-semibold">
                <i class="bi bi-people-fill"></i> Manajemen User
            </span>

            <a href="dashboard.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
        </div>
    </nav>



    <div class="container mt-4">


        <!-- =================================================
            FORM TAMBAH USER
        ================================================= -->
        <div class="card shadow-sm mb-4">

            <div class="card-header bg-primary text-white fw-semibold">
                Tambah User
            </div>

            <div class="card-body">
                <form method="POST" class="row g-3 align-items-end">

                    <div class="col-md-3">
                        <label class="small fw-bold">Nama</label>
                        <input type="text" name="nama_user" class="form-control" required>
                    </div>

                    <div class="col-md-3">
                        <label class="small fw-bold">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>

                    <div class="col-md-2">
                        <label class="small fw-bold">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="col-md-2">
                        <label class="small fw-bold">Role</label>
                        <select name="role" class="form-select">
                            <option value="petugas">Petugas</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div class="col-md-2 d-grid">
                        <button name="tambah_user" class="btn btn-primary">
                            Simpan
                        </button>
                    </div>

                </form>
            </div>
        </div>



        <!-- =================================================
            TABEL USER
        ================================================= -->
        <div class="card shadow-sm">

            <div class="card-header table-header">
                Data User
            </div>

            <div class="table-responsive">

                <table class="table table-bordered table-hover mb-0 align-middle text-center">

                    <thead>
                        <tr>
                            <th width="60">No</th>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Password Baru</th>
                            <th>Role</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php $no = 1;
                        while ($u = mysqli_fetch_assoc($query)): ?>

                            <tr>
                                <form method="POST">

                                    <input type="hidden" name="id_user" value="<?= $u['id_user'] ?>">

                                    <td><?= $no++ ?></td>

                                    <td>
                                        <input type="text" name="username"
                                            value="<?= $u['username'] ?>"
                                            class="form-control form-control-sm" required>
                                    </td>

                                    <td>
                                        <input type="text" name="nama_user"
                                            value="<?= $u['nama_user'] ?>"
                                            class="form-control form-control-sm" required>
                                    </td>

                                    <td>
                                        <input type="password"
                                            name="password"
                                            placeholder="kosongkan jika tidak ganti"
                                            class="form-control form-control-sm">
                                    </td>

                                    <td>
                                        <select name="role" class="form-select form-select-sm">
                                            <option value="petugas" <?= $u['role'] == 'petugas' ? 'selected' : '' ?>>Petugas</option>
                                            <option value="admin" <?= $u['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                        </select>
                                    </td>

                                    <td class="d-flex gap-1 justify-content-center">

                                        <button name="update_user" class="btn btn-success btn-sm">
                                            <i class="bi bi-check"></i>
                                        </button>

                                        <a href="?aksi=hapus&id=<?= $u['id_user'] ?>"
                                            onclick="return confirm('Hapus user ini?')"
                                            class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </a>

                                    </td>

                                </form>
                            </tr>

                        <?php endwhile; ?>

                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>

</html>