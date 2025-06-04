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
    $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $user_id = $_SESSION['id_user'];
        $action = "Menghapus barang dengan ID: $id";
        $log = $conn->prepare("INSERT INTO log_aktivitas (user_id, action) VALUES (?, ?)");
        $log->bind_param("is", $user_id, $action);
        $log->execute();

        exit;
    } else {
        echo "<script>alert('Gagal menghapus barang');window.location='kelola_barang.php';</script>";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = htmlspecialchars($_POST['nama_barang']);
    $deskripsi = htmlspecialchars($_POST['deskripsi']);
    $jumlah = intval($_POST['jumlah']);
    $harga = floatval($_POST['harga']);
    $user_id = $_SESSION['id_user'];

    if (isset($_POST['id']) && $_POST['id'] != '') {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("UPDATE items SET nama_barang=?, deskripsi=?, jumlah=?, harga=? WHERE id=?");
        $stmt->bind_param("ssidi", $nama, $deskripsi, $jumlah, $harga, $id);
        if ($stmt->execute()) {
            $action = "Mengubah data barang ID: $id";
            $log = $conn->prepare("INSERT INTO log_aktivitas (user_id, action) VALUES (?, ?)");
            $log->bind_param("is", $user_id, $action);
            $log->execute();

            $_SESSION['message'] = "Data barang berhasil diperbarui.";
            $_SESSION['message_type'] = "success";
            header("Location: kelola_barang.php");
            exit;
        } else {
            $_SESSION['message'] = "Gagal memperbarui data barang.";
            $_SESSION['message_type'] = "danger";
            header("Location: kelola_barang.php");
            exit;
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO items (nama_barang, deskripsi, jumlah, harga) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $nama, $deskripsi, $jumlah, $harga);
        if ($stmt->execute()) {
            $insert_id = $stmt->insert_id;
            $action = "Menambah barang baru ID: $insert_id";
            $log = $conn->prepare("INSERT INTO log_aktivitas (user_id, action) VALUES (?, ?)");
            $log->bind_param("is", $user_id, $action);
            $log->execute();

            $_SESSION['message'] = "Barang berhasil ditambahkan.";
            $_SESSION['message_type'] = "success";
            header("Location: kelola_barang.php");
            exit;
        } else {
            $_SESSION['message'] = "Gagal menambahkan barang.";
            $_SESSION['message_type'] = "danger";
            header("Location: kelola_barang.php");
            exit;
        }
    }
}

$result = $conn->query("SELECT * FROM items ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kelola Barang - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-4">
    <h3>Kelola Barang</h3>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah Barang</button>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Barang</th>
                <th>Deskripsi</th>
                <th>Jumlah</th>
                <th>Harga (Rp)</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                <td><?= $row['jumlah'] ?></td>
                <td><?= number_format($row['harga'], 2, ',', '.') ?></td>
                <td>
                    <button class="btn btn-sm btn-warning btn-edit" 
                        data-id="<?= $row['id'] ?>"
                        data-nama="<?= htmlspecialchars($row['nama_barang']) ?>"
                        data-deskripsi="<?= htmlspecialchars($row['deskripsi']) ?>"
                        data-jumlah="<?= $row['jumlah'] ?>"
                        data-harga="<?= $row['harga'] ?>"
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
    <form method="POST" id="formBarang">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTambahLabel">Tambah Barang</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <input type="hidden" name="id" id="idBarang" />
              <div class="mb-3">
                <label for="namaBarang" class="form-label">Nama Barang</label>
                <input type="text" class="form-control" id="namaBarang" name="nama_barang" required />
              </div>
              <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi"></textarea>
              </div>
              <div class="mb-3">
                <label for="jumlah" class="form-label">Jumlah</label>
                <input type="number" class="form-control" id="jumlah" name="jumlah" min="0" required />
              </div>
              <div class="mb-3">
                <label for="harga" class="form-label">Harga (Rp)</label>
                <input type="number" step="0.01" class="form-control" id="harga" name="harga" min="0" required />
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
        Ingin menghapus barang ini?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <a href="#" id="hapusLink" class="btn btn-danger">Ya</a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalNotif" tabindex="-1" aria-labelledby="modalNotifLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalNotifLabel">Notifikasi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalNotifBody">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.btn-edit').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.dataset.id;
        const nama = this.dataset.nama;
        const deskripsi = this.dataset.deskripsi;
        const jumlah = this.dataset.jumlah;
        const harga = this.dataset.harga;

        document.getElementById('modalTambahLabel').textContent = 'Edit Barang';
        document.getElementById('idBarang').value = id;
        document.getElementById('namaBarang').value = nama;
        document.getElementById('deskripsi').value = deskripsi;
        document.getElementById('jumlah').value = jumlah;
        document.getElementById('harga').value = harga;

        var modal = new bootstrap.Modal(document.getElementById('modalTambah'));
        modal.show();
    });
});

document.querySelectorAll('.btn-hapus').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.dataset.id;
        const hapusLink = document.getElementById('hapusLink');
        hapusLink.href = 'kelola_barang.php?hapus=' + id;

        var modal = new bootstrap.Modal(document.getElementById('modalHapus'));
        modal.show();
    });
});

var modalTambah = document.getElementById('modalTambah');
modalTambah.addEventListener('hidden.bs.modal', function () {
    document.getElementById('modalTambahLabel').textContent = 'Tambah Barang';
    document.getElementById('formBarang').reset();
    document.getElementById('idBarang').value = '';
});
</script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    <?php if(isset($_SESSION['message'])): ?>
      var modalNotif = new bootstrap.Modal(document.getElementById('modalNotif'));
      document.getElementById('modalNotifBody').textContent = "<?= $_SESSION['message'] ?>";

      var modalHeader = document.querySelector('#modalNotif .modal-header');
      <?php if($_SESSION['message_type'] === 'success'): ?>
        modalHeader.classList.remove('bg-danger');
        modalHeader.classList.add('bg-success', 'text-white');
      <?php else: ?>
        modalHeader.classList.remove('bg-success');
        modalHeader.classList.add('bg-danger', 'text-white');
      <?php endif; ?>

      modalNotif.show();
    <?php 
      unset($_SESSION['message']);
      unset($_SESSION['message_type']);
    endif; ?>
  });
</script>

</body>
</html>
