<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include '../koneksi.php'; // Pastikan jalur koneksi benar

$result = mysqli_query($conn, "SELECT * FROM produk ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SalmaGlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../style.css"> <style>
        /* Gaya khusus untuk admin dashboard */
        body {
            background-color: #f8f9fa; /* Light grey background */
        }
        .navbar-admin {
            background-color: #ff69b4 !important; /* Pink Navbar */
        }
        .navbar-admin .navbar-brand, .navbar-admin .nav-link, .navbar-admin .btn-outline-light {
            color: white !important;
        }
        .navbar-admin .btn-outline-light:hover {
            background-color: rgba(255,255,255,0.2) !important;
        }
        .container-dashboard {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-top: 30px;
        }
        .table thead {
            background-color: #ff69b4; /* Pink header for tables */
            color: white;
        }
        .table thead th {
            border-bottom: none;
        }
        .table tbody tr {
            transition: background-color 0.2s ease;
        }
        .table tbody tr:hover {
            background-color: #ffeef4; /* Light pink on hover */
        }
        .table img {
            max-width: 80px;
            height: auto;
            border-radius: 5px;
        }
        .btn-action {
            margin: 2px;
        }
        .btn-pink-add {
            background-color: #ff69b4;
            border-color: #ff69b4;
            color: white;
            font-weight: bold;
        }
        .btn-pink-add:hover {
            background-color: #e05e9a;
            border-color: #e05e9a;
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark navbar-admin shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                <i class="fas fa-tools me-2"></i> Admin SalmaGlow
            </a>
            <div class="d-flex">
                <a href="logout.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container container-dashboard mt-4 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h3><i class="fas fa-cube me-2"></i> Data Produk</h3>
            <a href="produk_tambah.php" class="btn btn-pink-add btn-sm">
                <i class="fas fa-plus-circle me-1"></i> Tambah Produk Baru
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Gambar</th>
                        <th scope="col">Nama Produk</th>
                        <th scope="col">Harga Normal</th>
                        <th scope="col">Harga Diskon</th>
                        <th scope="col">Stok</th> <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php $no=1; while($row=mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <th scope="row"><?= $no++; ?></th>
                        <td><img src="../assets/img/<?= htmlspecialchars($row['gambar']); ?>" alt="<?= htmlspecialchars($row['nama']); ?>"></td>
                        <td><?= htmlspecialchars($row['nama']); ?></td>
                        <td>Rp <?= number_format($row['harga'],0,',','.'); ?></td>
                        <td>
                            <?php if($row['harga_diskon'] > 0): ?>
                                <span class="text-success fw-bold">Rp <?= number_format($row['harga_diskon'],0,',','.'); ?></span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="<?= ($row['stok'] <= 5 && $row['stok'] > 0) ? 'text-warning fw-bold' : ''; ?>
                                        <?= ($row['stok'] == 0) ? 'text-danger fw-bold' : ''; ?>">
                                <?= htmlspecialchars($row['stok']); ?>
                                <?= ($row['stok'] == 0) ? ' (Habis)' : (($row['stok'] <= 5) ? ' (Segera Habis)' : ''); ?>
                            </span>
                        </td>
                        <td>
                            <a href="produk_edit.php?id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-warning btn-sm btn-action">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="produk_hapus.php?id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-danger btn-sm btn-action" onclick="return confirm('Yakin ingin menghapus produk <?= htmlspecialchars($row['nama']); ?>? Tindakan ini tidak dapat dibatalkan.');">
                                <i class="fas fa-trash-alt"></i> Hapus
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-box-open fa-2x mb-2 text-muted"></i> <br>
                            Belum ada produk yang ditambahkan.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>