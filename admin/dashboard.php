<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

// Fungsi helper untuk aktif menu
function isActive($filename) {
  return basename($_SERVER['PHP_SELF']) === $filename ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    .navbar-brand {
      font-weight: 700;
      font-size: 1.5rem;
      color: #ffffff;
    }
    .nav-link.active {
      font-weight: 600;
      color: #0d6efd !important;
    }
    .card-title i {
      margin-right: 8px;
    }
  </style>
</head>
<body>

<!-- Navbar Admin -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
      <img src="../img/logo.jpeg" alt="Logo Sekolah" width="40" height="40" class="me-2 rounded-circle" />
      SMAN 1 AMBALAWI
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin"
      aria-controls="navbarAdmin" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarAdmin">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= isActive('dashboard.php') ?>" href="dashboard.php">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= isActive('kelola_siswa.php') ?>" href="kelola_siswa.php">Kelola Siswa</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= isActive('verifikasi_pembayaran.php') ?>" href="verifikasi_pembayaran.php">Pembayaran</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= isActive('daftar_resi.php') ?>" href="daftar_resi.php">Resi Disetujui</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= isActive('kelola_berita.php') ?>" href="kelola_berita.php">Berita/Event</a>
        </li>
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

<!-- Konten -->
<div class="container mt-5">
  <div class="text-center mb-4">
    <h2>Selamat datang, <?= htmlspecialchars($_SESSION['user']['username']) ?> 👋</h2>
    <p class="text-muted">Gunakan dashboard ini untuk mengelola sistem SPP SMAN 1 AMBALAWI.</p>
  </div>

  <div class="row">
    <div class="col-md-4 mb-4">
      <div class="card border-primary shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-people"></i> Manajemen Siswa</h5>
          <p class="card-text">Tambah, ubah, dan hapus data siswa.</p>
          <a href="kelola_siswa.php" class="btn btn-primary w-100">Kelola Siswa</a>
        </div>
      </div>
    </div>

    <div class="col-md-4 mb-4">
      <div class="card border-success shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-cash-coin"></i> Pembayaran SPP</h5>
          <p class="card-text">Setujui pembayaran dan cetak resi.</p>
          <a href="verifikasi_pembayaran.php" class="btn btn-success w-100">Lihat Pembayaran</a>
        </div>
      </div>
    </div>

    <div class="col-md-4 mb-4">
      <div class="card border-warning shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-megaphone"></i> Berita / Event</h5>
          <p class="card-text">Kelola konten di halaman utama sekolah.</p>
          <a href="kelola_berita.php" class="btn btn-warning w-100 text-white">Kelola Berita</a>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
