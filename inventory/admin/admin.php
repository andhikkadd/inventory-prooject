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
        .dashboard-icon.green {
            color: #2ec4b6;
        }
        .dashboard-icon.orange {
            color: #f59e42;
        }
        .card-title {
            font-weight: 600;
            color: #22223b;
        }
        .card-text {
            color: #6c757d;
            margin-bottom: 40px;
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
            .dashboard-header { margin-top: 1.5rem; margin-bottom: 1.5rem;}
            .col-md-4 {
                max-width: 75%;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="dashboard-header text-center">
        <h2>Hai, Admin 👨‍💻</h2>
        <p>Selamat datang di Dashboard Inventory System</p>
    </div>
    <div class="row justify-content-center g-4 my-5">
        <div class="col-md-4">
            <a href="kelola_barang.php" style="text-decoration:none;">
                <div class="card card-dashboard text-center p-4 h-100">
                    <div class="dashboard-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <h5 class="card-title mb-2">Kelola Barang</h5>
                    <p class="card-text">Tambah, edit, dan hapus data barang.</p>
                    <span class="btn btn-dashboard">Kelola</span>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="kelola_user.php" style="text-decoration:none;">
                <div class="card card-dashboard text-center p-4 h-100">
                    <div class="dashboard-icon green">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h5 class="card-title mb-2">Kelola User</h5>
                    <p class="card-text">Atur user dan role mereka.</p>
                    <span class="btn btn-dashboard user" style="color:#2ec4b6;">Atur</span>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="../pages/laporan.php" style="text-decoration:none;">
                <div class="card card-dashboard text-center p-4 h-100">
                    <div class="dashboard-icon orange">
                        <i class="bi bi-bar-chart-line-fill"></i>
                    </div>
                    <h5 class="card-title mb-2">Laporan</h5>
                    <p class="card-text">Lihat dan cetak laporan stok.</p>
                    <span class="btn btn-dashboard laporan" style="color:#f59e42;">Lihat</span>
                </div>
            </a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>