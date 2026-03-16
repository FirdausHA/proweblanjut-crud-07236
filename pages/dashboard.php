<?php
include 'koneksi.php';
?>

<div class="page-title-block">
    <h2>Dashboard</h2>
    <p>Ringkasan informasi stok dan inventaris gudang.</p>
</div>

<div class="stats-row">
    <!-- Stock Alert Card -->
    <div class="card stat-card-flex">
        <div class="stat-icon-wrapper warning">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-content">
            <h3 class="stat-label">Stok Kurang (< 5)</h3>
            <?php
            $sql = "SELECT COUNT(*) as total FROM barang where stok < 5";
            $result = $koneksi->query($sql);
            $row = $result->fetch_assoc();
            ?>
            <div class="stat-value"><?php echo $row['total'] ?? 0; ?></div>
        </div>
    </div>

    <!-- Total Items Card -->
    <div class="card stat-card-flex">
        <div class="stat-icon-wrapper info">
            <i class="fas fa-boxes"></i>
        </div>
        <div class="stat-content">
            <h3 class="stat-label">Total Macam Barang</h3>
            <?php
            $sql = "SELECT COUNT(*) as total FROM barang";
            $result = $koneksi->query($sql);
            $row = $result->fetch_assoc();
            ?>
            <div class="stat-value"><?php echo $row['total']; ?></div>
        </div>
    </div>
</div>

<div class="card quick-menu-card">
    <div class="card-header-simple">
        <h3>Menu Cepat</h3>
    </div>
    <div class="btn-group-flex">
        <a href="index.php?page=data_barang" class="btn-primary">
            <i class="fas fa-box"></i> Lihat Data Barang
        </a>
        <a href="tambah.php" class="btn-secondary">
            <i class="fas fa-plus"></i> Tambah Barang Baru
        </a>
    </div>
</div>
