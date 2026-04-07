<?php
include 'koneksi.php';

// Hapus Cookie Remember Me
if (isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    mysqli_query($koneksi, "UPDATE users SET remember_token = NULL WHERE remember_token = '$token'");
    setcookie('remember_token', '', time() - 3600, "/");
}

// Hapus Session
session_destroy();

header("Location: login.php");
exit();
?>
