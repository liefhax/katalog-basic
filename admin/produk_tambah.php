<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include '../koneksi.php';

if (isset($_POST['simpan'])) {
    $nama  = $_POST['nama'];
    $harga = $_POST['harga'];
    
    $gambar = $_FILES['gambar']['name'];
    $tmp    = $_FILES['gambar']['tmp_name'];
    move_uploaded_file($tmp, "../assets/img/".$gambar);

    $query = "INSERT INTO produk (nama, harga, gambar) VALUES ('$nama','$harga','$gambar')";
    mysqli_query($conn, $query);
    header("Location: dashboard.php");
}

if (isset($_POST['simpan'])) {
    $nama  = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $harga_diskon = $_POST['harga_diskon'] ? $_POST['harga_diskon'] : 0;
    
    $gambar = $_FILES['gambar']['name'];
    $tmp    = $_FILES['gambar']['tmp_name'];
    move_uploaded_file($tmp, "../assets/img/".$gambar);

    $query = "INSERT INTO produk (nama, deskripsi, harga, harga_diskon, gambar) VALUES 
              ('$nama', '$deskripsi', '$harga', '$harga_diskon', '$gambar')";
    mysqli_query($conn, $query);
    header("Location: dashboard.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3>Tambah Produk</h3>
        <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
    <label>Nama Produk</label>
    <input type="text" name="nama" class="form-control" required>
</div>
<div class="mb-3">
    <label>Deskripsi</label>
    <textarea name="deskripsi" class="form-control" rows="4" required></textarea>
</div>
<div class="mb-3">
    <label>Harga Normal</label>
    <input type="number" name="harga" class="form-control" required>
</div>
<div class="mb-3">
    <label>Harga Diskon</label>
    <input type="number" name="harga_diskon" class="form-control" placeholder="Boleh kosong jika tidak ada diskon">
</div>
<div class="mb-3">
    <label>Gambar</label>
    <input type="file" name="gambar" class="form-control" required>
</div>
            <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
            <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>
</html>
