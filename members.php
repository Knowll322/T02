<?php
require_once 'config.php';

if(!is_logged_in() || get_role() != 'admin') {
    redirect('dashboard.php');
}

// Handle delete
if(isset($_GET['delete'])) {
    $id = clean_input($_GET['delete']);
    mysqli_query($conn, "DELETE FROM users WHERE id = $id");
    set_alert('success', 'Anggota berhasil dihapus!');
    redirect('members.php');
}

// Handle search
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';

require_once 'header.php';
?>

<main class="container">
    <div class="dashboard">
        <div class="sidebar">
            <h3>Menu</h3>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php">📊 Dashboard</a></li>
                <li><a href="members.php" class="active">👥 Manajemen Anggota</a></li>
                <li><a href="programs.php">📋 Program Kerja</a></li>
                <li><a href="finance.php">💰 Keuangan</a></li>
                <li><a href="events.php">📅 Jadwal Kegiatan</a></li>
                <li><a href="profile.php">⚙️ Profil</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="card">
                <div class="card-header">
                    <h3>Manajemen Anggota</h3>
                    <a href="member_add.php" class="btn btn-success">➕ Tambah Anggota</a>
                </div>
                <div class="card-body">
                    <?php $alert = get_alert(); if($alert): ?>
                        <div class="alert alert-<?php echo $alert['type']; ?>">
                            <?php echo $alert['message']; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="GET" style="margin-bottom: 1rem;">
                        <input type="text" name="search" placeholder="Cari anggota..." value="<?php echo $search; ?>">
                        <button type="submit" class="btn btn-sm">Cari</button>
                    </form>
                    
                    <div class="table-responsive">
                        <table>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Telepon</th>
                                <th>Divisi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                            <?php
                            $where = $search ? "WHERE (name LIKE '%$search%' OR email LIKE '%$search%')" : "";
                            $members = mysqli_query($conn, "SELECT * FROM users $where ORDER BY id DESC");
                            
                            while($member = mysqli_fetch_assoc($members)):
                            ?>
                            <tr>
                                <td><?php echo $member['id']; ?></td>
                                <td><?php echo $member['name']; ?></td>
                                <td><?php echo $member['email']; ?></td>
                                <td><?php echo $member['phone']; ?></td>
                                <td><?php echo $member['division'] ?? '-'; ?></td>
                                <td>
                                    <span class="status status-<?php echo $member['status'] == 'active' ? 'success' : 'danger'; ?>">
                                        <?php echo ucfirst($member['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="member_edit.php?id=<?php echo $member['id']; ?>" class="btn btn-sm">Edit</a>
                                    <a href="members.php?delete=<?php echo $member['id']; ?>" 
                                       onclick="return confirm('Yakin ingin menghapus anggota ini?')" 
                                       class="btn btn-sm" style="background: var(--danger);">Hapus</a>
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