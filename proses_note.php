<?php
// Menyertakan file koneksi database
include('koneksi.php'); 
// Membuat instance objek database
$db = new database(); 

// Mendapatkan parameter action dari URL (untuk menentukan operasi)
$action = isset($_GET['action']) ? $_GET['action'] : null; 

// Proses tambah data note
if ($action == "add" && isset($_POST['judul_note'], $_POST['deskripsi_note'])) {
    // Memanggil method tambah_note dengan data dari form
    $db->tambah_note($_POST['judul_note'], $_POST['deskripsi_note']);
    // Menampilkan alert dan redirect ke halaman note
    echo "<script>alert('Note berhasil ditambahkan!'); window.location.href='note.php';</script>";
    exit;
    
// Proses update data note
} elseif ($action == "update_note" && isset($_GET['id'], $_POST['judul_note'], $_POST['deskripsi_note'])) {
    // Memanggil method update_note dengan data dari form dan parameter id
    $db->update_note($_GET['id'], $_POST['judul_note'], $_POST['deskripsi_note']);
    // Menampilkan alert dan redirect
    echo "<script>alert('Note berhasil diupdate!'); window.location.href='note.php';</script>";
    exit;
    
// Proses hapus data note
} elseif ($action == "delete_note" && isset($_GET['id'])) {
    // Memanggil method delete_note dengan parameter id
    $db->delete_note($_GET['id']);
    // Menampilkan alert dan redirect
    echo "<script>alert('Note berhasil dihapus!'); window.location.href='note.php';</script>";
    exit;
}
?>