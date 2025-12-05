<?php
// koneksi.php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "kampus_db";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Koneksi Error: " . mysqli_connect_error());
}

/**
 * Fungsi query yang lebih aman:
 * - Mengembalikan array kosong jika query gagal
 * - Men-trigger warning dengan pesan error DB (bantu debugging)
 */
function query($sql) {
    global $conn;
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        // Tampilkan warning di log/halaman dev. Jangan tampilkan di production.
        trigger_error("Query error: " . mysqli_error($conn) . " -- SQL: " . $sql, E_USER_WARNING);
        return []; // kembalikan array kosong agar pemanggil tidak crash
    }
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}
?>
