<?php
// Memulai session untuk menyimpan data login
session_start();

// Jika user belum login, arahkan ke halaman login
if(!isset($_SESSION['login'])){
    header("Location: login.php");
    exit;
}

// Menghubungkan ke database
include('koneksi.php');
$db = new database();

// Menerima parameter pencarian jika ada
$search = isset($_GET['search']) ? $_GET['search'] : "";

// Mengambil data note dari database
$data_note = $db->tampil_note($search);

// Memanggil file untuk notifikasi reminder task
include('reminder_task.php');

// Mengambil data user yang sedang login
$user_id = $_SESSION['id_user'];
$user = $db->get_user_by_id($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Pengaturan dasar halaman web -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Rencana Manusia</title>
  <!-- Favicon -->
  <link rel="shortcut icon" type="image/png" href="images/logos/logo_rm.png" />
  <!-- File CSS untuk styling -->
  <link rel="stylesheet" href="css/styles.min.css" />
  <link rel="stylesheet" href="css/icons/tabler-icons/tabler-icons.css" />
</head>

<body>
  <!-- Container utama -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    
    <!-- Sidebar navigasi -->
    <aside class="left-sidebar">
      <div>
        <!-- Logo dan tombol tutup (untuk mobile) -->
        <div class="brand-logo d-flex align-items-center justify-content-between">
          <a href="index.php" class="text-nowrap logo-img d-flex align-items-center">
            <img src="images/logos/logo_rm.png" alt="Logo" style="width: 30px; height: auto; margin-left: 10px;" />
            <h5 class="mb-0">Rencana Manusia </h5>
          </a>
          <!-- Tombol close sidebar (untuk mobile) -->
          <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-8"></i>
          </div>
        </div>

        <!-- Menu navigasi -->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
          <ul id="sidebarnav">
            <!-- Menu Home -->
            <li class="sidebar-item">
              <a class="sidebar-link" href="index.php" aria-expanded="false">
                <span><iconify-icon icon="solar:home-smile-bold-duotone" class="fs-6"></iconify-icon></span>
                <span class="hide-menu">Home</span>
              </a>
            </li>
            
            <!-- Menu Task -->
            <li class="sidebar-item">
              <a class="sidebar-link" href="./task.php" aria-expanded="false">
                <span><iconify-icon icon="solar:file-smile-bold-duotone" class="fs-6"></iconify-icon></span>
                <span class="hide-menu">Task</span>
              </a>
            </li>
            
            <!-- Menu To Do List -->
            <li class="sidebar-item">
              <a class="sidebar-link" href="./todo.php" aria-expanded="false">
                <span><iconify-icon icon="solar:list-check-minimalistic-bold-duotone" class="fs-6"></iconify-icon></span>
                <span class="hide-menu">To Do List</span>
              </a>
            </li>
            
            <!-- Menu Target -->
            <li class="sidebar-item">
              <a class="sidebar-link" href="./target.php" aria-expanded="false">
                <span><iconify-icon icon="solar:danger-circle-bold-duotone" class="fs-6"></iconify-icon></span>
                <span class="hide-menu">Target</span>
              </a>
            </li>
            
            <!-- Menu Note (aktif) -->
            <li class="sidebar-item">
              <a class="sidebar-link" href="./note.php" aria-expanded="false">
                <span><iconify-icon icon="solar:bookmark-square-minimalistic-bold-duotone" class="fs-6"></iconify-icon></span>
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

    <!-- Bagian utama konten -->
    <div class="body-wrapper">
      <!-- Header -->
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <!-- Tombol toggle sidebar (mobile) -->
          <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
              <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                <i class="ti ti-menu-2"></i>
              </a>
            </li>
            
            <!-- Notifikasi -->
            <li class="nav-item">
              <a class="nav-link nav-icon-hover position-relative" href="#" data-bs-toggle="dropdown">
                <i class="ti ti-bell-ringing"></i>
                <!-- Tampilkan badge notifikasi jika ada reminder -->
                <?php if($jumlah_reminder > 0): ?>
                  <span class="position-absolute top-10 start-100 translate-middle badge rounded-pill bg-danger">
                    <?= $jumlah_reminder ?>
                  </span>
                <?php endif; ?>
              </a>
              
              <!-- Dropdown notifikasi -->
              <ul class="dropdown-menu dropdown-menu-start dropdown-menu-animate-up">
                <li class="dropdown-header">Reminder Task Deadline</li>
                <?php if($jumlah_reminder == 0): ?>
                  <li><span class="dropdown-item-text text-muted">Tidak ada task mendekati deadline</span></li>
                <?php else: ?>
                  <?php foreach($reminder_task as $task): ?>
                    <li>
                      <span class="dropdown-item-text">
                        <b><?= htmlspecialchars($task['judul']) ?></b><br>
                        Deadline: <?= $task['tanggal_dl'] ?> <?= $task['waktu_dl'] ?>
                      </span>
                    </li>
                  <?php endforeach; ?>
                <?php endif; ?>
              </ul>
            </li>
            <!-- End notif reminder -->
          </ul>
          
          <!-- Menu profil user -->
          <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
              <li class="nav-item dropdown">
                <!-- Foto profil user -->
                <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php 
                    // Tampilkan foto profil user atau default
                    $foto_profil_nav = !empty($user['foto_profil']) ? 
                        "images/uploads/" . $user['foto_profil'] : 
                        "images/user-2.jpg";
                    ?>
                    <img src="<?= $foto_profil_nav ?>" alt="Foto Profil" width="35" height="35" class="rounded-circle" style="object-fit: cover;">
                </a>
                
                <!-- Dropdown menu profil -->
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                  <div class="message-body">
                    <a href="myprofile.php" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-user fs-6"></i>
                      <p class="mb-0 fs-3">My Profile</p>
                    </a>
                    <a href="task.php" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-list-check fs-6"></i>
                      <p class="mb-0 fs-3">My Task</p>
                    </a>
                    <a href="./logout.php" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                  </div>
                </div>
                <!-- End dropdown profil -->
              </li>
            </ul>
          </div>
          <!-- End bagian profil user -->
        </nav>
      </header>
      <!--  Header End -->

      <!-- Konten utama -->
      <div class="container-fluid">
        <div class="card">
          <div class="card-body">
            <!-- Mode edit note -->
            <?php
            $edit_mode = false;
            $edit_note = null;
            // Cek apakah sedang dalam mode edit
            if (isset($_GET['action']) && $_GET['action'] == 'edit_note' && isset($_GET['id'])) {
                // Cari note yang akan diedit
                foreach ($data_note as $n) {
                    if ($n['id_note'] == $_GET['id']) {
                        $edit_note = $n;
                        $edit_mode = true;
                        break;
                    }
                }
            }
            ?>

            <!-- Form edit note (jika dalam mode edit) -->
            <?php if ($edit_mode && $edit_note): ?>
            <div class="card mb-4 text-center">
              <div class="card-body">
                <form action="proses_note.php?action=update_note&id=<?= $edit_note['id_note'] ?>" method="POST">
                  <div class="row justify-content-center">
                    <!-- Input judul note -->
                    <div class="col-md-4 mb-2">
                      <input type="text" name="judul_note" class="form-control" value="<?= htmlspecialchars($edit_note['judul_note']) ?>" required>
                    </div>
                    <!-- Input deskripsi note -->
                    <div class="col-md-4 mb-2">
                      <textarea name="deskripsi_note" class="form-control" rows="3" required><?= htmlspecialchars($edit_note['deskripsi_note']) ?></textarea>
                    </div>
                    <!-- Tombol update -->
                    <div class="col-md-2 mb-2">
                      <button type="submit" class="btn btn-warning w-100">Update Note</button>
                    </div>
                    <!-- Tombol cancel -->
                    <div class="col-md-2 mb-2">
                      <a href="note.php" class="btn btn-secondary w-100">Cancel</a>
                    </div>
                  </div>
                </form>
              </div>
            </div>
            <?php endif; ?>

            <!-- Form tambah note baru -->
            <div class="card mb-4 text-center">
              <div class="card-body">
                <h5 class="card-title">Start New Note</h5>
                <form action="proses_note.php?action=add" method="POST">
                  <div class="row justify-content-center">
                    <!-- Input judul -->
                    <div class="col-md-4 mb-2">
                      <input type="text" name="judul_note" class="form-control" placeholder="Title..." required>
                    </div>
                    <!-- Input deskripsi -->
                    <div class="col-md-4 mb-2">
                      <textarea name="deskripsi_note" class="form-control" placeholder="Write.." rows="3" required></textarea>
                    </div>
                    <!-- Tombol submit -->
                    <div class="col-md-2 mb-2">
                      <button type="submit" class="btn btn-primary w-100">Create Note</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>

            <!-- Daftar note -->
            <div class="row">
              <h4 class="card-title">Note</h4>
              <?php if (empty($data_note)) { ?>
                  <!-- Pesan jika tidak ada note -->
                  <div class="col-12"><div class="alert alert-warning">Belum ada note.</div></div>
              <?php } else { 
                  // Loop untuk setiap note
                  foreach($data_note as $note): ?>
                  <div class="col-md-4 mb-4">
                      <div class="card">
                          <!-- Gambar note -->
                          <img src="images/storytelling.png" class="card-img-top img-fluid" alt="note-image" style="max-height:150px; object-fit:contain;">
                          <div class="card-body">
                              <!-- Judul note -->
                              <h5 class="card-title fw-semibold mb-4"><?= htmlspecialchars($note['judul_note']) ?></h5>
                              <!-- Deskripsi note -->
                              <p class="card-text"><?= htmlspecialchars($note['deskripsi_note']) ?></p>
                              <!-- Tombol edit -->
                              <a href="note.php?action=edit_note&id=<?= $note['id_note']; ?>"
                              class="btn btn-sm btn-outline-secondary">
                              <i class="ti ti-edit"> Edit</i></a>
                              <!-- Tombol delete -->
                              <a href="proses_note.php?action=delete_note&id=<?= $note['id_note']; ?>"
                              class="btn btn-sm btn-outline-secondary"
                              onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"><i class="ti ti-trash"> Delete</i></a>
                          </div>
                      </div>
                  </div>
              <?php endforeach; } ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> 

  <!-- Script JavaScript -->
  <script src="libs/jquery/dist/jquery.min.js"></script>
  <script src="libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="libs/simplebar/dist/simplebar.js"></script>
  <script src="js/sidebarmenu.js"></script>
  <script src="js/app.min.js"></script>
  <script src="js/dashboard.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>
</html>