<?php
session_start();
include 'koneksi.php';

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

$query = mysqli_query($conn, "
    SELECT * FROM users 
    WHERE username='$username' 
    AND password='$password'
");

if (mysqli_num_rows($query) > 0) {

    $data = mysqli_fetch_assoc($query);

    session_regenerate_id();
    $session_id = session_id();

    mysqli_query($conn, "UPDATE users SET last_session_id='$session_id' WHERE id_user='" . $data['id_user'] . "'");

    $_SESSION['id_user']  = $data['id_user'];
    $_SESSION['username'] = $data['username'];
    $_SESSION['nama']     = $data['nama_user'];
    $_SESSION['role']     = $data['role'];
    $_SESSION['status']   = "login";

    header("location:dashboard.php");
    exit();
} else {
    header("location:login.php?pesan=gagal");
    exit();
}
