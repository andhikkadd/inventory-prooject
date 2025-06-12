<?php
session_start();
require_once '../config/db.php';
include '../pages/navbar.php';

$barang = $conn->query("SELECT * FROM items ORDER BY id DESC");

$barangArr = [];
$resBarang = $conn->query("SELECT id, nama_barang FROM items");
while($b = $resBarang->fetch_assoc()) {
    $barangArr[$b['id']] = $b['nama_barang'];
}

$log = $conn->query("SELECT l.*, u.username, u.role 
    FROM log_aktivitas l 
    JOIN users u ON l.user_id = u.id 
    WHERE u.role != 'admin'
    ORDER BY l.tanggal ASC LIMIT 100");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Inventory App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(120deg, #f8fafc 60%, #e3e9f7 100%); min-height: 100vh; }
        .card { border-radius: 1.2rem; box-shadow: 0 4px 24px rgba(34,34,59,0.07); border: none; }
        .table { background: #fff; border-radius: 5px; overflow: hidden; }
        .table thead th { background: #f0f2f5; color: #495af0; font-weight: 600; border-bottom: 2px solid #e3e9f7; }
        .table-striped > tbody > tr:nth-of-type(odd) { background-color: #f8fafc; }
        .table-hover tbody tr:hover { background-color: #e9ecef; }
        .btn-print { background: #f59e42; color: #fff; border: none; }
        .btn-print:hover { background: #d97706; color: #fff; }
        .card-title-custom {
            font-size: 1.15rem;
            font-weight: 600;
            margin: 0;
            padding: 1.2rem 1.5rem 0.5rem 1.5rem;
            background: none !important;
            border: none !important;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            #printArea, #printArea * {
                visibility: visible;
            }
            #printArea {
                position: absolute;
                left: 0;
                top: 0;
                display: block;
                width: 100%;
            }
        }
        #printArea {
            display: none;
        }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4 no-print">
        <div class="d-flex align-items-center gap-2">
            <a 
            <?php if ($_SESSION['role'] == 'admin'): ?> href="../admin/admin.php"<?php endif; ?>
            <?php if ($_SESSION['role'] == 'petugas'): ?> href="../petugas/petugas.php"<?php endif; ?>
             class="btn btn-primary me-2"><i class="bi bi-arrow-left"></i></a>
            <h3 class="mb-0">Laporan Data</h3>
        </div>
        <button class="btn btn-print" onclick="printDiv('printArea')"><i class="bi bi-printer"></i> Print</button>
    </div>

    <div id="printArea">
        <h2>Data Barang</h2>
        <table border="1" cellspacing="0" cellpadding="5" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Id Barang</th>
                    <th>Nama Barang</th>
                    <th>Distributor</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $query = mysqli_query($conn, "SELECT * FROM items ORDER BY id DESC");
                while ($data = mysqli_fetch_assoc($query)) {
                    echo "<tr>
                            <td>{$no}</td>
                            <td>{$data['id']}</td>
                            <td>{$data['nama_barang']}</td>
                            <td>{$data['distributor']}</td>
                            <td>" . number_format($data['harga'], 2, ',', '.') . "</td>
                            <td>{$data['jumlah']}</td>
                        </tr>";
                    $no++;
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="card-title-custom">Data Barang</div>
    <div class="card shadow-sm mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Barang</th>
                            <th>Distributor</th>
                            <th>Jumlah (Kardus)</th>
                            <th>Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($barang->num_rows > 0): ?>
                            <?php while($b = $barang->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $b['id'] ?></td>
                                    <td><?= htmlspecialchars($b['nama_barang']) ?></td>
                                    <td><?= htmlspecialchars($b['distributor']) ?></td>
                                    <td><?= $b['jumlah'] ?></td>
                                    <td>Rp. <?= number_format($b['harga'], 2, ',', '.') ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center text-secondary">Tidak ada data barang.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card-title-custom">Log Aktivitas Terakhir</div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>User</th>
                            <th>Aktivitas</th>
                            <th>Barang</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($log->num_rows > 0): ?>
                            <?php while($l = $log->fetch_assoc()):
                                $action = $l['action'];
                                $jenis = '';
                                $namaBarang = '';
                                $jumlah = '';
                                if (stripos($action, 'barang masuk') !== false) {
                                    $jenis = 'Barang Masuk';
                                    if (preg_match('/ID Barang: ([\w\-]+)/i', $action, $match) && isset($match[1])) {
                                        $idBarang = $match[1];
                                        $namaBarang = isset($barangArr[$idBarang]) ? $barangArr[$idBarang] : '-';
                                    }
                                    if (preg_match('/jumlah: (\d+)/i', $action, $matchJumlah) && isset($matchJumlah[1])) {
                                        $jumlah = $matchJumlah[1];
                                    }
                                } elseif (stripos($action, 'barang keluar') !== false) {
                                    $jenis = 'Barang Keluar';
                                    if (preg_match('/ID Barang: ([\w\-]+)/i', $action, $match) && isset($match[1])) {
                                        $idBarang = $match[1];
                                        $namaBarang = isset($barangArr[$idBarang]) ? $barangArr[$idBarang] : '-';
                                    }
                                    if (preg_match('/jumlah: (\d+)/i', $action, $matchJumlah) && isset($matchJumlah[1])) {
                                        $jumlah = $matchJumlah[1];
                                    }
                                } elseif (stripos($action, 'mengubah') !== false || stripos($action, 'mengedit') !== false) {
                                    $jenis = 'Update Barang';
                                    if (preg_match('/ID Barang: ([\w\-]+)/i', $action, $match) && isset($match[1])) {
                                        $idBarang = $match[1];
                                        $namaBarang = isset($barangArr[$idBarang]) ? $barangArr[$idBarang] : '-';
                                    }
                                    if (stripos($action, 'harga') !== false) {
                                        $jumlah = 'Harga diubah';
                                    } elseif (stripos($action, 'stok') !== false) {
                                        $jumlah = 'Stok diubah';
                                    }
                                } elseif (stripos($action, 'menghapus') !== false) {
                                    $jenis = 'Hapus Barang';
                                    if (preg_match('/ID Barang: ([\w\-]+)/i', $action, $match) && isset($match[1])) {
                                        $idBarang = $match[1];
                                        $namaBarang = isset($barangArr[$idBarang]) ? $barangArr[$idBarang] : '-';
                                    }
                                    $jumlah = '-';
                                } else {
                                    $jenis = 'Lainnya';
                                }
                            ?>
                            <tr>
                                <td><?= !empty($l['tanggal']) ? date('d-m-Y', strtotime($l['tanggal'])) : '-' ?></td>
                                <td><?= htmlspecialchars($l['username']) ?></td>
                                <td><?= htmlspecialchars($jenis) ?></td>
                                <td><?= htmlspecialchars($namaBarang) ?></td>
                                <td><?= htmlspecialchars($jumlah) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center text-secondary">Belum ada log aktivitas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function printDiv(divId) {
    var printContents = document.getElementById(divId).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
}
</script>
</body>
</html>
