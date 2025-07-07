<?php
// Script untuk menampilkan reminder task yang deadline-nya dalam 7 hari ke depan

// Inisialisasi array untuk menyimpan task reminder
$reminder_task = [];
// Mendapatkan tanggal hari ini
$today = date('Y-m-d');

// Jika $data_task belum ada, ambil dari database
if (!isset($data_task)) {
    $data_task = $db->tampil_data();
}

// Loop melalui semua task
foreach ($data_task as $task) {
    // Hitung selisih hari antara deadline dan hari ini
    $diff = (strtotime($task['tanggal_dl']) - strtotime($today)) / (60 * 60 * 24);
    
    // Jika deadline dalam 7 hari ke depan, tambahkan ke array reminder
    if ($diff >= 0 && $diff <= 7) {
        $reminder_task[] = $task;
    }
}

// Hitung jumlah reminder
$jumlah_reminder = count($reminder_task);
?>