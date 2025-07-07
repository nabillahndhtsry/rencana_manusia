<?php
// Memulai session
session_start();
// Menyertakan file koneksi database
include('koneksi.php');
// Membuat instance objek database
$db = new database();

// Memeriksa status login user
if(!isset($_SESSION['login'])) {
    // Redirect ke halaman login jika belum login
    header("Location: login.php");
    exit;
}

// Mendapatkan parameter action dari URL
$action = isset($_GET['action']) ? $_GET['action'] : null;

// Proses tambah target
if ($action == "add") {
    // Memanggil method tambah_target dengan data dari form
    $db->tambah_target($_POST['target'], $_POST['deskripsi_target'], $_POST['tanggal_target'], $_POST['status_target'], $_POST['fokus_target']);
    // Menampilkan alert dan redirect
    echo "<script>alert('Target baru berhasil ditambahkan!'); window.location.href='target.php';</script>";
    
// Proses hapus target
} elseif ($action == "delete_target") {
    $id_target = $_GET['id'];
    // Memanggil method delete_target
    $db->delete_target($id_target);
    echo "<script>alert('Target berhasil dihapus!'); window.location.href='target.php';</script>";
    
// Proses set target fokus
} elseif ($action == "set_focus") {
    $id_target = $_POST['id_target'];
    // Reset semua status fokus target
    $db->query("UPDATE target SET fokus_target = 0");
    // Set target yang dipilih sebagai fokus
    $db->query("UPDATE target SET fokus_target = 1 WHERE id_target = '$id_target'");
    echo "<script>alert('Target fokus berhasil diubah!'); window.location.href='target.php';</script>";
    
// Proses update status target
} elseif ($action == "update_status_target" && isset($_POST['id_target'], $_POST['status_target'])) {
    $db->update_status_target($_POST['id_target'], $_POST['status_target']);
    header("Location: target.php");
    exit;
    
// Proses set fokus (alternatif)
} elseif (($action == "set_focus" || (isset($_POST['action']) && $_POST['action'] == "set_focus")) && isset($_POST['id_target'])) {
    $id_target = $_POST['id_target'];
    // Reset semua status fokus
    mysqli_query($db->koneksi, "UPDATE target SET fokus_target = 0");
    // Set target yang dipilih sebagai fokus
    mysqli_query($db->koneksi, "UPDATE target SET fokus_target = 1 WHERE id_target = '$id_target'");
    echo "<script>alert('Target fokus berhasil diubah!'); window.location.href='target.php';</script>";
    exit;
    
// Proses unset fokus
} elseif (($action == "unset_focus" || (isset($_POST['action']) && $_POST['action'] == "unset_focus")) && isset($_POST['id_target'])) {
    $id_target = $_POST['id_target'];
    // Menghapus status fokus dari target tertentu
    mysqli_query($db->koneksi, "UPDATE target SET fokus_target = 0 WHERE id_target = '$id_target'");
    echo "<script>alert('Target tidak lagi menjadi fokus!'); window.location.href='target.php';</script>";
    exit;
}
?>