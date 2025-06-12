<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'petugas') {
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
    <title>Inventory App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body {
    background: #f6f8fa;
    min-height: 100vh;
}

.dashboard-header {
    margin-top: 2.5rem;
    margin-bottom: 2.5rem;
    text-align: center;
}

.dashboard-header h2 {
    font-weight: 700;
    color: #22223b;
}

.dashboard-header p {
    color: #6c757d;
    font-size: 1.1rem;
}

.card-dashboard {
    border: none;
    border-radius: 1.1rem;
    background: #fff;
    box-shadow: 0 2px 12px rgba(34,34,59,0.07);
    transition: box-shadow 0.2s, transform 0.2s;
    cursor: pointer;
    padding: 2rem;
}

.card-dashboard:hover {
    box-shadow: 0 6px 24px rgba(34,34,59,0.13);
    transform: translateY(-4px) scale(1.02);
}

.dashboard-icon {
    font-size: 2.5rem;
    color: #495af0;
    margin-bottom: 1rem;
}

.dashboard-icon.out {
    color: #2ec4b6;
}

.card-title {
    font-weight: 600;
    color: #22223b;
}

.card-text {
    color: #6c757d;
}

.btn-dashboard {
    background: #f6f8fa;
    color: #495af0;
    border: none;
    font-weight: 500;
    border-radius: 2rem;
    padding: 0.4rem 1.5rem;
    transition: background 0.2s, color 0.2s;
}

.btn-dashboard:hover {
    background: #495af0;
    color: #fff;
}

@media (max-width: 767px) {
    body {
        padding: 0;
    }

    .container {
        padding: 1rem;
    }

    .dashboard-header {
        margin-top: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .dashboard-header h2 {
        font-size: 1.5rem;
    }

    .dashboard-header p {
        font-size: 1rem;
    }

    .col-md-5 {
        flex: 0 0 100%;
        max-width: 75%;
        padding-left: 0.5px;
        padding-right: 0.5rem;
    }

    .card-dashboard {
        padding: 1.2rem;
        border-radius: 1rem;
    }

    .dashboard-icon {
        font-size: 2rem;
    }

    .card-title {
        font-size: 1rem;
    }

    .card-text {
        font-size: 0.9rem;
    }

    .btn-dashboard {
        font-size: 0.85rem;
        padding: 0.35rem 1.2rem;
    }
}

</style>

</head>
<body>
<div class="container px-3">
    <div class="dashboard-header text-center">
        <h2>Selamat Datang, Petugas</h2>
        <p>Silakan pilih menu untuk mengelola barang di gudang.</p>
    </div>
    <div class="row justify-content-center g-4 my-5">
        <div class="col-md-5">
            <a href="barang_masuk.php" style="text-decoration:none;">
                <div class="card card-dashboard text-center p-4 h-100">
                    <div class="dashboard-icon">
                        <i class="bi bi-box-arrow-in-down"></i>
                    </div>
                    <h5 class="card-title mb-2">Barang Masuk</h5>
                    <p class="card-text mb-3">Catat barang yang masuk ke gudang.</p>
                    <span class="btn btn-dashboard">Masuk</span>
                </div>
            </a>
        </div>
        <div class="col-md-5">
            <a href="barang_keluar.php" style="text-decoration:none;">
                <div class="card card-dashboard text-center p-4 h-100">
                    <div class="dashboard-icon out">
                        <i class="bi bi-box-arrow-up"></i>
                    </div>
                    <h5 class="card-title mb-2">Barang Keluar</h5>
                    <p class="card-text mb-3">Catat barang yang keluar dari gudang.</p>
                    <span class="btn btn-dashboard" style="color:#2ec4b6;">Keluar</span>
                </div>
            </a>
        </div>
        <div class="col-md-5">
            <a href="../pages/laporan.php" style="text-decoration:none;">
                <div class="card card-dashboard text-center p-4 h-100">
                    <div class="dashboard-icon" style="color:#f59e42;">
                        <i class="bi bi-clipboard-data"></i>
                    </div>
                    <h5 class="card-title mb-2">Laporan</h5>
                    <p class="card-text mb-3">Lihat data barang dan riwayat aktivitas.</p>
                    <span class="btn btn-dashboard" style="color:#f59e42;">Lihat</span>
                </div>
            </a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>