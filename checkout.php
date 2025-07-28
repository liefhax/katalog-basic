<?php
session_start();
include 'koneksi.php';

// Nomor WhatsApp Admin (Ganti dengan nomor WA admin Anda yang valid, dimulai dengan 62)
$wa_admin = '6285280507714'; // Contoh: Ganti dengan nomor WhatsApp admin SalmaGlow

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Jika keranjang kosong, redirect kembali ke keranjang.php
if (empty($cart)) {
    header("Location: keranjang.php");
    exit;
}

$total = 0;
$cart_details = []; // Untuk menyimpan detail produk di keranjang termasuk stok
$stock_issues = false; // Flag untuk menandai masalah stok

foreach ($cart as $id => $qty) {
    $q = mysqli_query($conn, "SELECT nama, gambar, harga, harga_diskon, stok FROM produk WHERE id='$id'");
    $p = mysqli_fetch_assoc($q);
    
    if ($p) {
        $harga = $p['harga_diskon'] > 0 ? $p['harga_diskon'] : $p['harga'];
        $subtotal = $harga * $qty;
        $total += $subtotal;
        
        $cart_details[$id] = [
            'nama' => $p['nama'],
            'gambar' => $p['gambar'],
            'harga' => $harga,
            'qty' => $qty,
            'stok_tersedia' => $p['stok']
        ];

        // Cek stok
        if ($qty > $p['stok']) {
            $stock_issues = true;
            echo "<script>alert('Maaf, jumlah produk " . htmlspecialchars($p['nama']) . " yang Anda minta melebihi stok tersedia. Harap sesuaikan keranjang Anda.'); window.location.href='keranjang.php';</script>";
            exit; // Hentikan proses jika ada masalah stok
        }
    } else {
        // Produk tidak ditemukan, mungkin sudah dihapus dari database
        unset($_SESSION['cart'][$id]); // Hapus dari keranjang
        echo "<script>alert('Beberapa produk di keranjang Anda tidak ditemukan atau sudah dihapus. Keranjang telah diperbarui.'); window.location.href='keranjang.php';</script>";
        exit;
    }
}

// Hitung Diskon Voucher
$diskon = 0;
$voucher_status = '';
$applied_voucher = '';

if (isset($_POST['voucher'])) {
    $input_voucher = strtoupper(trim($_POST['voucher']));
    if ($input_voucher == "DISKON10") {
        $diskon = $total * 0.10;
        $voucher_status = '<div class="alert alert-success mt-2 py-2"><i class="fas fa-check-circle me-1"></i> Voucher "DISKON10" berhasil diterapkan!</div>';
        $applied_voucher = $input_voucher;
    } else if (!empty($input_voucher)) {
        $voucher_status = '<div class="alert alert-warning mt-2 py-2"><i class="fas fa-exclamation-triangle me-1"></i> Voucher tidak valid atau sudah kadaluarsa.</div>';
    }
}

$grand_total = $total - $diskon;

