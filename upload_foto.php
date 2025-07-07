<?php
include('koneksi.php'); // Menghubungkan ke file koneksi
$db = new database(); // Membuat objek database
$action = isset($_GET['action']) ? $_GET['action'] : null; // Mendapatkan aksi dari parameter GET

// Proses upload foto profil
if ($action == "upload") {
    session_start();
    
    if(!isset($_SESSION['id_user'])) {
        echo "<script>alert('Anda harus login terlebih dahulu!'); window.location.href='login.php';</script>";
        exit;
    }

    $user_id = $_SESSION['id_user'];
    
    if(isset($_FILES['foto'])) {
        // Validasi file
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file = $_FILES['foto'];
        
        // Cek error upload
        if($file['error'] !== UPLOAD_ERR_OK) {
            echo "<script>alert('Error uploading file. Code: " . $file['error'] . "'); window.location.href='myprofile.php';</script>";
            exit;
        } 
        // Cek tipe file
        elseif(!in_array($file['type'], $allowed_types)) {
            echo "<script>alert('Format file tidak didukung. Hanya JPG, PNG, dan GIF yang diperbolehkan.'); window.location.href='myprofile.php';</script>";
            exit;
        }
        // Cek ukuran file (maksimal 2MB)
        elseif($file['size'] > 2097152) {
            echo "<script>alert('Ukuran file terlalu besar. Maksimal 2MB.'); window.location.href='myprofile.php';</script>";
            exit;
        } else {
            // Buat direktori jika belum ada
            $target_dir = "images/uploads/";
            if(!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            
            // Generate nama file unik
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $nama_file = 'profile_' . $user_id . '_' . time() . '.' . $ext;
            $target_file = $target_dir . $nama_file;
            
            // Hapus foto lama jika ada
            $user = $db->get_user_by_id($user_id);
            if(!empty($user['foto_profil']) && file_exists($target_dir . $user['foto_profil'])) {
                unlink($target_dir . $user['foto_profil']);
            }
            
            // Upload file baru
            if(move_uploaded_file($file['tmp_name'], $target_file)) {
                // Update database
                if($db->update_foto_profil($user_id, $nama_file)) {
                    $_SESSION['foto_profil'] = $nama_file;
                    echo "<script>alert('Foto profil berhasil diupdate!'); window.location.href='myprofile.php';</script>";
                    exit;
                } else {
                    echo "<script>alert('Gagal menyimpan informasi foto ke database.'); window.location.href='myprofile.php';</script>";
                    exit;
                }
            } else {
                echo "<script>alert('Gagal mengupload file.'); window.location.href='myprofile.php';</script>";
                exit;
            }
        }
    } else {
        echo "<script>alert('Tidak ada file yang dipilih.'); window.location.href='myprofile.php';</script>";
        exit;
    }
}

// Proses hapus foto profil
if ($action == "delete_photo") {
    session_start();
    
    if(!isset($_SESSION['id_user'])) {
        echo "<script>alert('Anda harus login terlebih dahulu!'); window.location.href='login.php';</script>";
        exit;
    }

    $user_id = $_SESSION['id_user'];
    $user = $db->get_user_by_id($user_id);
    
    if(!empty($user['foto_profil'])) {
        $target_dir = "images/uploads/";
        if(file_exists($target_dir . $user['foto_profil'])) {
            unlink($target_dir . $user['foto_profil']);
        }
        
        if($db->hapus_foto_profil($user_id)) {
            unset($_SESSION['foto_profil']);
            echo "<script>alert('Foto profil berhasil dihapus!'); window.location.href='myprofile.php';</script>";
            exit;
        } else {
            echo "<script>alert('Gagal menghapus foto profil dari database.'); window.location.href='myprofile.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Tidak ada foto profil yang bisa dihapus.'); window.location.href='myprofile.php';</script>";
        exit;
    }
}
?>