<?php
include 'koneksi.php';
?>

<div class="page-title-block">
    <h2>Dashboard</h2>
    <p>Ringkasan informasi stok dan inventaris gudang.</p>
</div>

<div class="stats-row">
    <!-- Stock Alert Card -->
    <a href="index.php?page=data_barang&filter=low_stock" class="card stat-card-flex stat-card-link">
        <div class="stat-icon-wrapper warning">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-content">
            <h3 class="stat-label">Stok Kurang (< 5)</h3>
            <?php
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM barang where jumlah < 5");
            $stmt->execute();
            $row = $stmt->fetch();
            ?>
            <div class="stat-value"><?php echo $row['total'] ?? 0; ?></div>
        </div>
    </a>

    <!-- Total Items Card -->
    <a href="index.php?page=data_barang" class="card stat-card-flex stat-card-link">
        <div class="stat-icon-wrapper info">
            <i class="fas fa-boxes"></i>
        </div>
        <div class="stat-content">
            <h3 class="stat-label">Total Macam Barang</h3>
            <?php
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM barang");
            $stmt->execute();
            $row = $stmt->fetch();
            ?>
            <div class="stat-value"><?php echo $row['total']; ?></div>
        </div>
    </a>
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
