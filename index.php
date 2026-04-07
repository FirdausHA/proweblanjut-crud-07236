<?php
include 'koneksi.php';

// Logika Remember Me: Cek Cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    $query = "SELECT * FROM users WHERE remember_token = '$token' LIMIT 1";
    $result = mysqli_query($koneksi, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['nama_lengkap'] = $row['nama_lengkap'];
    }
}

// Cek Autentikasi
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

$page_titles = array(
    'dashboard' => 'Dashboard',
    'data_barang' => 'Data Barang'
);

$page_title = $page_titles[$page] ?? 'Dashboard';
?>

<?php include 'includes/header.php'; ?>

<!-- Sidebar -->
<?php include 'includes/menu.php'; ?>

<!-- Main Content Wrapper -->
<div class="main-wrapper">
    <!-- Top Header -->
    <header class="header">
        <div class="header-left" style="display: flex; align-items: center; gap: 16px;">
            <div class="mobile-toggle" id="mobile-toggle">
                <i class="fas fa-bars"></i>
            </div>
            <nav class="breadcrumb">
                <span class="breadcrumb-item">Home</span>
                <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
                <span class="breadcrumb-item active"><?php echo $page_title; ?></span>
            </nav>
        </div>
        <div class="header-right">
            <div class="date-time">
                <span id="current-day-date"><?php echo date('l, d M Y'); ?></span>
                <span class="separator">•</span>
                <span id="current-time"><?php echo date('H:i'); ?></span>
            </div>
            <div class="user-info">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <span class="user-name"><?php echo $_SESSION['nama_lengkap']; ?></span>
                    </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="main-content">
        <div class="content-inner">
            <!-- Notifikasi -->
            <?php if(isset($_SESSION['pesan'])): ?>
            <div class="notification <?php echo $_SESSION['tipe']; ?>">
                <div class="notification-content">
                    <i class="fas <?php echo $_SESSION['tipe'] == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                    <span><?php echo $_SESSION['pesan']; ?></span>
                </div>
                <button class="notification-close" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <?php 
            unset($_SESSION['pesan']);
            unset($_SESSION['tipe']);
            endif; 
            ?>

            <!-- Page Content -->
            <?php
            // Load konten berdasarkan halaman
            switch($page) {
                case 'dashboard':
                    include 'pages/dashboard.php';
                    break;
                case 'data_barang':
                    include 'pages/data_barang.php';
                    break;
                default:
                    include 'pages/dashboard.php';
            }
            ?>
        </div> <!-- Close content-inner -->
    </main>
    <?php include 'includes/footer.php'; ?>
<?php // Closing tags are in footer.php ?>


