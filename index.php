<?php
session_start(); // Memulai sesi PHP untuk menyimpan data login

if(!isset($_SESSION['login'])){
    // Kalau belum login, redirect ke halaman login.php
    header("Location: login.php");
    exit; // Hentikan eksekusi script
}

// Menghubungkan ke file koneksi database
include('koneksi.php');
$db = new database(); // Membuat objek database

// Mengambil parameter pencarian jika ada
$search = isset($_GET['search']) ? $_GET['search'] : "";

// Mengambil reminder task untuk ditampilkan di notifikasi
include('reminder_task.php');

// Ambil data user aktif dari session
$user_id = $_SESSION['id_user'];
$user = $db->get_user_by_id($user_id);

// Ambil semua data task, target, dan note
$data_task = $db->tampil_data();
$data_target = $db->tampil_target();
$data_note = $db->tampil_note();

// Hitung total task dan yang sudah selesai
$total_task = count($data_task);
$done_task = 0;
foreach ($data_task as $task) {
    if ($task['status'] == 'done') $done_task++;
}

// Hitung total target dan jumlah per status
$total_target = count($data_target);
$pending_target = 0;
$in_progress_target = 0;
$done_target = 0;
foreach ($data_target as $target) {
    if ($target['status_target'] == 'pending') {
        $pending_target++;
    } elseif ($target['status_target'] == 'in_progress') {
        $in_progress_target++;
    } elseif ($target['status_target'] == 'done') {
        $done_target++;
    }
}

// Ambil target yang sedang difokuskan
$fokus_target = $db->query("SELECT * FROM target WHERE fokus_target = 1 LIMIT 1");
$fokus_target = $fokus_target ? mysqli_fetch_assoc($fokus_target) : null;

