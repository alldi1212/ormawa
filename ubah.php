<?php
require 'koneksi.php';

// cek id valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('ID tidak valid'); window.location='index.php';</script>";
    exit;
}
$id = (int) $_GET['id'];

// ambil data
$rows = query("SELECT * FROM mahasiswa3 WHERE id = $id");
if (empty($rows)) {
    echo "<script>alert('Data tidak ditemukan'); window.location='index.php';</script>";
    exit;
}
$mhs = $rows[0];

// fungsi upload (sama seperti tambah tapi mengembalikan special values)
function upload_image_edit() {
    if (!isset($_FILES['gambar'])) return 'NO_FILE';
    $file = $_FILES['gambar'];

    if ($file['error'] === UPLOAD_ERR_NO_FILE) return 'NO_FILE';
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo "<script>alert('Error saat upload. Code: {$file['error']}'); window.history.back();</script>";
        return false;
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        echo "<script>alert('Ukuran file terlalu besar. Maks 2 MB.'); window.history.back();</script>";
        return false;
    }
    if (@getimagesize($file['tmp_name']) === false) {
        echo "<script>alert('File bukan gambar yang valid.'); window.history.back();</script>";
        return false;
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png'])) {
        echo "<script>alert('Ekstensi tidak diperbolehkan.'); window.history.back();</script>";
        return false;
    }

    $newName = uniqid('foto_', true) . '.' . $ext;
    $uploadDir = __DIR__ . '/img/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
        echo "<script>alert('Gagal menyimpan file.'); window.history.back();</script>";
        return false;
    }

    return $newName;
}

if (isset($_POST['submit'])) {
    $nama = trim($_POST['nama'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');
    $angkatan = trim($_POST['angkatan'] ?? '');
    $gambarLama = trim($_POST['gambarLama'] ?? '');

    if ($nama === '' || $jurusan === '' || $angkatan === '') {
        echo "<script>alert('Lengkapi semua field'); window.history.back();</script>";
        exit;
    }

    $resUpload = upload_image_edit();
    if ($resUpload === false) exit; // error sudah ditampilkan
    if ($resUpload === 'NO_FILE') {
        $gambarBaru = $gambarLama;
    } else {
        $gambarBaru = $resUpload;
        // hapus file lama jika bukan nophoto.jpg
        if ($gambarLama && $gambarLama !== 'nophoto.jpg') {
            $old = __DIR__ . '/img/' . $gambarLama;
            if (file_exists($old)) @unlink($old);
        }
    }

    // update dengan prepared statement
    $stmt = mysqli_prepare($conn, "UPDATE mahasiswa3 SET nama = ?, jurusan = ?, angkatan = ?, gambar = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssisi", $nama, $jurusan, $angkatan, $gambarBaru, $id);
    mysqli_stmt_execute($stmt);

    echo "<script>alert('Data berhasil diubah'); window.location='index.php';</script>";
    exit;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ubah Data</title>
    <style>
        body{font-family:sans-serif;padding:20px;}
        form{width:340px;border:1px solid #ddd;padding:16px;}
        input{width:100%;padding:8px;margin-bottom:8px;box-sizing:border-box;}
        img{width:80px;height:80px;object-fit:cover;margin-bottom:8px;}
        button{padding:10px;background:#f39c12;color:#fff;border:none;cursor:pointer;}
        .note{font-size:12px;color:#888;}
    </style>
</head>
<body>

<h1>Ubah Data Mahasiswa</h1>

<form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="gambarLama" value="<?= htmlspecialchars($mhs['gambar']); ?>">

    <label>Nama</label>
    <input type="text" name="nama" value="<?= htmlspecialchars($mhs['nama']); ?>" required>

    <label>Jurusan</label>
    <input type="text" name="jurusan" value="<?= htmlspecialchars($mhs['jurusan']); ?>" required>

    <label>Angkatan</label>
    <input type="number" name="angkatan" value="<?= htmlspecialchars($mhs['angkatan']); ?>" required>

    <label>Foto saat ini</label><br>
    <?php
      $imgNow = 'img/' . ($mhs['gambar'] && file_exists(__DIR__ . '/img/' . $mhs['gambar']) ? $mhs['gambar'] : 'nophoto.jpg');
    ?>
    <img src="<?= htmlspecialchars($imgNow); ?>" alt="foto"><br>

    <label>Ganti Foto (opsional)</label>
    <input type="file" name="gambar" accept=".jpg,.jpeg,.png,image/*">
    <div class="note">Kosongkan jika tidak ganti. Maks 2MB. Hanya jpg/jpeg/png.</div>

    <br>
    <button type="submit" name="submit">Simpan Perubahan</button>
</form>

<br><a href="index.php">Kembali</a>

</body>
</html>
