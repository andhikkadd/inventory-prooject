<?php
session_start();
require_once 'config/db.php';

$status = '';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        $hash = $user['password'];
        if (password_verify($password, $hash)) {
            $_SESSION['id_user'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
            $status = 'success';
        } else {
            $status = 'wrong_password';
        }
    } else {
        $status = 'user_not_found';
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
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            border-radius: 1rem;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }
        .custom-input {
            border-radius: 0.5rem;
        }
        @media (max-width: 576px) {
            .login-card {
                padding: 1.5rem;
            }
            .container {
                max-width: 65%;
            }
        }
    </style>
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow rounded-4 border-0">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4 fw-bold">Selamat Datang 👋</h3>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" name="username" id="username" class="form-control custom-input" placeholder="Masukkan username..." required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control custom-input" placeholder="Masukkan password..." required>
                            </div>
                            <button type="submit" name="login" class="btn btn-primary w-100 mt-3 fw-semibold">Login</button>
                        </form>
                        <p class="text-center mt-3">
                            Belum punya akun? <a href="register.php">Register</a>
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
                title: 'Login Berhasil!',
                text: 'Kamu login sebagai <?= $_SESSION['role'] ?>!',
                icon: 'success',
                confirmButtonText: 'Masuk'
            }).then(() => {
                window.location.href = '<?= $_SESSION['role'] ?>/<?= $_SESSION['role'] ?>.php';
            });
        <?php elseif ($status === 'wrong_password'): ?>
            Swal.fire({
                title: 'Ups!',
                text: 'Password kamu salah, coba lagi ya!',
                icon: 'error',
                confirmButtonText: 'Oke'
            });
        <?php elseif ($status === 'user_not_found'): ?>
            Swal.fire({
                title: 'Hmm...',
                text: 'Username-nya belum terdaftar nih!',
                icon: 'warning',
                confirmButtonText: 'Coba Lagi'
            });
        <?php endif; ?>
    });
</script>
<?php endif; ?>
</body>
</html>