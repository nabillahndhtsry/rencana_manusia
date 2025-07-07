<?php
class database {
    var $host = "localhost";
    var $username = "root";
    var $password = "";
    var $database = "uas";
    var $koneksi;

    // constructor untuk menghubungkan ke database
    function __construct() {
        $this->koneksi = mysqli_connect($this->host, $this->username, $this->password, $this->database);
        // Mengecek jika koneksi gagal
        if (mysqli_connect_error()) {
            echo "Koneksi database gagal : " . mysqli_connect_error();
        }
    }

    function query($sql) {
    return mysqli_query($this->koneksi, $sql);
    }

    // fungsi cek login user
    function cek_login($username, $password) {
        $username = mysqli_real_escape_string($this->koneksi, $username);
        $password = mysqli_real_escape_string($this->koneksi, $password);
        $query = mysqli_query($this->koneksi, "SELECT * FROM user WHERE username='$username' AND password=MD5('$password')");
        return mysqli_num_rows($query) > 0;
    }


    // Update foto profil
    function update_foto_profil($id_user, $nama_file) {
        $query = "UPDATE user SET foto_profil = ? WHERE id_user = ?";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("si", $nama_file, $id_user);
        return $stmt->execute();
    }

    // Hapus foto profil
    function hapus_foto_profil($id_user) {
        $query = "UPDATE user SET foto_profil = NULL WHERE id_user = ?";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("i", $id_user);
        return $stmt->execute();
    }

    // Mendapatkan data user berdasarkan ID
    function get_user_by_id($id_user) {
        $query = "SELECT * FROM user WHERE id_user = ?";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }


    // menampilkan data task, bisa dengan pencarian nama task
    function tampil_data($search = "") {
        if ($search != "") {
            // Query dengan filter pencarian
            $data = mysqli_query($this->koneksi, "SELECT * FROM task WHERE judul LIKE '%$search%'");
        } else {
            // Query tanpa filter pencarian
            $data = mysqli_query($this->koneksi, "SELECT * FROM task");
        }
        $hasil = [];
        // Mengambil semua data hasil query
        while ($row = mysqli_fetch_array($data)) {
            $hasil[] = $row;
        }
        return $hasil;
    }

    // menampilkan data reminder task, bisa dengan pencarian nama task
    function tampil_data_reminder($search = "") {
        if ($search != "") {
            // Query dengan filter pencarian
            $data = mysqli_query($this->koneksi, "SELECT * FROM task WHERE judul LIKE '%$search%'");
        } else {
            // Query tanpa filter pencarian
            $data = mysqli_query($this->koneksi, "SELECT * FROM task");
        }
        $hasil = [];
        // Mengambil semua data hasil query
        while ($row = mysqli_fetch_array($data)) {
            $hasil[] = $row;
        }
        return $hasil;
    }

    // menambah data task ke database
    function tambah_data($judul, $deskripsi, $tanggal_dl, $waktu_dl, $status) {
        mysqli_query($this->koneksi, "INSERT INTO task VALUES ('', '$judul', '$deskripsi', '$tanggal_dl', '$waktu_dl', '$status')");
    }

    // mengambil data task berdasarkan id
    function get_by_id($id_task) {
        $query = mysqli_query($this->koneksi, "SELECT * FROM task WHERE id_task='$id_task'");
        return $query->fetch_array();
    }

    // mengupdate data task status berdasarkan id
    function update_status($id_task, $status) {
        $query = mysqli_query($this->koneksi, "UPDATE task SET status='$status' WHERE id_task='$id_task'");
    }

    // edit data task
    function edit_data($id_task, $judul, $deskripsi, $tanggal_dl, $waktu_dl) {
        $query = mysqli_query($this->koneksi, "UPDATE task SET judul='$judul', deskripsi='$deskripsi', tanggal_dl='$tanggal_dl', waktu_dl='$waktu_dl' WHERE id_task='$id_task'");
        return $query;
    }

    // menghapus data task berdasarkan id
    function delete_data($id_task) {
        $query = mysqli_query($this->koneksi, "DELETE FROM task WHERE id_task='$id_task'");
    }

    //menampilkan data target
    function tampil_target($search = "") {
        if ($search != "") {
            // Query dengan filter pencarian
            $data = mysqli_query($this->koneksi, "SELECT * FROM target WHERE target LIKE '%$search%'");
        } else {
            // Query tanpa filter pencarian
            $data = mysqli_query($this->koneksi, "SELECT * FROM target");
        }
        $hasil = [];
        // Mengambil semua data hasil query
        while ($row = mysqli_fetch_array($data)) {
            $hasil[] = $row;
        }
        return $hasil;
    }

    // mengupdate data target status berdasarkan id
    