// Ambil 3 note terbaru
$recent_notes = array_slice($data_note, 0, 3);
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Metadata halaman -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Rencana Manusia</title>
  <!-- Icon favicon -->
  <link rel="shortcut icon" type="image/png" href="images/logos/logo_rm.png" />
  <!-- CSS utama -->
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
        <!-- Logo Brand -->
        <div class="brand-logo d-flex align-items-center justify-content-between">
          <a href="index.php" class="text-nowrap logo-img d-flex align-items-center">
            <img src="images/logos/logo_rm.png" alt="" style="width: 30px; height: auto; margin-left: 10px;" />
            <h5 class="mb-0">Rencana Manusia </h5>
          </a>
          <!-- Tombol close sidebar (untuk mobile) -->
          <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-8"></i>
          </div>
        </div>

        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
          <ul id="sidebarnav">
            <!-- Menu Home -->
            <li class="sidebar-item">
              <a class="sidebar-link" href="index.php" aria-expanded="false">
                <span>
                  <iconify-icon icon="solar:home-smile-bold-duotone" class="fs-6"></iconify-icon>
                </span>
                <span class="hide-menu">Home</span>
              </a>
            </li>
            <!-- Menu Task -->
            <li class="sidebar-item">
              <a class="sidebar-link" href="./task.php" aria-expanded="false">
                <span>
                  <iconify-icon icon="solar:file-smile-bold-duotone" class="fs-6"></iconify-icon>
                </span>
                <span class="hide-menu">Task</span>
              </a>
            </li>
            <!-- Menu To Do List -->
            <li class="sidebar-item">
              <a class="sidebar-link" href="./todo.php" aria-expanded="false">
                <span>
                  <iconify-icon icon="solar:list-check-bold-duotone" class="fs-6"></iconify-icon>
                </span>
                <span class="hide-menu">To Do List</span>
              </a>
            </li>
            <!-- Menu Target -->
            <li class="sidebar-item">
              <a class="sidebar-link" href="./target.php" aria-expanded="false">
                <span>
                  <iconify-icon icon="solar:danger-circle-bold-duotone" class="fs-6"></iconify-icon>
                </span>
                <span class="hide-menu">Target</span>
              </a>
            </li>
            <!-- Menu Note (aktif) -->
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
            <!-- Toggler sidebar di mobile -->
            <li class="nav-item d-block d-xl-none">
              <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                <i class="ti ti-menu-2"></i>
              </a>
            </li>

            <!-- Notifikasi reminder -->
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

              <!-- Dropdown daftar reminder -->
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

          <!-- Bagian profil user -->
          <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
              <li class="nav-item dropdown">
                <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php 
                    // Tampilkan foto profil user atau default
                    $foto_profil_nav = !empty($user['foto_profil']) ? 
                        "images/uploads/" . $user['foto_profil'] : 
                        "images/user-2.jpg";
                    ?>
                    <!-- Foto profil -->
                    <img src="<?= $foto_profil_nav ?>" alt="" width="35" height="35" class="rounded-circle" style="object-fit: cover;">
                </a>

                <!-- Dropdown profil -->
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
        <div class="row">
          <!-- Task section -->
          <div class="col-lg-12">
            <div class="card task-card">
              <div class="card-body">
                <!-- Header Card -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="card-title mb-0">Task Summary
                  <!-- Link ke halaman task -->
                  <a href="task.php">
                    <i class="ti ti-file-star"></i></a></h5>
                </div>

                <!-- Ringkasan jumlah task -->
                <div class="d-flex justify-content-between mb-3">
                  <div class="text-center">
                    <h2 class="mb-0"><?= $total_task ?></h2>
                    <small class="text-muted">Total Task</small>
                  </div>
                  <div class="text-center">
                    <h2 class="mb-0 text-success"><?= $done_task ?></h2>
                    <small class="text-muted">Completed</small>
                  </div>
                  <div class="text-center">
                    <h2 class="mb-0 text-warning"><?= $total_task - $done_task ?></h2>
                    <small class="text-muted">Pending</small>
                  </div>
                </div>
                <!-- Progress bar untuk task -->
                <div class="progress progress-thin mb-3">
                  <div class="progress-bar bg-success" style="width: <?= $total_task ? ($done_task/$total_task)*100 : 0 ?>%"></div>
                </div>
                <!-- Daftar deadline task terdekat -->
                <h6 class="mb-3">Upcoming Deadlines:</h6>
                <div class="list-group">
                  <?php 
                  $today = date('Y-m-d');
                  $upcoming_tasks = array_slice($data_task, 0, 3);
                  foreach($upcoming_tasks as $task): 
                    $is_overdue = $task['tanggal_dl'] < $today && $task['status'] != 'done';
                  ?>
                   <a href="task.php" class="list-group-item list-group-item-action <?= $is_overdue ? 'list-group-item-danger' : '' ?>">
                    <div class="d-flex justify-content-between">
                      <span><?= htmlspecialchars($task['judul']) ?></span>
                      <!-- Badge status task -->
                      <span class="badge bg-<?= $task['status'] == 'done' ? 'success' : 'warning' ?>">
                        <?= ucfirst($task['status']) ?>
                      </span>
                    </div>
                    <small class="text-muted">Due: <?= $task['tanggal_dl'] ?></small>
                    </a>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>

        <!-- Tips produktivitas -->
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body text-center">
              <img src="images/backgrounds/product-tip.png" alt="image" class="img-fluid" width="205">
              <h4 class="mt-7">Productivity Tips!</h4>
              <p class="card-subtitle mt-2 mb-3">Who's stopping you? Just be productive</p>
            </div>
          </div>
        </div>

        <!-- Target section -->
         <div class="col-lg-8">
            <div class="card task-card">
              <div class="card-body">\
                <!-- Header Card -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="card-title mb-0">Target Summary
                  <a href="target.php">
                    <i class="ti ti-target-arrow"></i>
                  </a></h5>
                </div>
                
                <!-- Statistik Target -->
                <div class="d-flex justify-content-between mb-3">
                  <div class="text-center">
                    <h2 class="mb-0"><?= $total_target ?></h2>
                    <small class="text-muted">Total Target</small>
                  </div>
                  <div class="text-center">
                    <h2 class="mb-0 text-success"><?= $done_target ?></h2>
                    <small class="text-muted">Achieved</small>
                  </div>
                  <div class="text-center">
                    <h2 class="mb-0 text-warning"><?= $pending_target + $in_progress_target ?></h2>
                    <small class="text-muted">In Progress</small>
                  </div>
                </div>
                
                <!-- Progress bar -->
                <div class="progress progress-thin mb-3">
                  <div class="progress-bar bg-success" style="width: <?= $total_target ? ($done_target/$total_target)*100 : 0 ?>%"></div>
                </div>
                
                <!-- Target fokus jika ada -->
                <?php if($fokus_target): ?>
                <div class="alert alert-primary mb-3">
                  <h6>Current Focus:</h6>
                  <p class="mb-1"><strong><?= htmlspecialchars($fokus_target['target']) ?></strong></p>
                  <p class="mb-1 small"><?= htmlspecialchars($fokus_target['deskripsi_target']) ?></p>
                  <div class="d-flex justify-content-between">
                    <small>Target: <?= $fokus_target['tanggal_target'] ?></small>
                    <span class="badge bg-<?= 
                      ($fokus_target['status_target'] == 'done') ? 'success' : 
                      (($fokus_target['status_target'] == 'in_progress') ? 'warning' : 'secondary') 
                    ?>">
                      <?= ucfirst(str_replace('_', ' ', $fokus_target['status_target'])) ?>
                    </span>
                  </div>
                </div>
                <?php else: ?>
                <div class="alert alert-warning">
                  No focus target set. <a href="target.php" class="alert-link">Set focus target</a>
                </div>
                <?php endif; ?>
                
                <!-- Daftar 3 target terbaru -->
                <h6 class="mb-3">Recent Targets:</h6>
                <?php
                // Fungsi untuk menentukan warna badge status
                function getStatusBadgeClass($status) {
                  switch($status) {
                    case 'done': return 'success';
                    case 'in_progress': return 'warning';
                    default: return 'secondary';
                  }
                }
                ?>

                <!-- Daftar target -->
                <div class="list-group">
                  <?php 
                  $recent_targets = array_slice($data_target, 0, 3);
                  foreach($recent_targets as $target): 
                  ?>
                    <a href="target.php" class="list-group-item list-group-item-action">
                      <div class="d-flex justify-content-between">
                        <span><?= htmlspecialchars($target['target']) ?></span>
                        <span class="badge bg-<?= getStatusBadgeClass($target['status_target']) ?>">
                          <?= ucfirst(str_replace('_', ' ', $target['status_target'])) ?>
                        </span>
                      </div>
                      <small class="text-muted">Target: <?= $target['tanggal_target'] ?></small>
                    </a>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>

        <!-- Note section -->
         <div class="col-lg-12">
          <div class="card task-card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Recent Notes
                <a href="note.php">
                  <i class="ti ti-notes"></i>
                </a></h5>
              </div>
              
              <?php 
              // Get only the 3 most recent notes
              $recent_notes = array_slice($data_note, 0, 3);
              if (empty($recent_notes)): ?>
              <!-- Jika tidak ada note -->
              <div class="alert alert-info">No recent notes found.</div>
              <?php else: ?>
                <div class="row">
                  <?php foreach($recent_notes as $note): ?>
                  <div class="col-md-4 mb-4">
                    <div class="card h-100">
                      <!-- Note Image Container -->
                      <div class="image-container" style="height: 200px; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
                        <?php if(!empty($note['gambar'])): ?>
                          <img src="images/notes/<?= htmlspecialchars($note['gambar']) ?>" 
                              class="img-fluid" 
                              alt="Note image"
                              style="max-height: 100%; max-width: 100%; width: auto; height: auto; object-fit: contain;">
                        <?php else: ?>
                          <img src="images/storytelling.png" 
                              class="img-fluid" 
                              alt="Default note image"
                              style="max-height: 100%; max-width: 100%; width: auto; height: auto; object-fit: contain;">
                        <?php endif; ?>
                      </div>

                      <!-- Konten Note -->
                      <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($note['judul_note']) ?></h5>
                        <?php 
                        $short_desc = strlen($note['deskripsi_note']) > 100 
                          ? substr(htmlspecialchars($note['deskripsi_note']), 0, 100).'...' 
                          : htmlspecialchars($note['deskripsi_note']);
                        ?>
                        <p class="card-text"><?= $short_desc ?></p>
                      </div>
                      <!-- Tombol aksi -->
                      <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between">
                          <a href="note.php?action=edit_note&id=<?= $note['id_note'] ?>" 
                            class="btn btn-sm btn-outline-secondary">
                            <i class="ti ti-edit"></i> Edit
                          </a>
                          <a href="note.php?id=<?= $note['id_note'] ?>" 
                            class="btn btn-sm btn-outline-secondary">
                            <i class="ti ti-eye"></i> View
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div> <!-- /.row -->
      </div> <!-- /.container-fluid -->
    </div> <!-- /.body-wrapper -->
  </div> <!-- /.page-wrapper -->

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