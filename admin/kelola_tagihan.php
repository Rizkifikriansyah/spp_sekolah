<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

require_once '../db.php';

if (!isset($_GET['siswa_id'])) {
  die("ID siswa tidak ditemukan.");
}

$siswa_id = (int) $_GET['siswa_id'];

// Ambil data siswa
$stmt = $conn->prepare("SELECT nama FROM siswa WHERE id = ?");
$stmt->bind_param("i", $siswa_id);
$stmt->execute();
$siswa = $stmt->get_result()->fetch_assoc();
if (!$siswa) {
  die("Siswa tidak ditemukan.");
}

// Ambil data tagihan
$query = $conn->prepare("SELECT * FROM tagihan WHERE siswa_id = ? ORDER BY semester DESC");
$query->bind_param("i", $siswa_id);
$query->execute();
$tagihan = $query->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Kelola Tagihan Siswa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<div class="container mt-4">
  <h2>Tagihan SPP: <?= htmlspecialchars($siswa['nama']) ?></h2>
  <a href="tambah_tagihan.php?siswa_id=<?= $siswa_id ?>" class="btn btn-success mb-3">+ Tambah Tagihan</a>
  <a href="kelola_siswa.php" class="btn btn-secondary mb-3">← Kembali</a>

  <?php if ($tagihan->num_rows > 0): ?>
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>Semester</th>
          <th>Jumlah</th>
          <th>Tanggal</th>
          <th>Keterangan</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $tagihan->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['semester']) ?></td>
            <td>Rp<?= number_format($row['jumlah'], 0, ',', '.') ?></td>
            <td><?= htmlspecialchars($row['tanggal']) ?></td>
            <td><?= htmlspecialchars($row['keterangan']) ?></td>
            <td>
              <a href="edit_tagihan.php?id=<?= $row['id'] ?>&siswa_id=<?= $siswa_id ?>" class="btn btn-warning btn-sm">Edit</a>
              <a href="hapus_tagihan.php?id=<?= $row['id'] ?>&siswa_id=<?= $siswa_id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus tagihan ini?')">Hapus</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p class="text-muted">Belum ada tagihan untuk siswa ini.</p>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
