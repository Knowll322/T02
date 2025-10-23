<?php
require_once 'config.php';

if(is_logged_in()) {
    redirect('dashboard.php');
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = clean_input($_POST['email']);
    $password = clean_input($_POST['password']);
    $role = clean_input($_POST['role']);
    
    $sql = "SELECT * FROM users WHERE email = '$email' AND role = '$role'";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if(password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            set_alert('success', 'Login berhasil! Selamat datang ' . $user['name']);
            redirect('dashboard.php');
        }
    }
    
    set_alert('danger', 'Email atau password salah!');
}

require_once 'header.php';
?>

<main class="container">
    <div class="form-container">
        <h2 style="text-align: center; margin-bottom: 2rem;">Masuk Akun</h2>
        
        <?php $alert = get_alert(); if($alert): ?>
            <div class="alert alert-<?php echo $alert['type']; ?>">
                <?php echo $alert['message']; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="role-selector">
                <div class="role-option">
                    <input type="radio" id="roleAdmin" name="role" value="admin" checked>
                    <label for="roleAdmin">👑 Admin</label>
                </div>
                <div class="role-option">
                    <input type="radio" id="roleAnggota" name="role" value="anggota">
                    <label for="roleAnggota">👤 Anggota</label>
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn" style="width: 100%;">Masuk</button>
        </form>
        
        <p style="text-align: center; margin-top: 1rem;">
            Belum punya akun? <a href="register.php">Daftar sekarang</a>
        </p>
        
        <div style="margin-top: 2rem; padding: 1rem; background: #f8f9fa; border-radius: 5px;">
            <h4>Akun Demo:</h4>
            <p><strong>Admin:</strong> admin@karangtaruna.org / admin123</p>
            <p><strong>Anggota:</strong> john@karangtaruna.org / anggota123</p>
        </div>
    </div>
</main>

<?php require_once 'footer.php'; ?>