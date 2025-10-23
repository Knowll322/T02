<?php
require_once 'config.php';

if(!is_logged_in()) {
    redirect('login.php');
}

// Handle delete
if(isset($_GET['delete']) && get_role() == 'admin') {
    $id = clean_input($_GET['delete']);
    mysqli_query($conn, "DELETE FROM programs WHERE id = $id");
    set_alert('success', 'Program berhasil dihapus!');
    redirect('programs.php');
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
                <?php endif; ?>
                <li><a href="programs.php" class="active">📋 Program Kerja</a></li>
                <?php if(get_role() == 'admin'): ?>
                    <li><a href="finance.php">💰 Keuangan</a></li>
                <?php endif; ?>
                <li><a href="events.php">📅 Jadwal Kegiatan</a></li>
                <li><a href="profile.php">⚙️ Profil</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="card">
                <div class="card-header">
                    <h3>Program Kerja</h3>
                    <?php if(get_role() == 'admin'): ?>
                        <a href="program_add.php" class="btn btn-success">➕ Tambah Program</a>
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
                                <th>Nama Program</th>
                                <th>Kategori</th>
                                <th>Periode</th>
                                <th>Target</th>
                                <th>Anggaran</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                            <?php
                            $programs = mysqli_query($conn, "
                                SELECT p.*, COUNT(pp.id) as participants 
                                FROM programs p 
                                LEFT JOIN program_participants pp ON p.id = pp.program_id 
                                GROUP BY p.id 
                                ORDER BY p.created_at DESC
                            ");
                            
                            while($program = mysqli_fetch_assoc($programs)):
                            ?>
                            <tr>
                                <td><?php echo $program['name']; ?></td>
                                <td><?php echo $program['category']; ?></td>
                                <td>
                                    <?php echo date('d M Y', strtotime($program['start_date'])); ?> - 
                                    <?php echo date('d M Y', strtotime($program['end_date'])); ?>
                                </td>
                                <td><?php echo $program['target_participants']; ?> Orang</td>
                                <td><?php echo format_rupiah($program['budget']); ?></td>
                                <td>
                                    <span class="status status-<?php 
                                        echo $program['status'] == 'completed' ? 'success' : 
                                             ($program['status'] == 'ongoing' ? 'warning' : 'info'); ?>">
                                        <?php echo ucfirst($program['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="program_view.php?id=<?php echo $program['id']; ?>" class="btn btn-sm">Detail</a>
                                    <?php if(get_role() == 'admin'): ?>
                                        <a href="program_edit.php?id=<?php echo $program['id']; ?>" class="btn btn-sm">Edit</a>
                                    <?php else: ?>
                                        <?php
                                        $joined = mysqli_num_rows(mysqli_query($conn, 
                                            "SELECT * FROM program_participants WHERE program_id = {$program['id']} AND user_id = {$_SESSION['user_id']}"));
                                        if(!$joined && $program['status'] != 'completed'):
                                        ?>
                                            <a href="program_join.php?id=<?php echo $program['id']; ?>" class="btn btn-sm btn-success">Gabung</a>
                                        <?php endif; ?>
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