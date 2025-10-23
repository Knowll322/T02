<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karang Taruna Bhakti Mandiri</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <nav class="container">
        <div class="logo">Karang Taruna Bhakti Mandiri</div>
        <ul class="nav-links">
            <li><a href="index.php">Beranda</a></li>
            <?php if(is_logged_in()): ?>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="logout.php">Keluar</a></li>
            <?php else: ?>
                <li><a href="login.php">Masuk</a></li>
                <li><a href="register.php">Daftar</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>