// Proses Pesanan setelah hitung total jika sudah diklik "Lanjutkan ke WhatsApp"
if (isset($_POST['process_order'])) {
    if (empty($_POST['nama']) || empty($_POST['alamat'])) {
        echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-1"></i> Nama lengkap dan alamat wajib diisi.</div>';
    } else {
        // Final check stok sebelum decrement dan kirim WA
        $can_proceed = true;
        foreach ($cart_details as $id => $item) {
            $q_current_stok = mysqli_query($conn, "SELECT stok FROM produk WHERE id='$id'");
            $data_current_stok = mysqli_fetch_assoc($q_current_stok);
            if (!$data_current_stok || $item['qty'] > $data_current_stok['stok']) {
                $can_proceed = false;
                echo "<script>alert('Maaf, stok produk " . htmlspecialchars($item['nama']) . " tidak mencukupi lagi. Harap sesuaikan keranjang Anda.'); window.location.href='keranjang.php';</script>";
                exit;
            }
        }

        if ($can_proceed) {
            // Decrement stok di database
            foreach ($cart_details as $id => $item) {
                $new_stok = $item['stok_tersedia'] - $item['qty'];
                mysqli_query($conn, "UPDATE produk SET stok = '$new_stok' WHERE id='$id'");
            }

            // Format pesan WA
            $pesan = "Halo Admin SalmaGlow, saya *" . urlencode($_POST['nama']) . "* ingin order:%0A%0A";
            foreach ($cart_details as $id => $item) {
                $pesan .= "- " . urlencode($item['nama']) . " (x" . $item['qty'] . ") - Rp " . urlencode(number_format($item['harga'] * $item['qty'], 0, ',', '.')) . "%0A";
            }
            $pesan .= "%0ATotal Belanja: *Rp " . urlencode(number_format($total, 0, ',', '.')) . "*%0A";
            if ($diskon > 0) {
                $pesan .= "Diskon Voucher (" . urlencode($applied_voucher) . "): *Rp " . urlencode(number_format($diskon, 0, ',', '.')) . "*%0A";
            }
            $pesan .= "TOTAL BAYAR: *Rp " . urlencode(number_format($grand_total, 0, ',', '.')) . "*%0A%0A";
            $pesan .= "Alamat Pengiriman: " . urlencode($_POST['alamat']) . "%0A%0ATerima kasih!";

            // Hapus keranjang setelah pesanan diproses
            unset($_SESSION['cart']);

            // Redirect ke WhatsApp
            header("Location: https://wa.me/" . $wa_admin . "?text=" . $pesan);
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - SalmaGlow</title>
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
                <!-- Search Bar Placeholder -->
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
            <h1>Langkah Terakhir: Checkout Pesanan Anda</h1>
            <p class="lead">Isi detail pengiriman dan konfirmasi pesananmu.</p>
        </div>
    </section>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="checkout-card p-4">
                    <h3 class="mb-4 text-center" style="color: #ff69b4;">Detail Pengiriman & Pembayaran</h3>
                    <form method="POST" action="checkout.php">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" id="nama" name="nama" class="form-control" value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea id="alamat" name="alamat" class="form-control" rows="3" required><?= isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : ''; ?></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="voucher" class="form-label">Kode Voucher (opsional)</label>
                            <div class="input-group">
                                <input type="text" id="voucher" name="voucher" class="form-control" placeholder="Masukkan kode voucher" value="<?= isset($_POST['voucher']) ? htmlspecialchars($_POST['voucher']) : ''; ?>">
                                <button class="btn btn-outline-secondary" type="submit" name="apply_voucher"><i class="fas fa-tag me-1"></i> Terapkan</button>
                            </div>
                            <?= $voucher_status; // Tampilkan status voucher ?>
                        </div>

                        <hr>
                        <h4 class="mb-3 text-center">Ringkasan Pesanan</h4>
                        <div class="summary-details mb-4">
                            <p class="d-flex justify-content-between">
                                <span>Total Produk:</span>
                                <span>Rp <?= number_format($total, 0, ',', '.'); ?></span>
                            </p>
                            <p class="d-flex justify-content-between">
                                <span>Diskon Voucher:</span>
                                <span class="text-success fw-bold">- Rp <?= number_format($diskon, 0, ',', '.'); ?></span>
                            </p>
                            <h5 class="d-flex justify-content-between fw-bold">
                                <span>TOTAL BAYAR:</span>
                                <span style="color: #ff69b4;">Rp <?= number_format($grand_total, 0, ',', '.'); ?></span>
                            </h5>
                        </div>

                        <button class="btn btn-pink w-100 py-2" type="submit" name="process_order">
                            <i class="fab fa-whatsapp me-2"></i> Lanjutkan ke Pembayaran (WhatsApp)
                        </button>
                    </form>
                </div>
            </div>
        </div>
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