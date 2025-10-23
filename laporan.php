<?php
require_once 'config.php';

if(!is_logged_in()) {
    redirect('login.php');
}

// Handle filter
$report_type = isset($_GET['type']) ? clean_input($_GET['type']) : 'monthly';
$period = isset($_GET['period']) ? clean_input($_GET['period']) : date('Y-m');
$division = isset($_GET['division']) ? clean_input($_GET['division']) : '';

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
                <li><a href="events.php">📅 Jadwal Kegiatan</a></li>
                <li><a href="laporan.php" class="active">📝 Laporan</a></li>
                <li><a href="achievements.php">🏆 Prestasi</a></li>
                <li><a href="profile.php">⚙️ Profil</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="card">
                <div class="card-header">
                    <h3>Generate Laporan</h3>
                </div>
                <div class="card-body">
                    <?php $alert = get_alert(); if($alert): ?>
                        <div class="alert alert-<?php echo $alert['type']; ?>">
                            <?php echo $alert['message']; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Filter Form -->
                    <form method="GET" style="margin-bottom: 2rem;">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="type">Jenis Laporan</label>
                                <select id="type" name="type" onchange="this.form.submit()">
                                    <option value="monthly" <?php echo $report_type == 'monthly' ? 'selected' : ''; ?>>Laporan Bulanan</option>
                                    <option value="program" <?php echo $report_type == 'program' ? 'selected' : ''; ?>>Laporan Program</option>
                                    <option value="financial" <?php echo $report_type == 'financial' ? 'selected' : ''; ?>>Laporan Keuangan</option>
                                    <option value="members" <?php echo $report_type == 'members' ? 'selected' : ''; ?>>Laporan Anggota</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="period">Periode</label>
                                <?php if($report_type == 'monthly'): ?>
                                    <input type="month" id="period" name="period" value="<?php echo $period; ?>" onchange="this.form.submit()">
                                <?php else: ?>
                                    <select id="period" name="period" onchange="this.form.submit()">
                                        <option value="">Pilih Periode</option>
                                        <?php
                                        if($report_type == 'program') {
                                            $programs = mysqli_query($conn, "SELECT DISTINCT DATE_FORMAT(start_date, '%Y-%m') as period FROM programs ORDER BY period DESC");
                                            while($p = mysqli_fetch_assoc($programs)) {
                                                $selected = $period == $p['period'] ? 'selected' : '';
                                                echo "<option value='{$p['period']}' $selected>" . date('F Y', strtotime($p['period'] . '-01')) . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                <?php endif; ?>
                            </div>
                            
                            <?php if(get_role() == 'admin'): ?>
                                <div class="form-group">
                                    <label for="division">Divisi</label>
                                    <select id="division" name="division" onchange="this.form.submit()">
                                        <option value="">Semua Divisi</option>
                                        <option value="Pendidikan" <?php echo $division == 'Pendidikan' ? 'selected' : ''; ?>>Pendidikan</option>
                                        <option value="Kesehatan" <?php echo $division == 'Kesehatan' ? 'selected' : ''; ?>>Kesehatan</option>
                                        <option value="Lingkungan" <?php echo $division == 'Lingkungan' ? 'selected' : ''; ?>>Lingkungan</option>
                                        <option value="Seni & Budaya" <?php echo $division == 'Seni & Budaya' ? 'selected' : ''; ?>>Seni & Budaya</option>
                                        <option value="Olahraga" <?php echo $division == 'Olahraga' ? 'selected' : ''; ?>>Olahraga</option>
                                        <option value="Kewirausahaan" <?php echo $division == 'Kewirausahaan' ? 'selected' : ''; ?>>Kewirausahaan</option>
                                    </select>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div style="display: flex; gap: 1rem;">
                            <button type="submit" class="btn btn-primary">Tampilkan Laporan</button>
                            <button type="button" class="btn btn-success" onclick="window.print()">🖨️ Cetak</button>
                            <a href="laporan_export.php?type=<?php echo $report_type ?>&period=<?php echo $period ?>&division=<?php echo $division ?>" class="btn btn-info">📥 Export Excel</a>
                        </div>
                    </form>
                    
                    <!-- Report Content -->
                    <?php
                    if($report_type == 'monthly') {
                        // Laporan Bulanan
                        $year = substr($period, 0, 4);
                        $month = substr($period, 5, 2);
                        
                        // Statistik Bulanan
                        $stats = mysqli_fetch_assoc(mysqli_query($conn, "
                            SELECT 
                                COUNT(DISTINCT u.id) as total_members,
                                COUNT(DISTINCT p.id) as total_programs,
                                COUNT(DISTINCT e.id) as total_events
                            FROM users u
                            LEFT JOIN programs p ON YEAR(p.start_date) = $year AND MONTH(p.start_date) = $month
                            LEFT JOIN events e ON YEAR(e.event_date) = $year AND MONTH(e.event_date) = $month
                            WHERE u.role = 'anggota'
                        "));
                        
                        // Keuangan Bulanan
                        $finance = mysqli_fetch_assoc(mysqli_query($conn, "
                            SELECT 
                                SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income,
                                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense
                            FROM finance 
                            WHERE YEAR(transaction_date) = $year AND MONTH(transaction_date) = $month
                        "));
                        
                        echo '<h4>Laporan Bulanan - ' . date('F Y', strtotime($period . '-01')) . '</h4>';
                        ?>
                        
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-value"><?php echo $stats['total_members']; ?></div>
                                <div>Total Anggota</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value"><?php echo $stats['total_programs']; ?></div>
                                <div>Program Aktif</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value"><?php echo $stats['total_events']; ?></div>
                                <div>Kegiatan</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value"><?php echo format_rupiah($finance['income'] - $finance['expense']); ?></div>
                                <div>Saldo Bulan Ini</div>
                            </div>
                        </div>
                        
                        <h5 style="margin-top: 2rem;">Detail Keuangan</h5>
                        <div class="table-responsive">
                            <table>
                                <tr>
                                    <th>Pemasukan</th>
                                    <th>Pengeluaran</th>
                                    <th>Saldo</th>
                                </tr>
                                <tr>
                                    <td style="color: var(--success); font-weight: bold;"><?php echo format_rupiah($finance['income']); ?></td>
                                    <td style="color: var(--danger); font-weight: bold;"><?php echo format_rupiah($finance['expense']); ?></td>
                                    <td style="color: var(--primary); font-weight: bold;"><?php echo format_rupiah($finance['income'] - $finance['expense']); ?></td>
                                </tr>
                            </table>
                        </div>
                        
                    <?php } elseif($report_type == 'program') {
                        // Laporan Program
                        echo '<h4>Laporan Program Periode ' . date('F Y', strtotime($period . '-01')) . '</h4>';
                        ?>
                        
                        <div class="table-responsive">
                            <table>
                                <tr>
                                    <th>Nama Program</th>
                                    <th>Divisi</th>
                                    <th>Target</th>
                                    <th>Realisasi</th>
                                    <th>Persentase</th>
                                    <th>Anggaran</th>
                                    <th>Status</th>
                                </tr>
                                <?php
                                $where = [];
                                if($period) {
                                    $where[] = "DATE_FORMAT(start_date, '%Y-%m') = '$period'";
                                }
                                if($division) {
                                    $where[] = "category = '$division'";
                                }
                                $where_clause = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";
                                
                                $programs = mysqli_query($conn, "
                                    SELECT p.*, COUNT(pp.id) as participants 
                                    FROM programs p 
                                    LEFT JOIN program_participants pp ON p.id = pp.program_id 
                                    $where_clause
                                    GROUP BY p.id
                                ");
                                
                                while($program = mysqli_fetch_assoc($programs)):
                                $percentage = $program['target_participants'] > 0 ? 
                                    round(($program['participants'] / $program['target_participants']) * 100, 1) : 0;
                                ?>
                                <tr>
                                    <td><?php echo $program['name']; ?></td>
                                    <td><?php echo $program['category']; ?></td>
                                    <td><?php echo $program['target_participants']; ?></td>
                                    <td><?php echo $program['participants']; ?></td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: <?php echo $percentage; ?>%"></div>
                                        </div>
                                        <?php echo $percentage; ?>%
                                    </td>
                                    <td><?php echo format_rupiah($program['budget']); ?></td>
                                    <td>
                                        <span class="status status-<?php 
                                            echo $program['status'] == 'completed' ? 'success' : 
                                                 ($program['status'] == 'ongoing' ? 'warning' : 'info'); ?>">
                                            <?php echo ucfirst($program['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </table>
                        </div>
                        
                    <?php } elseif($report_type == 'financial') {
                        // Laporan Keuangan
                        echo '<h4>Laporan Keuangan Periode ' . date('F Y', strtotime($period . '-01')) . '</h4>';
                        ?>
                        
                        <div class="stats-grid">
                            <?php
                            $finance_summary = mysqli_fetch_assoc(mysqli_query($conn, "
                                SELECT 
                                    SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income,
                                    SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense
                                FROM finance 
                                WHERE DATE_FORMAT(transaction_date, '%Y-%m') = '$period'
                            "));
                            ?>
                            <div class="stat-card">
                                <div class="stat-value"><?php echo format_rupiah($finance_summary['income']); ?></div>
                                <div>Total Pemasukan</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value"><?php echo format_rupiah($finance_summary['expense']); ?></div>
                                <div>Total Pengeluaran</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value"><?php echo format_rupiah($finance_summary['income'] - $finance_summary['expense']); ?></div>
                                <div>Saldo Bersih</div>
                            </div>
                        </div>
                        
                        <h5 style="margin-top: 2rem;">Detail Transaksi</h5>
                        <div class="table-responsive">
                            <table>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Kategori</th>
                                    <th>Keterangan</th>
                                    <th>Jumlah</th>
                                </tr>
                                <?php
                                $transactions = mysqli_query($conn, "
                                    SELECT * FROM finance 
                                    WHERE DATE_FORMAT(transaction_date, '%Y-%m') = '$period'
                                    ORDER BY transaction_date DESC
                                ");
                                
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
                                </tr>
                                <?php endwhile; ?>
                            </table>
                        </div>
                        
                    <?php } elseif($report_type == 'members') {
                        // Laporan Anggota
                        echo '<h4>Laporan Keanggotaan Periode ' . date('F Y', strtotime($period . '-01')) . '</h4>';
                        ?>
                        
                        <div class="stats-grid">
                            <?php
                            $member_stats = mysqli_fetch_assoc(mysqli_query($conn, "
                                SELECT 
                                    COUNT(*) as total_members,
                                    COUNT(CASE WHEN division = 'Pendidikan' THEN 1 END) as pendidikan,
                                    COUNT(CASE WHEN division = 'Kesehatan' THEN 1 END) as kesehatan,
                                    COUNT(CASE WHEN division = 'Lingkungan' THEN 1 END) as lingkungan,
                                    COUNT(CASE WHEN division = 'Seni & Budaya' THEN 1 END) as seni,
                                    COUNT(CASE WHEN division = 'Olahraga' THEN 1 END) as olahraga,
                                    COUNT(CASE WHEN division = 'Kewirausahaan' THEN 1 END) as kewirausahaan
                                FROM users 
                                WHERE role = 'anggota'
                            "));
                            ?>
                            <div class="stat-card">
                                <div class="stat-value"><?php echo $member_stats['total_members']; ?></div>
                                <div>Total Anggota</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value"><?php echo $member_stats['pendidikan']; ?></div>
                                <div>Pendidikan</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value"><?php echo $member_stats['kesehatan']; ?></div>
                                <div>Kesehatan</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value"><?php echo $member_stats['lingkungan']; ?></div>
                                <div>Lingkungan</div>
                            </div>
                        </div>
                        
                        <h5 style="margin-top: 2rem;">Daftar Anggota per Divisi</h5>
                        <div class="table-responsive">
                            <table>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Telepon</th>
                                    <th>Divisi</th>
                                    <th>Tanggal Bergabung</th>
                                </tr>
                                <?php
                                $where = $division ? "WHERE division = '$division'" : "";
                                $members = mysqli_query($conn, "
                                    SELECT name, email, phone, division, join_date 
                                    FROM users 
                                    WHERE role = 'anggota' $where
                                    ORDER BY division, name
                                ");
                                
                                while($member = mysqli_fetch_assoc($members)):
                                ?>
                                <tr>
                                    <td><?php echo $member['name']; ?></td>
                                    <td><?php echo $member['email']; ?></td>
                                    <td><?php echo $member['phone']; ?></td>
                                    <td><?php echo $member['division']; ?></td>
                                    <td><?php echo date('d M Y', strtotime($member['join_date'])); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </table>
                        </div>
                        
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'footer.php'; ?>