<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'petugas') {
    echo "<script>alert('Akses ditolak!');window.location='../login.php';</script>";
    exit;
}

require_once '../config/db.php';
include '../pages/navbar.php';

$items = mysqli_query($conn, "SELECT id, nama_barang, jumlah FROM items");

if (isset($_POST['tambah'])) {
    $id_barang = $_POST['id_barang'];
    $jumlah = $_POST['jumlah'];
    $tanggal = $_POST['tanggal']; 
    $user_id = $_SESSION['id_user'];

    $stok_query = $conn->query("SELECT jumlah FROM items WHERE id = $id_barang");
    $stok = $stok_query->fetch_assoc()['jumlah'];

    if ($id_barang && $jumlah > 0 && $tanggal) {
        if ($jumlah > $stok) {
            $_SESSION['message'] = "Jumlah yang diminta melebihi stok yang tersedia!";
            $_SESSION['message_type'] = "danger";
        } else {
            $stmt = $conn->prepare("INSERT INTO barang_keluar (id_barang, jumlah, tanggal, user_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iisi", $id_barang, $jumlah, $tanggal, $user_id);
            if ($stmt->execute()) {
                $conn->query("UPDATE items SET jumlah = jumlah - $jumlah WHERE id = $id_barang");
                $action = "Menambah barang keluar (ID Barang: $id_barang, Jumlah: $jumlah, Tanggal: $tanggal)";
                $stmt2 = $conn->prepare("INSERT INTO log_aktivitas (user_id, action) VALUES (?, ?)");
                $stmt2->bind_param("is", $user_id, $action);
                $stmt2->execute();
                $_SESSION['message'] = "Barang keluar berhasil ditambahkan!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Gagal menambah barang keluar!";
                $_SESSION['message_type'] = "danger";
            }
        }
    } else {
        $_SESSION['message'] = "Data tidak valid!";
        $_SESSION['message_type'] = "danger";
    }
    header("Location: barang_keluar.php");
    exit;
}

if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $id_barang = $_POST['id_barang'];
    $jumlah_baru = $_POST['jumlah'];
    $tanggal = $_POST['tanggal'];
    $user_id = $_SESSION['id_user'];

    $q = $conn->query("SELECT * FROM barang_keluar WHERE id=$id");
    $lama = $q->fetch_assoc();
    $jumlah_lama = $lama['jumlah'];
    $id_barang_lama = $lama['id_barang'];

    $stok_query = $conn->query("SELECT jumlah FROM items WHERE id = $id_barang");
    $stok = $stok_query->fetch_assoc()['jumlah'];

    if ($id && $id_barang && $jumlah_baru > 0 && $tanggal) {
        if ($jumlah_baru > $stok + $jumlah_lama) { 
            $_SESSION['message'] = "Jumlah yang diminta melebihi stok yang tersedia!";
            $_SESSION['message_type'] = "danger";
        } else {
            $stmt = $conn->prepare("UPDATE barang_keluar SET id_barang=?, jumlah=?, tanggal=? WHERE id=?");
            $stmt->bind_param("iisi", $id_barang, $jumlah_baru, $tanggal, $id);
            if ($stmt->execute()) {
                $conn->query("UPDATE items SET jumlah = jumlah + $jumlah_lama WHERE id = $id_barang_lama");
                $conn->query("UPDATE items SET jumlah = jumlah - $jumlah_baru WHERE id = $id_barang");
                $action = "Mengedit barang keluar (ID: $id, ID Barang: $id_barang, Jumlah: $jumlah_baru, Tanggal: $tanggal)";
                $stmt2 = $conn->prepare("INSERT INTO log_aktivitas (user_id, action) VALUES (?, ?)");
                $stmt2->bind_param("is", $user_id, $action);
                $stmt2->execute();
                $_SESSION['message'] = "Barang keluar berhasil diubah!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Gagal mengubah barang keluar!";
                $_SESSION['message_type'] = "danger";
            }
        }
    } else {
        $_SESSION['message'] = "Data tidak valid!";
        $_SESSION['message_type'] = "danger";
    }
    header("Location: barang_keluar.php");
    exit;
}

if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $user_id = $_SESSION['id_user'];
    $q = $conn->query("SELECT * FROM barang_keluar WHERE id=$id");
    if ($q->num_rows > 0) {
        $lama = $q->fetch_assoc();
        $id_barang = $lama['id_barang'];
        $jumlah = $lama['jumlah'];
        $conn->query("DELETE FROM barang_keluar WHERE id=$id");
        $conn->query("UPDATE items SET jumlah = jumlah + $jumlah WHERE id = $id_barang");
        $action = "Menghapus barang keluar (ID: $id, ID Barang: $id_barang, Jumlah: $jumlah)";
        $stmt2 = $conn->prepare("INSERT INTO log_aktivitas (user_id, action) VALUES (?, ?)");
        $stmt2->bind_param("is", $user_id, $action);
        $stmt2->execute();
        $_SESSION['message'] = "Barang keluar berhasil dihapus!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Data tidak ditemukan!";
        $_SESSION['message_type'] = "danger";
    }
    header("Location: barang_keluar.php");
    exit;
}

