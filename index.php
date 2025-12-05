<?php
// index.php
require 'koneksi.php';

// Ambil semua mahasiswa (aman)
$mahasiswa = query("SELECT * FROM mahasiswa3 ORDER BY nama ASC");
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Mahasiswa</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        img.thumb { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; }
        .btn { display: inline-block; padding: 6px 10px; background: #4CAF50; color: #fff; text-decoration: none; border-radius: 4px; }
        .btn-red { background: #e74c3c; }
    </style>
</head>
<body>

<h1>Daftar ORMAWA</h1>

<a href="tambah.php" class="btn">Tambah Data</a>
<br><br>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Foto</th>
            <th>Nama</th>
            <th>Jurusan</th>
            <th>Angkatan</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($mahasiswa)): ?>
        <tr><td colspan="6">Belum ada data.</td></tr>
    <?php else: $i = 1; ?>
        <?php foreach ($mahasiswa as $m): ?>
            <?php
                // Ambil nama file gambar dari database
                $foto = $m['gambar'] ?? '';
                // Tentukan path gambar
                $imgPath = 'img/' . preg_replace('/[^a-zA-Z0-9-_\.]/', '', $foto);
                // Cek apakah file gambar ada
                if (!$foto || !file_exists(__DIR__ . '/' . $imgPath)) {
                    $imgPath = 'img/nophoto.jpg'; // Gambar default jika tidak ada
                }
            ?>
            <tr>
                <td><?= $i++; ?></td>
                <td><img src="<?= htmlspecialchars($imgPath); ?>" class="thumb" alt="foto"></td>
                <td><?= htmlspecialchars($m['nama']); ?></td>
                <td><?= htmlspecialchars($m['jurusan']); ?></td>
                <td><?= htmlspecialchars($m['angkatan']); ?></td>
                <td>
                    <a href="ubah.php?id=<?= urlencode($m['id']); ?>" class="btn">Edit</a>
                    <a href="hapus.php?id=<?= urlencode($m['id']); ?>" class="btn btn-red" onclick="return confirm('Yakin akan menghapus data ini?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

</body>
</html>