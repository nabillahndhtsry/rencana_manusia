<?php
// Memulai session untuk menyimpan data login
session_start();
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
$data_task = $db->tampil_data($search);
// Reminder task di notifikasi
include('reminder_task.php');

// ambil data user
$user_id = $_SESSION['id_user'];
$user = $db->get_user_by_id($user_id);

// Menghitung total task, done task, dan pending task
$total_task = count($data_task);
$done_task = 0;
$pending_task = 0;

foreach ($data_task as $row) {
    if ($row['status'] == 'done') {
        $done_task++;
    } elseif ($row['status'] == 'pending') {
        $pending_task++;
    }
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
            <!-- Menu Note -->
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
              <a class="nav-link nav-icon-hover position-relative"  data-bs-toggle="dropdown">
                <i class="ti ti-bell-ringing"></i>
                <!-- Tampilkan badge notifikasi jika ada reminder -->
                <?php if($jumlah_reminder > 0): ?>
                  <span class="position-absolute top-10 start-100 translate-middle badge rounded-pill bg-danger">
                    <?= $jumlah_reminder ?>
                  </span>
                <?php endif; ?>
              </a>
              <!-- Dropdown notifikasi -->
              <ul class="dropdown-menu dropdown-menu-start">
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

      <!-- content -->
      <div class="container-fluid">
        <div class="row">

        <!-- task content -->
          <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Looking your Task?</h5>
              <!-- Form Pencarian Task -->
              <form class="mb-5 d-flex" method="GET" action="task.php">
                <input type="text" class="form-control me-2" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn btn-primary">Search</button>
              </form>

              <h5 class="card-title">Your Task!</h5>
              <div class="table-responsive">
                <!-- Tabel untk menampilkan data task -->
                <table class="table text-nowrap align-middle mb-0">
                  <thead>
                    <tr class="border-2 border-bottom border-primary border-0"> 
                      <th scope="col" class="ps-0">No</th>
                      <th scope="col" class="text-center">Judul Tugas</th>
                      <th scope="col" class="text-center">Deskripsi Tugas</th>
                      <th scope="col" class="text-center">Tanggal Deadline</th>
                      <th scope="col" class="text-center">Waktu Deadline</th>
                      <th scope="col" class="text-center">Status</th>
                      <th scope="col" class="text-center">Action</th>
                    </tr>
                  </thead>
                  
                  <tbody class="table-group-divider">
                    <?php
                    // tampilkan pesan jika data task kosong
                    if (empty($data_task)) {
                      echo "<tr><td colspan='6'>Tidak ada data ditemukan</td></tr>";
                    } else {
                      $no = 1;
                      // loop untuk menampilkan setiap data task
                      foreach($data_task as $row){
                    ?>
                    <tr>
                      <span class="text-truncate">
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['judul']; ?></td>
                        <td><?php echo $row['deskripsi']; ?></td>
                        <td><?php echo $row['tanggal_dl']; ?></td>
                        <td><?php echo $row['waktu_dl']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td>
                          <div class="d-flex gap-2 align-items-center">
                            <!-- Form update status langsung -->
                            <form action="proses_task.php?action=update" method="POST" style="display:inline;">
                              <input type="hidden" name="id_task" value="<?php echo $row['id_task']; ?>">
                              <select name="status" onchange="this.form.submit()" class="form-select form-select-sm" style="width:auto; display:inline;">
                                <option value="pending" <?php if($row['status']=='pending') echo 'selected'; ?>>Pending</option>
                                <option value="done" <?php if($row['status']=='done') echo 'selected'; ?>>Done</option>
                              </select>
                            </form>

                            <!-- Edit task -->
                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditTask<?= $row['id_task']; ?>">
                              <i class="ti ti-edit"></i> Edit
                            </button>

                            <!-- Delete Task -->
                            <a href="proses_task.php?action=delete&id=<?php echo $row['id_task']; ?>"
                              onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"
                              class="btn btn-sm btn-danger" title="Hapus">
                              <i class="ti ti-trash"></i> Delete
                            </a>
                          </div>
                        </td>
                        </span>
                    </tr>
                      <?php
                          }
                      }
                      ?>
                  </tbody>
                </table>
              </div>
            </div>
            
            <!-- Tombol tambah task di bawah tabel -->
            <button type="button" style="background:none; border:none; padding:0; margin-bottom:16px; cursor:pointer;" data-bs-toggle="modal" data-bs-target="#modalTambahTask" title="Tambah Task">
              <iconify-icon icon="mdi:plus-thick" width="36" height="36"></iconify-icon>
            </button>

            <!-- Modal Tambah Task -->
            <div class="modal fade" id="modalTambahTask" tabindex="-1" aria-labelledby="modalTambahTaskLabel" aria-hidden="true">
              <div class="modal-dialog">
                <form action="proses_task.php?action=add" method="POST" class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahTaskLabel">Tambah Task Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label for="judul" class="form-label">Title Task</label>
                      <input type="text" class="form-control" id="judul" name="judul" required>
                    </div>
                    <div class="mb-3">
                      <label for="deskripsi" class="form-label">Description</label>
                      <input type="text" class="form-control" id="deskripsi" name="deskripsi" required>
                    </div>
                    <div class="mb-3">
                      <label for="tanggal_dl" class="form-label">Deadline Date</label>
                      <input type="date" class="form-control" id="tanggal_dl" name="tanggal_dl" required>
                    </div>
                    <div class="mb-3">
                      <label for="waktu_dl" class="form-label">Deadline</label>
                      <input type="time" class="form-control" id="waktu_dl" name="waktu_dl" required>
                    </div>
                    <div class="mb-3">
                      <label for="status" class="form-label">Status</label>
                      <select class="form-control" id="status" name="status" required>
                        <option value="pending">Pending</option>
                        <option value="done">Done</option>
                      </select>
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

        
            <?php foreach($data_task as $row): ?>
            <!-- Modal Edit Task -->
            <div class="modal fade" id="modalEditTask<?= $row['id_task']; ?>" tabindex="-1" aria-labelledby="modalEditTaskLabel<?= $row['id_task']; ?>" aria-hidden="true">
              <div class="modal-dialog">
                <form action="proses_task.php?action=edit" method="POST" class="modal-content">
                  <input type="hidden" name="id_task" value="<?= $row['id_task']; ?>">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modalEditTaskLabel<?= $row['id_task']; ?>">Edit Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="form-label">Title Task</label>
                      <input type="text" class="form-control" name="judul" value="<?= htmlspecialchars($row['judul']); ?>" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Description</label>
                      <input type="text" class="form-control" name="deskripsi" value="<?= htmlspecialchars($row['deskripsi']); ?>" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Deadline Date</label>
                      <input type="date" class="form-control" name="tanggal_dl" value="<?= $row['tanggal_dl']; ?>" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Deadline</label>
                      <input type="time" class="form-control" name="waktu_dl" value="<?= $row['waktu_dl']; ?>" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Status</label>
                      <select class="form-control" name="status" required>
                        <option value="pending" <?= $row['status']=='pending'?'selected':''; ?>>Pending</option>
                        <option value="done" <?= $row['status']=='done'?'selected':''; ?>>Done</option>
                      </select>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                  </div>
                </form>
              </div>
            </div>
            <?php endforeach; ?>
        
        <!-- Card untuk motivasi -->
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
        
        <!-- progres -->
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title d-flex align-items-center gap-2 mb-5 pb-3">Progress 
                <iconify-icon icon="solar:progress-bar-bold-duotone" class="fs-6 text-primary"></iconify-icon>
              </h5>
              <!-- Menampilkan progress done -->
              <div class="row">
                <div class="col-6">
                  <iconify-icon icon="solar:check-circle-bold-duotone" class="fs-10 d-flex text-success"></iconify-icon>
                  <span class="fs-11 mt-2 d-block text-nowrap">Complete</span>
                  <h4 class="mb-0 mt-1"><?php echo $done_task; ?></h4>
                </div>
                <!-- Menampilkan progress pending -->
                <div class="col-6">
                  <iconify-icon icon="solar:close-circle-bold-duotone" class="fs-10 d-flex text-danger"></iconify-icon>
                  <span class="fs-11 mt-2 d-block text-nowrap">Not yet</span>
                  <h4 class="mb-0 mt-1"><?php echo $pending_task; ?></h4>
                </div>

                <div class="progress mt-6" role="progressbar" aria-label="Progress" aria-valuenow="<?php echo $done_percent; ?>" aria-valuemin="0" aria-valuemax="100">
                  <div class="progress-bar bg-primary" style="width: <?php echo $done_percent; ?>%"></div>
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