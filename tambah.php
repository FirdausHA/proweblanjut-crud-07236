<?php
include 'koneksi.php';

if (!function_exists('clean_input')) {
    function clean_input($data) {
        global $koneksi;
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return mysqli_real_escape_string($koneksi, $data);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_barang = clean_input($_POST['kode_barang']);
    $nama_barang = clean_input($_POST['nama_barang']);
    $kategori = clean_input($_POST['kategori']);
    $jumlah = clean_input($_POST['jumlah']);
    $harga = clean_input($_POST['harga']);
    $deskripsi = clean_input($_POST['deskripsi']);
    $tanggal_masuk = clean_input($_POST['tanggal_masuk']);
    
    // Cek kode unik
    $check_query = "SELECT id FROM barang WHERE kode_barang = '$kode_barang'";
    $check_result = mysqli_query($koneksi, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['pesan'] = "Kode barang sudah digunakan!";
        $_SESSION['tipe'] = "error";
    } else {
        $query = "INSERT INTO barang (kode_barang, nama_barang, kategori, jumlah, harga, deskripsi, tanggal_masuk, status, created_at) 
                  VALUES ('$kode_barang', '$nama_barang', '$kategori', '$jumlah', '$harga', '$deskripsi', '$tanggal_masuk', 'aktif', NOW())";
        
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['pesan'] = "Barang berhasil ditambahkan!";
            $_SESSION['tipe'] = "success";
            header("Location: index.php?page=data_barang");
            exit();
        } else {
            $_SESSION['pesan'] = "Gagal menambahkan barang: " . mysqli_error($koneksi);
            $_SESSION['tipe'] = "error";
        }
    }
}

$page_title = "Tambah Barang";
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
                <a href="index.php?page=data_barang" class="breadcrumb-item">Data Barang</a>
                <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
                <span class="breadcrumb-item active" style="color: var(--primary);">Tambah</span>
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
                <span class="user-name">Staff Gudang</span>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="content-inner">
            <div class="form-header-row">
            <a href="index.php?page=data_barang" class="btn-back-circle">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="page-title-block" style="margin-bottom: 0;">
                <h2 style="font-size: 24px; font-weight: 700;">Tambah Barang</h2>
                <p style="font-size: 14px;">Buat data barang baru untuk stok gudang.</p>
            </div>
        </div>
        
        <div class="card" style="padding: 48px !important;">
            <form method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
                    <!-- Column 1 -->
                    <div>
                        <div class="form-group" style="margin-bottom: 24px;">
                            <label class="form-label-required">Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control-custom" placeholder="Contoh: Kipas Angin" required>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 24px;">
                            <label class="form-label-required">Kategori</label>
                            <select name="kategori" class="form-control-custom" required>
                                <option value="">Pilih Kategori</option>
                                <option value="Elektronik">Elektronik</option>
                                <option value="Pakaian">Pakaian</option>
                                <option value="Makanan">Makanan</option>
                                <option value="Minuman">Minuman</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 24px;">
                            <label class="form-label-required">Jumlah Stok</label>
                            <input type="number" name="jumlah" class="form-control-custom" placeholder="0" min="0" required>
                        </div>

                        <div class="form-group" style="margin-bottom: 24px;">
                            <label class="form-label-required">Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk" class="form-control-custom" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    
                    <!-- Column 2 -->
                    <div>
                        <div class="form-group" style="margin-bottom: 24px;">
                            <label class="form-label-required">Harga (Rp)</label>
                            <input type="number" name="harga" class="form-control-custom" placeholder="Contoh: 100.000" min="0" step="0.01" required>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 24px;">
                            <label class="form-label-required">Kode Barang</label>
                            <input type="text" name="kode_barang" class="form-control-custom" placeholder="BRG001" required>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 24px;">
                            <label class="form-label-required">Status</label>
                            <select name="status" class="form-control-custom" required>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group" style="margin-top: 8px;">
                    <label class="form-label-required">Deskripsi Barang</label>
                    <textarea name="deskripsi" class="form-control-custom" rows="3" style="resize: none;" placeholder="Isi deskripsi barang..."></textarea>
                </div>

                <div class="form-actions-end">
                    <a href="index.php?page=data_barang" class="btn-batal">Batal</a>
                    <button type="submit" class="btn-simpan">Simpan Barang</button>
                </div>
            </form>
        </div>
    </div> <!-- Close content-inner -->
</main>
</div>

<?php include 'includes/footer.php'; ?>
