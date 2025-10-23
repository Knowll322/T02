<?php
require_once 'config.php';

if(!is_logged_in() || get_role() != 'admin') {
    redirect('dashboard.php');
}

// Handle delete
if(isset($_GET['delete'])) {
    $id = clean_input($_GET['delete']);
    mysqli_query($conn, "DELETE FROM finance WHERE id = $id");
    set_alert('success', 'Transaksi berhasil dihapus!');
    redirect('finance.php');
}

require_once 'header.php';
?>

<main class="container">
    <div class="dashboard">
        <div class="sidebar">
            <h3>Menu</h3>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php">📊 Dashboard</a></li>
                <li><a href="members.php">👥 Manajemen Anggota</a></li>
                <li><a href="programs.php">📋 Program Kerja</a></li>
                <li><a href="finance.php" class="active">💰 Keuangan</a></li>
                <li><a href="events.php">📅 Jadwal Kegiatan</a></li>
                <li><a href="profile.php">⚙️ Profil</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="card">
                <div class="card-header">
                    <h3>Laporan Keuangan</h3>
                    <a href="finance_add.php" class="btn btn-success">➕ Tambah Transaksi</a>
                </div>
                <div class="card-body">
                    <?php $alert = get_alert(); if($alert): ?>
                        <div class="alert alert-<?php echo $alert['type']; ?>">
                            <?php echo $alert['message']; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Summary -->
                    <div class="stats-grid">
                        <?php
                        $summary = mysqli_fetch_assoc(mysqli_query($conn, "
                            SELECT 
                                SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income,
                                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense
                            FROM finance
                        "));
                        ?>
                        <div class="stat-card">
                            <div class="stat-value"><?php echo format_rupiah($summary['income']); ?></div>
                            <div>Total Pemasukan</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?php echo format_rupiah($summary['expense']); ?></div>
                            <div>Total Pengeluaran</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?php echo format_rupiah($summary['income'] - $summary['expense']); ?></div>
                            <div>Saldo</div>
                        </div>
                    </div>
                    
                    <!-- Filter -->
                    <form method="GET" style="margin: 1rem 0;">
                        <input type="date" name="start_date" value="<?php echo $_GET['start_date'] ?? ''; ?>">
                        <input type="date" name="end_date" value="<?php echo $_GET['end_date'] ?? ''; ?>">
                        <select name="type">
                            <option value="">Semua</option>
                            <option value="income" <?php echo ($_GET['type'] ?? '') == 'income' ? 'selected' : ''; ?>>Pemasukan</option>
                            <option value="expense" <?php echo ($_GET['type'] ?? '') == 'expense' ? 'selected' : ''; ?>>Pengeluaran</option>
                        </select>
                        <button type="submit" class="btn btn-sm">Filter</button>
                    </form>
                    
                    <div class="table-responsive">
                        <table>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Kategori</th>
                                <th>Keterangan</th>
                                <th>Jumlah</th>
                                <th>Aksi</th>
                            </tr>
                            <?php
                            $where = [];
                            if(isset($_GET['start_date']) && $_GET['start_date']) {
                                $where[] = "transaction_date >= '{$_GET['start_date']}'";
                            }
                            if(isset($_GET['end_date']) && $_GET['end_date']) {
                                $where[] = "transaction_date <= '{$_GET['end_date']}'";
                            }
                            if(isset($_GET['type']) && $_GET['type']) {
                                $where[] = "type = '{$_GET['type']}'";
                            }
                            
                            $where_clause = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";
                            
                            $transactions = mysqli_query($conn, "SELECT * FROM finance $where_clause ORDER BY transaction_date DESC");
                            
                            while($trans = mysqli_fetch_assoc($transactions)):
                            ?>
                            <tr>
                                <td><?php echo date('d M Y', strtotime($trans['transaction_date'])); ?></td>
                                <td>
                                    <span class="status status-<?php echo $trans['type'] == 'income' ? 'success' : 'danger'; ?>">
                                        <?php echo ucfirst($trans['type']); ?>
                                    </span>
                                </td>
                                <td><?php echo $trans['category']; ?></td>
                                <td><?php echo $trans['description']; ?></td>
                                <td style="font-weight: bold; color: <?php echo $trans['type'] == 'income' ? 'var(--success)' : 'var(--danger)'; ?>">
                                    <?php echo format_rupiah($trans['amount']); ?>
                                </td>
                                <td>
                                    <a href="finance_edit.php?id=<?php echo $trans['id']; ?>" class="btn btn-sm">Edit</a>
                                    <a href="finance.php?delete=<?php echo $trans['id']; ?>" 
                                       onclick="return confirm('Yakin ingin menghapus transaksi ini?')" 
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