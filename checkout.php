<?php
session_start();
include 'koneksi.php';

// Nomor WhatsApp Admin
$wa_admin = '628123456789';

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

$total = 0;
foreach ($cart as $id => $qty) {
    $q = mysqli_query($conn, "SELECT * FROM produk WHERE id='$id'");
    $p = mysqli_fetch_assoc($q);
    $harga = $p['harga_diskon'] > 0 ? $p['harga_diskon'] : $p['harga'];
    $total += $harga * $qty;
}

// Hitung Diskon Voucher
$diskon = 0;
if (isset($_POST['voucher']) && $_POST['voucher'] == "DISKON10") {
    $diskon = $total * 0.10;
}
$grand_total = $total - $diskon;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Checkout</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Alamat</label>
            <textarea name="alamat" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Kode Voucher (opsional)</label>
            <input type="text" name="voucher" class="form-control" value="<?= isset($_POST['voucher']) ? $_POST['voucher'] : ''; ?>">
        </div>
        <button class="btn btn-primary" type="submit">Hitung Total</button>
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
        <hr>
        <h4>Ringkasan Pembayaran</h4>
        <p>Total Produk: Rp <?= number_format($total, 0, ',', '.'); ?></p>
        <p>Diskon: Rp <?= number_format($diskon, 0, ',', '.'); ?></p>
        <h5>Total Bayar: Rp <?= number_format($grand_total, 0, ',', '.'); ?></h5>
        <?php
        // Format pesan WA
        $pesan = "Halo Admin, saya *" . $_POST['nama'] . "* ingin order:%0A";
        foreach ($cart as $id => $qty) {
            $q = mysqli_query($conn, "SELECT * FROM produk WHERE id='$id'");
            $p = mysqli_fetch_assoc($q);
            $pesan .= "- " . $p['nama'] . " x" . $qty . "%0A";
        }
        $pesan .= "Total: Rp " . number_format($grand_total, 0, ',', '.') . "%0AAlamat: " . $_POST['alamat'];
        ?>
        <a href="https://wa.me/<?= $wa_admin = '+6285280507714'; ?>?text=<?= $pesan; ?>" target="_blank" class="btn btn-success">Lanjutkan ke WhatsApp</a>
        <?php
        // Hapus keranjang setelah checkout
        unset($_SESSION['cart']);
        ?>
    <?php endif; ?>
</div>
</body>
</html>
