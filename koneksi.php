<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Koneksi ke database..........................................
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "gudang";

    $koneksi = mysqli_connect($host, $username, $password, $database);

    // Cek koneksi..............................................
    if (!$koneksi) {
        die("Koneksi database gagal: " . mysqli_connect_error());
    }
?>