<?php
// Menyertakan file koneksi database
include('koneksi.php'); 
// Membuat instance objek database dengan nama berbeda
$koneksi = new database(); 
// Mendapatkan parameter action dari URL
$action = isset($_GET['action']) ? $_GET['action'] : null; 

// Proses tambah task
if ($action == "add") {
    // Memanggil method tambah_data dengan data dari form
    $koneksi->tambah_data($_POST['judul'], $_POST['deskripsi'], $_POST['tanggal_dl'], $_POST['waktu_dl'], $_POST['status']);
    echo "<script>alert('Tugas baru bertambah!'); window.location.href='task.php';</script>";
    
// Proses update status task
} elseif ($action == "update" && isset($_POST['id_task'], $_POST['status'])) {
    // Memanggil method update_status
    $koneksi->update_status($_POST['id_task'], $_POST['status']);
    header("Location: task.php");
    exit;
    
// Proses edit task
} elseif ($action == "edit" && isset($_POST['id_task'], $_POST['judul'], $_POST['deskripsi'], $_POST['tanggal_dl'], $_POST['waktu_dl'], $_POST['status'])) {
    $id_task = $_POST['id_task'];
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $tanggal_dl = $_POST['tanggal_dl'];
    $waktu_dl = $_POST['waktu_dl'];
    $status = $_POST['status'];
    // Memanggil method edit_data
    $koneksi->edit_data($id_task, $judul, $deskripsi, $tanggal_dl, $waktu_dl, $status);
    echo "<script>alert('Tugas berhasil diupdate!'); window.location.href='task.php';</script>";
    exit;
    
// Proses hapus task
} elseif ($action == "delete") {
    $id_task = $_GET['id'];
    // Memanggil method delete_data
    $koneksi->delete_data($id_task);
    echo "<script>alert('Tugas dihapus!'); window.location.href='task.php';</script>";
}
?>