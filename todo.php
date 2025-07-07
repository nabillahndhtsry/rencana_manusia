<?php
// Memulai session untuk menyimpan data login
session_start();

// Jika user belum login, redirect ke halaman login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Menampilkan pesan sukses dari cookie jika ada
if (isset($_COOKIE['todo_message'])) {
    echo '<div class="alert alert-success">'.htmlspecialchars($_COOKIE['todo_message']).'</div>';
    // Hapus cookie setelah ditampilkan
    setcookie('todo_message', '', time() - 3600, '/'); 
}

// Menampilkan pesan error/sukses dari session jika ada
if (isset($_SESSION['messages'])) {
    foreach ($_SESSION['messages'] as $type => $message) {
        // Tampilkan alert danger untuk error, success untuk pesan biasa
        echo '<div class="alert alert-'.($type === 'error' ? 'danger' : 'success').'">'
            .htmlspecialchars($message).
            '</div>';
    }
    // Hapus pesan setelah ditampilkan
    unset($_SESSION['messages']); 
}

// Hubungkan ke database
include('koneksi.php');
$db = new database();

// Include file untuk reminder task
include('reminder_task.php');

// Ambil data user yang login
$user_id = $_SESSION['id_user'];
$user = $db->get_user_by_id($user_id);

// Daftar hari dalam seminggu
$hari_todo = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];

// Proses form tambah to-do list
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_todo'])) {
    $hari = $_POST['hari'];
    $deskripsi = $_POST['deskripsi_todo'];
    // Tambahkan to-do ke database
    $db->tambah_todo($deskripsi, $hari);
    // Redirect untuk menghindari resubmit form
    header("Location: todo.php"); 
    exit;
}

// Hitung progress to-do
$total_todo = 0;  // Total semua to-do
$done_todo = 0;   // To-do yang sudah selesai

foreach ($hari_todo as $hari) {
    // Ambil to-do per hari
    $todos = $db->get_todo_by_day($hari);
    $total_todo += count($todos);
    
    // Hitung yang sudah selesai
    foreach ($todos as $todo) {
        if ($todo['status_todo'] == 'done') {
            $done_todo++;
        }
    }
}

