
<!-- FILE: config/controller.php (Login + Role Redirect + Session) -->
<?php
session_start();
include 'db.php';

if (isset($_POST['login'])) {
  $username = $conn->real_escape_string($_POST['username']);
  $password = hash('sha256', $_POST['password']);

  $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
  $result = $conn->query($query);

  if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $_SESSION['id_user'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    $redirect = ($user['role'] === 'admin') ? '../pages/admin.php' : (($user['role'] === 'petugas') ? '../pages/petugas.php' : '../pages/viewer.php');
    header("Location: ../login.php?success=1&redirect=$redirect");
  } else {
    header("Location: ../login.php?error=Username atau Password salah!");
  }
}
?>
