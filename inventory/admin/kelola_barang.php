<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Akses ditolak!');window.location='../login.php';</script>";
    exit;
}

require_once '../config/db.php';
include '../pages/navbar.php';
include '../config/function.php';

if (isset($_GET['hapus'])) {
    $id = ($_GET['hapus']);
    $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
    $stmt->bind_param("s", $id);
    if ($stmt->execute()) {
        $user_id = $_SESSION['id_user'];
        $action = "Menghapus barang dengan ID: $id";
        $log = $conn->prepare("INSERT INTO log_aktivitas (user_id, action) VALUES (?, ?)");
        $log->bind_param("ss", $user_id, $action);
        $log->execute();

        $_SESSION['message'] = "Data barang berhasil dihapus.";
        $_SESSION['message_type'] = "success";
        header("Location: kelola_barang.php");
        exit;
    } else {
        echo "<script>alert('Gagal menghapus barang');window.location='kelola_barang.php';</script>";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = htmlspecialchars($_POST['nama_barang']);
    $distributor = htmlspecialchars($_POST['distributor']);
    $jumlah = intval($_POST['jumlah']);
    $harga = floatval($_POST['harga']);
    $user_id = $_SESSION['id_user'];

    if (isset($_POST['id']) && $_POST['id'] != '') {
        $id = ($_POST['id']);
        $stmt = $conn->prepare("UPDATE items SET nama_barang=?, distributor=?, jumlah=?, harga=? WHERE id=?");
        $stmt->bind_param("ssids", $nama, $distributor, $jumlah, $harga, $id);
        if ($stmt->execute()) {
            $action = "Mengubah data barang ID: $id";
            $log = $conn->prepare("INSERT INTO log_aktivitas (user_id, action) VALUES (?, ?)");
            $log->bind_param("ss", $user_id, $action);
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
        $cek = $conn->prepare("SELECT id FROM items WHERE nama_barang = ?");
        $cek->bind_param("s", $nama);
        $cek->execute();
        $cek->store_result();

        if ($cek->num_rows > 0) {
            $_SESSION['message'] = "Gagal menambahkan barang.";
            $_SESSION['message_type'] = "danger";
            header("Location: kelola_barang.php");
            exit;
        }

        $id_barang = generateUniqueID($conn, 'items', 'id', 'I');
        $stmt = $conn->prepare("INSERT INTO items (id, nama_barang, distributor, jumlah, harga) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssid", $id_barang, $nama, $distributor, $jumlah, $harga);
        if ($stmt->execute()) {
            $insert_id = $stmt->insert_id;
            $action = "Menambah barang baru ID: $insert_id";
            $log = $conn->prepare("INSERT INTO log_aktivitas (user_id, action) VALUES (?, ?)");
            $log->bind_param("ss", $user_id, $action);
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

$result = $conn->query("SELECT * FROM items ORDER BY nama_barang ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Inventory App</title>
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
        <h3 class="mb-0">Kelola Barang</h3>
      </div>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-lg me-1"></i>Tambah Barang
      </button>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:40px;">ID</th>
                            <th>Nama Barang</th>
                            <th>Distibutor</th>
                            <th>Jumlah (Kardus)</th>
                            <th>Harga</th>
                            <th style="width:40px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                    <td><?= htmlspecialchars($row['distributor']) ?></td>
                                    <td><?= $row['jumlah'] ?></td>
                                    <td>Rp. <?= number_format($row['harga'], 2, ',', '.') ?></td>
                                    <td>
                                        <div class="aksi-btns">
                                            <button class="btn btn-edit p-0"
                                                data-id="<?= $row['id'] ?>"
                                                data-nama="<?= htmlspecialchars($row['nama_barang']) ?>"
                                                data-distributor="<?= htmlspecialchars($row['distributor']) ?>"
                                                data-jumlah="<?= $row['jumlah'] ?>"
                                                data-harga="<?= $row['harga'] ?>"
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
                                <td colspan="6" class="text-center text-secondary">Belum ada data barang.</td>
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
    <form method="POST" id="formBarang" class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalTambahLabel"><i class="bi bi-plus-circle"></i> Tambah Barang</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" id="idBarang" />
            <div class="mb-3">
                <label for="namaBarang" class="form-label">Nama Barang</label>
                <input type="text" class="form-control" id="namaBarang" name="nama_barang" required />
            </div>
            <div class="mb-3">
              <label for="distributor" class="form-label">Distributor</label>
              <input type="text" class="form-control" id="distributor" name="distributor" />
            </div>
            <div class="mb-3">
                <label for="jumlah" class="form-label">Jumlah (Kardus)</label>
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
        Ingin menghapus barang ini?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <a href="kelola_barang.php" id="hapusLink" class="btn btn-danger">Ya</a>
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
        const nama = this.dataset.nama;
        const distributor = this.dataset.distributor;
        const jumlah = this.dataset.jumlah;
        const harga = this.dataset.harga;

        document.getElementById('modalTambahLabel').innerHTML = '<i class="bi bi-pencil-square"></i> Edit Barang';
        document.getElementById('idBarang').value = id;
        document.getElementById('namaBarang').value = nama;
        document.getElementById('distributor').value = distributor;
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
    document.getElementById('modalTambahLabel').innerHTML = '<i class="bi bi-plus-circle"></i> Tambah Barang';
    document.getElementById('formBarang').reset();
    document.getElementById('idBarang').value = '';
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