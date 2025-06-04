<?php
if (!isset($_SESSION)) session_start();
$username = $_SESSION['username'] ?? 'User';
$role = $_SESSION['role'] ?? 'guest';
?>

<nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm mb-4">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold text-primary" href="#">
      <i class="fas fa-box-open me-2"></i>Inventory App
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <?php if ($role == 'admin'): ?>
          <li class="nav-item"><a class="nav-link text-dark" href="admin.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link text-dark" href="kelola_barang.php">Kelola Barang</a></li>
          <li class="nav-item"><a class="nav-link text-dark" href="kelola_user.php">Kelola User</a></li>
          <li class="nav-item"><a class="nav-link text-dark" href="laporan.php">Laporan</a></li>
        <?php elseif ($role == 'petugas'): ?>
          <li class="nav-item"><a class="nav-link text-dark" href="petugas.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link text-dark" href="barang_masuk.php">Barang Masuk</a></li>
          <li class="nav-item"><a class="nav-link text-dark" href="barang_keluar.php">Barang Keluar</a></li>
        <?php elseif ($role == 'viewer'): ?>
          <li class="nav-item"><a class="nav-link text-dark" href="viewer.php">Dashboard</a></li>
        <?php endif; ?>
      </ul>

      <ul class="navbar-nav">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-primary fw-semibold" href="#" role="button" data-bs-toggle="dropdown">
            <i class="fas fa-user-circle me-1"></i> <?= ucfirst($username) ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profil</a></li>
            <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
