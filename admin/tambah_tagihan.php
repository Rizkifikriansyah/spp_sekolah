<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}
require_once '../db.php';

$siswa_id = $_GET['siswa_id'] ?? null;
if (!$siswa_id) {
  die("ID siswa tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $semester = $_POST['semester'];
  $jumlah = $_POST['jumlah'];
  $tanggal = $_POST['tanggal'];
  $keterangan = $_POST['keterangan'];

  $stmt = $conn->prepare("INSERT INTO tagihan (siswa_id, semester, jumlah, tanggal, keterangan) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("isiss", $siswa_id, $semester, $jumlah, $tanggal, $keterangan);
  $stmt->execute();

  header("Location: kelola_tagihan.php?siswa_id=$siswa_id");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Tambah Tagihan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
  <h2>Tambah Tagihan</h2>
  <form method="POST">
    <div class="mb-3">
      <label for="semester" class="form-label">Semester</label>
      <input type="text" class="form-control" name="semester" required>
    </div>
    <div class="mb-3">
      <label for="jumlah" class="form-label">Jumlah (Rp)</label>
      <input type="number" class="form-control" name="jumlah" required>
    </div>
    <div class="mb-3">
      <label for="tanggal" class="form-label">Tanggal Tagihan</label>
      <input type="date" class="form-control" name="tanggal" required>
    </div>
    <div class="mb-3">
      <label for="keterangan" class="form-label">Keterangan</label>
      <textarea class="form-control" name="keterangan"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="kelola_tagihan.php?siswa_id=<?= $siswa_id ?>" class="btn btn-secondary">Batal</a>
  </form>
</div>
</body>
</html>
