<?php
include 'koneksi.php';

// Hapus Cookie Remember Me
if (isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL WHERE remember_token = ?");
    $stmt->execute([$token]);
    setcookie('remember_token', '', time() - 3600, "/");
}

// Hapus Session
session_destroy();

header("Location: login.php");
exit();
?>
