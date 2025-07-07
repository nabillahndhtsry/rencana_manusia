<?php
// Memulai session untuk menyimpan/mengambil data sesi login
session_start();

// Memasukkan file koneksi database (berisi class database dan fungsi query)
include('koneksi.php');

// Cek apakah ada data yang dikirim melalui metode POST
if ($_POST) {
  // Mengambil nilai id target yang dikirim dari form
  $id = $_POST['id'];

  // Mengambil nilai progress baru dari form
  $progress = $_POST['progress'];

  // Menjalankan query UPDATE untuk mengubah nilai progress pada target yang sesuai
  $db->query("UPDATE targets SET progress = $progress WHERE id = $id");
  // Setelah update, redirect pengguna kembali ke halaman target.php
  header("Location: target.php");
}
?>