    function status_target($id_target, $status_target) {
        mysqli_query($this->koneksi, "UPDATE target SET status_target='$status_target' WHERE id_target='$id_target'");

    }

    // Tambahkan method untuk menambah target dengan fokus
    function tambah_target($target, $deskripsi_target, $tanggal_target, $status_target, $fokus_target = 0) {
        $query = "INSERT INTO target (target, deskripsi_target, tanggal_target, status_target, fokus_target) 
                VALUES ('$target', '$deskripsi_target', '$tanggal_target', '$status_target', '$fokus_target')";
        return mysqli_query($this->koneksi, $query);
    }

    // mengupdate data target status berdasarkan id
    function update_status_target($id_target, $status_target) {
        $query = mysqli_query($this->koneksi, "UPDATE target SET status_target='$status_target' WHERE id_target='$id_target'");
    }

    // Tambahkan method untuk set fokus target
    function set_fokus_target($id_target) {
        // Reset semua fokus terlebih dahulu
        mysqli_query($this->koneksi, "UPDATE target SET fokus_target = 0");
        // Set target yang dipilih sebagai fokus
        return mysqli_query($this->koneksi, "UPDATE target SET fokus_target = 1 WHERE id_target = '$id_target'");
    }

    // menghapus data target berdasarkan id
    function delete_target($id_target) {
        $query = mysqli_query($this->koneksi, "DELETE FROM target WHERE id_target='$id_target'");
    }

    // Menampilkan semua data note
    function tampil_note($search = "") {
        if ($search != "") {
            $data = mysqli_query($this->koneksi, "SELECT * FROM note WHERE judul_note LIKE '%$search%'");
        } else {
            $data = mysqli_query($this->koneksi, "SELECT * FROM note");
        }
        $hasil = [];
        while ($row = mysqli_fetch_array($data)) {
            $hasil[] = $row;
        }
        return $hasil;
    }

    // Menambah data note
    function tambah_note($judul_note, $deskripsi_note) {
        $query = "INSERT INTO note (judul_note, deskripsi_note) VALUES ('$judul_note', '$deskripsi_note')";
        return mysqli_query($this->koneksi, $query);
    }

    // Mengupdate data note
    function update_note($id_note, $judul_note, $deskripsi_note) {
        $query = "UPDATE note SET judul_note='$judul_note', deskripsi_note='$deskripsi_note' WHERE id_note='$id_note'";
        return mysqli_query($this->koneksi, $query);
    }

    // Menghapus data note
    function delete_note($id_note) {
        $query = mysqli_query($this->koneksi, "DELETE FROM note WHERE id_note='$id_note'");
        return $query;
    }

    // Mengambil data note berdasarkan id
    function get_note_by_id($id_note) {
        $query = mysqli_query($this->koneksi, "SELECT * FROM note WHERE id_note='$id_note'");
        return $query->fetch_array();
    }

    // Menampilkan semua data todo
    function tampil_todo($search = "") {
        if ($search != "") {
            $data = mysqli_query($this->koneksi, "SELECT * FROM todo WHERE deskripsi_todo LIKE '%$search%'");
        } else {
            $data = mysqli_query($this->koneksi, "SELECT * FROM todo");
        }
        $hasil = [];
        while ($row = mysqli_fetch_array($data)) {
            $hasil[] = $row;
        }
        return $hasil;
    }

    // Menambah data todo
    function tambah_todo( $deskripsi_todo, $hari_todo) {
        $query = "INSERT INTO todo (deskripsi_todo, hari_todo) VALUES ('$deskripsi_todo', '$hari_todo')";
        return mysqli_query($this->koneksi, $query);
    }

    // Mengupdate data todo
    function update_todo($id_todo, $deskripsi_todo, $hari_todo) {
        $query = "UPDATE todo SET deskripsi_todo='$deskripsi_todo', hari_todo='$hari_todo' WHERE id_todo='$id_todo'";
        return mysqli_query($this->koneksi, $query);
    }

    // Menghapus data todo
    function delete_todo($id_todo) {
    return mysqli_query($this->koneksi, 
        "DELETE FROM todo WHERE id_todo = '$id_todo'");
    }

    // Untuk mendapatkan todo berdasarkan hari
    function get_todo_by_day($hari) {
        $query = "SELECT * FROM todo 
                WHERE hari_todo = '$hari'
                ORDER BY id_todo ASC";
        $result = mysqli_query($this->koneksi, $query);
        $todos = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $todos[] = $row;
        }
        return $todos;
    }

    // Untuk update status todo
    function update_todo_status($id_todo, $status_todo) {
    return mysqli_query($this->koneksi, 
        "UPDATE todo SET status_todo = '$status_todo' WHERE id_todo = '$id_todo'");

    }
}
?>