$sql = "SELECT bk.id, i.nama_barang, bk.jumlah, bk.tanggal, u.username, bk.id_barang
        FROM barang_keluar bk
        JOIN items i ON bk.id_barang = i.id
        JOIN users u ON bk.user_id = u.id
        ORDER BY bk.tanggal DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Barang Keluar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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
        <a href="petugas.php" type="button" class="btn btn-primary me-2">
          <i class="bi bi-arrow-left"></i>
        </a>
        <h3 class="mb-0">Barang Keluar</i></h3>
      </div>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-lg me-1"></i>Tambah
      </button>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Tanggal</th>
                            <th>Petugas</th>
                            <th style="width:90px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($result) > 0): $no=1; ?>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                                    <td><?= $row['jumlah']; ?></td>
                                    <td><?= date('d F Y', strtotime($row['tanggal'])); ?></td>
                                    <td><?= htmlspecialchars($row['username']); ?></td>
                                    <td>
                                        <div class="aksi-btns">
                                            <button class="btn btn-edit" 
                                                data-id="<?= $row['id'] ?>"
                                                data-id_barang="<?= $row['id_barang'] ?>"
                                                data-jumlah="<?= $row['jumlah'] ?>"
                                                data-tanggal="<?= $row['tanggal'] ?>"
                                                data-bs-toggle="modal" data-bs-target="#modalEdit"
                                                title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button class="btn btn-hapus" 
                                                data-id="<?= $row['id'] ?>"
                                                data-nama="<?= htmlspecialchars($row['nama_barang']) ?>"
                                                data-bs-toggle="modal" data-bs-target="#modalHapus"
                                                title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-secondary">Belum ada data barang keluar.</td>
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
    <form method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTambahLabel"><i class="bi bi-plus-circle"></i> Tambah Barang Keluar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label for="id_barang" class="form-label">Nama Barang</label>
            <select class="form-select" id="id_barang" name="id_barang" required>
                <option value="" selected disabled>Pilih barang...</option>
                <?php mysqli_data_seek($items, 0); while($item = mysqli_fetch_assoc($items)): ?>
                    <option value="<?= $item['id']; ?>"><?= htmlspecialchars($item['nama_barang']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="jumlah" class="form-label">Jumlah</label>
            <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" required>
        </div>
        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal</label>
            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <input type="hidden" name="id" id="edit_id">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditLabel"><i class="bi bi-pencil-square"></i> Edit Barang Keluar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label for="edit_id_barang" class="form-label">Nama Barang</label>
            <select class="form-select" id="edit_id_barang" name="id_barang" required>
                <option value="" selected disabled>Pilih barang...</option>
                <?php $items2 = mysqli_query($conn, "SELECT id, nama_barang FROM items"); while($item = mysqli_fetch_assoc($items2)): ?>
                    <option value="<?= $item['id']; ?>"><?= htmlspecialchars($item['nama_barang']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="edit_jumlah" class="form-label">Jumlah</label>
            <input type="number" class="form-control" id="edit_jumlah" name="jumlah" min="1" required>
        </div>
        <div class="mb-3">
            <label for="edit_tanggal" class="form-label">Tanggal</label>
            <input type="date" class="form-control" id="edit_tanggal" name="tanggal" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" name="edit" class="btn btn-primary">Simpan Perubahan</button>
      </div>
    </form>
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

<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="get" action="barang_keluar.php">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="modalHapusLabel"><i class="bi bi-exclamation-triangle-fill"></i> Konfirmasi Hapus</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="hapus" id="hapus_id">
          <p>Yakin ingin menghapus data <b id="hapus_nama"></b>?</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Ya, Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
<?php if(isset($_SESSION['message'])): ?>
    document.addEventListener('DOMContentLoaded', function() {
        var notifModal = new bootstrap.Modal(document.getElementById('modalNotif'));
        var notifBody = document.getElementById('modalNotifBody');
        var notifIcon = document.getElementById('notifIcon');
        notifBody.textContent = "<?= $_SESSION['message'] ?>";
        <?php if($_SESSION['message_type'] === 'success'): ?>
            notifIcon.innerHTML = '<i class="bi bi-check-circle-fill icon-success"></i>';
        <?php else: ?>
            notifIcon.innerHTML = '<i class="bi bi-x-circle-fill icon-danger"></i>';
        <?php endif; ?>
        notifModal.show();
        setTimeout(function() {
            notifModal.hide();
        }, 2000);
    });
<?php unset($_SESSION['message']); unset($_SESSION['message_type']); endif; ?>

document.querySelectorAll('.btn-edit').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.getElementById('edit_id').value = this.dataset.id;
        document.getElementById('edit_id_barang').value = this.dataset.id_barang;
        document.getElementById('edit_jumlah').value = this.dataset.jumlah;
        document.getElementById('edit_tanggal').value = this.dataset.tanggal;
    });
});

document.querySelectorAll('.btn-hapus').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.getElementById('hapus_nama').textContent = this.dataset.nama;
        document.getElementById('hapus_id').value = this.dataset.id;
    });
});
</script>
</body>
</html>
