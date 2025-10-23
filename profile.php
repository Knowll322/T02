<?php
require_once 'config.php';

if(!is_logged_in()) {
    redirect('login.php');
}

$user = get_user_info($_SESSION['user_id']);

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = clean_input($_POST['name']);
    $phone = clean_input($_POST['phone']);
    $address = clean_input($_POST['address']);
    $division = clean_input($_POST['division']);
    
    if(!empty($_POST['password'])) {
        $password = password_hash(clean_input($_POST['password']), PASSWORD_DEFAULT);
        $sql = "UPDATE users SET name = '$name', phone = '$phone', address = '$address', 
                division = '$division', password = '$password' WHERE id = {$_SESSION['user_id']}";
    } else {
        $sql = "UPDATE users SET name = '$name', phone = '$phone', address = '$address', 
                division = '$division' WHERE id = {$_SESSION['user_id']}";
    }
    
    if(mysqli_query($conn, $sql)) {
        $_SESSION['name'] = $name;
        set_alert('success', 'Profil berhasil diperbarui!');
    } else {
        set_alert('danger', 'Gagal memperbarui profil!');
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
                <?php if(get_role() == 'admin'): ?>
                    <li><a href="members.php">👥 Manajemen Anggota</a></li>
                    <li><a href="programs.php">📋 Program Kerja</a></li>
                    <li><a href="finance.php">💰 Keuangan</a></li>
                <?php endif; ?>
                <li><a href="events.php">📅 Jadwal Kegiatan</a></li>
                <li><a href="profile.php" class="active">⚙️ Profil</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="card">
                <div class="card-header">
                    <h3>Profil Saya</h3>
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
                                <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" value="<?php echo $user['email']; ?>" disabled>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Telepon</label>
                                <input type="tel" id="phone" name="phone" value="<?php echo $user['phone']; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="division">Divisi</label>
                                <select id="division" name="division" required>
                                    <option value="">Pilih Divisi</option>
                                    <option value="Pendidikan" <?php echo $user['division'] == 'Pendidikan' ? 'selected' : ''; ?>>Pendidikan</option>
                                    <option value="Kesehatan" <?php echo $user['division'] == 'Kesehatan' ? 'selected' : ''; ?>>Kesehatan</option>
                                    <option value="Lingkungan" <?php echo $user['division'] == 'Lingkungan' ? 'selected' : ''; ?>>Lingkungan</option>
                                    <option value="Seni & Budaya" <?php echo $user['division'] == 'Seni & Budaya' ? 'selected' : ''; ?>>Seni & Budaya</option>
                                    <option value="Olahraga" <?php echo $user['division'] == 'Olahraga' ? 'selected' : ''; ?>>Olahraga</option>
                                    <option value="Kewirausahaan" <?php echo $user['division'] == 'Kewirausahaan' ? 'selected' : ''; ?>>Kewirausahaan</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Alamat</label>
                            <textarea id="address" name="address" rows="3" required><?php echo $user['address']; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password Baru (Kosongkan jika tidak ingin mengubah)</label>
                            <input type="password" id="password" name="password">
                        </div>
                        
                        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'footer.php'; ?>