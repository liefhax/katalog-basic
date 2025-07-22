<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include '../koneksi.php';

$result = mysqli_query($conn, "SELECT * FROM produk ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - NU Uncle FO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Admin Panel</a>
            <div class="d-flex">
                <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Data Produk</h3>
            <a href="produk_tambah.php" class="btn btn-primary btn-sm">+ Tambah Produk</a>
        </div>
        <table class="table table-bordered table-hover">
        <thead class="table-dark">
    <tr>
        <th>No</th>
        <th>Gambar</th>
        <th>Nama Produk</th>
        <th>Harga Normal</th>
        <th>Harga Diskon</th>
        <th>Aksi</th>
    </tr>
</thead>
<tbody>
<?php $no=1; while($row=mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?= $no++; ?></td>
    <td><img src="../assets/img/<?= $row['gambar']; ?>" width="80"></td>
    <td><?= $row['nama']; ?></td>
    <td>Rp <?= number_format($row['harga'],0,',','.'); ?></td>
    <td>
        <?php if($row['harga_diskon']>0): ?>
            <span class="text-success fw-bold">Rp <?= number_format($row['harga_diskon'],0,',','.'); ?></span>
        <?php else: ?>
            <span class="text-muted">-</span>
        <?php endif; ?>
    </td>
    <td>
        <a href="produk_edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
        <a href="produk_hapus.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</a>
    </td>
</tr>
<?php endwhile; ?>
</tbody>

        </table>
    </div>
</body>
</html>
