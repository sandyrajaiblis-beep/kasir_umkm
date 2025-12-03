<?php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>POS UMKM - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <nav id="sidebar" class="bg-dark text-white">
        <div class="sidebar-header p-3 text-center">
            <h4><i class="bi bi-shop"></i> POS UMKM</h4>
            <!-- Info user diubah menjadi statis -->
            <small>Administrator</small>
        </div>
        <ul class="list-unstyled components">
            <li><a href="#" data-section="dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li><a href="#" data-section="transaction"><i class="bi bi-cart-check"></i> Transaksi</a></li>
            <li><a href="#" data-section="products"><i class="bi bi-box-seam"></i> Stok Barang</a></li>
            <li><a href="#" data-section="customers"><i class="bi bi-people"></i> Pelanggan</a></li>
            <li><a href="#" data-section="sales"><i class="bi bi-receipt"></i> Riwayat Penjualan</a></li>
            <!-- Menu Manajemen User sekarang selalu visible -->
            <li><a href="#" data-section="users"><i class="bi bi-person-gear"></i> Manajemen User</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div id="content" class="w-100">
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container-fluid">
                <h4 class="mb-0" id="page-title">Dashboard</h4>
            </div>
        </nav>
        <div class="container-fluid p-4">
            <!-- Konten Dinamis Akan Dimuat Di Sini oleh JavaScript -->
            <div id="dynamic-content">
                <h1>Selamat datang di Dashboard!</h1>
            </div>
        </div>
    </div>
</div>

<!-- Modal Container -->
<div id="modal-container"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data user diubah menjadi statis, tidak lagi dari PHP session
    const currentUser = {
        id: null, // Tidak ada ID user
        username: 'guest',
        nama_lengkap: 'Administrator',
        role: 'admin' // Anggap semua user sebagai admin
    };
    
</script>
<script src="assets/js/script.js"></script>
</body>
</html>