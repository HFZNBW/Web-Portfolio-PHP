<?php
include 'koneksi.php';
$id = $_GET['id'];

$query = "DELETE FROM ruangan WHERE id_ruang = '$id'";
if (mysqli_query($conn, $query)) {
    header("location:data_ruangan.php");
}
