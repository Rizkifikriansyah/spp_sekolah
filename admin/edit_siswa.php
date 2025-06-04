<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}
require_once '../db.php';

if (!isset($_GET['id'])) {
  header("Location: kelola_siswa.php");
  exit;
}

$id = $_GET['id'];
$query = "SELECT s.*, u.username FROM siswa s JOIN users u ON s.user_id = u.id WHERE s.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$siswa = $result->fetch_assoc();

if (!$siswa) {
  echo "<div class='alert alert-danger'>Siswa tidak ditemukan!</div>";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nis = $_POST['nis'];
  $nama = $_POST['nama'];
  $kelas = $_POST['kelas'];
  $alamat = $_POST['alamat'];
  $username = $_POST['username'];

  $stmtUser = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
  $stmtUser->bind_param("si", $username, $siswa['user_id']);
  $stmtUser->execute();

  $stmtSiswa = $conn->prepare("UPDATE siswa SET nis = ?, nama = ?, kelas = ?, alamat = ? WHERE id = ?");
  $stmtSiswa->bind_param("ssssi", $nis, $nama, $kelas, $alamat, $id);
  $stmtSiswa->execute();

  header("Location: kelola_siswa.php");
  exit;
}

function isActive($file) {
  return basename($_SERVER['PHP_SELF']) === $file ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Siswa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .navbar-brand { font-weight: bold; font-size: 1.6rem; color: #fff; }
    .nav-link.active { font-weight: 600; color: #0d6efd !important; }
    .nav-link { color: #ddd; }
  </style>
</head>
<body>

<!-- NAVBAR ADMIN -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">Admin Sekolah</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin" aria-controls="navbarAdmin" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarAdmin">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link <?= isActive('dashboard.php') ?>" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link <?= isActive('kelola_siswa.php') ?>" href="kelola_siswa.php">Kelola Siswa</a></li>
        <li class="nav-item"><a class="nav-link <?= isActive('verifikasi_pembayaran.php') ?>" href="verifikasi_pembayaran.php">Pembayaran</a></li>
        <li class="nav-item"><a class="nav-link <?= isActive('kelola_berita.php') ?>" href="kelola_berita.php">Berita/Event</a></li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <?= htmlspecialchars($_SESSION['user']['username']) ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
            <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- FORM EDIT -->
<div class="container mt-4">
  <h2>Edit Data Siswa</h2>
  <form method="POST">
    <div class="mb-3">
      <label>NIS</label>
      <input type="text" name="nis" class="form-control" value="<?= htmlspecialchars($siswa['nis']) ?>" required>
    </div>
    <div class="mb-3">
      <label>Nama</label>
      <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($siswa['nama']) ?>" required>
    </div>
    <div class="mb-3">
      <label>Kelas</label>
      <input type="text" name="kelas" class="form-control" value="<?= htmlspecialchars($siswa['kelas']) ?>" required>
    </div>
    <div class="mb-3">
      <label>Alamat</label>
      <textarea name="alamat" class="form-control" required><?= htmlspecialchars($siswa['alamat']) ?></textarea>
    </div>
    <hr>
    <h5>Akun Login Siswa</h5>
    <div class="mb-3">
      <label>Username</label>
      <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($siswa['username']) ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="kelola_siswa.php" class="btn btn-secondary">Batal</a>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