// Hitung persentase yang sudah selesai
$done_percent = $total_todo > 0 ? round(($done_todo / $total_todo) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Pengaturan dasar halaman -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rencana Manusia</title>
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="images/logos/logo_rm.png" />
    <!-- Stylesheet -->
    <link rel="stylesheet" href="css/styles.min.css" />
    <link rel="stylesheet" href="css/icons/tabler-icons/tabler-icons.css" />
    <!-- Icon library -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</head>

<body>
    <!-- Wrapper utama -->
    <div class="page-wrapper" id="main-wrapper">
        <!-- Sidebar navigasi -->
        <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        
        <!-- Sidebar Start -->
        <aside class="left-sidebar">
            <!-- Logo dan tombol tutup sidebar (mobile) -->
            <div class="brand-logo d-flex align-items-center justify-content-between">
                <a href="index.php" class="text-nowrap logo-img d-flex align-items-center">
                    <img src="images/logos/logo_rm.png" alt="Logo" style="width: 30px; height: auto; margin-left: 10px;" />
                    <h5 class="mb-0">Rencana Manusia </h5>
                </a>
                <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                    <i class="ti ti-x fs-8"></i>
                </div>
            </div>

            <!-- Menu navigasi sidebar -->
            <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
                <ul id="sidebarnav">
                    <!-- Home -->
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="index.php" aria-expanded="false">
                            <span><iconify-icon icon="solar:home-smile-bold-duotone" class="fs-6"></iconify-icon></span>
                            <span class="hide-menu">Home</span>
                        </a>
                    </li>
                    
                    <!-- Task -->
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="./task.php" aria-expanded="false">
                            <span><iconify-icon icon="solar:file-smile-bold-duotone" class="fs-6"></iconify-icon></span>
                            <span class="hide-menu">Task</span>
                        </a>
                    </li>
                    
                    <!-- To Do List -->
                    <li class="sidebar-item">
                        <a class="sidebar-link active" href="./todo.php" aria-expanded="false">
                            <span><iconify-icon icon="solar:list-check-minimalistic-bold-duotone" class="fs-6"></iconify-icon></span>
                            <span class="hide-menu">To Do List</span>
                        </a>
                    </li>
                    
                    <!-- Target -->
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="./target.php" aria-expanded="false">
                            <span><iconify-icon icon="solar:danger-circle-bold-duotone" class="fs-6"></iconify-icon></span>
                            <span class="hide-menu">Target</span>
                        </a>
                    </li>
                    
                    <!-- Note -->
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="./note.php" aria-expanded="false">
                            <span><iconify-icon icon="solar:bookmark-square-minimalistic-bold-duotone" class="fs-6"></iconify-icon></span>
                            <span class="hide-menu">Note</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- End Sidebar navigation -->
        </aside>
        <!-- End Sidebar -->

        <!-- Main wrapper -->
        <div class="body-wrapper">
            <!-- Header -->
            <header class="app-header">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <!-- Tombol toggle sidebar (mobile) -->
                    <ul class="navbar-nav">
                        <li class="nav-item d-block d-xl-none">
                            <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="#">
                                <i class="ti ti-menu-2"></i>
                            </a>
                        </li>
                        
                        <!-- Notifikasi -->
                        <li class="nav-item">
                            <a class="nav-link nav-icon-hover position-relative" href="#" data-bs-toggle="dropdown">
                                <i class="ti ti-bell-ringing"></i>
                                <!-- Badge notifikasi jika ada reminder -->
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
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <!-- End Header -->

            <!-- Konten utama -->
            <div class="container-fluid">
                <div class="row">
                    <!-- Daftar To-Do per minggu -->
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="mb-4">Weekly To Do List</h5>
                                <div class="row row-cols-1 row-cols-md-4 g-4">
                                    <!-- Loop untuk setiap hari -->
                                    <?php foreach ($hari_todo as $hari): ?>
                                    <div class="col">
                                        <div class="card h-100">
                                            <!-- Header kartu dengan nama hari -->
                                            <div class="card-header bg-primary text-black">
                                                <?= ucfirst($hari) ?>
                                            </div>
                                            <div class="card-body">
                                                <!-- Form tambah to-do -->
                                                <form method="POST" class="mb-3">
                                                    <input type="hidden" name="hari" value="<?= $hari ?>">
                                                    <div class="input-group">
                                                        <input type="text" name="deskripsi_todo" 
                                                               class="form-control" placeholder="Add agenda..." required>
                                                        <button type="submit" name="tambah_todo" 
                                                                class="btn btn-success">
                                                            <iconify-icon icon="mdi:plus-thick" width="16" height="16"></iconify-icon>
                                                        </button>
                                                    </div>
                                                </form>
                                                
                                                <!-- Daftar to-do -->
                                                <?php
                                                $todos = $db->get_todo_by_day($hari);
                                                if (empty($todos)): ?>
                                                    <!-- Jika tidak ada to-do -->
                                                    <p class="text-muted">Take a break</p>
                                                <?php else: ?>
                                                    <ul class="list-group">
                                                        <!-- Loop untuk setiap to-do -->
                                                        <?php foreach ($todos as $todo): ?>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <!-- Form update status -->
                                                                <form method="POST" action="proses_todo.php" class="d-inline">
                                                                    <input type="hidden" name="action" value="update_status">
                                                                    <input type="hidden" name="id_todo" value="<?= $todo['id_todo'] ?>">
                                                                    <div class="form-check form-check-inline">
                                                                        <!-- Checkbox status -->
                                                                        <input class="form-check-input" type="checkbox" 
                                                                            name="status_todo" value="done"
                                                                            <?= $todo['status_todo'] == 'done' ? 'checked' : '' ?>
                                                                            onchange="this.form.submit()">
                                                                    </div>
                                                                </form>
                                                                <!-- Deskripsi to-do -->
                                                                <span class="<?= $todo['status_todo'] == 'done' ? 'text-decoration-line-through' : '' ?>">
                                                                    <?= htmlspecialchars($todo['deskripsi_todo']) ?>
                                                                </span>
                                                            </div>
                                                            <!-- Tombol hapus -->
                                                            <a href="proses_todo.php?action=delete_todo&id_todo=<?= $todo['id_todo'] ?>" 
                                                            class="btn btn-sm btn-danger ms-2"
                                                            onclick="return confirm('Yakin ingin menghapus?')">
                                                                <i class="ti ti-trash"></i>
                                                            </a>
                                                        </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress bar -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">This week's progress </h5>
                                <!-- Bar progress -->
                                <div class="progress mt-3" style="height: 25px;">
                                    <div class="progress-bar bg-success" 
                                         style="width: <?= $done_percent ?>%"
                                         role="progressbar">
                                        <?= $done_percent ?>%
                                    </div>
                                </div>
                                <!-- Ringkasan progress -->
                                <div class="mt-2 d-flex justify-content-between">
                                    <span class="text-success">
                                        <iconify-icon icon="mdi:check"></iconify-icon>
                                        <?= $done_todo ?> Done
                                    </span>
                                    <span class="text-danger">
                                        <iconify-icon icon="mdi:clock"></iconify-icon>
                                        <?= $total_todo - $done_todo ?> Pending
                                    </span>
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