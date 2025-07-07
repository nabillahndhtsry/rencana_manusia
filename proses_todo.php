<?php
// Menyertakan file koneksi database
include('koneksi.php');
// Membuat instance objek database
$db = new database();

// Mendapatkan parameter action dari URL
$action = isset($_GET['action']) ? $_GET['action'] : null;

// Proses hapus todo
if ($action == "delete_todo" && isset($_GET['id_todo'])) {
    // Memanggil method delete_todo
    $db->delete_todo($_GET['id_todo']);
    header("Location: todo.php");
    exit;
}

// Proses update status todo (via POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_status') {
    $id_todo = $_POST['id_todo'];
    // Menentukan status baru
    $status_todo = isset($_POST['status_todo']) && $_POST['status_todo'] == 'done' ? 'done' : 'pending';
    // Memanggil method update_todo_status
    $db->update_todo_status($id_todo, $status_todo);
    header("Location: todo.php");
    exit;
}

// Default redirect jika tidak ada action yang valid
header("Location: todo.php");
exit;
?>