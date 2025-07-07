<?php
// Memulai session
session_start();
// Menyertakan file koneksi database
include('koneksi.php');
// Membuat instance objek database
$db = new database();

// Proses pendaftaran jika method POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = $_POST['nama_lengkap'];
    $username = $_POST['username'];
    // Enkripsi password dengan MD5 (sesuai dengan login)
    $password = md5($_POST['password']); 
    
    // Cek apakah username sudah terdaftar
    $cek = mysqli_query($db->koneksi, "SELECT * FROM user WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Username sudah terdaftar!";
    } else {
        // Insert data user baru
        mysqli_query($db->koneksi, "INSERT INTO user (nama_lengkap, username, password) VALUES ('$nama_lengkap', '$username', '$password')");
        // Set pesan sukses dan redirect ke login
        $_SESSION['success_message'] = "Akun berhasil dibuat, silakan login!";
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Meta tags dasar -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register Rencana Manusia</title>
  <!-- Favicon dan CSS -->
  <link rel="shortcut icon" type="image/png" href="images/logos/logo_rm.png" />
  <link rel="stylesheet" href="css/styles.min.css" />
  <link rel="stylesheet" href="css/icons/tabler-icons/tabler-icons.css" />
</head>
<body>
  <!-- Wrapper utama -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <div class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
      <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100">
          <div class="col-md-8 col-lg-6 col-xxl-3">
            <div class="card mb-0">
              <div class="card-body">
                <!-- Logo dan judul -->
                <a href="index.php" class="text-nowrap logo-img text-center d-block py-3 w-100">
                  <img src="images/logos/logo_rm.png" alt="logo" class="logo-icon" style="width: 60px; height:auto" />
                  <br><br>
                  <span><h3>Rencana Manusia</h3></span>
                </a>
                <p class="text-center">Your Companion</p>

                <!-- Menampilkan error jika ada -->
                <?php if (isset($error)): ?>
                  <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <!-- Form pendaftaran -->
                <form method="POST">
                  <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="nama_lengkap" class="form-control" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                  </div>
                  <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-dark w-100 py-8 fs-4 mb-4">Register</button>
                  </div>
                  <div class="text-center">
                    <p class="mb-0">Already have an account? <a href="login.php" class="text-primary fw-bold">Login</a></p>
                  </div>
                </form>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Script JS -->
  <script src="libs/jquery/dist/jquery.min.js"></script>
  <script src="libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>
</html>