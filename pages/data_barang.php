<?php
    // Query data barang
    include 'koneksi.php';
    
    // Search & Filter Logic
    $search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
    $kategori_filter = isset($_GET['kategori']) ? mysqli_real_escape_string($koneksi, $_GET['kategori']) : '';
    
    $where = "WHERE 1=1";
    if ($search) {
        $where .= " AND (nama_barang LIKE '%$search%' OR kode_barang LIKE '%$search%' OR kategori LIKE '%$search%')";
    }
    if ($kategori_filter) {
        $where .= " AND kategori = '$kategori_filter'";
    }
    
    $query = "SELECT * FROM barang $where ORDER BY id DESC";
    $result = mysqli_query($koneksi, $query);
    
    // Get all categories for filter
    $cat_query = "SELECT DISTINCT kategori FROM barang WHERE kategori IS NOT NULL AND kategori != ''";
    $cat_result = mysqli_query($koneksi, $cat_query);
?>

<div class="page-title-block">
    <h2>Data Barang</h2>
    <p>Daftar semua barang dari semua supplier (referensi admin).</p>
</div>

<div class="filters-bar">
    <form action="index.php" method="GET" class="filters-left">
        <input type="hidden" name="page" value="data_barang">
        <select name="kategori" class="filter-select" onchange="this.form.submit()">
            <option value="">Semua Kategori</option>
            <?php while($cat = mysqli_fetch_assoc($cat_result)): ?>
                <option value="<?php echo $cat['kategori']; ?>" <?php echo $kategori_filter == $cat['kategori'] ? 'selected' : ''; ?>>
                    <?php echo $cat['kategori']; ?>
                </option>
            <?php endwhile; ?>
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
                    <th>Stok</th>
                    <th>Supplier</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($row['nama_barang']); ?>&background=random&color=fff&size=48" alt="Product" class="product-img">
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
                                if ($row['stok'] <= 5) $stock_class = 'stock-low';
                                elseif ($row['stok'] <= 15) $stock_class = 'stock-medium';
                            ?>
                            <div class="stock-text <?php echo $stock_class; ?>">
                                <?php echo $row['stok']; ?> unit
                            </div>
                        </td>
                        <td>
                            <div class="supplier-text">Toko Sebelah</div> <!-- Placeholder since no supplier in DB -->
                        </td>
                        <td>
                            <div style="display: flex; gap: 10px;">
                                <a href="detail.php?id=<?php echo $row['id']; ?>" class="btn-action-square btn-view-solid" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn-action-square btn-edit-solid" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="hapus.php?id=<?php echo $row['id']; ?>" class="btn-action-square btn-delete-solid" title="Hapus" onclick="return confirm('Yakin hapus barang ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">
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
