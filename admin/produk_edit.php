<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include '../koneksi.php';

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM produk WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

if (isset($_POST['update'])) {
    $nama  = $_POST['nama'];
    $harga = $_POST['harga'];

    if ($_FILES['gambar']['name'] != '') {
        $gambar = $_FILES['gambar']['name'];
        $tmp    = $_FILES['gambar']['tmp_name'];
        move_uploaded_file($tmp, "../assets/img/".$gambar);
        mysqli_query($conn, "UPDATE produk SET nama='$nama', harga='$harga', gambar='$gambar' WHERE id='$id'");
    } else {
        mysqli_query($conn, "UPDATE produk SET nama='$nama', harga='$harga' WHERE id='$id'");
    }
    header("Location: dashboard.php");
}

if (isset($_POST['update'])) {
    $nama  = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $harga_diskon = $_POST['harga_diskon'] ? $_POST['harga_diskon'] : 0;

    if ($_FILES['gambar']['name'] != '') {
        $gambar = $_FILES['gambar']['name'];
        $tmp    = $_FILES['gambar']['tmp_name'];
        move_uploaded_file($tmp, "../assets/img/".$gambar);
        mysqli_query($conn, "UPDATE produk SET nama='$nama', deskripsi='$deskripsi', harga='$harga', harga_diskon='$harga_diskon', gambar='$gambar' WHERE id='$id'");
    } else {
        mysqli_query($conn, "UPDATE produk SET nama='$nama', deskripsi='$deskripsi', harga='$harga', harga_diskon='$harga_diskon' WHERE id='$id'");
    }
    header("Location: dashboard.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    
    <div class="container mt-5">
        <h3>Edit Produk</h3>
        <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
    <label>Nama Produk</label>
    <input type="text" name="nama" class="form-control" required>
</div>
        <div class="mb-3">
    <label>Deskripsi</label>
    <textarea name="deskripsi" class="form-control" rows="4"><?= $data['deskripsi']; ?></textarea>
</div>
<div class="mb-3">
    <label>Harga Normal</label>
    <input type="number" name="harga" value="<?= $data['harga']; ?>" class="form-control" required>
</div>
<div class="mb-3">
    <label>Harga Diskon</label>
    <input type="number" name="harga_diskon" value="<?= $data['harga_diskon']; ?>" class="form-control">
</div>
            <button type="submit" name="update" class="btn btn-success">Update</button>
            <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>
</html>
