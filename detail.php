<?php
include 'koneksi.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$query = "SELECT * FROM barang WHERE id = $id";
$result = mysqli_query($koneksi, $query);
$barang = mysqli_fetch_assoc($result);

if (!$barang) {
    $_SESSION['pesan'] = "Barang tidak ditemukan!";
    $_SESSION['tipe'] = "error";
    header("Location: index.php?page=data_barang");
    exit();
}

$page_title = "Detail Barang";
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
                <span class="breadcrumb-item active" style="color: var(--primary);">Detail</span>
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
                <h2 style="font-size: 24px; font-weight: 700;">Detail Barang</h2>
                <p style="font-size: 14px;">Informasi lengkap produk.</p>
            </div>
        </div>
        
        <div class="card" style="padding: 0 !important;">
            <div class="card-header-simple" style="padding: 24px 32px; border-bottom: 1.5px solid #f1f5f9;">
                <h3 style="font-size: 18px; font-weight: 700;">Informasi Produk</h3>
            </div>
            
            <div class="card-body" style="padding: 40px;">
                <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 48px; align-items: start;">
                    <!-- Column 1: Image -->
                    <div style="text-align: center;">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($barang['nama_barang']); ?>&background=00897b&color=fff&size=400&font-size=0.3" alt="Product Image" class="detail-product-image">
                    </div>
                    
                    <!-- Column 2: Information -->
                    <div>
                        <div class="detail-display-group">
                            <label class="detail-display-label">Kode Barang</label>
                            <div class="detail-display-value"><?php echo htmlspecialchars($barang['kode_barang']); ?></div>
                        </div>
                        
                        <div class="detail-display-group">
                            <label class="detail-display-label">Nama Barang</label>
                            <div class="detail-display-value"><?php echo htmlspecialchars($barang['nama_barang']); ?></div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 20px;">
                            <div class="detail-display-group" style="margin-bottom: 0;">
                                <label class="detail-display-label">Kategori</label>
                                <div class="detail-display-value"><?php echo htmlspecialchars($barang['kategori']); ?></div>
                            </div>
                            <div class="detail-display-group" style="margin-bottom: 0;">
                                <label class="detail-display-label">Harga Satuan</label>
                                <div class="detail-display-value">Rp <?php echo number_format($barang['harga'], 0, ',', '.'); ?></div>
                            </div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 20px;">
                            <div class="detail-display-group" style="margin-bottom: 0;">
                                <label class="detail-display-label">Jumlah Stok</label>
                                <div class="detail-display-value <?php echo $barang['jumlah'] < 10 ? 'stock-low' : ''; ?>">
                                    <?php echo $barang['jumlah']; ?> unit
                                </div>
                            </div>
                            <div class="detail-display-group" style="margin-bottom: 0;">
                                <label class="detail-display-label">Tanggal Masuk</label>
                                <div class="detail-display-value"><?php echo date('d/m/Y', strtotime($barang['tanggal_masuk'])); ?></div>
                            </div>
                        </div>
                        
                        <div class="detail-display-group">
                            <label class="detail-display-label">Deskripsi Produk</label>
                            <div class="detail-display-value detail-display-textarea">
                                <?php echo !empty($barang['deskripsi']) ? nl2br(htmlspecialchars($barang['deskripsi'])) : 'Tidak ada deskripsi.'; ?>
                            </div>
                        </div>
                        
                        <div style="margin-top: 32px; display: flex; gap: 12px; justify-content: flex-end;">
                            <a href="edit.php?id=<?php echo $barang['id']; ?>" class="btn-primary">
                                <i class="fas fa-edit"></i> Edit Barang
                            </a>
                            <a href="index.php?page=data_barang" class="btn-secondary">
                                <i class="fas fa-list"></i> Kembali ke Daftar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- Close content-inner -->
</main>
</div>

<?php include 'includes/footer.php'; ?>
