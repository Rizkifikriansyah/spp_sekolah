<?php
require_once 'db.php';

// Ambil 3 organisasi terbaru
$orgQuery = "SELECT nama, deskripsi, foto_utama, tahun_dibangun FROM organisasi ORDER BY tahun_dibangun DESC LIMIT 3";
$orgResult = $conn->query($orgQuery);

// Ambil 3 berita terbaru
$query = "SELECT id, judul, isi, tanggal FROM berita ORDER BY tanggal DESC LIMIT 3";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Website SMAN 2 Kota Bima</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }

    .hero {
  background-image: url('img/bg.jpeg'); /* Ganti dengan path gambar sekolah kamu */
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  height: 50vh; /* atau 400px jika ingin lebih pendek */
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  text-align: center;
  padding: 30px 20px;
  position: relative;
}

/* Tambahkan overlay gelap agar teks lebih jelas */
.hero::before {
  content: "";
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background-color: rgba(0, 0, 0, 0.5); /* gelap transparan */
  z-index: 1;
}

.hero .container {
  position: relative;
  z-index: 2;
}
    .hero h1 {
      font-size: 3rem;
      font-weight: bold;
    }

    .card-news {
      transition: 0.3s;
      border: none;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 0 20px rgba(0,0,0,0.06);
    }

    .card-news:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.12);
    }

    .news-excerpt {
      font-size: 0.95rem;
      color: #555;
    }

    .news-date {
      font-size: 0.8rem;
      color: #888;
    }

    .visi-misi {
      background-color: #fff;
      padding: 40px 30px;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.05);
      margin-bottom: 60px;
    }

    footer {
      background-color: #343a40;
      color: #ccc;
      padding: 30px 0;
      text-align: center;
    }

    .footer-links a {
      color: #ccc;
      margin: 0 10px;
      text-decoration: none;
      transition: color 0.3s;
    }

    .footer-links a:hover {
      color: #fff;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(to right, rgb(0, 0, 0), rgb(63, 63, 63));">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
      <img src="img/logo.jpeg" alt="Logo Sekolah" width="40" height="40" class="me-2 rounded-circle" />
      SMAN 1 AMBALAWI
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
      </ul>
    </div>
  </div>
</nav>


<!-- Hero Section -->
<section class="hero">
  <div class="container">
    <h1>Selamat Datang di Website Sekolah</h1>
    <h2>SMA 1 AMBALAWI</h2>
    <p class="lead">Media informasi resmi dan pusat kegiatan sekolah</p>
  </div>
</section>

<!-- Visi dan Misi -->
<div class="container visi-misi">
  <h2 class="text-center mb-4">Visi dan Misi Sekolah</h2>
  <h5><strong>Visi:</strong></h5>
  <p>"Menjadi sekolah unggulan dalam prestasi, berkarakter, dan berwawasan lingkungan."</p>
  <h5><strong>Misi:</strong></h5>
  <ul>
    <li>Meningkatkan mutu pendidikan secara menyeluruh.</li>
    <li>Menanamkan nilai-nilai karakter kepada seluruh warga sekolah.</li>
    <li>Mengembangkan potensi peserta didik melalui kegiatan akademik dan non-akademik.</li>
    <li>Mewujudkan sekolah yang peduli dan berbudaya lingkungan.</li>
  </ul>
</div>

<!-- Konten Berita -->
<div class="container my-5">
  <h2 class="mb-4">Berita & Event Terbaru</h2>
  <div class="row g-4">
    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col-md-4">
          <div class="card card-news h-100">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($row['judul']) ?></h5>
              <p class="news-date">Diposting: <?= date('d M Y', strtotime($row['tanggal'])) ?></p>
              <p class="news-excerpt"><?= substr(strip_tags($row['isi']), 0, 100) ?>...</p>
              <a href="berita_detail.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm mt-2">Baca Selengkapnya</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="col">
        <div class="alert alert-warning text-center">Belum ada berita tersedia.</div>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Organisasi Sekolah -->
<div class="container my-5">
  <h2 class="mb-4">Organisasi Sekolah</h2>
  <div class="row g-4">
    <?php
    $orgQuery = "SELECT id, nama, deskripsi, foto_utama, tahun_dibangun FROM organisasi ORDER BY tahun_dibangun DESC LIMIT 3";
    $orgResult = $conn->query($orgQuery);
    ?>

    <?php if ($orgResult && $orgResult->num_rows > 0): ?>
      <?php while ($org = $orgResult->fetch_assoc()): ?>
        <div class="col-md-4">
          <div class="card h-100 shadow-sm">
            <img src="uploads/<?= htmlspecialchars($org['foto_utama']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="Foto Organisasi">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($org['nama']) ?></h5>
              <p class="card-text"><?= substr(strip_tags($org['deskripsi']), 0, 100) ?>...</p>
              <p class="text-muted small">Tahun Berdiri: <?= htmlspecialchars($org['tahun_dibangun']) ?></p>
              <a href="organisasi_detail.php?id=<?= $org['id'] ?>" class="btn btn-primary btn-sm mt-auto">Selengkapnya</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="col">
        <div class="alert alert-info text-center">Belum ada data organisasi.</div>
      </div>
    <?php endif; ?>
  </div>
</div>



<!-- Footer -->
<footer>
  <div class="container">
    <p>&copy; <?= date('Y') ?> SMAN 1 Ambalawi. All rights reserved.</p>
    <div class="footer-links">
      <a href="https://www.facebook.com/smansatuambalawi" target="_blank">Facebook</a> |
      <a href="https://www.instagram.com/smansa.ambalawi?igsh=MWMyZWMyYXRocGFhdw==" target="_blank">Instagram</a>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
