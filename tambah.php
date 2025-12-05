<?php
require 'koneksi.php';

// ------------ FUNGSI UPLOAD ------------
function upload() {
    $namaFile = $_FILES['gambar']['name'];
    $ukuranFile = $_FILES['gambar']['size'];
    $error = $_FILES['gambar']['error'];
    $tmpName = $_FILES['gambar']['tmp_name'];

    // Jika tidak upload file
    if ($error === 4) {
        return "nophoto.jpg"; // default
    }

    // Cek ekstensi
    $ekstensiValid = ['jpg','jpeg','png'];
    $ekstensi = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

    if (!in_array($ekstensi, $ekstensiValid)) {
        echo "<script>alert('Yang Anda upload bukan gambar (jpg/jpeg/png)!');</script>";
        return false;
    }

    // Cek ukuran max 2MB
    if ($ukuranFile > 2 * 1024 * 1024) {
        echo "<script>alert('Ukuran file terlalu besar! Maksimal 2 MB');</script>";
        return false;
    }

    // Nama baru unik
    $namaBaru = uniqid("foto_", true) . '.' . $ekstensi;

    // Pindahkan file
    move_uploaded_file($tmpName, 'img/' . $namaBaru);

    return $namaBaru;
}
// ---------------------------------------


// Submit ditekan
if (isset($_POST["submit"])) {

    $nama = htmlspecialchars($_POST["nama"]);
    $jurusan = htmlspecialchars($_POST["jurusan"]);
    $angkatan = htmlspecialchars($_POST["angkatan"]);

    $gambar = upload();
    if ($gambar === false) {
        return false; // hentikan proses
    }

    $query = "INSERT INTO mahasiswa3 VALUES 
              ('', '$nama', '$jurusan', '$angkatan', '$gambar')";
    mysqli_query($conn, $query);

    if (mysqli_affected_rows($conn) > 0) {
        echo "<script>
                alert('Data berhasil ditambahkan!');
                document.location.href = 'index.php';
              </script>";
    } else {
        echo "<script>alert('Gagal menambah data');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Data Mahasiswa</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        ul li { list-style: none; margin-bottom: 10px; }
        input { padding: 8px; width: 100%; }
        button { padding: 10px; background: green; color: white; border: none; cursor: pointer; }
        form { width: 320px; border:1px solid #ccc; padding:20px; }
    </style>
</head>
<body>

<h1>Tambah Data Mahasiswa</h1>

<form action="" method="post" enctype="multipart/form-data">
    <ul>
        <li>
            <label>Nama :</label>
            <input type="text" name="nama" required>
        </li>
        <li>
            <label>Jurusan :</label>
            <input type="text" name="jurusan" required>
        </li>
        <li>
            <label>Angkatan :</label>
            <input type="number" name="angkatan" required>
        </li>
        <li>
            <label>Foto :</label>
            <input type="file" name="gambar" accept=".jpg,.jpeg,.png">
        </li>
        <li>
            <button type="submit" name="submit">Tambah!</button>
        </li>
    </ul>
</form>

<br>
<a href="index.php">Kembali</a>

</body>
</html>
