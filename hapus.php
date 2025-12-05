<?php
require 'koneksi.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('ID tidak valid'); window.location='index.php';</script>";
    exit;
}
$id = (int) $_GET['id'];

// ambil data untuk mengetahui nama file
$rows = query("SELECT * FROM mahasiswa3 WHERE id = $id");
if (empty($rows)) {
    echo "<script>alert('Data tidak ditemukan'); window.location='index.php';</script>";
    exit;
}
$mhs = $rows[0];
$gambar = $mhs['gambar'] ?? 'nophoto.jpg';

// hapus record
$stmt = mysqli_prepare($conn, "DELETE FROM mahasiswa3 WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) > 0) {
    // jika file bukan nophoto.jpg, hapus file fisik
    if ($gambar && $gambar !== 'nophoto.jpg') {
        $path = __DIR__ . '/img/' . $gambar;
        if (file_exists($path)) @unlink($path);
    }
    echo "<script>alert('Data berhasil dihapus'); window.location='index.php';</script>";
    exit;
} else {
    echo "<script>alert('Gagal menghapus data'); window.location='index.php';</script>";
    exit;
}
