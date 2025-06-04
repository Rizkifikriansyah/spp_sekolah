<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $judul = $_POST['judul'];
  $isi = $_POST['isi'];
  $tanggal = $_POST['tanggal'];
  $gambar = $_FILES['gambar'];

  $namaFile = null;
  if ($gambar['error'] === 0 && in_array($gambar['type'], ['image/jpeg', 'image/png'])) {
    $ext = pathinfo($gambar['name'], PATHINFO_EXTENSION);
    $namaFile = uniqid() . '.' . $ext;
    move_uploaded_file($gambar['tmp_name'], '../uploads/' . $namaFile);
  }

  $stmt = $conn->prepare("INSERT INTO berita (judul, isi, tanggal, gambar) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $judul, $isi, $tanggal, $namaFile);
  $stmt->execute();

  header("Location: kelola_berita.php");
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
  <title>Tambah Berita/Event</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .navbar-brand {
      font-weight: bold;
      font-size: 1.6rem;
      color: #fff;
    }
    .nav-link.active {
      font-weight: 600;
      color: #0d6efd !important;
    }
    .nav-link {
      color: #ddd;
    }
  </style>
</head>
<body>

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

<div class="container mt-4">
  <h2 class="mb-4">Tambah Berita / Event Sekolah</h2>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label for="judul" class="form-label">Judul</label>
      <input type="text" name="judul" id="judul" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="isi" class="form-label">Isi</label>
      <textarea name="isi" id="isi" class="form-control" rows="5" required></textarea>
    </div>
    <div class="mb-3">
      <label for="tanggal" class="form-label">Tanggal</label>
      <input type="date" name="tanggal" id="tanggal" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="gambar" class="form-label">Gambar (Opsional, JPG/PNG)</label>
      <input type="file" name="gambar" id="gambar" class="form-control" accept="image/jpeg, image/png">
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="kelola_berita.php" class="btn btn-secondary ms-2">Batal</a>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
