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

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Keranjang Belanja</h2>
    <?php if(empty($cart)): ?>
        <div class="alert alert-info">Keranjang masih kosong. <a href="index.php">Belanja sekarang</a></div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $total = 0;
            foreach($cart as $id=>$qty):
                $q = mysqli_query($conn, "SELECT * FROM produk WHERE id='$id'");
                $p = mysqli_fetch_assoc($q);
                $harga = $p['harga_diskon'] > 0 ? $p['harga_diskon'] : $p['harga'];
                $subtotal = $harga * $qty;
                $total += $subtotal;
            ?>
                <tr>
                    <td><?= $p['nama']; ?></td>
                    <td>Rp <?= number_format($harga,0,',','.'); ?></td>
                    <td><?= $qty; ?></td>
                    <td>Rp <?= number_format($subtotal,0,',','.'); ?></td>
                    <td><a href="?remove=<?= $id; ?>" class="btn btn-danger btn-sm">Hapus</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <h4>Total: Rp <?= number_format($total,0,',','.'); ?></h4>
        <a href="checkout.php" class="btn btn-success">Lanjut ke Checkout</a>
    <?php endif; ?>
</div>
</body>
</html>
