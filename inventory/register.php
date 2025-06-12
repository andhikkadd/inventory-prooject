<?php
session_start();
require_once 'config/db.php';
include 'config/function.php';

$status = '';

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");

    if (mysqli_num_rows($cek) > 0) {
        $status = 'username_exists';
    } else {
        $id_user = generateUniqueId($conn, 'users', 'id' ,'00');
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $insert = mysqli_query($conn, "INSERT INTO users (id, username, password, role) VALUES ('$id_user', '$username', '$hashed', '$role')");
        if ($insert) {
            $status = 'success';
        } else {
            $status = 'failed';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Inventory App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .role-card {
            cursor: pointer;
            border: 2px solid #e9ecef;
            transition: border-color 0.2s, box-shadow 0.2s;
            padding: 0.6rem 0.2rem !important;
        }
        .role-card.selected, .role-card:hover {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.15rem rgba(13,110,253,.10);
        }
        .role-radio {
            display: none;
        }
        .role-icon {
            font-size: 1.4rem;
            margin-bottom: 0.2rem;
        }
        .role-label {
            font-weight: 500;
            font-size: 0.98rem;
        }
        .role-desc {
            font-size: 0.78rem;
        }
        @media (max-width: 576px) {
            .role-card {
                padding: 0.5rem 0.1rem !important;
            }
            .role-icon {
                font-size: 1.1rem;
            }
            .role-label {
                font-size: 0.93rem;
            }
            .role-desc {
                font-size: 0.72rem;
            }
            .container {
                max-width: 70%;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow rounded-4 border-0">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4 fw-bold">Buat Akun 👤</h3>
                        <form method="POST" action="" id="registerForm">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" name="username" id="username" class="form-control custom-input" placeholder="Masukkan username..." required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control custom-input" placeholder="Masukkan password..." required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label mb-2">Daftar Sebagai</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="role-card w-100 text-center rounded-3" id="role-admin">
                                            <input type="radio" name="role" value="admin" class="role-radio" required>
                                            <div class="role-icon text-primary"><i class="bi bi-person-badge"></i></div>
                                            <div class="role-label">Admin</div>
                                            <div class="text-muted role-desc">Kelola data & user</div>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <label class="role-card w-100 text-center rounded-3" id="role-petugas">
                                            <input type="radio" name="role" value="petugas" class="role-radio" required>
                                            <div class="role-icon text-success"><i class="bi bi-person-check"></i></div>
                                            <div class="role-label">Petugas</div>
                                            <div class="text-muted role-desc">Kelola barang & stok</div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" name="register" class="btn btn-primary w-100 mt-3 fw-semibold">Daftar Sekarang</button>
                        </form>
                        <p class="text-center mt-3">
                            Sudah punya akun? <a href="login.php">Login di sini</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php if (!empty($status)): ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        <?php if ($status === 'success'): ?>
            Swal.fire({
                title: 'Registrasi Berhasil!',
                text: 'Silakan login dengan akun baru kamu.',
                icon: 'success',
                confirmButtonText: 'Login'
            }).then(() => {
                window.location.href = 'login.php';
            });
        <?php elseif ($status === 'username_exists'): ?>
            Swal.fire({
                title: 'Username sudah ada!',
                text: 'Silakan gunakan username lain.',
                icon: 'warning',
                confirmButtonText: 'Oke'
            });
        <?php elseif ($status === 'failed'): ?>
            Swal.fire({
                title: 'Gagal!',
                text: 'Registrasi gagal, coba lagi.',
                icon: 'error',
                confirmButtonText: 'Oke'
            });
        <?php endif; ?>
    });
</script>
<?php endif; ?>

<script>
    document.querySelectorAll('.role-card').forEach(function(card) {
        card.addEventListener('click', function() {
            document.querySelectorAll('.role-card').forEach(function(c) {
                c.classList.remove('selected');
            });
            card.classList.add('selected');
            card.querySelector('input[type=radio]').checked = true;
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.role-radio').forEach(function(radio) {
            if (radio.checked) {
                radio.closest('.role-card').classList.add('selected');
            }
        });
    });
</script>

</body>
</html>
