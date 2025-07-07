<?php
// Memulai session untuk mengakses data session yang ada
session_start(); // Mulai session
// Menghapus semua data session (logout)
session_destroy(); // Hapus semua data session
// Mengarahkan pengguna kembali ke halaman login
header("Location: login.php"); // Arahkan ke halaman login
exit; // Menghentikan eksekusi script
?>