<?php
session_start();
// Menghapus semua session yang terdaftar
session_destroy();

// Melempar kembali ke halaman login, BUKAN ke manajemen user
header("location:login.php?pesan=logout");
exit(); // Tambahkan exit agar script di bawahnya tidak tereksekusi
