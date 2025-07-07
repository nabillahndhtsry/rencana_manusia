<?php
// Memulai session untuk menyimpan data login
session_start();
// Jika user belum login, arahkan ke halaman login
if(!isset($_SESSION['login'])){
    // Kalau belum login, redirect ke halaman login.php
    header("Location: login.php");
    exit;
}

// Menghubungkan ke file koneksi
include('koneksi.php');
$db = new database();
// Mengambil parameter pencarian jika ada
$search = isset($_GET['search']) ? $_GET['search'] : "";
// Mengambil data barang dari database
$data_target = $db->tampil_target($search);
// Reminder task di notifikasi
include('reminder_task.php');
// ambil data user
$user_id = $_SESSION['id_user'];
$user = $db->get_user_by_id($user_id);

// Menghitung jumlah reminder task yang mendekati deadline
$pending_target = 0;
$in_progress_target = 0;
$done_target = 0;

foreach ($data_target as $row) {
    if ($row['status_target'] == 'pending') {
        $pending_target++;
    } elseif ($row['status_target'] == 'in_progress') {
        $in_progress_target++;
    }    elseif ($row['status_target'] == 'done') {
        $done_target++;  
    }
}

// target fokus
$fokus_target = mysqli_fetch_assoc(mysqli_query($db->koneksi, "SELECT * FROM target WHERE fokus_target=1"));
if($fokus_target) {
    $fokus_target = "<div class='alert alert-info'>Target Fokus: <b>{$fokus_target['target']}</b></div>";
} else {
    $fokus_target = "<div class='alert alert-warning'>Tidak ada target fokus saat ini.</div>";
}


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
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    
    <!-- Sidebar Start -->
    <aside class="left-sidebar">
      <!-- Sidebar scroll-->
      <div>
        <!-- Logo dan tombol tutup (untuk mobile) -->
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
                  <iconify-icon icon="solar:list-check-minimalistic-bold-duotone" class="fs-6"></iconify-icon>
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
                    <img src="<?= $foto_profil_nav ?>" alt="" width="35" height="35" class="rounded-circle" style="object-fit: cover;">
                </a>
                <!-- Dropdown menu profil -->
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                  <div class="message-body">
                    <a href="myp." class="d-flex align-items-center gap-2 dropdown-item">
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

      <!-- content -->
      <div class="container-fluid">
        <div class="row">

        <!-- target content -->
          <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Target!</h4>
              <div class="table-responsive">
                <!-- Tabel untk menampilkan data target -->
                <table class="table text-nowrap align-middle mb-0">
                  <thead>
                    <tr class="border-2 border-bottom border-primary border-0"> 
                      <th scope="col" class="ps-0">No</th>
                      <th scope="col" class="text-center">Target</th>
                      <th scope="col" class="text-center">Description</th>
                      <th scope="col" class="text-center">Date achieved</th>
                      <th scope="col" class="text-center">Status</th>
                      <th scope="col" class="text-center">Action</th>
                    </tr>
                  </thead>
                  
                  <tbody class="table-group-divider">
                    <?php 
                    // tampilkan pesan jika data task kosong
                    if (empty($data_target)) {
                      echo "<tr><td colspan='6'>Tidak ada data ditemukan</td></tr>";
                    } else {
                      $no = 1;
                    foreach($data_target as $row): 
                    ?>
                    <tr>
                        <span class="text-truncate">
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['target'] ?></td>
                        <td><?php echo $row['deskripsi_target'] ?></td>
                        <td><?php echo $row['tanggal_target'] ?></td>
                        <td><?php echo $row['status_target']; ?></td>   
                        <td>
                          <div class="d-flex gap-2 align-items-center">
                            <!-- Form update status langsung -->
                            <form action="proses_target.php?action=update_status_target" method="POST" style="display:inline;">
                              <input type="hidden" name="id_target" value="<?php echo $row['id_target']; ?>">
                              <select name="status_target" onchange="this.form.submit()" class="form-select form-select-sm" style="width:auto; display:inline;">
                                <option value="pending" <?php if($row['status_target']=='pending') echo 'selected'; ?>>Pending</option>
                                <option value="in_progress" <?php if($row['status_target']=='in_progress') echo 'selected'; ?>>In Progress</option>
                                <option value="done" <?php if($row['status_target']=='done') echo 'selected'; ?>>Done</option>
                              </select>
                            </form>
                            <!-- Tombol Fokus -->
                            <form action="proses_target.php" method="POST" style="display:inline;">
                              <input type="hidden" name="action" value="set_focus">
                              <input type="hidden" name="id_target" value="<?= $row['id_target'] ?>">
                              <button type="submit" class="btn btn-sm <?= $row['fokus_target'] ? 'btn-success' : 'btn-outline-secondary' ?>" title="Jadikan Fokus">
                                <?= $row['fokus_target'] ? '★ Focus' : 'Set Focus' ?>
                              </button>
                            </form>
                            <!-- Tombol Delete -->
                            <a href="proses_target.php?action=delete_target&id=<?php echo $row['id_target']; ?>"
                              onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"
                              class="btn btn-sm btn-danger" title="Hapus">
                              <i class="ti ti-trash"></i> Delete
                            </a>
                          </div>
                        </td>
                        </span>
                    </tr>
                    <?php endforeach; } ?>
                  </tbody>
                </table>
              </div>
            </div>
            
            <!-- Tombol tambah target di bawah tabel -->
            <button type="button" style="background:none; border:none; padding:0; margin-bottom:16px; cursor:pointer;" data-bs-toggle="modal" data-bs-target="#modalTambahTarget" title="Tambah Target">
              <iconify-icon icon="ic:round-add" width="36" height="36"></iconify-icon>
            </button>

            <!-- card Tambah Target -->
            <div class="modal fade" id="modalTambahTarget" tabindex="-1" aria-labelledby="modalTambahTargetLabel" aria-hidden="true">
              <div class="modal-dialog">
                <form action="proses_target.php?action=add" method="POST" class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahTargetLabel">Add New Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label for="target" class="form-label">Target</label>
                      <input type="text" class="form-control" id="judul" name="target" required>
                    </div>
                    <div class="mb-3">
                      <label for="deskripsi_target" class="form-label">Description</label>
                      <input type="text" class="form-control" id="deskripsi_target" name="deskripsi_target" required>
                    </div>
                    <div class="mb-3">
                      <label for="tanggal_target" class="form-label">Date achieved</label>
                      <input type="date" class="form-control" id="tanggal_target" name="tanggal_target" required>
                    </div>
                    <div class="mb-3">
                      <label for="status_target" class="form-label">Status</label>
                      <select class="form-control" id="status_target" name="status_target" required>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="done">Done</option>
                      </select>
                    </div>
                    <div class="mb-3">
                        <label>Focus</label>
                        <input type="text" name="fokus" class="form-control" required>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- progres fokus -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        <iconify-icon icon="solar:target-bold-duotone"></iconify-icon>
                        Target Focus
                    </h4>
            
                  <?php 
                  // Ambil target yang sedang fokus
                  $fokus_target = $db->query("SELECT * FROM target WHERE fokus_target = 1 LIMIT 1");
                  
                  if($fokus_target && mysqli_num_rows($fokus_target) > 0) {
                      $fokus_target = mysqli_fetch_assoc($fokus_target);
                      $progress = $fokus_target['progress'] ?? 0;
                  ?>
                      <h6><?= htmlspecialchars($fokus_target['target']) ?></h6>
                      <small>Deadline: <?= $fokus_target['tanggal_target'] ?></small>
                      <div class="mt-2">
                          <span class="badge bg-<?= 
                              ($fokus_target['status_target'] == 'done') ? 'success' : 
                              (($fokus_target['status_target'] == 'in_progress') ? 'warning' : 'secondary') 
                          ?>">
                              Status: <?= ucfirst(str_replace('_', ' ', $fokus_target['status_target'])) ?>
                          </span>
                      </div>
                      <!-- Tombol Unfokus -->
                      <form action="proses_target.php" method="POST" class="mt-3">
                          <input type="hidden" name="action" value="unset_focus">
                          <input type="hidden" name="id_target" value="<?= $fokus_target['id_target'] ?>">
                          <button type="submit" class="btn btn-outline-danger btn-sm"><i class="ti ti-trash"> Delete</i></button>
                      </form>
                  <?php } else { ?>
                      <div class="text-center text-muted py-3">
                          <iconify-icon icon="solar:target-line-duotone" class="fs-1"></iconify-icon>
                          <p>No focus target</p>
                      </div>
                  <?php } ?>
              </div>
          </div>
      </div>

      <!-- motivation card -->
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body text-center">
              <img src="images/backgrounds/product-tip.png" alt="image" class="img-fluid" width="205">
              <h4 class="mt-7">Productivity Tips!</h4>
              <p class="card-subtitle mt-2 mb-3">Who's stopping you? Just be productive</p>
            </div>
          </div>
        </div>
        
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