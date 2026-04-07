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

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data barang
$query = "SELECT * FROM barang WHERE id = $id";
$result = mysqli_query($koneksi, $query);
$barang = mysqli_fetch_assoc($result);

if (!$barang) {
    $_SESSION['pesan'] = "Barang tidak ditemukan!";
    $_SESSION['tipe'] = "error";
    header("Location: index.php?page=data_barang");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_barang = clean_input($_POST['kode_barang']);
    $nama_barang = clean_input($_POST['nama_barang']);
    $kategori = clean_input($_POST['kategori']);
    $jumlah = clean_input($_POST['jumlah']);
    $harga = clean_input($_POST['harga']);
    $deskripsi = clean_input($_POST['deskripsi']);
    $tanggal_masuk = clean_input($_POST['tanggal_masuk']);
    $status = clean_input($_POST['status']);
    
    // Cek kode unik (kecuali untuk barang ini)
    $check_query = "SELECT id FROM barang WHERE kode_barang = '$kode_barang' AND id != $id";
    $check_result = mysqli_query($koneksi, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['pesan'] = "Kode barang sudah digunakan!";
        $_SESSION['tipe'] = "error";
    } else {
        $query = "UPDATE barang SET 
                  kode_barang = '$kode_barang',
                  nama_barang = '$nama_barang',
                  kategori = '$kategori',
                  jumlah = '$jumlah',
                  harga = '$harga',
                  deskripsi = '$deskripsi',
                  tanggal_masuk = '$tanggal_masuk',
                  status = '$status',
                  updated_at = NOW()
                  WHERE id = $id";
        
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['pesan'] = "Barang berhasil diperbarui!";
            $_SESSION['tipe'] = "success";
            header("Location: index.php?page=data_barang");
            exit();
        } else {
            $_SESSION['pesan'] = "Gagal memperbarui barang: " . mysqli_error($koneksi);
            $_SESSION['tipe'] = "error";
        }
    }
}

$page_title = "Edit Barang";
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
                <span class="breadcrumb-item active">Edit Barang</span>
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
                <h2 style="font-size: 24px; font-weight: 700;">Edit Barang</h2>
                <p style="font-size: 14px;">Perbarui informasi item barang dalam inventaris.</p>
            </div>
        </div>
        
        <div class="card" style="padding: 48px !important;">
            <form method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
                    <!-- Column 1 -->
                    <div>
                        <div class="form-group" style="margin-bottom: 24px;">
                            <label class="form-label-required">Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control-custom" value="<?php echo htmlspecialchars($barang['nama_barang']); ?>" required>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 24px;">
                            <label class="form-label-required">Kategori</label>
                            <select name="kategori" class="form-control-custom" required>
                                <option value="">Pilih Kategori</option>
                                <option value="Elektronik" <?php echo $barang['kategori'] == 'Elektronik' ? 'selected' : ''; ?>>Elektronik</option>
                                <option value="Pakaian" <?php echo $barang['kategori'] == 'Pakaian' ? 'selected' : ''; ?>>Pakaian</option>
                                <option value="Makanan" <?php echo $barang['kategori'] == 'Makanan' ? 'selected' : ''; ?>>Makanan</option>
                                <option value="Minuman" <?php echo $barang['kategori'] == 'Minuman' ? 'selected' : ''; ?>>Minuman</option>
                                <option value="Lainnya" <?php echo $barang['kategori'] == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                            </select>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 24px;">
                            <label class="form-label-required">Jumlah Stok</label>
                            <input type="number" name="jumlah" class="form-control-custom" value="<?php echo $barang['jumlah']; ?>" min="0" required>
                        </div>

                        <div class="form-group" style="margin-bottom: 24px;">
                            <label class="form-label-required">Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk" class="form-control-custom" value="<?php echo date('Y-m-d', strtotime($barang['tanggal_masuk'])); ?>" required>
                        </div>
                    </div>
                    
                    <!-- Column 2 -->
                    <div>
                        <div class="form-group" style="margin-bottom: 24px;">
                            <label class="form-label-required">Harga (Rp)</label>
                            <input type="number" name="harga" class="form-control-custom" value="<?php echo $barang['harga']; ?>" min="0" step="0.01" required>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 24px;">
                            <label class="form-label-required">Kode Barang</label>
                            <input type="text" name="kode_barang" class="form-control-custom" value="<?php echo htmlspecialchars($barang['kode_barang']); ?>" required>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 24px;">
                            <label class="form-label-required">Status</label>
                            <select name="status" class="form-control-custom" required>
                                <option value="aktif" <?php echo $barang['status'] == 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                                <option value="nonaktif" <?php echo $barang['status'] == 'nonaktif' ? 'selected' : ''; ?>>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group" style="margin-top: 8px;">
                    <label class="form-label-required">Deskripsi Barang</label>
                    <textarea name="deskripsi" class="form-control-custom" rows="3" style="resize: none;"><?php echo htmlspecialchars($barang['deskripsi']); ?></textarea>
                </div>

                <div class="form-actions-end">
                    <a href="index.php?page=data_barang" class="btn-batal">Batal</a>
                    <button type="submit" class="btn-simpan">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div> <!-- Close content-inner -->
</main>
</div>

<?php include 'includes/footer.php'; ?>
