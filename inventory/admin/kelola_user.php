<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Akses ditolak!');window.location='../login.php';</script>";
    exit;
}

require_once '../config/db.php';
include '../pages/navbar.php';

if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "User berhasil dihapus.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Gagal menghapus user.";
        $_SESSION['message_type'] = "danger";
    }
    header("Location: kelola_user.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $role = $_POST['role'];
    $password = $_POST['password'];

    if (isset($_POST['id']) && $_POST['id'] != '') {
        $id = intval($_POST['id']);
        if ($password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET username=?, role=?, password=? WHERE id=?");
            $stmt->bind_param("sssi", $username, $role, $hashed_password, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username=?, role=? WHERE id=?");
            $stmt->bind_param("ssi", $username, $role, $id);
        }
        $exec = $stmt->execute();
        if ($exec) {
            $_SESSION['message'] = "User berhasil diperbarui.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Gagal memperbarui user.";
            $_SESSION['message_type'] = "danger";
        }
    } else {
        if (!$password) {
            $_SESSION['message'] = "Password wajib diisi untuk user baru.";
            $_SESSION['message_type'] = "danger";
            header("Location: kelola_user.php");
            exit;
        }
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, role, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $role, $hashed_password);
        if ($stmt->execute()) {
            $_SESSION['message'] = "User baru berhasil ditambahkan.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Gagal menambahkan user baru.";
            $_SESSION['message_type'] = "danger";
        }
    }
    header("Location: kelola_user.php");
    exit;
}

$result = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kelola User - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background: linear-gradient(120deg, #f8fafc 60%, #e3e9f7 100%); min-height: 100vh; }
        .page-title {
            font-weight: 700;
            color: #22223b;
            margin-top: 2rem;
            margin-bottom: 1.5rem;
        }
        .card {
            border-radius: 1.2rem;
            box-shadow: 0 4px 24px rgba(34,34,59,0.07);
            border: none;
        }
        .table {
            background: #fff;
            border-radius: 5px;
            overflow: hidden;
        }
        .table thead th {
            background: #f0f2f5;
            color: #495af0;
            font-weight: 600;
            border-bottom: 2px solid #e3e9f7;
        }
        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: #f8fafc;
        }
        .table-hover tbody tr:hover {
            background-color: #e9ecef;
        }
        .aksi-btns {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }
        .btn-edit, .btn-hapus {
            border-radius: 50%;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            border: none;
            transition: background 0.2s, color 0.2s;
        }
        .btn-edit {
            background: #e7f0fd;
            color: #495af0;
        }
        .btn-edit:hover {
            background: #495af0;
            color: #fff;
        }
        .btn-hapus {
            background: #fde2e1;
            color: #e74c3c;
        }
        .btn-hapus:hover {
            background: #e74c3c;
            color: #fff;
        }
        .modal-header { background: #f6f8fa; }
        .form-label { font-weight: 500; }
        .modal-notif .modal-content {
            border-radius: 1.2rem;
            border: none;
            box-shadow: 0 8px 32px rgba(34,34,59,0.13);
            text-align: center;
        }
        .modal-notif .modal-header {
            border-bottom: none;
            justify-content: center;
        }
        .modal-notif .modal-body {
            font-size: 1.1rem;
            padding-top: 0;
        }
        .modal-notif .icon-success {
            font-size: 2.5rem;
            color: #38d39f;
        }
        .modal-notif .icon-danger {
            font-size: 2.5rem;
            color: #e74c3c;
        }
        @media (max-width: 767px) {
            .table-responsive { font-size: 0.95rem; }
            .btn-edit, .btn-hapus { width: 32px; height: 32px; font-size: 1rem; }
        }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="page-header d-flex align-items-center justify-content-between flex-wrap mb-4">
      <div class="d-flex align-items-center gap-2">
        <a href="admin.php" type="button" class="btn btn-primary me-2">
          <i class="bi bi-arrow-left"></i>
        </a>
        <h3 class="mb-0">Kelola User</h3>
      </div>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-lg me-1"></i>Tambah User
      </button>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:40px;">ID</th>
                            <th>Username</th>
                            <th>Peran</th>
                            <th style="width:10px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['username']) ?></td>
                                    <td><?= $row['role'] ?></td>
                                    <td>
                                        <div class="aksi-btns">
                                            <button class="btn btn-edit p-0"
                                                data-id="<?= $row['id'] ?>"
                                                data-username="<?= htmlspecialchars($row['username']) ?>"
                                                data-role="<?= $row['role'] ?>"
                                                title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button class="btn btn-hapus p-0" data-id="<?= $row['id'] ?>" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-secondary">Belum ada data user.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" id="formUser" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTambahLabel"><i class="bi bi-plus-circle"></i> Tambah User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="idUser" />
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" class="form-control" id="username" name="username" required />
        </div>
        <div class="mb-3">
          <label for="role" class="form-label">Sebagai</label>
          <select class="form-select" id="role" name="role" required>
            <option value="admin">Admin</option>
            <option value="petugas">Petugas</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password <span id="passwordNote" class="text-muted">(kosongkan jika tidak diubah)</span></label>
          <input type="password" class="form-control" id="password" name="password" />
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-bg-danger">
        <h5 class="modal-title" id="modalHapusLabel"><i class="bi bi-exclamation-triangle-fill"></i> Konfirmasi Hapus</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        Ingin menghapus user ini?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <a href="#" id="hapusLink" class="btn btn-danger">Ya</a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade modal-notif" id="modalNotif" tabindex="-1" aria-labelledby="modalNotifLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <span id="notifIcon"></span>
      </div>
      <div class="modal-body" id="modalNotifBody"></div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.btn-edit').forEach(button => {
  button.addEventListener('click', function() {
    const id = this.dataset.id;
    const username = this.dataset.username;
    const role = this.dataset.role;

    document.getElementById('modalTambahLabel').innerHTML = '<i class="bi bi-pencil-square"></i> Edit User';
    document.getElementById('idUser').value = id;
    document.getElementById('username').value = username;
    document.getElementById('role').value = role;
    document.getElementById('password').value = '';
    document.getElementById('passwordNote').textContent = '(kosongkan jika tidak diubah)';

    var modal = new bootstrap.Modal(document.getElementById('modalTambah'));
    modal.show();
  });
});

