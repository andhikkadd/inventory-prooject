<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'petugas') {
    echo "<script>alert('Akses ditolak!');window.location='../login.php';</script>";
    exit;
}

require_once '../config/db.php';
include '../components/navbar.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h2 class="my-4">Selamat datang, Petugas!</h2>

    <div class="row">
        <div class="col-md-6">
            <div class="card text-bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Barang Masuk</h5>
                    <p class="card-text">Catat barang yang masuk ke gudang.</p>
                    <a href="barang_masuk.php" class="btn btn-light">Akses</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-bg-secondary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Barang Keluar</h5>
                    <p class="card-text">Catat barang yang keluar dari gudang.</p>
                    <a href="barang_keluar.php" class="btn btn-light">Akses</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
