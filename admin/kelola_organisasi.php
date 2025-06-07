<?php
require_once '../db.php';
session_start();

// Fungsi untuk menandai menu aktif
function isActive($filename) {
  return basename($_SERVER['PHP_SELF']) === $filename ? 'active' : '';
}

// Ambil data organisasi
$organisasi = $conn->query("SELECT * FROM organisasi ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Organisasi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    .navbar-brand {
      font-weight: bold;
      font-size: 1.5rem;
    }
    .nav-link.active {
      font-weight: 600;
      color: #0d6efd !important;
    }
    img {
      border-radius: 5px;
    }
    body {
      background-color: #f8f9fa;
    }
    .form-section {
      background-color: white;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.05);
    }
  </style>
</head>
<body>

<!-- Navbar -->
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
        <li class="nav-item"><a class="nav-link <?= isActive('dashboard.php') ?>" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link <?= isActive('kelola_siswa.php') ?>" href="kelola_siswa.php">Kelola Siswa</a></li>
        <li class="nav-item"><a class="nav-link <?= isActive('verifikasi_pembayaran.php') ?>" href="verifikasi_pembayaran.php">Pembayaran</a></li>
        <li class="nav-item"><a class="nav-link <?= isActive('daftar_resi.php') ?>" href="daftar_resi.php">Resi Disetujui</a></li>
        <li class="nav-item"><a class="nav-link <?= isActive('kelola_berita.php') ?>" href="kelola_berita.php">Berita/Event</a></li>
        <li class="nav-item"><a class="nav-link <?= isActive('kelola_organisasi.php') ?>" href="kelola_organisasi.php">Organisasi</a></li>
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

<!-- Konten Utama -->
<div class="container mt-4">
  <div class="form-section">
    <h2 class="mb-4">Tambah Organisasi Sekolah</h2>

    <form action="proses_tambah_organisasi.php" method="post" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="nama" class="form-label">Nama Organisasi</label>
        <input type="text" name="nama" id="nama" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="deskripsi" class="form-label">Deskripsi</label>
        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4" required></textarea>
      </div>

      <div class="mb-3">
        <label for="tahun_dibangun" class="form-label">Tahun Dibangun</label>
        <input type="number" name="tahun_dibangun" id="tahun_dibangun" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="foto_utama" class="form-label">Foto Utama</label>
        <input type="file" name="foto_utama" id="foto_utama" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="dokumentasi" class="form-label">Dokumentasi Kegiatan (opsional)</label>
        <input type="file" name="dokumentasi[]" id="dokumentasi" class="form-control" multiple>
      </div>

      <button type="submit" class="btn btn-primary">Simpan Organisasi</button>
    </form>
  </div>

  <!-- Daftar Organisasi -->
  <div class="mt-5">
    <h4 class="mb-3">Daftar Organisasi</h4>
    <?php if (!empty($organisasi)): ?>
      <div class="row g-3">
        <?php foreach ($organisasi as $org): ?>
          <div class="col-md-4">
            <div class="card shadow-sm h-100">
              <img src="../uploads/<?= htmlspecialchars($org['foto_utama']) ?>" class="card-img-top" style="height: 180px; object-fit: cover;" alt="Foto Organisasi">
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($org['nama']) ?></h5>
                <p class="text-muted small mb-1">Tahun: <?= htmlspecialchars($org['tahun_dibangun']) ?></p>
                <p class="card-text"><?= substr(strip_tags($org['deskripsi']), 0, 100) ?>...</p>
                <div class="d-flex justify-content-between mt-3">
                  <a href="edit_organisasi.php?id=<?= $org['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                  <a href="hapus_organisasi.php?id=<?= $org['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus organisasi ini?')">Hapus</a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="alert alert-info">Belum ada organisasi yang ditambahkan.</div>
    <?php endif; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
