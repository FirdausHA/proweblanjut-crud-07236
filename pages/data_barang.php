<?php
    // Query data barang
    include 'koneksi.php';
    
    // Proteksi Halaman
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // Search & Filter Logic (PDO)
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $kategori_filter = isset($_GET['kategori']) ? $_GET['kategori'] : '';
    $stok_filter = isset($_GET['filter']) ? $_GET['filter'] : '';
    
    $where = "WHERE 1=1";
    $params = [];
    if ($search) {
        $where .= " AND (nama_barang LIKE ? OR kode_barang LIKE ? OR kategori LIKE ?)";
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
    }
    if ($kategori_filter) {
        $where .= " AND kategori = ?";
        $params[] = $kategori_filter;
    }
    if ($stok_filter == 'low_stock') {
        $where .= " AND jumlah < 5";
    }
    
    $query = "SELECT * FROM barang $where ORDER BY id DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $barang_list = $stmt->fetchAll();
    
    // Get all categories for filter
    $cat_query = "SELECT DISTINCT kategori FROM barang WHERE kategori IS NOT NULL AND kategori != ''";
    $stmt_cat = $pdo->prepare($cat_query);
    $stmt_cat->execute();
    $categories = $stmt_cat->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="page-title-block">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h2>Data Barang</h2>
            <p>Daftar semua barang dalam inventaris gudang.</p>
        </div>
        <?php if ($stok_filter == 'low_stock'): ?>
            <div class="filter-badge">
                <i class="fas fa-filter"></i> Menampilkan: Stok Kurang (< 5)
                <a href="index.php?page=data_barang" class="btn-clear-filter" title="Hapus Filter"><i class="fas fa-times"></i></a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="filters-bar">
    <form action="index.php" method="GET" class="filters-left">
        <input type="hidden" name="page" value="data_barang">
        <select name="kategori" class="filter-select" onchange="this.form.submit()">
            <option value="">Semua Kategori</option>
            <?php foreach($categories as $cat): ?>
                <option value="<?php echo $cat; ?>" <?php echo $kategori_filter == $cat ? 'selected' : ''; ?>>
                    <?php echo $cat; ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <div class="search-input-group">
            <input type="text" name="search" placeholder="Cari nama / kode / kateg" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn-search">Cari</button>
        </div>
    </form>
    
    <a href="tambah.php" class="btn-add">
        <i class="fas fa-plus"></i> Tambah Barang
    </a>
</div>

<div class="card">
    <div class="table-container" style="margin: -24px; border-radius: 0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Nama Barang</th>
                    <th>Kode Barang</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Tgl Masuk</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($barang_list) > 0): ?>
                    <?php foreach($barang_list as $row): ?>
                    <tr>
                        <td>
                            <?php if($row['gambar'] && file_exists('uploads/' . $row['gambar'])): ?>
                                <img src="uploads/<?php echo $row['gambar']; ?>" alt="Product" class="product-img">
                            <?php else: ?>
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($row['nama_barang']); ?>&background=random&color=fff&size=48" alt="Product" class="product-img">
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="product-name"><?php echo htmlspecialchars($row['nama_barang']); ?></div>
                        </td>
                        <td>
                            <div class="product-code"><?php echo htmlspecialchars($row['kode_barang']); ?></div>
                        </td>
                        <td>
                            <span class="badge-category"><?php echo htmlspecialchars($row['kategori']); ?></span>
                        </td>
                        <td>
                            <div class="price-text">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></div>
                        </td>
                        <td>
                            <?php 
                                $stock_class = 'stock-high';
                                if ($row['jumlah'] <= 5) $stock_class = 'stock-low';
                                elseif ($row['jumlah'] <= 15) $stock_class = 'stock-medium';
                            ?>
                            <div class="stock-text <?php echo $stock_class; ?>">
                                <?php echo $row['jumlah']; ?> unit
                            </div>
                        </td>
                        <td>
                            <div class="date-text"><?php echo date('d/m/Y', strtotime($row['tanggal_masuk'])); ?></div>
                        </td>
                        <td>
                            <span class="badge-status <?php echo $row['status'] == 'aktif' ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 10px;">
                                <a href="detail.php?id=<?php echo $row['id']; ?>" class="btn-action-square btn-view-solid" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn-action-square btn-edit-solid" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn-action-square btn-delete-solid" title="Hapus" onclick="openDeleteModal(<?php echo $row['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="empty-state">
                                <i class="fas fa-box-open fa-3x"></i>
                                <h4>Belum ada data barang</h4>
                                <p>Mulai dengan menambahkan barang baru</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
