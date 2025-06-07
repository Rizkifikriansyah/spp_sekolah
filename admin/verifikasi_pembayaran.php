<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

require_once '../db.php';

// Ambil data pembayaran dengan status 'menunggu'
$query = "SELECT p.*, s.nama, s.kelas FROM pembayaran p 
          JOIN siswa s ON p.siswa_id = s.id 
          WHERE p.status = 'menunggu'
          ORDER BY p.tanggal_pembayaran DESC";

if ($stmt = $conn->prepare($query)) {
  $stmt->execute();
  $result = $stmt->get_result();
} else {
  die("Query error: " . $conn->error);
}

function isActive($filename) {
  return basename($_SERVER['PHP_SELF']) === $filename ? 'active' : '';
}

function formatTanggal($date) {
  return date('d M Y', strtotime($date));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Verifikasi Pembayaran - Admin</title>
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
  </style>
</head>
<body>

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
                <li class="nav-item">
          <a class="nav-link <?= isActive('kelola_organisasi.php') ?>" href="kelola_organisasi.php">Organisasi</a>
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

<div class="container mt-4">
  <h2>Verifikasi Pembayaran SPP</h2>

  <?php if ($result && $result->num_rows > 0): ?>
  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-dark">
        <tr>
          <th>Nama Siswa</th>
          <th>Kelas</th>
          <th>Jumlah</th>
          <th>Tanggal</th>
          <th>Bukti Resi</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['nama']) ?></td>
          <td><?= htmlspecialchars($row['kelas']) ?></td>
          <td>Rp<?= number_format($row['jumlah'], 0, ',', '.') ?></td>
          <td><?= formatTanggal($row['tanggal_pembayaran']) ?></td>
          <td>
            <a href="../uploads/<?= urlencode($row['bukti_resi']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">Lihat</a>
          </td>
          <td>
            <a href="proses_verifikasi.php?id=<?= (int)$row['id'] ?>&aksi=setujui" class="btn btn-success btn-sm" onclick="return confirm('Setujui pembayaran ini?')">Setujui</a>
            <a href="proses_verifikasi.php?id=<?= (int)$row['id'] ?>&aksi=tolak" class="btn btn-danger btn-sm" onclick="return confirm('Tolak pembayaran ini?')">Tolak</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
    <div class="alert alert-info">Tidak ada pembayaran yang menunggu verifikasi.</div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
