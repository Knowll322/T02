<?php
session_start();

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'karangtaruna');

// Connect to Database
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Functions
function clean_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function get_role() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function format_rupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function get_user_info($user_id) {
    global $conn;
    $sql = "SELECT * FROM users WHERE id = $user_id";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result);
}

// Alert Messages
function set_alert($type, $message) {
    $_SESSION['alert'] = [
        'type' => $type,
        'message' => $message
    ];
}

function get_alert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        unset($_SESSION['alert']);
        return $alert;
    }
    return null;
}
?>