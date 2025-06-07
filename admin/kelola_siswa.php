<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

require_once '../db.php';

// Ambil data siswa dan username
$sql = "SELECT s.id, s.nis, s.nama, s.kelas, s.alamat, u.username 
        FROM siswa s 
        JOIN users u ON s.user_id = u.id";
$result = $conn->query($sql);

// Fungsi untuk cek menu aktif
function isActive($filename) {
  return basename($_SERVER['PHP_SELF']) === $filename ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Kelola Data Siswa - Admin</title>
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
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link <?= isActive('dashboard.php') ?>" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link <?= isActive('kelola_siswa.php') ?>" href="kelola_siswa.php">Kelola Siswa</a></li>
        <li class="nav-item"><a class="nav-link <?= isActive('verifikasi_pembayaran.php') ?>" href="verifikasi_pembayaran.php">Pembayaran</a></li>
                <li class="nav-item">
          <a class="nav-link <?= isActive('daftar_resi.php') ?>" href="daftar_resi.php">Resi Disetujui</a>
        </li>
        <li class="nav-item"><a class="nav-link <?= isActive('kelola_berita.php') ?>" href="kelola_berita.php">Berita/Event</a></li>
                <li class="nav-item">
          <a class="nav-link <?= isActive('kelola_organisasi.php') ?>" href="kelola_organisasi.php">Organisasi</a>
        </li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <?= htmlspecialchars($_SESSION['user']['username']) ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Konten utama -->
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Kelola Data Siswa</h2>
    <a href="tambah_siswa.php" class="btn btn-success">+ Tambah Siswa</a>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-dark">
        <tr>
          <th>No</th>
          <th>NIS</th>
          <th>Nama</th>
          <th>Kelas</th>
          <th>Alamat</th>
          <th>Username</th>
          <th width="180">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): $no = 1; ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($row['nis']) ?></td>
              <td><?= htmlspecialchars($row['nama']) ?></td>
              <td><?= htmlspecialchars($row['kelas']) ?></td>
              <td><?= htmlspecialchars($row['alamat']) ?></td>
              <td><?= htmlspecialchars($row['username']) ?></td>
              <td>
                <a href="edit_siswa.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="hapus_siswa.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus siswa ini?')">Hapus</a>
                <a href="kelola_tagihan.php?siswa_id=<?= $row['id'] ?>" class="btn btn-info btn-sm mt-1">Tagihan</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="7" class="text-center">Belum ada data siswa</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
