<?php
include 'koneksi.php';

// Proteksi Halaman
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$errors = [];
$old_data = $_POST; // Simpan data lama untuk ditampilkan kembali jika validasi gagal

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_barang = trim($_POST['nama_barang']);
    $kode_barang = trim($_POST['kode_barang']);
    $kategori = trim($_POST['kategori']);
    $jumlah = $_POST['jumlah'];
    $harga = $_POST['harga'];
    $deskripsi = trim($_POST['deskripsi']);
    $tanggal_masuk = $_POST['tanggal_masuk'];

    // 1. Validasi Server Side
    if (empty($nama_barang)) {
        $errors[] = "Nama barang tidak boleh kosong.";
    }
    if (!is_numeric($jumlah)) {
        $errors[] = "Jumlah harus berupa angka numerik.";
    }
    if (!is_numeric($harga)) {
        $errors[] = "Harga harus berupa angka numerik.";
    }

    // 2. Cek Kode Barang Unik (Prepared Statement)
    $stmt_check = $pdo->prepare("SELECT id FROM barang WHERE kode_barang = ?");
    $stmt_check->execute([$kode_barang]);
    if ($stmt_check->rowCount() > 0) {
        $errors[] = "Kode barang sudah digunakan!";
    }

    // 3. Logika Unggah Gambar
    $nama_file_baru = null;
    if (empty($errors) && isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $file_name = $_FILES['gambar']['name'];
        $file_size = $_FILES['gambar']['size'];
        $file_tmp = $_FILES['gambar']['tmp_name'];
        $file_type = pathinfo($file_name, PATHINFO_EXTENSION);
        $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array(strtolower($file_type), $allowed_types)) {
            $errors[] = "Tipe file tidak diizinkan. Hanya JPG, PNG, WEBP.";
        } elseif ($file_size > 2000000) { // 2MB
            $errors[] = "Ukuran file terlalu besar (Maks 2MB).";
        } else {
            $nama_file_baru = uniqid() . '_' . time() . '.' . $file_type;
            $upload_path = 'uploads/' . $nama_file_baru;
            if (!move_uploaded_file($file_tmp, $upload_path)) {
                $errors[] = "Gagal mengunggah gambar.";
            }
        }
    }

    // 4. Simpan ke Database jika tidak ada error
    if (empty($errors)) {
        try {
            $sql = "INSERT INTO barang (kode_barang, nama_barang, kategori, jumlah, harga, deskripsi, tanggal_masuk, status, gambar, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'aktif', ?, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$kode_barang, $nama_barang, $kategori, $jumlah, $harga, $deskripsi, $tanggal_masuk, $nama_file_baru]);

            $_SESSION['pesan'] = "Barang berhasil ditambahkan!";
            $_SESSION['tipe'] = "success";
            header("Location: index.php?page=data_barang");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Database Error: " . $e->getMessage();
        }
    }
}

$page_title = "Tambah Barang";
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
                <div class="user-avatar"><i class="fas fa-user"></i></div>
                <span class="user-name">Selamat datang, <?php echo $_SESSION['username']; ?></span>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="content-inner">
            <div class="form-header-row">
                <a href="index.php?page=data_barang" class="btn-back-circle"><i class="fas fa-arrow-left"></i></a>
                <div class="page-title-block" style="margin-bottom: 0;">
                    <h2 style="font-size: 24px; font-weight: 700;">Tambah Barang</h2>
                    <p style="font-size: 14px;">Buat data barang baru untuk stok gudang.</p>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="notification error">
                    <div class="notification-content">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            <strong>Terjadi kesalahan:</strong>
                            <ul style="margin-top: 5px; list-style: disc; padding-left: 20px;">
                                <?php foreach($errors as $err) echo "<li>$err</li>"; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="card" style="padding: 48px !important;">
                <form method="POST" enctype="multipart/form-data">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
                        <!-- Column 1 -->
                        <div>
                            <div class="form-group" style="margin-bottom: 24px;">
                                <label class="form-label-required">Nama Barang</label>
                                <input type="text" name="nama_barang" class="form-control-custom" placeholder="Contoh: Kipas Angin" value="<?php echo htmlspecialchars($old_data['nama_barang'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="form-group" style="margin-bottom: 24px;">
                                <label class="form-label-required">Kategori</label>
                                <select name="kategori" class="form-control-custom" required>
                                    <option value="">Pilih Kategori</option>
                                    <?php 
                                    $cats = ['Elektronik', 'Pakaian', 'Makanan', 'Minuman', 'Lainnya'];
                                    foreach($cats as $c) {
                                        $selected = (isset($old_data['kategori']) && $old_data['kategori'] == $c) ? 'selected' : '';
                                        echo "<option value=\"$c\" $selected>$c</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="form-group" style="margin-bottom: 24px;">
                                <label class="form-label-required">Jumlah Stok</label>
                                <input type="number" name="jumlah" class="form-control-custom" placeholder="0" min="0" value="<?php echo htmlspecialchars($old_data['jumlah'] ?? ''); ?>" required>
                            </div>

                            <div class="form-group" style="margin-bottom: 24px;">
                                <label class="form-label-required">Tanggal Masuk</label>
                                <input type="date" name="tanggal_masuk" class="form-control-custom" value="<?php echo htmlspecialchars($old_data['tanggal_masuk'] ?? date('Y-m-d')); ?>" required>
                            </div>
                        </div>
                        
                        <!-- Column 2 -->
                        <div>
                            <div class="form-group" style="margin-bottom: 24px;">
                                <label class="form-label-required">Harga (Rp)</label>
                                <input type="number" name="harga" class="form-control-custom" placeholder="Contoh: 100.000" min="0" step="0.01" value="<?php echo htmlspecialchars($old_data['harga'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="form-group" style="margin-bottom: 24px;">
                                <label class="form-label-required">Kode Barang</label>
                                <input type="text" name="kode_barang" class="form-control-custom" placeholder="BRG001" value="<?php echo htmlspecialchars($old_data['kode_barang'] ?? ''); ?>" required>
                            </div>

                            <div class="form-group" style="margin-bottom: 24px;">
                                <label class="form-label-required">Upload Gambar</label>
                                <input type="file" name="gambar" class="form-control-custom" accept="image/*">
                                <small style="color: var(--text-muted);">Format: JPG, PNG, WEBP. Maks 2MB.</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-top: 8px;">
                        <label class="form-label-required">Deskripsi Barang</label>
                        <textarea name="deskripsi" class="form-control-custom" rows="3" style="resize: none;" placeholder="Isi deskripsi barang..."><?php echo htmlspecialchars($old_data['deskripsi'] ?? ''); ?></textarea>
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
