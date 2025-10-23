<?php
require_once 'config.php';

session_destroy();
set_alert('info', 'Anda telah berhasil logout.');
redirect('index.php');
?>