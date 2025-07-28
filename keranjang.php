<?php
session_start();
include 'koneksi.php';

// Hapus produk dari keranjang
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    unset($_SESSION['cart'][$remove_id]);
    header("Location: keranjang.php");
    exit;
}

// Update jumlah produk di keranjang (opsional, jika Anda ingin input quantity)
if (isset($_POST['update_qty'])) {
    $product_id = $_POST['product_id'];
    $new_qty = (int)$_POST['new_qty'];

    // Ambil stok dari database
    $q_stok = mysqli_query($conn, "SELECT stok FROM produk WHERE id='$product_id'");
    $data_stok = mysqli_fetch_assoc($q_stok);
    $stok_tersedia = $data_stok ? $data_stok['stok'] : 0;

    if ($new_qty > 0 && $new_qty <= $stok_tersedia) {
        $_SESSION['cart'][$product_id] = $new_qty;
    } elseif ($new_qty > $stok_tersedia) {
        echo "<script>alert('Jumlah yang diminta melebihi stok tersedia. Stok tersedia: " . $stok_tersedia . "');</script>";
        $_SESSION['cart'][$product_id] = $stok_tersedia; // Set ke stok maksimal
    } else {
        unset($_SESSION['cart'][$product_id]); // Hapus jika kuantitas 0
    }
    header("Location: keranjang.php");
    exit;
}


$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - SalmaGlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css"> <!-- LINK KE FILE STYLE.CSS -->
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">SalmaGlow</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Search Bar Placeholder (Optional: You can add search functionality here too if needed) -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="fas fa-home me-1"></i> Beranda</a>
                    </li>
                </ul>
                <!-- Bagian kanan navbar: Keranjang dan Admin Login -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="keranjang.php">
                            <i class="fas fa-shopping-cart"></i> Keranjang (<span id="cart-count"><?= isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?></span>)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-light ms-lg-3" href="admin/login.php">
                            <i class="fas fa-user-shield"></i> Admin Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>Keranjang Belanja Anda</h1>
            <p class="lead">Lihat kembali produk pilihanmu sebelum melanjutkan pembayaran.</p>
        </div>
    </section>

    <div class="container py-4">
        <?php if(empty($cart)): ?>
            <div class="alert alert-info text-center py-4 rounded-3">
                <i class="fas fa-box-open fa-2x mb-3 d-block"></i>
                Keranjang belanjamu masih kosong. <br>
                Ayo, <a href="index.php" class="alert-link">belanja produk favoritmu sekarang!</a>
            </div>
        <?php else: ?>
            <div class="cart-table-container">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">Produk</th>
                                <th scope="col">Harga</th>
                                <th scope="col">Jumlah</th>
                                <th scope="col">Stok Tersedia</th>
                                <th scope="col">Subtotal</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $total = 0;
                        foreach($cart as $id=>$qty):
                            $q = mysqli_query($conn, "SELECT nama, gambar, harga, harga_diskon, stok FROM produk WHERE id='$id'");
                            $p = mysqli_fetch_assoc($q);
                            // Pastikan produk ditemukan
                            if ($p) {
                                $harga = $p['harga_diskon'] > 0 ? $p['harga_diskon'] : $p['harga'];
                                $subtotal = $harga * $qty;
                                $total += $subtotal;
                                $stok_tersedia = $p['stok'];
                                $is_qty_exceeds_stock = ($qty > $stok_tersedia);
                            ?>
                                <tr class="<?= $is_qty_exceeds_stock ? 'table-warning' : ''; ?>">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="assets/img/<?= htmlspecialchars($p['gambar']); ?>" alt="<?= htmlspecialchars($p['nama']); ?>" class="me-3 img-thumbnail">
                                            <span><?= htmlspecialchars($p['nama']); ?></span>
                                        </div>
                                    </td>
                                    <td>Rp <?= number_format($harga,0,',','.'); ?></td>
                                    <td>
                                        <form method="POST" class="d-flex align-items-center">
                                            <input type="hidden" name="product_id" value="<?= htmlspecialchars($id); ?>">
                                            <input type="number" name="new_qty" value="<?= htmlspecialchars($qty); ?>" min="1" max="<?= htmlspecialchars($stok_tersedia); ?>" class="form-control form-control-sm text-center" style="width: 70px;">
                                            <button type="submit" name="update_qty" class="btn btn-sm btn-outline-secondary ms-2"><i class="fas fa-sync-alt"></i></button>
                                        </form>
                                    </td>
                                    <td>
                                        <span class="<?= $stok_tersedia <= 0 ? 'text-danger fw-bold' : 'text-success'; ?>">
                                            <?= htmlspecialchars($stok_tersedia); ?>
                                        </span>
                                    </td>
                                    <td>Rp <?= number_format($subtotal,0,',','.'); ?></td>
                                    <td>
                                        <a href="?remove=<?= htmlspecialchars($id); ?>" class="btn btn-danger-outline btn-sm" onclick="return confirm('Yakin ingin menghapus produk ini dari keranjang?');">
                                            <i class="fas fa-trash-alt"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                                <?php if ($is_qty_exceeds_stock): ?>
                                    <tr>
                                        <td colspan="6" class="text-danger text-center small fw-bold">
                                            <i class="fas fa-exclamation-triangle me-1"></i> Jumlah yang diminta (<?= htmlspecialchars($qty); ?>) melebihi stok tersedia (<?= htmlspecialchars($stok_tersedia); ?>). Harap sesuaikan!
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php
                            } // End if $p
                        endforeach;
                        ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <h4>Total Belanja: <span class="text-pink fw-bold">Rp <?= number_format($total, 0, ',', '.'); ?></span></h4>
                    <div>
                        <a href="index.php" class="btn btn-secondary me-2"><i class="fas fa-arrow-left me-1"></i> Lanjut Belanja</a>
                        <a href="checkout.php" class="btn btn-pink"><i class="fas fa-cash-register me-1"></i> Lanjut ke Checkout</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer text-center">
        <div class="container">
            <p>&copy; <?= date('Y'); ?> SalmaGlow. Semua Hak Cipta Dilindungi.</p>
            <p>
                <a href="#" class="text-light mx-2"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="text-light mx-2"><i class="fab fa-instagram"></i></a>
                <a href="#" class="text-light mx-2"><i class="fab fa-twitter"></i></a>
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>