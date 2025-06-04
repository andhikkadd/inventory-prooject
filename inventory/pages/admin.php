<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Akses ditolak!');window.location='../login.php';</script>";
    exit;
}

require_once '../config/db.php';
include '../pages/navbar.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f3f5;
        }

        .dashboard-header {
            margin-top: 40px;
            margin-bottom: 30px;
            text-align: center;
        }

        .card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
        }

        .card i {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .btn-light {
            font-weight: 500;
            border-radius: 8px;
        }

        .mini-stats {
            font-size: 0.9rem;
            color: #ffffffcc;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="dashboard-header">
        <h2 class="fw-bold">Hai, Admin 👨‍💻</h2>
        <p class="text-muted">Selamat datang di Dashboard Inventory System</p>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white p-4">
                <div class="card-body text-center">
                    <i class="fas fa-box"></i>
                    <h5 class="card-title mt-2">Kelola Barang</h5>
                    <p class="card-text">Tambah, edit, dan hapus data barang.</p>
                    <p class="mini-stats">Total barang: 120+</p>
                    <a href="kelola_barang.php" class="btn btn-light mt-2">Kelola</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white p-4">
                <div class="card-body text-center">
                    <i class="fas fa-users-cog"></i>
                    <h5 class="card-title mt-2">Kelola User</h5>
                    <p class="card-text">Atur user dan role mereka.</p>
                    <p class="mini-stats">Total user: 8</p>
                    <a href="kelola_user.php" class="btn btn-light mt-2">Atur</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark p-4">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line"></i>
                    <h5 class="card-title mt-2">Laporan</h5>
                    <p class="card-text">Lihat dan cetak laporan stok & transaksi.</p>
                    <p class="mini-stats">Laporan minggu ini: 5</p>
                    <a href="laporan.php" class="btn btn-dark mt-2">Lihat</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CDN Bootstrap & FontAwesome -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
