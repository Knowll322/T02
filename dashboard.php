<?php
require_once 'config.php';

if(!is_logged_in()) {
    redirect('login.php');
}

$user = get_user_info($_SESSION['user_id']);

// Get statistics
$total_members = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'anggota'"))['total'];
$total_programs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM programs"))['total'];
$total_events = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM events"))['total'];

// Get total income and expense
$finance = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT 
        SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income,
        SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense
    FROM finance
"));

$total_income = $finance['income'] ?? 0;
$total_expense = $finance['expense'] ?? 0;
$balance = $total_income - $total_expense;

require_once 'header.php';
?>

<main class="container">
    <div class="dashboard">
        <div class="sidebar">
            <h3>Menu</h3>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active">📊 Dashboard</a></li>
                <?php if(get_role() == 'admin'): ?>
                    <li><a href="members.php">👥 Manajemen Anggota</a></li>
                    <li><a href="programs.php">📋 Program Kerja</a></li>
                    <li><a href="finance.php">💰 Keuangan</a></li>
                <?php endif; ?>
                <li><a href="events.php">📅 Jadwal Kegiatan</a></li>
                <li><a href="profile.php">⚙️ Profil</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="card welcome-card">
                <div class="card-body">
                    <h2>Selamat Datang, <?php echo $user['name']; ?>! 👋</h2>
                    <p>Terima kasih telah menjadi bagian dari Karang Taruna Bhakti Mandiri</p>
                    <p><strong>Role:</strong> <?php echo ucfirst($user['role']); ?> | 
                       <strong>Divisi:</strong> <?php echo $user['division'] ?? 'Belum ditentukan'; ?></p>
                </div>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $total_members; ?></div>
                    <div>Total Anggota</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $total_programs; ?></div>
                    <div>Program Aktif</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $total_events; ?></div>
                    <div>Kegiatan</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo format_rupiah($balance); ?></div>
                    <div>Saldo Kas</div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>Aktivitas Terbaru</h3>
                    <?php if(get_role() == 'admin'): ?>
                        <a href="events.php" class="btn btn-sm btn-info">Lihat Semua</a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kegiatan</th>
                                <th>Status</th>
                            </tr>
                            <?php
                            $recent_events = mysqli_query($conn, "
                                SELECT e.*, ep.status as participation_status 
                                FROM events e 
                                LEFT JOIN event_participants ep ON e.id = ep.event_id AND ep.user_id = {$_SESSION['user_id']}
                                ORDER BY e.event_date DESC 
                                LIMIT 5
                            ");
                            
                            while($event = mysqli_fetch_assoc($recent_events)):
                            ?>
                            <tr>
                                <td><?php echo date('d M Y', strtotime($event['event_date'])); ?></td>
                                <td><?php echo $event['title']; ?></td>
                                <td>
                                    <?php if($event['participation_status']): ?>
                                        <span class="status status-<?php echo $event['participation_status'] == 'going' ? 'info' : 'success'; ?>">
                                            <?php echo ucfirst($event['participation_status']); ?>
                                        </span>
                                    <?php else: ?>
                                        <a href="event_join.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-success">Ikuti</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'footer.php'; ?>