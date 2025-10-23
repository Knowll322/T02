<?php
require_once 'config.php';

if(!is_logged_in()) {
    redirect('login.php');
}

if(isset($_GET['id'])) {
    $program_id = clean_input($_GET['id']);
    $user_id = $_SESSION['user_id'];
    
    // Check if already joined
    $check = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM program_participants WHERE program_id = $program_id AND user_id = $user_id"));
    
    if($check == 0) {
        mysqli_query($conn, "INSERT INTO program_participants (program_id, user_id, join_date) VALUES ($program_id, $user_id, CURDATE())");
        set_alert('success', 'Anda berhasil bergabung dalam program!');
    } else {
        set_alert('info', 'Anda sudah bergabung dalam program ini.');
    }
}

redirect('programs.php');
?>