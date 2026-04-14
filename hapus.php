<?php
include 'koneksi.php';

// Proteksi Halaman
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    try {
        // 1. Ambil nama file gambar sebelum dihapus
        $stmt_get = $pdo->prepare("SELECT gambar FROM barang WHERE id = ?");
        $stmt_get->execute([$id]);
        $barang = $stmt_get->fetch();

        if ($barang) {
            // 2. Hapus file gambar fisik jika ada
            if ($barang['gambar'] && file_exists('uploads/' . $barang['gambar'])) {
                unlink('uploads/' . $barang['gambar']);
            }

            // 3. Hapus data dari database (Prepared Statement)
            $stmt_del = $pdo->prepare("DELETE FROM barang WHERE id = ?");
            if ($stmt_del->execute([$id])) {
                $_SESSION['pesan'] = "Barang berhasil dihapus!";
                $_SESSION['tipe'] = "success";
            }
        } else {
            $_SESSION['pesan'] = "Barang tidak ditemukan!";
            $_SESSION['tipe'] = "error";
        }
    } catch (PDOException $e) {
        $_SESSION['pesan'] = "Gagal menghapus barang: " . $e->getMessage();
        $_SESSION['tipe'] = "error";
    }
}

header("Location: index.php?page=data_barang");
exit();
?>
