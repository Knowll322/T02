<?php
require_once 'config.php';

if(!is_logged_in()) {
    redirect('login.php');
}

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="laporan_' . date('Ymd') . '.xls');

$report_type = isset($_GET['type']) ? clean_input($_GET['type']) : 'monthly';
$period = isset($_GET['period']) ? clean_input($_GET['period']) : date('Y-m');
$division = isset($_GET['division']) ? clean_input($_GET['division']) : '';

echo "<table border='1'>";
echo "<tr><th colspan='6' style='background: #e74c3c; color: white;'>Laporan Karang Taruna Bhakti Mandiri</th></tr>";

if($report_type == 'monthly') {
    echo "<tr><td colspan='6'><strong>Laporan Bulanan - " . date('F Y', strtotime($period . '-01')) . "</strong></td></tr>";
    
    // Get financial data
    $finance = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT 
            SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income,
            SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense
        FROM finance 
        WHERE DATE_FORMAT(transaction_date, '%Y-%m') = '$period'
    "));
    
    echo "<tr>";
    echo "<td><strong>Total Pemasukan</strong></td>";
    echo "<td><strong>Total Pengeluaran</strong></td>";
    echo "<td><strong>Saldo</strong></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>" . format_rupiah($finance['income']) . "</td>";
    echo "<td>" . format_rupiah($finance['expense']) . "</td>";
    echo "<td>" . format_rupiah($finance['income'] - $finance['expense']) . "</td>";
    echo "</tr>";
    
    // Transaction details
    echo "<tr><td colspan='6'><strong>Detail Transaksi</strong></td></tr>";
    echo "<tr><th>Tanggal</th><th>Jenis</th><th>Kategori</th><th>Keterangan</th><th>Jumlah</th></tr>";
    
    $transactions = mysqli_query($conn, "
        SELECT * FROM finance 
        WHERE DATE_FORMAT(transaction_date, '%Y-%m') = '$period'
        ORDER BY transaction_date DESC
    ");
    
    while($trans = mysqli_fetch_assoc($transactions)) {
        echo "<tr>";
        echo "<td>" . date('d M Y', strtotime($trans['transaction_date'])) . "</td>";
        echo "<td>" . ucfirst($trans['type']) . "</td>";
        echo "<td>" . $trans['category'] . "</td>";
        echo "<td>" . $trans['description'] . "</td>";
        echo "<td>" . format_rupiah($trans['amount']) . "</td>";
        echo "</tr>";
    }
    
} elseif($report_type == 'program') {
    echo "<tr><td colspan='6'><strong>Laporan Program - " . date('F Y', strtotime($period . '-01')) . "</strong></td></tr>";
    
    echo "<tr><th>Nama Program</th><th>Divisi</th><th>Target</th><th>Realisasi</th><th>Persentase</th><th>Status</th></tr>";
    
    $where = $period ? "WHERE DATE_FORMAT(start_date, '%Y-%m') = '$period'" : "";
    $programs = mysqli_query($conn, "
        SELECT p.*, COUNT(pp.id) as participants 
        FROM programs p 
        LEFT JOIN program_participants pp ON p.id = pp.program_id 
        $where
        GROUP BY p.id
    ");
    
    while($program = mysqli_fetch_assoc($programs)) {
        $percentage = $program['target_participants'] > 0 ? 
            round(($program['participants'] / $program['target_participants']) * 100, 1) : 0;
            
        echo "<tr>";
        echo "<td>" . $program['name'] . "</td>";
        echo "<td>" . $program['category'] . "</td>";
        echo "<td>" . $program['target_participants'] . "</td>";
        echo "<td>" . $program['participants'] . "</td>";
        echo "<td>" . $percentage . "%</td>";
        echo "<td>" . ucfirst($program['status']) . "</td>";
        echo "</tr>";
    }
    
} elseif($report_type == 'members') {
    echo "<tr><td colspan='6'><strong>Laporan Keanggotaan - " . date('F Y', strtotime($period . '-01')) . "</strong></td></tr>";
    
    echo "<tr><th>Nama</th><th>Email</th><th>Telepon</th><th>Divisi</th><th>Tanggal Bergabung</th></tr>";
    
    $where = $division ? "WHERE division = '$division'" : "";
    $members = mysqli_query($conn, "
        SELECT name, email, phone, division, join_date 
        FROM users 
        WHERE role = 'anggota' $where
        ORDER BY division, name
    ");
    
    while($member = mysqli_fetch_assoc($members)) {
        echo "<tr>";
        echo "<td>" . $member['name'] . "</td>";
        echo "<td>" . $member['email'] . "</td>";
        echo "<td>" . $member['phone'] . "</td>";
        echo "<td>" . $member['division'] . "</td>";
        echo "<td>" . date('d M Y', strtotime($member['join_date'])) . "</td>";
        echo "</tr>";
    }
}

echo "</table>";
?>