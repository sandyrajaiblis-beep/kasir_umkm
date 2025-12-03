<?php
require_once 'api/db.php';

// --- Inisialisasi Variabel ---
 $pendapatanHariIni = 0;
 $jumlahTransaksiHariIni = 0;
 $produkTerlaris = 'Belum ada';
 $jumlahStokMenipis = 0;
 $transaksiTerbaru = [];

try {
    // 1. Pendapatan & Transaksi Hari Ini
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(total_harga), 0) as total, COUNT(id) as count FROM penjualan WHERE DATE(tanggal_waktu) = CURDATE()");
    $stmt->execute();
    $todayStats = $stmt->fetch(PDO::FETCH_ASSOC);
    $pendapatanHariIni = (float)$todayStats['total'];
    $jumlahTransaksiHariIni = (int)$todayStats['count'];

    // 2. Produk Terlaris (berdasarkan jumlah terjual)
    $stmt = $pdo->query("
        SELECT p.nama_produk 
        FROM detail_penjualan dp 
        JOIN produk p ON dp.produk_id = p.id 
        GROUP BY p.id 
        ORDER BY SUM(dp.jumlah) DESC 
        LIMIT 1
    ");
    if ($stmt->rowCount() > 0) {
        $produkTerlaris = $stmt->fetchColumn();
    }

    // 3. Jumlah Stok Menipis
    $stmt = $pdo->prepare("SELECT COUNT(id) FROM produk WHERE stok < 10");
    $stmt->execute();
    $jumlahStokMenipis = (int)$stmt->fetchColumn();

    // 4. Transaksi Terbaru Hari Ini
    $stmt = $pdo->prepare("
        SELECT p.tanggal_waktu, p.id as nota, pl.nama as pelanggan, p.total_harga 
        FROM penjualan p 
        LEFT JOIN pelanggan pl ON p.pelanggan_id = pl.id 
        WHERE DATE(p.tanggal_waktu) = CURDATE() 
        ORDER BY p.tanggal_waktu DESC 
        LIMIT 5
    ");
    $transaksiTerbaru = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Dalam produksi, log error ini
    // die("Error: " . $e->getMessage());
}
?>

<!-- Konten Dashboard -->
<div class="container-fluid">
    <!-- Kartu Metrik di Atas -->
    <div class="row mb-4">
        <!-- Kartu Pendapatan -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Pendapatan Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?php echo number_format($pendapatanHariIni, 0, ',', '.'); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cash-stack fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kartu Transaksi -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Jumlah Transaksi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $jumlahTransaksiHariIni; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-receipt-cutoff fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kartu Produk Terlaris -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Produk Terlaris</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo htmlspecialchars($produkTerlaris); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kartu Stok Menipis -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Stok Menipis</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $jumlahStokMenipis; ?> Produk</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik dan Tabel di Bawah -->
    <div class="row">
        <!-- Grafik Penjualan -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold">Penjualan 7 Hari Terakhir</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Transaksi Terbaru -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">Transaksi Terbaru Hari Ini</h6>
                </div>
                <div class="card-body">
                    <?php if (count($transaksiTerbaru) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Waktu</th>
                                        <th>No. Nota</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transaksiTerbaru as $transaksi): ?>
                                        <tr>
                                            <td><?php echo date('H:i', strtotime($transaksi['tanggal_waktu'])); ?></td>
                                            <td><?php echo htmlspecialchars($transaksi['nota']); ?></td>
                                            <td>Rp <?php echo number_format($transaksi['total_harga'], 0, ',', '.'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Belum ada transaksi hari ini.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>