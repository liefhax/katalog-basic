<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include '../koneksi.php'; // Pastikan jalur koneksi benar

if (isset($_POST['simpan'])) {
    $nama           = mysqli_real_escape_string($conn, $_POST['nama']);
    $deskripsi      = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $harga          = (int)$_POST['harga'];
    $harga_diskon   = $_POST['harga_diskon'] ? (int)$_POST['harga_diskon'] : 0;
    $stok           = (int)$_POST['stok']; // Tambah stok

    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK) {
        $gambar_name = $_FILES['gambar']['name'];
        $gambar_tmp = $_FILES['gambar']['tmp_name'];
        $upload_dir = "../assets/img/"; // Pastikan folder ini ada dan bisa ditulis
        $gambar = basename($gambar_name); // Ambil hanya nama file

        if (!move_uploaded_file($gambar_tmp, $upload_dir . $gambar)) {
            echo "<script>alert('Gagal mengunggah gambar.');</script>";
            $gambar = ''; // Set gambar kosong jika gagal upload
        }
    }

    $query = "INSERT INTO produk (nama, deskripsi, harga, harga_diskon, gambar, stok) VALUES
              ('$nama', '$deskripsi', '$harga', '$harga_diskon', '$gambar', '$stok')"; // Tambah stok ke query
    
    if (mysqli_query($conn, $query)) {
        header("Location: dashboard.php");
        exit;
    } else {
        echo "<script>alert('Gagal menambahkan produk: " . mysqli_error($conn) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - Admin SalmaGlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Anda bisa menambahkan link ke style.css admin jika ada -->
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 700px; background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        h3 { color: #ff69b4; margin-bottom: 30px; text-align: center; }
        .btn-primary { background-color: #ff69b4; border-color: #ff69b4; }
        .btn-primary:hover { background-color: #e05e9a; border-color: #e05e9a; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h3><i class="fas fa-plus-circle me-2"></i> Tambah Produk Baru</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Produk</label>
                <input type="text" id="nama" name="nama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" class="form-control" rows="4" required></textarea>
            </div>
            <div class="mb-3">
                <label for="harga" class="form-label">Harga Normal (Rp)</label>
                <input type="number" id="harga" name="harga" class="form-control" required min="0">
            </div>
            <div class="mb-3">
                <label for="harga_diskon" class="form-label">Harga Diskon (Rp)</label>
                <input type="number" id="harga_diskon" name="harga_diskon" class="form-control" placeholder="Boleh kosong jika tidak ada diskon" min="0">
            </div>
            <div class="mb-3">
                <label for="stok" class="form-label">Stok Produk</label>
                <input type="number" id="stok" name="stok" class="form-control" required min="0">
            </div>
            <div class="mb-3">
                <label for="gambar" class="form-label">Gambar Produk</label>
                <input type="file" id="gambar" name="gambar" class="form-control" accept="image/*" required>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <button type="submit" name="simpan" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Produk</button>
                <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>