<?php
include 'koneksi.php';

// Proteksi Halaman
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$errors = [];

// 1. Ambil ID dari URL (Prepared Statement)
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt_get = $pdo->prepare("SELECT * FROM barang WHERE id = ?");
$stmt_get->execute([$id]);
$barang = $stmt_get->fetch();

if (!$barang) {
    $_SESSION['pesan'] = "Barang tidak ditemukan!";
    $_SESSION['tipe'] = "error";
    header("Location: index.php?page=data_barang");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_barang = trim($_POST['nama_barang']);
    $kode_barang = trim($_POST['kode_barang']);
    $kategori = trim($_POST['kategori']);
    $jumlah = $_POST['jumlah'];
    $harga = $_POST['harga'];
    $deskripsi = trim($_POST['deskripsi']);
    $tanggal_masuk = $_POST['tanggal_masuk'];
    $status = $_POST['status'];

    // 2. Validasi Server Side
    if (empty($nama_barang)) $errors[] = "Nama barang tidak boleh kosong.";
    if (!is_numeric($jumlah)) $errors[] = "Jumlah harus berupa angka numerik.";
    if (!is_numeric($harga)) $errors[] = "Harga harus berupa angka numerik.";

    // 3. Cek Kode Barang Unik (kecuali barang ini)
    $stmt_check = $pdo->prepare("SELECT id FROM barang WHERE kode_barang = ? AND id != ?");
    $stmt_check->execute([$kode_barang, $id]);
    if ($stmt_check->rowCount() > 0) $errors[] = "Kode barang sudah digunakan!";

    // 4. Logika Unggah Gambar
    $nama_file_baru = $barang['gambar']; // Default pakai gambar lama
    if (empty($errors) && isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $file_name = $_FILES['gambar']['name'];
        $file_tmp = $_FILES['gambar']['tmp_name'];
        $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($file_type, $allowed_types)) {
            $errors[] = "Tipe file tidak diizinkan.";
        } else {
            // Hapus gambar lama jika ada
            if ($barang['gambar'] && file_exists('uploads/' . $barang['gambar'])) {
                unlink('uploads/' . $barang['gambar']);
            }
            $nama_file_baru = uniqid() . '_' . time() . '.' . $file_type;
            move_uploaded_file($file_tmp, 'uploads/' . $nama_file_baru);
        }
    }

    // 5. Update Database
    if (empty($errors)) {
        try {
            $sql = "UPDATE barang SET kode_barang = ?, nama_barang = ?, kategori = ?, jumlah = ?, harga = ?, deskripsi = ?, tanggal_masuk = ?, status = ?, gambar = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$kode_barang, $nama_barang, $kategori, $jumlah, $harga, $deskripsi, $tanggal_masuk, $status, $nama_file_baru, $id]);

            $_SESSION['pesan'] = "Barang berhasil diperbarui!";
            $_SESSION['tipe'] = "success";
            header("Location: index.php?page=data_barang");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Database Error: " . $e->getMessage();
        }
    }
}

$page_title = "Edit Barang";
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/menu.php'; ?>

<div class="main-wrapper">
    <header class="header">
        <div class="header-left" style="display: flex; align-items: center; gap: 16px;">
            <div class="mobile-toggle" id="mobile-toggle"><i class="fas fa-bars"></i></div>
            <nav class="breadcrumb">
                <span class="breadcrumb-item">Home</span>
                <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
                <a href="index.php?page=data_barang" class="breadcrumb-item">Data Barang</a>
                <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
                <span class="breadcrumb-item active">Edit</span>
            </nav>
        </div>
        <div class="header-right">
            <div class="user-info">
                <div class="user-avatar"><i class="fas fa-user"></i></div>
                <span class="user-name"><?php echo $_SESSION['username']; ?></span>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="content-inner">
            <div class="form-header-row">
                <a href="index.php?page=data_barang" class="btn-back-circle"><i class="fas fa-arrow-left"></i></a>
                <div class="page-title-block" style="margin-bottom: 0;">
                    <h2 style="font-size: 24px; font-weight: 700;">Edit Barang</h2>
                    <p style="font-size: 14px;">Perbarui data inventaris.</p>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="notification error">
                    <div class="notification-content">
                        <i class="fas fa-exclamation-circle"></i>
                        <ul style="margin: 0; padding-left: 20px;">
                            <?php foreach($errors as $err) echo "<li>$err</li>"; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="card" style="padding: 48px !important;">
                <form method="POST" enctype="multipart/form-data">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
                        <div>
                            <div class="form-group" style="margin-bottom: 24px;">
                                <label class="form-label-required">Nama Barang</label>
                                <input type="text" name="nama_barang" class="form-control-custom" value="<?php echo htmlspecialchars($_POST['nama_barang'] ?? $barang['nama_barang']); ?>" required>
                            </div>
                            <div class="form-group" style="margin-bottom: 24px;">
                                <label class="form-label-required">Kategori</label>
                                <select name="kategori" class="form-control-custom" required>
                                    <?php 
                                    $cats = ['Elektronik', 'Pakaian', 'Makanan', 'Minuman', 'Lainnya'];
                                    foreach($cats as $c) {
                                        $selected = (($_POST['kategori'] ?? $barang['kategori']) == $c) ? 'selected' : '';
                                        echo "<option value=\"$c\" $selected>$c</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom: 24px;">
                                <label class="form-label-required">Jumlah Stok</label>
                                <input type="number" name="jumlah" class="form-control-custom" value="<?php echo htmlspecialchars($_POST['jumlah'] ?? $barang['jumlah']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label-required">Tanggal Masuk</label>
                                <input type="date" name="tanggal_masuk" class="form-control-custom" value="<?php echo htmlspecialchars($_POST['tanggal_masuk'] ?? $barang['tanggal_masuk']); ?>" required>
                            </div>
                        </div>
                        
                        <div>
                            <div class="form-group" style="margin-bottom: 24px;">
                                <label class="form-label-required">Harga (Rp)</label>
                                <input type="number" name="harga" class="form-control-custom" step="0.01" value="<?php echo htmlspecialchars($_POST['harga'] ?? $barang['harga']); ?>" required>
                            </div>
                            <div class="form-group" style="margin-bottom: 24px;">
                                <label class="form-label-required">Kode Barang</label>
                                <input type="text" name="kode_barang" class="form-control-custom" value="<?php echo htmlspecialchars($_POST['kode_barang'] ?? $barang['kode_barang']); ?>" required>
                            </div>
                            <div class="form-group" style="margin-bottom: 24px;">
                                <label class="form-label-required">Status</label>
                                <select name="status" class="form-control-custom" required>
                                    <option value="aktif" <?php echo (($_POST['status'] ?? $barang['status']) == 'aktif') ? 'selected' : ''; ?>>Aktif</option>
                                    <option value="nonaktif" <?php echo (($_POST['status'] ?? $barang['status']) == 'nonaktif') ? 'selected' : ''; ?>>Nonaktif</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Ganti Gambar (Opsional)</label>
                                <input type="file" name="gambar" class="form-control-custom" accept="image/*">
                                <?php if($barang['gambar']): ?>
                                    <small>Gambar saat ini: <?php echo $barang['gambar']; ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-top: 24px;">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control-custom" rows="3"><?php echo htmlspecialchars($_POST['deskripsi'] ?? $barang['deskripsi']); ?></textarea>
                    </div>

                    <div class="form-actions-end">
                        <a href="index.php?page=data_barang" class="btn-batal">Batal</a>
                        <button type="submit" class="btn-simpan">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>