document.querySelectorAll('.btn-hapus').forEach(button => {
  button.addEventListener('click', function() {
    const id = this.dataset.id;
    const hapusLink = document.getElementById('hapusLink');
    hapusLink.href = 'kelola_user.php?hapus=' + id;

    var modal = new bootstrap.Modal(document.getElementById('modalHapus'));
    modal.show();
  });
});

var modalTambah = document.getElementById('modalTambah');
modalTambah.addEventListener('hidden.bs.modal', function () {
  document.getElementById('modalTambahLabel').innerHTML = '<i class="bi bi-plus-circle"></i> Tambah User';
  document.getElementById('formUser').reset();
  document.getElementById('idUser').value = '';
  document.getElementById('passwordNote').textContent = '(kosongkan jika tidak diubah)';
});
</script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    <?php if(isset($_SESSION['message'])): ?>
      var modalNotif = new bootstrap.Modal(document.getElementById('modalNotif'));
      document.getElementById('modalNotifBody').textContent = "<?= $_SESSION['message'] ?>";
      var notifIcon = document.getElementById('notifIcon');
      <?php if($_SESSION['message_type'] === 'success'): ?>
        notifIcon.innerHTML = '<i class="bi bi-check-circle-fill icon-success"></i>';
      <?php else: ?>
        notifIcon.innerHTML = '<i class="bi bi-x-circle-fill icon-danger"></i>';
      <?php endif; ?>
      modalNotif.show();
      setTimeout(function() {
          modalNotif.hide();
      }, 2000);
    <?php 
      unset($_SESSION['message']);
      unset($_SESSION['message_type']);
    endif; ?>
  });
</script>
</body>
</html>
