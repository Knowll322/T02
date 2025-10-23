<?php
require_once 'config.php';

if(!is_logged_in() || get_role() != 'admin') {
    redirect('dashboard.php');
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = clean_input($_POST['name']);
    $email = clean_input($_POST['email']);
    $password = password_hash(clean_input($_POST['password']), PASSWORD_DEFAULT);
    $phone = clean_input($_POST['phone']);
    $address = clean_input($_POST['address']);
    $division = clean_input($_POST['division']);
    
    $sql = "INSERT INTO users (name, email, password, phone, address, division, join_date) 
            VALUES ('$name', '$email', '$password', '$phone', '$address', '$division', CURDATE())";
    
    if(mysqli_query($conn, $sql)) {
        set_alert('success', 'Anggota berhasil ditambahkan!');
        redirect('members.php');
    } else {
        set_alert('danger', 'Gagal menambahkan anggota!');
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
                    <h3>Tambah Anggota Baru</h3>
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
                                <label for="name">Nama Lengkap</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Telepon</label>
                                <input type="tel" id="phone" name="phone" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="division">Divisi</label>
                                <select id="division" name="division" required>
                                    <option value="">Pilih Divisi</option>
                                    <option value="Pendidikan">Pendidikan</option>
                                    <option value="Kesehatan">Kesehatan</option>
                                    <option value="Lingkungan">Lingkungan</option>
                                    <option value="Seni & Budaya">Seni & Budaya</option>
                                    <option value="Olahraga">Olahraga</option>
                                    <option value="Kewirausahaan">Kewirausahaan</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Alamat</label>
                            <textarea id="address" name="address" rows="3" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-success">Simpan</button>
                        <a href="members.php" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'footer.php'; ?>