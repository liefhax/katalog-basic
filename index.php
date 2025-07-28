<?php
session_start();
include 'koneksi.php';

// Tambah ke keranjang jika ada add_cart
if (isset($_GET['add_cart'])) {
    $id = $_GET['add_cart'];

    // Ambil stok produk dari database
    $q_stok = mysqli_query($conn, "SELECT stok FROM produk WHERE id='$id'");
    $data_stok = mysqli_fetch_assoc($q_stok);
    $stok_tersedia = $data_stok ? $data_stok['stok'] : 0;

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $current_qty_in_cart = isset($_SESSION['cart'][$id]) ? $_SESSION['cart'][$id] : 0;

    if ($current_qty_in_cart < $stok_tersedia) {
        $_SESSION['cart'][$id]++;
        // Redirect agar tidak ada reload berulang
        header("Location: index.php"); // Kembali ke halaman index setelah menambah ke keranjang
        exit;
    } else {
        // Jika stok tidak cukup, bisa tampilkan pesan error
        echo "<script>alert('Maaf, stok produk ini tidak mencukupi.'); window.location.href='index.php';</script>";
        exit;
    }
}

// Handle search functionality
$search_query = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
    $query = "SELECT * FROM produk WHERE nama LIKE '%$search_query%' ORDER BY id DESC";
} else {
    $query = "SELECT * FROM produk ORDER BY id DESC";
}

$result = mysqli_query($conn, $query);

// Periksa error query database
if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SalmaGlow - Produk Kecantikan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css"> <!-- LINK KE FILE STYLE.CSS BARU ANDA -->
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
                <!-- Bagian kiri navbar: Search Bar -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <form class="d-flex" action="index.php" method="GET">
                            <div class="input-group">
                                <input class="form-control me-2" type="search" placeholder="Cari produk..." aria-label="Search" name="search" value="<?= htmlspecialchars($search_query); ?>">
                                <button class="btn btn-outline-light" type="submit"><i class="fas fa-search"></i></button>
                            </div>
                        </form>
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

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1>Sempurnakan Kecantikanmu Bersama SalmaGlow</h1>
            <p class="lead">Temukan koleksi perawatan kulit dan kosmetik premium yang membuatmu bersinar.</p>
            <a href="#products" class="btn btn-light btn-lg">Jelajahi Produk Kami <i class="fas fa-arrow-down ms-2"></i></a>
        </div>
    </section>

    <!-- Produk Section -->
    <section id="products" class="py-5">
        <div class="container">
            <h2 class="product-section-title">Katalog Produk SalmaGlow</h2>
            <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="row g-4">
                <?php while ($row = mysqli_fetch_assoc($result)):
                    $stok_produk = $row['stok'];
                    $is_out_of_stock = ($stok_produk <= 0);
                ?>
<div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
    <div class="card shadow-sm <?= $is_out_of_stock ? 'border-danger' : ''; ?>">
        <a href="detail_produk.php?id=<?= htmlspecialchars($row['id']); ?>">
            <img src="assets/img/<?php echo htmlspecialchars($row['gambar']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['nama']); ?>">
        </a>
        <div class="card-body text-center d-flex flex-column">
            <h5 class="card-title">
                <a href="detail_produk.php?id=<?= htmlspecialchars($row['id']); ?>" class="text-decoration-none text-dark">
                    <?= htmlspecialchars($row['nama']); ?>
                </a>
            </h5>
            <p class="text-muted small flex-grow-1"><?= substr(htmlspecialchars($row['deskripsi']), 0, 80); ?><?php echo (strlen($row['deskripsi']) > 80) ? '...' : ''; ?></p>
            <?php if ($is_out_of_stock): ?>
                <button class="btn btn-secondary btn-sm mt-3" disabled><i class="fas fa-times-circle me-1"></i> Stok Habis</button>
            <?php else: ?>
                <a href="index.php?add_cart=<?= htmlspecialchars($row['id']); ?>" class="btn btn-primary btn-sm mt-3">
                    <i class="fas fa-cart-plus me-1"></i> Tambah Keranjang
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
                <div class="alert alert-warning text-center" role="alert">
                    Maaf, tidak ada produk ditemukan.
                </div>
            <?php endif; ?>
        </div>
    </section>

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