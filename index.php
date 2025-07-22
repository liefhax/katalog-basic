<?php
session_start();
include 'koneksi.php';

// Tambah ke keranjang jika ada add_cart
if (isset($_GET['add_cart'])) {
    $id = $_GET['add_cart'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]++;
    } else {
        $_SESSION['cart'][$id] = 1;
    }

    // Redirect agar tidak ada reload berulang
    header("Location: keranjang.php");
    exit;
}

// Ambil data produk
$query  = "SELECT * FROM produk ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SalmaGlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">SalmaGlow</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="keranjang.php">
                            Keranjang (<?= isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>)
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Produk -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="card shadow-sm h-100">
                            <img src="assets/img/<?php echo $row['gambar']; ?>" class="card-img-top" alt="<?php echo $row['nama']; ?>">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?= $row['nama']; ?></h5>
                                <p class="text-muted small"><?= substr($row['deskripsi'], 0, 60); ?>...</p>
                                <?php if ($row['harga_diskon'] > 0): ?>
                                    <p>
                                        <span class="text-decoration-line-through text-danger">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></span>
                                        <br>
                                        <span class="text-success fw-bold">Rp <?= number_format($row['harga_diskon'], 0, ',', '.'); ?></span>
                                    </p>
                                <?php else: ?>
                                    <p class="text-muted">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></p>
                                <?php endif; ?>
                                <a href="index.php?add_cart=<?= $row['id']; ?>" class="btn btn-primary btn-sm">+ Keranjang</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
</body>
</html>
