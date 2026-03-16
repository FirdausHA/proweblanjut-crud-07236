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
    $stok = clean_input($_POST['stok']);
    $harga = clean_input($_POST['harga']);
    $deskripsi = clean_input($_POST['deskripsi']);
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
                  stok = '$stok',
                  harga = '$harga',
                  deskripsi = '$deskripsi',
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
        <div class="page-title-block">
            <h2>Edit Barang</h2>
            <p>Perbarui informasi item barang dalam inventaris.</p>
        </div>
        
        <div class="card">
            <div class="card-header-simple" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h3 style="font-size: 18px; font-weight: 600;">Form Edit Barang</h3>
                <a href="index.php?page=data_barang" class="btn-secondary" style="padding: 8px 16px;">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
            
            <div class="card-body">
                <form method="POST" class="form-vertical">
                    <div class="form-row" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; margin-bottom: 24px;">
                        <div class="form-group">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-color);">
                                <i class="fas fa-barcode"></i> Kode Barang *
                            </label>
                            <input type="text" name="kode_barang" value="<?php echo htmlspecialchars($barang['kode_barang']); ?>" required style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; outline: none;">
                        </div>
                        
                        <div class="form-group">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-color);">
                                <i class="fas fa-box"></i> Nama Barang *
                            </label>
                            <input type="text" name="nama_barang" value="<?php echo htmlspecialchars($barang['nama_barang']); ?>" required style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; outline: none;">
                        </div>
                    </div>
                    
                    <div class="form-row" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; margin-bottom: 24px;">
                        <div class="form-group">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-color);">
                                <i class="fas fa-tags"></i> Kategori *
                            </label>
                            <select name="kategori" required style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; background: white; outline: none;">
                                <option value="">Pilih Kategori</option>
                                <option value="Elektronik" <?php echo $barang['kategori'] == 'Elektronik' ? 'selected' : ''; ?>>Elektronik</option>
                                <option value="Pakaian" <?php echo $barang['kategori'] == 'Pakaian' ? 'selected' : ''; ?>>Pakaian</option>
                                <option value="Makanan" <?php echo $barang['kategori'] == 'Makanan' ? 'selected' : ''; ?>>Makanan</option>
                                <option value="Minuman" <?php echo $barang['kategori'] == 'Minuman' ? 'selected' : ''; ?>>Minuman</option>
                                <option value="Alat Tulis" <?php echo $barang['kategori'] == 'Alat Tulis' ? 'selected' : ''; ?>>Alat Tulis</option>
                                <option value="Olahraga" <?php echo $barang['kategori'] == 'Olahraga' ? 'selected' : ''; ?>>Olahraga</option>
                                <option value="Lainnya" <?php echo $barang['kategori'] == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-color);">
                                <i class="fas fa-cubes"></i> Stok *
                            </label>
                            <input type="number" name="stok" value="<?php echo $barang['stok']; ?>" min="0" required style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; outline: none;">
                        </div>
                        
                        <div class="form-group">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-color);">
                                <i class="fas fa-money-bill-wave"></i> Harga (Rp) *
                            </label>
                            <input type="number" name="harga" value="<?php echo $barang['harga']; ?>" min="0" required style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; outline: none;">
                        </div>
                    </div>
                    
                    <div class="form-row" style="margin-bottom: 24px;">
                        <div class="form-group">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-color);">
                                <i class="fas fa-toggle-on"></i> Status
                            </label>
                            <select name="status" required style="width: 100%; max-width: 250px; padding: 12px; border: 1px solid var(--border); border-radius: 8px; background: white; outline: none;">
                                <option value="aktif" <?php echo $barang['status'] == 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                                <option value="nonaktif" <?php echo $barang['status'] == 'nonaktif' ? 'selected' : ''; ?>>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 24px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-color);">
                            <i class="fas fa-align-left"></i> Deskripsi
                        </label>
                        <textarea name="deskripsi" rows="4" style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; outline: none;"><?php echo htmlspecialchars($barang['deskripsi']); ?></textarea>
                    </div>
                    
                    <div class="form-actions" style="display: flex; justify-content: flex-end; gap: 12px; padding-top: 24px; border-top: 1px solid var(--border);">
                        <button type="reset" class="btn-secondary" style="border: none; cursor: pointer;">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                        <button type="submit" class="btn-primary" style="border: none; cursor: pointer;">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>
