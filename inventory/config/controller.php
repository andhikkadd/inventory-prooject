require_once 'config/db.php'; // koneksi
session_start();

if (isset($_POST['login'])) {
  $username = htmlspecialchars($_POST['username']);
  $password = $_POST['password'];

  $query = $conn->prepare("SELECT * FROM users WHERE username = ?");
  $query->execute([$username]);
  $user = $query->fetch(PDO::FETCH_ASSOC);

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = [
      'id' => $user['id'],
      'username' => $user['username'],
      'role' => $user['role']
    ];
    $_SESSION['login_status'] = 'success';
  } else {
    $_SESSION['login_status'] = 'failed';
  }

  header('Location: login.php');
  exit;
}
