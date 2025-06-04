<?php
require_once 'db.php';

if (!isset($_GET['id'])) {
  echo "<div class='alert alert-danger text-center mt-5'>ID berita tidak ditemukan.</div>";
  exit;
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM berita WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$berita = $result->fetch_assoc();

if (!$berita) {
  echo "<div class='alert alert-warning text-center mt-5'>Berita tidak ditemukan.</div>";
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($berita['judul']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f4f6f9;
      font-family: 'Segoe UI', sans-serif;
    }
    .navbar {
      background: linear-gradient(45deg, #0d6efd, #6610f2);
    }
    .card {
      border: none;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 0 25px rgba(0, 0, 0, 0.07);
    }
    .card-title {
      font-size: 2rem;
      font-weight: bold;
    }
    .card-text {
      font-size: 1.1rem;
      line-height: 1.7;
    }
    .date-label {
      font-size: 0.95rem;
      color: #6c757d;
    }
    .btn-secondary {
      border-radius: 30px;
      padding-left: 20px;
      padding-right: 20px;
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark py-3">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">Portal Berita Sekolah</a>
  </div>
</nav>

<!-- BERITA DETAIL -->
<div class="container my-5">
  <div class="card mx-auto" style="max-width: 900px;">
    <?php if (!empty($berita['gambar'])): ?>
      <img src="uploads/<?= htmlspecialchars($berita['gambar']) ?>" class="w-100" style="height: 400px; object-fit: cover;" alt="Gambar Berita">
    <?php endif; ?>
    <div class="card-body p-5">
      <h2 class="card-title"><?= htmlspecialchars($berita['judul']) ?></h2>
      <p class="date-label mb-4">Diposting pada <?= date('d M Y', strtotime($berita['tanggal'])) ?></p>
      <p class="card-text"><?= nl2br(htmlspecialchars($berita['isi'])) ?></p>
      <a href="index.php" class="btn btn-secondary mt-4">← Kembali ke Beranda</a>
    </div>
  </div>
</div>

<!-- FOOTER -->
<footer class="text-center text-muted mt-5 mb-4">
  <small>&copy; <?= date('Y') ?> Portal Berita Sekolah. All rights reserved.</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
