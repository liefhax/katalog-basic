<?php
session_start();
include 'koneksi.php';

// Ambil ID produk dari parameter URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id == 0) {
    // Jika tidak ada ID atau ID tidak valid, arahkan kembali ke halaman utama
    header("Location: index.php");
    exit;
}

// Query untuk mendapatkan detail produk
$query_product = mysqli_query($conn, "SELECT * FROM produk WHERE id='$product_id'");
$product = mysqli_fetch_assoc($query_product);

if (!$product) {
    // Jika produk tidak ditemukan, arahkan kembali ke halaman utama
    header("Location: index.php");
    exit;
}

// Logic tambah ke keranjang (sama seperti di index.php)
if (isset($_GET['add_cart_detail'])) {
    $id_to_add = $_GET['add_cart_detail'];

    // Ambil stok produk dari database
    $q_stok = mysqli_query($conn, "SELECT stok FROM produk WHERE id='$id_to_add'");
    $data_stok = mysqli_fetch_assoc($q_stok);
    $stok_tersedia = $data_stok ? $data_stok['stok'] : 0;

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $current_qty_in_cart = isset($_SESSION['cart'][$id_to_add]) ? $_SESSION['cart'][$id_to_add] : 0;

    if ($current_qty_in_cart < $stok_tersedia) {
        $_SESSION['cart'][$id_to_add]++;
        echo "<script>alert('Produk berhasil ditambahkan ke keranjang!'); window.location.href='detail_produk.php?id=" . htmlspecialchars($product_id) . "';</script>";
        exit;
    } else {
        echo "<script>alert('Maaf, stok produk ini tidak mencukupi.'); window.location.href='detail_produk.php?id=" . htmlspecialchars($product_id) . "';</script>";
        exit;
    }
}

// Variabel untuk memudahkan
$product_name = htmlspecialchars($product['nama']);
$product_desc = nl2br(htmlspecialchars($product['deskripsi'])); // nl2br untuk baris baru dari textarea
$product_price = $product['harga'];
$product_discount_price = $product['harga_diskon'];
$product_image = htmlspecialchars($product['gambar']);
$product_stock = $product['stok'];

$is_out_of_stock = ($product_stock <= 0);

$display_price = $product_discount_price > 0 ? $product_discount_price : $product_price;

// Hitung jumlah item di keranjang untuk tampilan navbar
$cart_item_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $product_name; ?> - SalmaGlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        /* Gaya khusus untuk halaman detail produk */
        .product-detail-card {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            padding: 40px;
            margin-bottom: 50px;
        }
        .product-image-container {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            background-color: #fce8f0; /* Light pink background */
        }
        .product-image-container img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .product-info h1 {
            color: #ff69b4;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .product-info .price-section {
            font-size: 1.8rem;
            margin-bottom: 20px;
        }
        .product-info .price-section .original-price {
            text-decoration: line-through;
            color: #dc3545; /* Bootstrap red for striking out */
            font-size: 1.3rem;
            margin-right: 10px;
        }
        .product-info .price-section .discount-price {
            color: #28a745; /* Bootstrap green for discount price */
            font-weight: bold;
        }
        .product-info .price-section .normal-price {
             color: #6c757d;
             font-weight: bold;
        }
        .product-info .stock-info {
            font-size: 1.1rem;
            margin-bottom: 25px;
            font-weight: 500;
        }
        .product-info .description-header {
            color: #ff69b4;
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 15px;
            border-bottom: 2px solid #ffeef4;
            padding-bottom: 10px;
        }
        .product-info .product-description {
            line-height: 1.8;
            color: #495057;
            text-align: justify;
        }
        .btn-pink {
            background-color: #ff69b4;
            border-color: #ff69b4;
            color: white;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .btn-pink:hover {
            background-color: #e05e9a;
            border-color: #e05e9a;
            color: white;
            transform: translateY(-2px);
        }
        .btn-secondary-outline {
            color: #6c757d;
            border-color: #6c757d;
            background-color: transparent;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .btn-secondary-outline:hover {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">SalmaGlow</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="fas fa-home me-1"></i> Beranda</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="keranjang.php">
                            <i class="fas fa-shopping-cart"></i> Keranjang (<span id="cart-count"><?= $cart_item_count; ?></span>)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-light ms-lg-3" href="admin_login.php">
                            <i class="fas fa-user-shield"></i> Admin Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="page-header">
        <div class="container">
            <h1>Detail Produk</h1>
            <p class="lead">Informasi lengkap tentang <?= $product_name; ?></p>
        </div>
    </section>

    <div class="container py-5">
        <div class="product-detail-card">
            <div class="row">
                <div class="col-md-5">
                    <div class="product-image-container">
                        <img src="assets/img/<?= $product_image; ?>" class="img-fluid" alt="<?= $product_name; ?>">
                    </div>
                </div>
                <div class="col-md-7 product-info mt-4 mt-md-0">
                    <h1><?= $product_name; ?></h1>
                    
                    <div class="price-section">
                        <?php if ($product_discount_price > 0): ?>
                            <span class="original-price">Rp <?= number_format($product_price, 0, ',', '.'); ?></span>
                            <span class="discount-price">Rp <?= number_format($product_discount_price, 0, ',', '.'); ?></span>
                        <?php else: ?>
                            <span class="normal-price">Rp <?= number_format($product_price, 0, ',', '.'); ?></span>
                        <?php endif; ?>
                    </div>

                    <p class="stock-info">
                        Stok Tersedia: 
                        <span class="<?= ($product_stock <= 5 && $product_stock > 0) ? 'text-warning fw-bold' : ''; ?>
                                    <?= ($product_stock == 0) ? 'text-danger fw-bold' : 'text-success'; ?>">
                            <?= $product_stock; ?>
                            <?= ($product_stock == 0) ? ' (Habis)' : (($product_stock <= 5 && $product_stock > 0) ? ' (Segera Habis)' : ''); ?>
                        </span>
                    </p>

                    <?php if ($is_out_of_stock): ?>
                        <button class="btn btn-danger btn-lg mt-3" disabled><i class="fas fa-times-circle me-2"></i> Stok Habis</button>
                    <?php else: ?>
                        <a href="detail_produk.php?id=<?= htmlspecialchars($product_id); ?>&add_cart_detail=<?= htmlspecialchars($product_id); ?>" class="btn btn-pink btn-lg mt-3">
                            <i class="fas fa-cart-plus me-2"></i> Tambah ke Keranjang
                        </a>
                    <?php endif; ?>
                    <a href="index.php" class="btn btn-secondary-outline btn-lg mt-3 ms-2"><i class="fas fa-arrow-left me-2"></i> Kembali Belanja</a>

                    <h4 class="description-header mt-5">Deskripsi Produk</h4>
                    <p class="product-description">
                        <?= $product_desc; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

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