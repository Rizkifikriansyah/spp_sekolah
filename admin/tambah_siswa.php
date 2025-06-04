<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $nis = $_POST['nis'];
  $nama = $_POST['nama'];
  $kelas = $_POST['kelas'];
  $alamat = $_POST['alamat'];
  $username = $_POST['username'];
  $password = md5($_POST['password']); // gunakan md5 sesuai permintaan

  // Simpan ke users dulu
  $stmtUser = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'siswa')");
  $stmtUser->bind_param("ss", $username, $password);
  $stmtUser->execute();
  $user_id = $stmtUser->insert_id;

  // Simpan ke siswa
  $stmtSiswa = $conn->prepare("INSERT INTO siswa (user_id, nis, nama, kelas, alamat) VALUES (?, ?, ?, ?, ?)");
  $stmtSiswa->bind_param("issss", $user_id, $nis, $nama, $kelas, $alamat);
  $stmtSiswa->execute();

  header("Location: kelola_siswa.php");
  exit;
}

function isActive($filename) {
  return basename($_SERVER['PHP_SELF']) === $filename ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Tambah Siswa - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    .navbar-brand {
      font-weight: 700;
      font-size: 1.5rem;
      color: #0d6efd;
    }
    .nav-link.active {
      font-weight: 600;
      color: #0d6efd !important;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">Admin Sekolah</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin" 
      aria-controls="navbarAdmin" aria-expanded="false" aria-label="Toggle navigation">
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

<div class="container mt-4">
  <h2>Tambah Siswa</h2>
  <form method="POST" autocomplete="off">
    <div class="mb-3">
      <label for="nis" class="form-label">NIS</label>
      <input type="text" name="nis" id="nis" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="nama" class="form-label">Nama</label>
      <input type="text" name="nama" id="nama" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="kelas" class="form-label">Kelas</label>
      <input type="text" name="kelas" id="kelas" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="alamat" class="form-label">Alamat</label>
      <textarea name="alamat" id="alamat" class="form-control" rows="3" required></textarea>
    </div>
    <hr>
    <h5>Akun Login Siswa</h5>
    <div class="mb-3">
      <label for="username" class="form-label">Username</label>
      <input type="text" name="username" id="username" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">Password</label>
      <input type="password" name="password" id="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Simpan</button>
    <a href="kelola_siswa.php" class="btn btn-secondary ms-2">Kembali</a>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
