<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include '../koneksi.php'; // Pastikan jalur koneksi benar

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM produk WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Produk tidak ditemukan.'); window.location.href='dashboard.php';</script>";
    exit;
}

if (isset($_POST['update'])) {
    $nama           = mysqli_real_escape_string($conn, $_POST['nama']);
    $deskripsi      = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $harga          = (int)$_POST['harga'];
    $harga_diskon   = $_POST['harga_diskon'] ? (int)$_POST['harga_diskon'] : 0;
    $stok           = (int)$_POST['stok']; // Tambah stok

    $gambar_update = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK && $_FILES['gambar']['name'] != '') {
        $gambar_name = $_FILES['gambar']['name'];
        $gambar_tmp = $_FILES['gambar']['tmp_name'];
        $upload_dir = "../assets/img/";
        $gambar_update = basename($gambar_name);

        // Hapus gambar lama jika ada
        if (!empty($data['gambar']) && file_exists($upload_dir . $data['gambar'])) {
            unlink($upload_dir . $data['gambar']);
        }

        if (!move_uploaded_file($gambar_tmp, $upload_dir . $gambar_update)) {
            echo "<script>alert('Gagal mengunggah gambar baru.');</script>";
            $gambar_update = $data['gambar']; // Gunakan gambar lama jika gagal upload baru
        }
    } else {
        $gambar_update = $data['gambar']; // Tetap gunakan gambar lama jika tidak ada upload baru
    }

    $query_update = "UPDATE produk SET
                        nama='$nama',
                        deskripsi='$deskripsi',
                        harga='$harga',
                        harga_diskon='$harga_diskon',
                        gambar='$gambar_update',
                        stok='$stok'
                    WHERE id='$id'"; // Tambah stok ke query update
    
    if (mysqli_query($conn, $query_update)) {
        header("Location: dashboard.php");
        exit;
    } else {
        echo "<script>alert('Gagal memperbarui produk: " . mysqli_error($conn) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Admin SalmaGlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Anda bisa menambahkan link ke style.css admin jika ada -->
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 700px; background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        h3 { color: #ff69b4; margin-bottom: 30px; text-align: center; }
        .btn-success { background-color: #28a745; border-color: #28a745; }
        .btn-success:hover { background-color: #218838; border-color: #1e7e34; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h3><i class="fas fa-edit me-2"></i> Edit Produk</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Produk</label>
                <input type="text" id="nama" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" class="form-control" rows="4"><?= htmlspecialchars($data['deskripsi']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="harga" class="form-label">Harga Normal (Rp)</label>
                <input type="number" id="harga" name="harga" value="<?= htmlspecialchars($data['harga']); ?>" class="form-control" required min="0">
            </div>
            <div class="mb-3">
                <label for="harga_diskon" class="form-label">Harga Diskon (Rp)</label>
                <input type="number" id="harga_diskon" name="harga_diskon" value="<?= htmlspecialchars($data['harga_diskon']); ?>" class="form-control" placeholder="Boleh kosong jika tidak ada diskon" min="0">
            </div>
            <div class="mb-3">
                <label for="stok" class="form-label">Stok Produk</label>
                <input type="number" id="stok" name="stok" value="<?= htmlspecialchars($data['stok']); ?>" class="form-control" required min="0">
            </div>
            <div class="mb-3">
                <label for="gambar" class="form-label">Gambar Produk (Kosongkan jika tidak ingin mengubah)</label>
                <input type="file" id="gambar" name="gambar" class="form-control" accept="image/*">
                <?php if (!empty($data['gambar'])): ?>
                    <small class="form-text text-muted mt-2">Gambar saat ini: <img src="../assets/img/<?= htmlspecialchars($data['gambar']); ?>" alt="Current Image" style="max-width: 100px; height: auto; border-radius: 5px;"></small>
                <?php endif; ?>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <button type="submit" name="update" class="btn btn-success"><i class="fas fa-sync-alt me-1"></i> Perbarui Produk</button>
                <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>