<?php
session_start();
if(!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// menghubungkan koneksi database
include('koneksi.php');
$db = new database();

// Ambil data user dari session
if(isset($_SESSION['id_user'])) {
    $user_id = $_SESSION['id_user'];
    $user = $db->get_user_by_id($user_id);
    
    // Query dengan prepared statement untuk keamanan
    $query = "SELECT id_user, username, nama_lengkap, tanggal_daftar, foto_profil FROM user WHERE id_user = ?";
    $stmt = $db->koneksi->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        // Handle jika user tidak ditemukan
        die("User data not found");
    }
} else {
    // Handle jika session id_user tidak ada
    header("Location: login.php");
    exit;
}

// Include reminder task jika diperlukan
include('reminder_task.php');
// Di bagian atas file setelah include
if(isset($_GET['error']) && isset($_SESSION['upload_error'])) {
    echo '<div class="alert alert-danger">'.$_SESSION['upload_error'].'</div>';
    unset($_SESSION['upload_error']);
}
if(isset($_GET['success'])) {
    echo '<div class="alert alert-success">Foto profil berhasil diupdate!</div>';
}
?>

<!DOCTYPE html>
<html lang="en">

<!-- Head section -->
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Rencana Manusia</title>
  <link rel="shortcut icon" type="image/png" href="images/logos/logo_rm.png" />
  <link rel="stylesheet" href="css/styles.min.css" />
  <link rel="stylesheet" href="css/icons/tabler-icons/tabler-icons.css" />
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <!-- Sidebar Start -->
    <aside class="left-sidebar">
      <!-- Sidebar scroll-->
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
          <a href="index.php" class="text-nowrap logo-img d-flex align-items-center">
            <img src="images/logos/logo_rm.png" alt="" style="width: 30px; height: auto; margin-left: 10px;" />
            <h5 class="mb-0">Rencana Manusia </h5>
          </a>
          <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-8"></i>
          </div>
        </div>

        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
          <ul id="sidebarnav">
            <li class="sidebar-item">
              <a class="sidebar-link" href="index.php" aria-expanded="false">
                <span>
                  <iconify-icon icon="solar:home-smile-bold-duotone" class="fs-6"></iconify-icon>
                </span>
                <span class="hide-menu">Home</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./task.php" aria-expanded="false">
                <span>
                  <iconify-icon icon="solar:file-smile-bold-duotone" class="fs-6"></iconify-icon>
                </span>
                <span class="hide-menu">Task</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./todo.php" aria-expanded="false">
                <span>
                  <iconify-icon icon="solar:list-check-bold-duotone" class="fs-6"></iconify-icon>
                </span>
                <span class="hide-menu">To Do List</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./target.php" aria-expanded="false">
                <span>
                  <iconify-icon icon="solar:danger-circle-bold-duotone" class="fs-6"></iconify-icon>
                </span>
                <span class="hide-menu">Target</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./note.php" aria-expanded="false">
                <span>
                  <iconify-icon icon="solar:bookmark-square-minimalistic-bold-duotone" class="fs-6"></iconify-icon>
                </span>
                <span class="hide-menu">Note</span>
              </a>
            </li>
          </ul>
        </nav>
        <!-- End Sidebar navigation -->
      </div>
      <!-- End Sidebar scroll-->
    </aside>
    <!--  Sidebar End -->

    <!--  Main wrapper -->
    <div class="body-wrapper">
      <!--  Header Start -->
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <ul class="navbar-nav">
            <!-- Navbar Item untuk Toggle Sidebar (hanya tampil di mobile) -->
            <li class="nav-item d-block d-xl-none">
                <!-- Tombol toggle sidebar dengan icon menu -->
                <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                    <i class="ti ti-menu-2"></i>
                </a>
            </li>

            <!-- Navbar Item untuk Notifikasi -->
            <li class="nav-item">
                <!-- Tombol notifikasi dengan icon bell -->
                <a class="nav-link nav-icon-hover position-relative" href="#" data-bs-toggle="dropdown">
                    <i class="ti ti-bell-ringing"></i>
                    
                    <!-- Badge notifikasi (hanya muncul jika ada reminder) -->
                    <?php if($jumlah_reminder > 0): ?>
                    <span class="position-absolute top-10 start-100 translate-middle badge rounded-pill bg-danger">
                        <?= $jumlah_reminder ?> <!-- Menampilkan jumlah reminder -->
                    </span>
                    <?php endif; ?>
                </a>
                
                <!-- Dropdown menu untuk notifikasi -->
                <ul class="dropdown-menu dropdown-menu-start dropdown-menu-animate-up">
                    <!-- Header dropdown -->
                    <li class="dropdown-header">Reminder Task Deadline</li>
                    
                    <?php if($jumlah_reminder == 0): ?>
                    <!-- Jika tidak ada reminder -->
                    <li><span class="dropdown-item-text text-muted">Tidak ada task mendekati deadline</span></li>
                    <?php else: ?>
                        <!-- Jika ada reminder, loop melalui setiap task -->
                        <?php foreach($reminder_task as $task): ?>
                            <li>
                                <span class="dropdown-item-text">
                                    <!-- Menampilkan judul task -->
                                    <b><?= htmlspecialchars($task['judul']) ?></b><br>
                                    <!-- Menampilkan deadline task -->
                                    Deadline: <?= $task['tanggal_dl'] ?> <?= $task['waktu_dl'] ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </li>
            </ul>

          <!-- profil user -->
          <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
              <!-- Dropdown Menu Profil User -->
              <li class="nav-item dropdown">
                  <!-- Tombol trigger dropdown dengan foto profil -->
                  <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" 
                    data-bs-toggle="dropdown" aria-expanded="false">
                      <?php 
                      // Cek apakah user memiliki foto profil
                      // Jika ada, gunakan foto user, jika tidak gunakan foto default
                      $foto_profil_nav = !empty($user['foto_profil']) ? 
                          "images/uploads/" . $user['foto_profil'] : 
                          "images/user-2.jpg";
                      ?>
                      <!-- Tampilkan foto profil -->
                      <img src="<?= $foto_profil_nav ?>" alt="User Profile" width="35" height="35" 
                          class="rounded-circle" style="object-fit: cover;">
                  </a>
                  
                  <!-- Menu dropdown yang muncul saat tombol diklik -->
                  <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                      <div class="message-body">
                          <!-- Link ke profil user -->
                          <a href="myprofile.php" class="d-flex align-items-center gap-2 dropdown-item">
                              <i class="ti ti-user fs-6"></i> <!-- Icon user -->
                              <p class="mb-0 fs-3">My Profile</p>
                          </a>
                          
                          <!-- Link ke halaman task -->
                          <a href="task.php" class="d-flex align-items-center gap-2 dropdown-item">
                              <i class="ti ti-list-check fs-6"></i> <!-- Icon checklist -->
                              <p class="mb-0 fs-3">My Task</p>
                          </a>
                          
                          <!-- Tombol logout -->
                          <a href="./logout.php" class="btn btn-outline-primary mx-3 mt-2 d-block">
                              Logout
                          </a>
                      </div>
                  </div>
              </li>
            </ul>
          </div>
        </nav>
      </header>
      <!--  Header End -->

      <!-- Halaman my profile -->
      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="col-lg-8">
            <div class="card profile-card">
                <!-- Bagian Header Profil -->
                <div class="profile-header text-center">
                    <?php
                    // Cek apakah user punya foto profil
                    // Jika ada gunakan foto user, jika tidak gunakan foto default
                    $foto_profil = !empty($user['foto_profil']) ? 
                        "images/uploads/" . $user['foto_profil'] : 
                        "images/user-2.jpg";
                    ?>
                    
                    <!-- Menampilkan Foto Profil -->
                    <img src="<?= $foto_profil ?>" alt="Foto Profil" class="profile-img mb-3" 
                        style="width: 100px; height: 100px; border-radius: 100%; object-fit: cover;">
                    
                    <!-- Form Upload Foto Profil -->
                    <form action="upload_foto.php?action=upload" method="post" enctype="multipart/form-data" class="mt-4 text-center">
                        <label for="foto" class="form-label fw-bold">Ganti Foto Profil</label>
                        <!-- Input file untuk foto baru -->
                        <input type="file" name="foto" id="foto" class="form-control mb-2" 
                              style="max-width: 300px; margin: 0 auto;" 
                              accept="image/*" required>
                        <!-- Tombol submit -->
                        <button type="submit" name="upload" class="btn btn-primary">Upload Foto</button>
                    </form>
                    
                    <!-- Nama Lengkap User -->
                    <h3><?= htmlspecialchars($user['nama_lengkap']) ?></h3>
                    <!-- Tanggal bergabung (format tanggal) -->
                    <p class="text-white-50">Member sejak <?= date('d M Y', strtotime($user['tanggal_daftar'])) ?></p>
                </div>
              
                <!-- Bagian Detail Profil -->
                <div class="card-body">
                    <!-- Item Detail Username -->
                    <div class="detail-item">
                        <h5>
                            <iconify-icon icon="solar:user-bold-duotone" class="me-2"></iconify-icon> 
                            Username
                        </h5>
                        <p><?= htmlspecialchars($user['username']) ?></p>
                    </div>
                    
                    <!-- Item Detail Nama Lengkap -->
                    <div class="detail-item">
                        <h5>
                            <iconify-icon icon="solar:user-hand-up-bold-duotone" class="me-2"></iconify-icon> 
                            Nama Lengkap
                        </h5>
                        <p><?= htmlspecialchars($user['nama_lengkap']) ?></p>
                    </div>
                    
                    <!-- Item Detail Tanggal Daftar -->
                    <div class="detail-item">
                        <h5>
                            <iconify-icon icon="solar:calendar-bold-duotone" class="me-2"></iconify-icon> 
                            Tanggal Daftar
                        </h5>
                        <p><?= date('l, d F Y', strtotime($user['tanggal_daftar'])) ?></p>
                    </div>
                </div>
            </div>
            <!-- End my profile -->
    </div>
  </div>
  
<!-- Memuat library jQuery (versi minified) -->
<script src="libs/jquery/dist/jquery.min.js"></script>

<!-- Memuat library Bootstrap JS (termasuk Popper.js) dalam versi bundle minified -->
<script src="libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script untuk fungsi sidebar menu -->
<script src="/js/sidebarmenu.js"></script>

<!-- File JavaScript utama aplikasi (versi minified) -->
<script src="/js/app.min.js"></script>

<!-- Memuat library Iconify dari CDN untuk menampilkan icon modern -->
<script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>
</html>