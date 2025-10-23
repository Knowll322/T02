<?php
require_once 'config.php';

if(!is_logged_in() || get_role() != 'admin') {
    redirect('dashboard.php');
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $transaction_date = clean_input($_POST['transaction_date']);
    $type = clean_input($_POST['type']);
    $category = clean_input($_POST['category']);
    $description = clean_input($_POST['description']);
    $amount = clean_input($_POST['amount']);
    $program_id = !empty($_POST['program_id']) ? clean_input($_POST['program_id']) : 'NULL';
    
    $sql = "INSERT INTO finance (transaction_date, type, category, description, amount, program_id, created_by) 
            VALUES ('$transaction_date', '$type', '$category', '$description', $amount, $program_id, {$_SESSION['user_id']})";
    
    if(mysqli_query($conn, $sql)) {
        set_alert('success', 'Transaksi berhasil ditambahkan!');
        redirect('finance.php');
    } else {
        set_alert('danger', 'Gagal menambahkan transaksi!');
    }
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
                <li><a href="achievements.php">🏆 Prestasi</a></li>
                <li><a href="profile.php">⚙️ Profil</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="card">
                <div class="card-header">
                    <h3>Tambah Transaksi Keuangan</h3>
                </div>
                <div class="card-body">
                    <?php $alert = get_alert(); if($alert): ?>
                        <div class="alert alert-<?php echo $alert['type']; ?>">
                            <?php echo $alert['message']; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="transaction_date">Tanggal Transaksi</label>
                                <input type="date" id="transaction_date" name="transaction_date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="type">Jenis Transaksi</label>
                                <select id="type" name="type" required>
                                    <option value="">Pilih Jenis</option>
                                    <option value="income">Pemasukan</option>
                                    <option value="expense">Pengeluaran</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="category">Kategori</label>
                                <select id="category" name="category" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="Donasi">Donasi</option>
                                    <option value="Iuran Anggota">Iuran Anggota</option>
                                    <option value="Kegiatan">Kegiatan</option>
                                    <option value="Operasional">Operasional</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="amount">Jumlah (Rp)</label>
                                <input type="number" id="amount" name="amount" placeholder="0" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="program_id">Program (Opsional)</label>
                            <select id="program_id" name="program_id">
                                <option value="">-- Pilih Program --</option>
                                <?php
                                $programs = mysqli_query($conn, "SELECT id, name FROM programs WHERE status != 'completed'");
                                while($program = mysqli_fetch_assoc($programs)):
                                ?>
                                <option value="<?php echo $program['id']; ?>"><?php echo $program['name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Keterangan</label>
                            <input type="text" id="description" name="description" placeholder="Masukkan keterangan transaksi" required>
                        </div>
                        
                        <button type="submit" class="btn btn-success">Simpan Transaksi</button>
                        <a href="finance.php" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'footer.php'; ?>