<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'];
  $password = md5($_POST['password']); // Perlu upgrade ke bcrypt di masa depan
  $role     = $_POST['role'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=? AND role=?");
  $stmt->bind_param("sss", $username, $password, $role);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $_SESSION['user'] = $user;

    if ($role == 'admin') {
      header("Location: admin/dashboard.php");
    } else {
      header("Location: user/dashboard.php");
    }
    exit;
  }

  $error = "Username atau password salah!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login - Website Sekolah</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #0d6efd, #6610f2);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', sans-serif;
    }

    .login-card {
      background: #ffffff;
      padding: 40px 30px;
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
    }

    .login-card h2 {
      margin-bottom: 25px;
      font-weight: 600;
      text-align: center;
      color: #343a40;
    }

    .form-control {
      border-radius: 10px;
    }

    .btn-primary {
      border-radius: 10px;
      background: linear-gradient(to right, #0d6efd, #6610f2);
      border: none;
    }

    .btn-primary:hover {
      background: linear-gradient(to right, #0b5ed7, #520dc2);
    }

    .alert {
      border-radius: 10px;
    }
  </style>
</head>
<body>

<div class="login-card">
  <h2>Login Pengguna</h2>

  <?php if (!empty($error)) : ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Username</label>
      <input type="text" name="username" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-4">
      <label class="form-label">Login sebagai:</label>
      <select name="role" class="form-control" required>
        <option value="siswa">Siswa</option>
        <option value="admin">Admin</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary w-100">Login</button>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
