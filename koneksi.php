<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "gudang";

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        // Set error mode ke exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Set default fetch mode ke associative array
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Koneksi database gagal: " . $e->getMessage());
    }
?>
