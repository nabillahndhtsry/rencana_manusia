<?php
// Memulai session untuk menyimpan data login
session_start();
// Menyertakan file koneksi database
include('koneksi.php');
// Membuat instance objek database
$db = new database();

// Mengambil data dari form login
$username = $_POST['username'];
$password = $_POST['password'];

// Memeriksa kredensial login
if ($db->cek_login($username, $password)) {
    // Query untuk mendapatkan data user
    $result = mysqli_query($db->koneksi, "SELECT * FROM user WHERE username='$username'");
    $user = mysqli_fetch_assoc($result);

    // Set session untuk login berhasil
    $_SESSION['success_message'] = "Login berhasil!";
    $_SESSION['login'] = true;
    $_SESSION['id_user'] = $user['id_user'];
    // Redirect ke halaman utama
    header("Location: index.php");
    exit();
} else {
    // Set pesan error untuk login gagal
    $_SESSION['error_message'] = "Username atau password salah!";
    // Redirect kembali ke halaman login
    header("Location: login.php");
    exit();
}
?>