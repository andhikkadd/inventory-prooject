<?php
if (!isset($_SESSION)) session_start();
$username = $_SESSION['username'] ?? 'User';
$role = $_SESSION['role'] ?? 'guest';

?>

<nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm mb-4 p-3">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold fs-3 text-primary"
      <?php if ($_SESSION['role'] == 'admin'): ?> href="../admin/admin.php"<?php endif; ?>
      <?php if ($_SESSION['role'] == 'petugas'): ?> href="../petugas/petugas.php"<?php endif; ?>
    ><i class="fas fa-box-open me-2"></i>Inventory App</class=>
    
    <ul class="navbar-nav">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle text-primary fw-semibold fs-5" href="#" role="button" data-bs-toggle="dropdown">
          <i class="fas fa-user-circle me-1"></i> <?= ucfirst($username) ?>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
        </ul>
      </li>
    </ul>

  </div>
</nav>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
