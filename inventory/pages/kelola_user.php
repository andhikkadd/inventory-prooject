<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Akses ditolak!');window.location='../login.php';</script>";
    exit;
}

require_once '../config/db.php';

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
    $nama_lengkap = htmlspecialchars($_POST['nama_lengkap']);
    $role = $_POST['role'];
    $password = $_POST['password'];

    if (isset($_POST['id']) && $_POST['id'] != '') {
        $id = intval($_POST['id']);
        if ($password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET username=?, nama_lengkap=?, role=?, password=? WHERE id=?");
            $stmt->bind_param("ssssi", $username, $nama_lengkap, $role, $hashed_password, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username=?, nama_lengkap=?, role=? WHERE id=?");
            $stmt->bind_param("sssi", $username, $nama_lengkap, $role, $id);
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
        $stmt = $conn->prepare("INSERT INTO users (username, nama_lengkap, role, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $nama_lengkap, $role, $hashed_password);
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
</head>
<body>
<div class="container mt-4">
  <h3>Kelola User</h3>
  <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah User</button>

  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Nama Lengkap</th>
        <th>Role</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()) : ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['username']) ?></td>
          <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
          <td><?= $row['role'] ?></td>
          <td>
            <button class="btn btn-sm btn-warning btn-edit"
              data-id="<?= $row['id'] ?>"
              data-username="<?= htmlspecialchars($row['username']) ?>"
              data-nama="<?= htmlspecialchars($row['nama_lengkap']) ?>"
              data-role="<?= $row['role'] ?>"
            >Edit</button>
            <button class="btn btn-sm btn-danger btn-hapus" data-id="<?= $row['id'] ?>">Hapus</button>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" id="formUser">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTambahLabel">Tambah User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="idUser" />
          <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required />
          </div>
          <div class="mb-3">
            <label for="namaLengkap" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="namaLengkap" name="nama_lengkap" />
          </div>
          <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select class="form-select" id="role" name="role" required>
              <option value="admin">Admin</option>
              <option value="petugas">Petugas</option>
              <option value="viewer">Viewer</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password (kosongkan jika tidak diubah)</label>
            <input type="password" class="form-control" id="password" name="password" />
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header text-bg-danger">
        <h5 class="modal-title" id="modalHapusLabel">Konfirmasi Hapus</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.btn-edit').forEach(button => {
  button.addEventListener('click', function() {
    const id = this.dataset.id;
    const username = this.dataset.username;
    const nama = this.dataset.nama;
    const role = this.dataset.role;

    document.getElementById('modalTambahLabel').textContent = 'Edit User';
    document.getElementById('idUser').value = id;
    document.getElementById('username').value = username;
    document.getElementById('namaLengkap').value = nama;
    document.getElementById('role').value = role;
    document.getElementById('password').value = '';

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
  document.getElementById('modalTambahLabel').textContent = 'Tambah User';
  document.getElementById('formUser').reset();
  document.getElementById('idUser').value = '';
});