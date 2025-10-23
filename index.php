<?php
require_once 'config.php';
require_once 'header.php';
?>

<main>
    <section class="hero">
        <div class="hero-content">
            <h1>Karang Taruna Bhakti Mandiri</h1>
            <p>Membangun Generasi Muda yang Berkarakter untuk Indonesia Maju</p>
            <?php if(!is_logged_in()): ?>
                <a href="login.php" class="btn">Masuk</a>
                <a href="register.php" class="btn btn-secondary">Daftar</a>
            <?php else: ?>
                <a href="dashboard.php" class="btn">Dashboard</a>
            <?php endif; ?>
        </div>
    </section>

    <section class="container">
        <h2 style="text-align: center; margin-bottom: 2rem;">Fitur Utama</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>👥 Manajemen Anggota</h3>
                <p>Kelola data anggota dengan mudah dan efisien</p>
            </div>
            <div class="stat-card">
                <h3>📋 Program Kerja</h3>
                <p>Atur dan monitor setiap program kerja organisasi</p>
            </div>
            <div class="stat-card">
                <h3>💰 Keuangan</h3>
                <p>Sistem keuangan yang transparan dan terpercaya</p>
            </div>
            <div class="stat-card">
                <h3>📅 Jadwal Kegiatan</h3>
                <p>Kelola jadwal kegiatan dan partisipasi anggota</p>
            </div>
        </div>
    </section>
</main>

<?php require_once 'footer.php'; ?>