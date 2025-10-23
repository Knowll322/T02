<?php
require_once 'config.php';

if(!is_logged_in()) {
    redirect('login.php');
}

if(isset($_GET['id'])) {
    $event_id = clean_input($_GET['id']);
    $user_id = $_SESSION['user_id'];
    
    // Check if already joined
    $check = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM event_participants WHERE event_id = $event_id AND user_id = $user_id"));
    
    if($check == 0) {
        mysqli_query($conn, "INSERT INTO event_participants (event_id, user_id) VALUES ($event_id, $user_id)");
        set_alert('success', 'Anda berhasil bergabung dalam kegiatan!');
    } else {
        set_alert('info', 'Anda sudah bergabung dalam kegiatan ini.');
    }
}

redirect('events.php');
?>