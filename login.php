<?php
// Memulai session untuk menyimpan data login
session_start();
// Menyertakan file koneksi database
include('koneksi.php');
// Membuat instance objek database
$db = new database();

// Memeriksa apakah request menggunakan method POST (form submit)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil nilai username dari form
    $username = $_POST['username'];
    // Mengambil password dan mengenkripsi dengan MD5 (sesuai dengan format di database)
    $password = md5($_POST['password']); // gunakan hash sesuai yang di database

    // Membuat query untuk mencari user dengan username dan password yang cocok
    $query = mysqli_query($db->koneksi, "SELECT * FROM user WHERE username='$username' AND password='$password'");
    
    // Memeriksa apakah query mengembalikan hasil (user ditemukan)
    if (mysqli_num_rows($query) > 0) {
        // Mengambil data user sebagai array asosiatif
        $user = mysqli_fetch_assoc($query);
        // Set session login sebagai true
        $_SESSION['login'] = true;
        // Menyimpan id_user ke dalam session
        $_SESSION['id_user'] = $user['id_user'];
        // Redirect ke halaman index.php setelah login sukses
        header("Location: index.php");
        exit; // Menghentikan eksekusi script
    } else {
        // Jika login gagal, set pesan error
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Meta tags untuk pengaturan dasar halaman -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Rencana Manusia</title>
  <!-- Favicon dan stylesheet -->
  <link rel="shortcut icon" type="image/png" href="images/logos/logo_rm.png" />
  <link rel="stylesheet" href="css/styles.min.css" />
  <link rel="stylesheet" href="css/icons/tabler-icons/tabler-icons.css" />
</head>

<body>
  <!-- Wrapper utama untuk layout halaman -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <div
      class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
      <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100">
          <div class="col-md-8 col-lg-6 col-xxl-3">
            <div class="card mb-0">
              <div class="card-body">
                <!-- Logo dan judul aplikasi -->
                <a href="index.php" class="text-nowrap logo-img text-center d-block py-3 w-100">
                  <img src="images/logos/logo_rm.png" alt="logo" class="logo-icon" style="width: 60px; height:auto" />
                  <br><br>
                  <span><h3>Rencana Manusia</h3></span>
                </a>
                <p class="text-center">Your Companion</p>

                <!-- Bagian untuk menampilkan pesan sukses/error dari session -->
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success">
                        <?php
                        echo $_SESSION['success_message'];
                        unset($_SESSION['success_message']); // Hapus pesan setelah ditampilkan
                        ?>
                    </div>
                <?php elseif (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <?php
                        echo $_SESSION['error_message'];
                        unset($_SESSION['error_message']); // Hapus pesan setelah ditampilkan
                        ?>
                    </div>
                <?php endif; ?>

                <!-- Form login dengan method POST -->
                <form action="./proses-login.php" method="POST">
                  <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                  </div>
                  <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                  </div>
                  <div class="d-grid gap-2">
                    <button type="submit" name="login" class="btn btn-dark w-100 py-8 fs-4 mb-4">Login</button>
                  </div>

                    <!-- Tautan untuk pendaftaran pengguna baru -->
                    <div class="text-center">
                        <p class="mb-0">Don't have an account yet? <a href="register.php" class="text-primary fw-bold">Register</a></p>
                    </div>

                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Script JavaScript yang diperlukan -->
   
  <script src="libs/jquery/dist/jquery.min.js"></script>
  <script src="libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>

</html>