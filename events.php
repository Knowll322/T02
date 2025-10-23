<?php
require_once 'config.php';

if(!is_logged_in()) {
    redirect('login.php');
}

// Handle delete
if(isset($_GET['delete']) && get_role() == 'admin') {
    $id = clean_input($_GET['delete']);
    mysqli_query($conn, "DELETE FROM events WHERE id = $id");
    set_alert('success', 'Kegiatan berhasil dihapus!');
    redirect('events.php');
}

require_once 'header.php';
?>

<main class="container">
    <div class="dashboard">
        <div class="sidebar">
            <h3>Menu</h3>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php">📊 Dashboard</a></li>
                <?php if(get_role() == 'admin'): ?>
                    <li><a href="members.php">👥 Manajemen Anggota</a></li>
                    <li><a href="programs.php">📋 Program Kerja</a></li>
                    <li><a href="finance.php">💰 Keuangan</a></li>
                <?php endif; ?>
                <li><a href="events.php" class="active">📅 Jadwal Kegiatan</a></li>
                <li><a href="profile.php">⚙️ Profil</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="card">
                <div class="card-header">
                    <h3>Jadwal Kegiatan</h3>
                    <?php if(get_role() == 'admin'): ?>
                        <a href="event_add.php" class="btn btn-success">➕ Tambah Kegiatan</a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php $alert = get_alert(); if($alert): ?>
                        <div class="alert alert-<?php echo $alert['type']; ?>">
                            <?php echo $alert['message']; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="table-responsive">
                        <table>
                            <tr>
                                <th>Tanggal & Waktu</th>
                                <th>Nama Kegiatan</th>
                                <th>Lokasi</th>
                                <th>Peserta</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                            <?php
                            $events = mysqli_query($conn, "
                                SELECT e.*, COUNT(ep.id) as total_participants 
                                FROM events e 
                                LEFT JOIN event_participants ep ON e.id = ep.event_id 
                                GROUP BY e.id 
                                ORDER BY e.event_date ASC
                            ");
                            
                            while($event = mysqli_fetch_assoc($events)):
                            $is_joined = mysqli_num_rows(mysqli_query($conn, 
                                "SELECT * FROM event_participants WHERE event_id = {$event['id']} AND user_id = {$_SESSION['user_id']}"));
                            ?>
                            <tr>
                                <td>
                                    <?php echo date('d M Y H:i', strtotime($event['event_date'])); ?>
                                </td>
                                <td><?php echo $event['title']; ?></td>
                                <td><?php echo $event['location']; ?></td>
                                <td>
                                    <?php echo $event['total_participants']; ?> / <?php echo $event['participants_limit']; ?>
                                </td>
                                <td>
                                    <?php if($event['event_date'] < date('Y-m-d H:i:s')): ?>
                                        <span class="status status-success">Selesai</span>
                                    <?php elseif($is_joined): ?>
                                        <span class="status status-info">Akan Hadir</span>
                                    <?php else: ?>
                                        <span class="status status-warning">Belum Bergabung</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="event_view.php?id=<?php echo $event['id']; ?>" class="btn btn-sm">Detail</a>
                                    <?php if(get_role() == 'admin'): ?>
                                        <a href="event_edit.php?id=<?php echo $event['id']; ?>" class="btn btn-sm">Edit</a>
                                    <?php elseif(!$is_joined && $event['event_date'] > date('Y-m-d H:i:s')): ?>
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
