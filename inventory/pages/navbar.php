<?php
if (!isset($_SESSION)) session_start();
$username = $_SESSION['username'] ?? 'User';
$role = $_SESSION['role'] ?? 'guest';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Inventory App</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <?php if ($role == 'admin'): ?>
          <li class="nav-item"><a class="nav-link" href="admin.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="kelola_barang.php">Kelola Barang</a></li>
          <li class="nav-item"><a class="nav-link" href="kelola_user.php">Kelola User</a></li>
          <li class="nav-item"><a class="nav-link" href="laporan.php">Laporan</a></li>
        <?php elseif ($role == 'petugas'): ?>
          <li class="nav-item"><a class="nav-link" href="petugas.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="barang_masuk.php">Barang Masuk</a></li>
          <li class="nav-item"><a class="nav-link" href="barang_keluar.php">Barang Keluar</a></li>
        <?php elseif ($role == 'viewer'): ?>
          <li class="nav-item"><a class="nav-link" href="viewer.php">Dashboard</a></li>
        <?php endif; ?>
      </ul>

      <ul class="navbar-nav">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            <?= ucfirst($username) ?> (<?= $role ?>)
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#">Profil</a></li>
            <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
